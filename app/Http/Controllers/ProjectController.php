<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    /**
     * Display a listing of projects.
     */
    public function index()
    {
        $projects = Project::latest()->paginate(15);
        return view('projects.index', compact('projects'));
    }

    /**
     * Show the form for creating a new project.
     */
    public function create()
    {
        return view('projects.form');
    }

    /**
     * Store a newly created project.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'vendor' => 'required|string|max:255',
            'no_project' => 'required|string|unique:projects,no_project|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'status' => 'required|in:in_progress,canceled,completed',
            'files.*' => 'nullable|file|max:2048|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png',
        ]);

        DB::beginTransaction();
        try {
            $project = Project::create($validated);

            // Handle file uploads
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $filePath = $file->store('project_files', 'public');

                    ProjectFile::create([
                        'project_id' => $project->id,
                        'file_name' => $fileName,
                        'file_path' => $filePath,
                        'file_type' => $file->getClientMimeType(),
                        'file_size' => $file->getSize(),
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('projects.index')
                ->with('success', 'Project created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create project: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified project.
     */
    public function show(Project $project)
    {
        $project->load('budgets.user', 'budgets.items', 'files');
        return view('projects.show', compact('project'));
    }

    /**
     * Show the form for editing the specified project.
     */
    public function edit(Project $project)
    {
        $project->load('files');
        return view('projects.form', compact('project'));
    }

    /**
     * Update the specified project.
     */
    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'vendor' => 'required|string|max:255',
            'no_project' => 'required|string|unique:projects,no_project,' . $project->id . '|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'status' => 'required|in:in_progress,canceled,completed',
            'files.*' => 'nullable|file|max:2048|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png',
        ]);

        DB::beginTransaction();
        try {
            $project->update($validated);

            // Handle file uploads
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $filePath = $file->store('project_files', 'public');

                    ProjectFile::create([
                        'project_id' => $project->id,
                        'file_name' => $fileName,
                        'file_path' => $filePath,
                        'file_type' => $file->getClientMimeType(),
                        'file_size' => $file->getSize(),
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('projects.index')
                ->with('success', 'Project updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update project: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified project.
     */
    public function destroy(Project $project)
    {
        // Check if project has budgets
        if ($project->budgets()->count() > 0) {
            return redirect()->route('projects.index')
                ->with('error', 'Cannot delete project with existing budgets.');
        }

        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', 'Project deleted successfully.');
    }
}
