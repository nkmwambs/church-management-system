<?php 

namespace App\Cells;

class RawNotificationCell
{
    public function show(): string
    {
        return view("components/raw_notification", []);
    }
}