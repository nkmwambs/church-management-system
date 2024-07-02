<?php 

namespace App\Cells;

class ChatCell
{
    public function show(): string
    {
        return view("components/chat", []);
    }
}