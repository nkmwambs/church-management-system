<?php 

namespace App\Cells;

class SideBarCell
{
    public function show(): string
    {
        return view("components/sidebar", []);
    }
}