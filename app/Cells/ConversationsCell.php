<?php 

namespace App\Cells;

class ConversationsCell
{
    public function show(): string
    {
        return view("components/conversations", []);
    }
}