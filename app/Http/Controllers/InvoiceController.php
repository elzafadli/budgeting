<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceFile;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with(['project', 'files'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('invoices.index', compact('invoices'));
    }

    public function create()
    {
        $projects = Project::orderBy('name')->get();
        return view('invoices.form', compact('projects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'invoice_date' => 'required|date',
            'description' => 'nullable|string',
            'files.*' => 'nullable|file|max:2040|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png',
        ]);

        DB::beginTransaction();
        try {
            // Generate invoice number
            $lastInvoice = Invoice::latest()->first();
            $invoiceNumber = 'INV-' . date('Ymd') . '-' . str_pad(($lastInvoice ? $lastInvoice->id + 1 : 1), 4, '0', STR_PAD_LEFT);

            // Create invoice
            $invoice = Invoice::create([
                'project_id' => $validated['project_id'],
                'invoice_number' => $invoiceNumber,
                'invoice_date' => $validated['invoice_date'],
                'description' => $validated['description'],
                'amount' => 0,
                'status' => 'completed',
            ]);

            // Update project status to 'invoice'
            $project = Project::find($validated['project_id']);
            if ($project) {
                $project->update(['status' => 'invoice']);
            }

            // Handle file uploads
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $filePath = $file->store('invoice_files', 'public');

                    InvoiceFile::create([
                        'invoice_id' => $invoice->id,
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $filePath,
                        'file_type' => $file->getClientMimeType(),
                        'file_size' => $file->getSize(),
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('invoices.show', $invoice)->with('success', 'Invoice created successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to create invoice: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['project', 'files']);
        return view('invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        $invoice->load('files');
        $projects = Project::orderBy('name')->get();
        return view('invoices.form', compact('invoice', 'projects'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'invoice_date' => 'required|date',
            'description' => 'nullable|string',
            'files.*' => 'nullable|file|max:2040|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png',
        ]);

        DB::beginTransaction();
        try {
            // Update invoice
            $invoice->update([
                'project_id' => $validated['project_id'],
                'invoice_date' => $validated['invoice_date'],
                'description' => $validated['description'],
            ]);

            // Handle file uploads
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $filePath = $file->store('invoice_files', 'public');

                    InvoiceFile::create([
                        'invoice_id' => $invoice->id,
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $filePath,
                        'file_type' => $file->getClientMimeType(),
                        'file_size' => $file->getSize(),
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('invoices.show', $invoice)->with('success', 'Invoice updated successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to update invoice: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Invoice $invoice)
    {
        // Delete associated files from storage
        foreach ($invoice->files as $file) {
            Storage::disk('public')->delete($file->file_path);
        }

        $invoice->delete();
        return redirect()->route('invoices.index')->with('success', 'Invoice deleted successfully.');
    }
}
