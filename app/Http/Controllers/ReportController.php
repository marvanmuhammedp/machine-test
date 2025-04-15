<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ReportController extends Controller
{
    
    public function show($id)
    {
        try {
            $project = Project::with(['tasks.remarks'])
                ->where('id', $id)
                ->where('user_id', auth()->id())
                ->firstOrFail();

            $report = [
                'project' => [
                    'id'          => $project->id,
                    'name'        => $project->name,
                    'description' => $project->description,
                ],
                'tasks' => $project->tasks->map(function ($task) {
                    return [
                        'task_id'          => $task->id,
                        'title'       => $task->title,
                        'description' => $task->description,
                        'status'      => $task->status,
                        'remarks'     => $task->remarks->map(function ($remark) {
                            return [
                                'status' => $remark->status,
                                'remark' => $remark->remark,
                                'date'   => Carbon::parse($remark->created_at)->toDateString(), // Ensuring Carbon instance
                            ];
                        }),
                    ];
                }),
            ];

            return response()->json([
                'status' => true,
                'message' => 'Project details fetched successfully',
                'data' => $report,
            ], 200);
            
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Project not found or unauthorized',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch project details',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
