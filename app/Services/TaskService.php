<?php
namespace App\Services;  

use App\Models\Task;
use Illuminate\Support\Facades\Auth;

class TaskService
{
    public function createTask(array $data): Task
    {
        return Task::create($data);
    }

    public function updateTask(Task $task, array $data): Task
    {
       
        if (Auth::user()->role === 'member') {  
            $data = ['status' => $data['status'] ?? $task->status];
        }

        $task->update($data);
        return $task->fresh();  
    }
}