<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    public function viewAny(User $user)
    {
        return true; 
    }

    public function view(User $user, Task $task)
    {
        return true; 
    }

    public function create(User $user)
    {
        return $user->role === 'admin'; 
    }

    public function update(User $user, Task $task)
    {
        return $user->role === 'admin'; 
    }

    public function delete(User $user, Task $task)
    {
        return $user->role === 'admin'; 
    }

    public function restore(User $user, Task $task)
    {
        return $user->role === 'admin'; 
    }

    public function forceDelete(User $user, Task $task)
    {
        return $user->role === 'admin'; 
    }
    
    public function updateStatus(User $user, Task $task)
    {
        return $user->role === 'member'; 
    }
}