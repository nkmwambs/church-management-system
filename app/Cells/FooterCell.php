<?php 

namespace App\Cells;

class FooterCell
{
    public function show(): string
    {
        return view("components/footer", []);
    }
}