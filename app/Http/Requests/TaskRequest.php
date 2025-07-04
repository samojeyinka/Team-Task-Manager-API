<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'sometimes|in:pending,in_progress,completed',
            'due_date' => 'required|date|after_or_equal:today',
        ];

        if ($this->isMethod('POST') || auth()->user()->isAdmin()) {
            $rules['assigned_to'] = 'required|exists:users,id';
        }

        return $rules;
    }
}