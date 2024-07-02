<?php 

namespace App\Cells;

class MessageNotificationCell
{
    public function show(): string
    {
        return view("components/message_notification", []);
    }
}