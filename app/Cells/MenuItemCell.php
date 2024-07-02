<?php 

namespace App\Cells;

class MenuItemCell
{
    public function show(): string
    {
        return view("components/menu_item", []);
    }
}