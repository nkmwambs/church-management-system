<?php 

namespace App\Libraries;

class ParticipantLibrary extends CoreLibrary {
    public function __construct() {
        parent::__construct();
    }

    function columns(){
        $fields = ['member_id', 'event_id', 'status', 'created_at', 'created_by','mark_participation'];
        return $fields;
    }

    public function displayAs(){
        return ['member_id' => get_phrase('member'), 'event_id' => get_phrase('event'),'status' => get_phrase('participant_status')];
    }

    public function buildCrud($crud){
        $crud->setRelation('event_id', 'events', 'name');
        $crud->setRelation('member_id', 'members', '{first_name} {last_name} - {date_of_birth}');

        $crud->callbackColumn('mark_participation', function ($value, $row) {
            $buttonLabel = get_phrase('attend');
            $newStatus = 'attended';
            $colorCode = 'primary';
            if($row->status == 'attended'){
                $buttonLabel = get_phrase('unattend');
                $newStatus = 'registered';
                $colorCode = 'info';
            }

            $buttonCancelLabel = get_phrase('cancel');
            $newCancelStatus = 'cancelled';
            $colorCancelCode = 'danger';
            if($row->status == 'cancelled'){
                $buttonCancelLabel = get_phrase('reinstate');
                $newCancelStatus = 'registered';
                $colorCancelCode = 'info';
            }

            $attendButton = "";
            if($value = 'registered '){
                $attendButton = '<a href="'.site_url('participant/mark_attendance/'.$row->id.'/'.$newStatus).'" class = "btn btn-'.$colorCode.' markAttend">'.$buttonLabel.'</a>';
            }
            $cancelButton = '<a href="'.site_url('participant/mark_attendance/'.$row->id.'/'.$newCancelStatus).'" class = "btn btn-'.$colorCancelCode.' markAttend">'.$buttonCancelLabel.'</a>';
            return $attendButton .' '. $cancelButton;
            
        });
    }

    private function getParticipantInfo($participant_id){
        $builder = $this->read_db->table($this->table);
        $builder->where('id', $participant_id);
        $participant = $builder->get()->getFirstRow();

        return $participant;
    }

    public function markAttendance($participant_id, $new_status){
        // $participant = $this->getParticipantInfo($participant_id);
        // if($participant->status == 'registered'){
            $builder = $this->write_db->table($this->table);
            $builder->where('id', $participant_id);
            $builder->update(['status' => $new_status]);
        // }
        // log_message('error', $status);
    }
}