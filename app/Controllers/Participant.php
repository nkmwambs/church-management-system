<?php

namespace App\Controllers;

class Participant extends BaseController
{
   function mark_attendance($participant_id, $new_status = 'attended'){
        // log_message('error', $participant_id);
        $participantLibrary = new \App\Libraries\ParticipantLibrary();
        $participantLibrary->markAttendance($participant_id, $new_status);
        return redirect()->to('participant');
   }
}
