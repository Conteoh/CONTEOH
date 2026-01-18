<?php

namespace App\Models;

class Page_model extends MY_Model
{

    protected $table = "page";

    public function __construct()
    {
        parent::__construct();

        $this->exportation_exclused_fields = [
            "is_deleted",
            "modified_date",
        ];
        $this->yes_no_fields = [];

	
    }

    
}