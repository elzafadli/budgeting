<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\BudgetItem;
use App\Models\BudgetFile;
use App\Models\BudgetApproval;
use App\Models\Project;
use App\Models\Account;
use App\Models\AccountBank;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class BudgetDetailController extends Controller
{
    public function index()
    {
        $projects = Project::orderBy('name')->get();
        $users = User::orderBy('name')->get();
        $accounts = Account::orderBy('account_number')->get();
        return view('budget-details.index', compact('projects', 'users', 'accounts'));
    }

    public function getData(Request $request)
    {
        $query = BudgetItem::with(['budget.user', 'budget.project', 'account'])
            ->join('budgets', 'budget_items.budget_id', '=', 'budgets.id')
            ->leftJoin('projects', 'budgets.project_id', '=', 'projects.id')
            ->leftJoin('users', 'budgets.user_id', '=', 'users.id')
            ->leftJoin('accounts', 'budget_items.account_id', '=', 'accounts.id')
            ->select('budget_items.*');

        // Apply project filter
        if ($request->has('project_id') && $request->project_id != '') {
            $query->where('budgets.project_id', $request->project_id);
        }

        // Apply requestor filter
        if ($request->has('user_id') && $request->user_id != '') {
            $query->where('budgets.user_id', $request->user_id);
        }

        // Apply status filter
        if ($request->has('status') && $request->status != '') {
            $query->where('budgets.status', $request->status);
        }

        // Apply account filter
        if ($request->has('account_id') && $request->account_id != '') {
            $query->where('budget_items.account_id', $request->account_id);
        }

        // Calculate grand total from filtered results
        $grandTotal = (clone $query)->sum('budget_items.total_price');

        return DataTables::of($query)
            ->addColumn('request_no', function ($item) {
                if ($item->budget) {
                    return '<span class="small"><a href="' . route('budgets.show', $item->budget_id) . '" target="_blank">' . $item->budget->request_no . '</a></span>';
                }
                return '<span class="small">-</span>';
            })
            ->addColumn('project', function ($item) {
                if ($item->budget && $item->budget->project) {
                    return '<span class="small"><a href="' . route('projects.show', $item->budget->project->id) . '" target="_blank">' . $item->budget->project->name . '</a></span>';
                }
                return '<span class="small">-</span>';
            })
            ->addColumn('requestor', function ($item) {
                return '<span class="small">' . ($item->budget && $item->budget->user ? $item->budget->user->name : '-') . '</span>';
            })
            ->addColumn('account', function ($item) {
                return '<span class="small">' . ($item->account ? $item->account->account_description : '-') . '</span>';
            })
            ->addColumn('remarks', function ($item) {
                if (!$item->remarks) {
                    return '<span class="small">-</span>';
                }

                $remarks = $item->remarks;
                $maxLength = 50;

                if (strlen($remarks) > $maxLength) {
                    $shortRemarks = substr($remarks, 0, $maxLength) . '...';
                    $fullRemarks = htmlspecialchars($remarks, ENT_QUOTES);
                    return '<span class="small">' . htmlspecialchars($shortRemarks) . ' <i class="bi bi-info-circle text-primary" style="cursor: pointer;" data-bs-toggle="tooltip" data-bs-html="true" title="' . $fullRemarks . '"></i></span>';
                }

                return '<span class="small">' . htmlspecialchars($remarks) . '</span>';
            })
            ->addColumn('qty', function ($item) {
                return '<span class="small">' . $item->qty . '</span>';
            })
            ->addColumn('date', function ($item) {
                if ($item->budget && $item->budget->document_date) {
                    return '<span class="small">' . $item->budget->document_date->format('d M Y') . '</span>';
                }
                return '<span class="small">-</span>';
            })
            ->addColumn('total_price', function ($item) {
                $currentUserRole = auth()->user()->role;
                $isCashierBudget = $item->budget && $item->budget->user && $item->budget->user->role === 'cashier';
                $canViewAmount = in_array($currentUserRole, ['cashier', 'finance']) || !$isCashierBudget;

                if ($canViewAmount) {
                    return '<span class="small text-end">' . number_format($item->total_price, 0, ',', '.') . '</span>';
                } else {
                    return '<span class="small text-center"><span class="badge bg-warning text-dark">Restricted</span></span>';
                }
            })
            ->addColumn('status', function ($item) {
                $badges = [
                    'draft' => '<span class="badge bg-secondary">Draft</span>',
                    'submitted' => '<span class="badge bg-primary">Submitted</span>',
                    'pm_approved' => '<span class="badge bg-info">PM Approved</span>',
                    'finance_approved' => '<span class="badge bg-success">Finance Approved</span>',
                    'rejected' => '<span class="badge bg-danger">Rejected</span>',
                    'completed' => '<span class="badge bg-dark">Completed</span>',
                ];
                $status = $item->budget ? $item->budget->status : 'draft';
                return '<span class="small">' . ($badges[$status] ?? '<span class="badge bg-secondary">' . ucfirst($status) . '</span>') . '</span>';
            })
            ->rawColumns(['request_no', 'project', 'requestor', 'account', 'remarks', 'qty', 'date', 'total_price', 'status'])
            ->with('grandTotal', $grandTotal)
            ->make(true);
    }

    public function export(Request $request)
    {
        $query = BudgetItem::with(['budget.user', 'budget.project', 'account'])
            ->join('budgets', 'budget_items.budget_id', '=', 'budgets.id')
            ->leftJoin('projects', 'budgets.project_id', '=', 'projects.id')
            ->leftJoin('users', 'budgets.user_id', '=', 'users.id')
            ->leftJoin('accounts', 'budget_items.account_id', '=', 'accounts.id')
            ->select('budget_items.*', 'budgets.request_no', 'budgets.status', 'projects.name as project_name', 'users.name as user_name', 'accounts.account_description');

        // Apply filters
        if ($request->has('project_id') && $request->project_id != '') {
            $query->where('budgets.project_id', $request->project_id);
        }
        if ($request->has('user_id') && $request->user_id != '') {
            $query->where('budgets.user_id', $request->user_id);
        }
        if ($request->has('status') && $request->status != '') {
            $query->where('budgets.status', $request->status);
        }
        if ($request->has('account_id') && $request->account_id != '') {
            $query->where('budget_items.account_id', $request->account_id);
        }

        $items = $query->orderBy('projects.name')->get();

        // Create Excel file
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set title
        $sheet->setTitle('Laporan Pengajuan');

        // Set headers
        $headers = ['Project', 'No. Document', 'Requestor', 'Kategori', 'Uraian', 'Qty', 'Harga Satuan', 'Total', 'Status'];
        $column = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($column . '1', $header);
            $column++;
        }

        // Style header row
        $sheet->getStyle('A1:I1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);

        // Add data
        $row = 2;
        $currentUserRole = auth()->user()->role;

        foreach ($items as $item) {
            $status = $item->status ?? 'draft';
            $statusText = ucfirst(str_replace('_', ' ', $status));

            // Check if budget was created by cashier
            $isCashierBudget = $item->budget && $item->budget->user && $item->budget->user->role === 'cashier';
            $canViewAmount = in_array($currentUserRole, ['cashier', 'finance']) || !$isCashierBudget;

            $sheet->setCellValue('A' . $row, $item->project_name ?? '-');
            $sheet->setCellValue('B' . $row, $item->request_no ?? '-');
            $sheet->setCellValue('C' . $row, $item->user_name ?? '-');
            $sheet->setCellValue('D' . $row, $item->account_description ?? '-');
            $sheet->setCellValue('E' . $row, $item->remarks ?? '-');
            $sheet->setCellValue('F' . $row, $item->qty);

            if ($canViewAmount) {
                $sheet->setCellValue('G' . $row, $item->unit_price ?? 0);
                $sheet->setCellValue('H' . $row, $item->total_price);
            } else {
                $sheet->setCellValue('G' . $row, 0);
                $sheet->setCellValue('H' . $row, 0);
            }

            $sheet->setCellValue('I' . $row, $statusText);

            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Apply borders to all data
        $sheet->getStyle('A1:I' . ($row - 1))->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);

        // Generate filename
        $filename = 'laporan_pengajuan_' . date('Y-m-d_His') . '.xlsx';

        // Create writer and save to output
        $writer = new Xlsx($spreadsheet);

        // Set headers for download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

}
