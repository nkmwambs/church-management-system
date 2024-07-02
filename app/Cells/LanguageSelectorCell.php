<?php 

namespace App\Cells;

class LanguageSelectorCell
{
    public function show(): string
    {
        return view("components/language_selector", []);
    }
}