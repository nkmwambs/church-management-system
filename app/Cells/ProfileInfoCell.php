<?php 

namespace App\Cells;

class ProfileInfoCell
{
    public function show(): string
    {
        $session = \Config\Services::session();
        $userFullName = $session->get('full_name');
        $user_id = $session->get('user_id');
        return view("components/profile_info", ['userFullName' => $userFullName, 'user_id' => $user_id]);
    }
}