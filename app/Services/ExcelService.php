<?php
namespace App\Services;

use App\Models\Task;
use App\Models\User;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExcelService
{
    public function importTasks($file): array
    {
        $importedCount = 0;
        $failedCount = 0;
        $errors = [];
        
        if (($handle = fopen($file->getPathname(), 'r')) !== false) {
          
            fgetcsv($handle);
            
            while (($row = fgetcsv($handle)) !== false) {
                try {
                    Task::create([
                        'title' => $row[0] ?? '',
                        'description' => $row[1] ?? '',
                        'status' => $row[2] ?? 'pending',
                        'due_date' => isset($row[3]) ? \Carbon\Carbon::parse($row[3]) : null,
                        'assigned_to' => $row[4] ?? null,
                    ]);
                    $importedCount++;
                } catch (\Exception $e) {
                    $failedCount++;
                    $errors[] = [
                        'row' => $row,
                        'error' => $e->getMessage()
                    ];
                }
            }
            fclose($handle);
        }
        
        return [
            'message' => 'Tasks imported successfully',
            'imported_count' => $importedCount,
            'failed_count' => $failedCount,
            'errors' => $errors
        ];
    }

    public function exportTasks(User $user): StreamedResponse
    {
        $tasks = Task::visible($user)
            ->with('assignedUser')
            ->get();
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="tasks.csv"',
        ];
        
        return response()->stream(function() use ($tasks) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8 compatibility with Excel
            fwrite($file, "\xEF\xBB\xBF");
            
            // Headers
            fputcsv($file, [
                'Title', 
                'Description', 
                'Status', 
                'Assigned To Email', 
                'Due Date'
            ]);
            
            // Data
            foreach ($tasks as $task) {
                fputcsv($file, [
                    $task->title,
                    $task->description,
                    $task->status,
                    $task->assignedUser->email ?? '',
                    $task->due_date?->format('Y-m-d'),
                ]);
            }
            
            fclose($file);
        }, 200, $headers);
    }
}