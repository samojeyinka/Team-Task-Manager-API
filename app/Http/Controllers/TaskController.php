<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Gate;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\TaskRequest;
use App\Services\TaskService;
use App\Services\ExcelService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; 

class TaskController extends Controller
{
    use AuthorizesRequests; 
    protected $taskService;
    protected $excelService;

    public function __construct(TaskService $taskService, ExcelService $excelService)
    {
        $this->taskService = $taskService;
        $this->excelService = $excelService;
    }

    public function index(Request $request)
    {
        $tasks = Task::visible($request->user())
            ->with('assignedUser')
            ->paginate(15);

        return response()->json($tasks);
    }

    public function store(TaskRequest $request)
    {
      
        
        $task = $this->taskService->createTask($request->validated());

        return response()->json($task->load('assignedUser'), 201);
    }

    public function show(Task $task)
    {
        $this->authorize('view', $task);

        return response()->json($task->load('assignedUser'));
    }

    public function update(TaskRequest $request, Task $task)
    {
        $this->authorize('update', $task);

        $task = $this->taskService->updateTask($task, $request->validated());

        return response()->json($task->load('assignedUser'));
    }

    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);

        $task->delete();

        return response()->json(['message' => 'Task deleted successfully']);
    }

    public function trashed(Request $request)
    {
    

        $tasks = Task::onlyTrashed()
            ->with('assignedUser')
            ->paginate(15);

        return response()->json($tasks);
    }

    public function restore($id)
    {
        $task = Task::onlyTrashed()->findOrFail($id);
    

        $task->restore();

        return response()->json(['message' => 'Task restored successfully']);
    }

    public function forceDelete($id)
    {
        $task = Task::onlyTrashed()->findOrFail($id);
       

        $task->forceDelete();

        return response()->json(['message' => 'Task permanently deleted']);
    }

    public function importTasks(Request $request)
    {

        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv'
        ]);

        $result = $this->excelService->importTasks($request->file('file'));

        return response()->json($result);
    }

    public function exportTasks(Request $request)
    {
        return $this->excelService->exportTasks($request->user());
    }

    public function updateStatus(Request $request, Task $task)
    {
 

        $request->validate([
            'status' => 'required|in:pending,in_progress,completed'
        ]);

        $task->update(['status' => $request->status]);

        return response()->json($task->load('assignedUser'));
    }
}