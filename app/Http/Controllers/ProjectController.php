<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProjectController extends Controller
{
  
    public function index()
    {
        try {
            $projects = Project::where('user_id', auth()->id())->get();
    
            if ($projects->isEmpty()) {
                return response()->json([
                    'status' => true,
                    'message' => 'No projects found',
                    'data' => []
                ], 200);
            }
    
            return response()->json([
                'status' => true,
                'message' => 'Projects fetched successfully',
                'data' => $projects
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch projects',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function store(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $project = Project::create([
                'user_id'    => auth()->id(),
                'name'       => $request->name,
                'description'=> $request->description ?? null,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Project created successfully',
                'data' => $project
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to create project',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    
    public function update(Request $request, $id)
    {
        $validator = \Validator::make($request->all(), [
            'name'       => 'required|string|max:255',
            'description'=> 'nullable|string',
            // 'status'     => 'nullable|string|in:pending,approved,rejected',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }
    
        try {
            $project = Project::where('id', $id)
                              ->where('user_id', auth()->id())
                              ->firstOrFail();
    
            $project->update([
                'name'        => $request->name,
                'description' => $request->description ?? null,
                // 'status'      => $request->status ?? null,
            ]);
    
            return response()->json([
                'status'  => true,
                'message' => 'Project updated successfully',
                'data'    => $project
            ]);
    
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Project not found or unauthorized'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Failed to update project',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
    
    public function destroy($id)
    {
        try {
            $project = Project::where('id', $id)
                              ->where('user_id', auth()->id())
                              ->firstOrFail();

            $project->delete();

            return response()->json([
                'status' => true,
                'message' => 'Project deleted successfully'
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Project not found or unauthorized'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to delete project',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
