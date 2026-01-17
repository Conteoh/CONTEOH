<?php

namespace App\Models;

class Enquiry_model extends MY_Model
{

    protected $table = "enquiry";

    public function __construct()
    {
        parent::__construct();

        $this->exportation_exclused_fields = [
            "is_deleted",
            "modified_date",
        ];
        $this->yes_no_fields = [];

	
    }

    public function status_kv_list()
    {
        return [
            "0" => "Pending",
			"1" => "Processing",
			"2" => "Processed",
        ];
    }

	
}