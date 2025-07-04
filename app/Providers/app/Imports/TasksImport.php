<?php
namespace App\Imports;

use App\Models\Task;
use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Validation\Rule;

class TasksImport implements ToModel, WithHeadingRow, WithValidation
{
    private $importedCount = 0;
    private $failedCount = 0;
    private $errors = [];

    public function model(array $row)
    {
        try {
            $user = User::where('email', $row['assigned_to_email'])->first();
            
            if (!$user) {
                $this->failedCount++;
                $this->errors[] = "User not found for email: {$row['assigned_to_email']}";
                return null;
            }

            $this->importedCount++;
            
            return new Task([
                'title' => $row['title'],
                'description' => $row['description'] ?? '',
                'assigned_to' => $user->id,
                'status' => $row['status'],
                'due_date' => $row['due_date'],
            ]);
        } catch (\Exception $e) {
            $this->failedCount++;
            $this->errors[] = "Error importing row: " . $e->getMessage();
            return null;
        }
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'status' => ['required', Rule::in(['pending', 'in_progress', 'completed'])],
            'assigned_to_email' => 'required|email',
            'due_date' => 'required|date',
        ];
    }

    public function getImportedCount()
    {
        return $this->importedCount;
    }

    public function getFailedCount()
    {
        return $this->failedCount;
    }

    public function getErrors()
    {
        return $this->errors;
    }
}