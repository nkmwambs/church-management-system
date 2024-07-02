<?php 

namespace App\Cells;

class TaskNotificationCell
{
    public function show(): string
    {
        return view("components/task_notification", []);
    }
}