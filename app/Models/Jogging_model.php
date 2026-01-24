<?php

namespace App\Models;

class Jogging_model extends MY_Model
{

    protected $table = "jogging";

    public function __construct()
    {
        parent::__construct();

        $this->exportation_exclused_fields = [
            "is_deleted",
            "modified_date",
        ];
        $this->yes_no_fields = [];

	
    }

    public function section_kv_list()
    {
        return [
            "0" => "Morning",
			"1" => "Afternoon",
			"2" => "Evening",
			"3" => "Night",
        ];
    }

	
}