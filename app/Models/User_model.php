<?php

namespace App\Models;

class User_model extends MY_Model
{

    protected $table = "user";

    public function __construct()
    {
        parent::__construct();

        $this->yes_no_fields = ['is_email_verified'];
        $this->exportation_exclused_fields = ['password', 'verification_token', 'last_sent_time', 'avatar'];
    }

    public function level_kv_list()
    {
        return [
            "-1" => "Superadmin",
            "1" => "Administrator",
        ];
    }

    public function status_kv_list()
    {

        $kv_list = array(
            '0' => 'Inactive',
            '1' => 'Active',
        );

        return $kv_list;
    }
}
