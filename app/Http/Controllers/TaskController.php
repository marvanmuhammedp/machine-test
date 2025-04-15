<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use App\Models\TaskRemark;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TaskController extends Controller
{
    
    public function store(Request $request, $projectId)
    {
        try {
            $project = Project::where('id', $projectId)
                              ->where('user_id', auth()->id())
                              ->firstOrFail();  
    
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
            ]);
    
            $task = Task::create([
                'project_id' => $project->id,
                'title'      => $validated['title'],
                'description'=> $validated['description'] ?? null,
            ]);
    
            return response()->json([
                'status'  => true,
                'message' => 'Task created successfully',
                'data'    => $task
            ], 201);
    
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Project not found or unauthorized'
            ], 404);
    
        } catch (ValidationException $e) {
            return response()->json([
                'status'  => false,
                'message' => 'The given data was invalid.',
                'errors'  => $e->errors()
            ], 422);
    
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Failed to create task',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
    

    public function update(Request $request, $id)
    {
        try {
            $task = Task::findOrFail($id);

            $project = $task->project;
            if ($project->user_id !== Auth::id()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'status'     => 'nullable|string|in:Pending,In Progress,Completed',

            ]);

            $task->update([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'status' => $validated['status'],
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Task updated successfully',
                'data' => $task
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Task not found',
            ], 404);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to update task',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $task = Task::findOrFail($id);
            
            if ($task->project->user_id !== auth()->id()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            $task->delete();

            return response()->json([
                'status' => true,
                'message' => 'Task deleted successfully'
            ], 200);

        } catch (ModelNotFoundException $e) {
           
            return response()->json([
                'status' => false,
                'message' => 'Task not found'
            ], 404);

        } catch (\Exception $e) {
           
            return response()->json([
                'status' => false,
                'message' => 'Failed to delete task',
                'error' => $e->getMessage()
            ], 500);

        }
    }


    public function updateStatus(Request $request, $id)
    {
        try {
            $task = Task::findOrFail($id);

            if ($task->project->user_id !== auth()->id()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            $request->validate([
                'status' => 'required|in:Pending,In Progress,Completed',
                'remark' => 'required|string',
            ]);

            $task->status = $request->status;
            $task->save();

            TaskRemark::create([
                'task_id' => $task->id,
                'remark'  => $request->remark,
                'status'  => $request->status,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Status updated with remark successfully'
            ], 200);
            
        } catch (ModelNotFoundException $e) {
            
            return response()->json([
                'status' => false,
                'message' => 'Task not found'
            ], 404);

        } catch (\Exception $e) {
            
            return response()->json([
                'status' => false,
                'message' => 'Failed to update task status',
                'error' => $e->getMessage()
            ], 500);
        
        }
    }

}
