<?php

namespace App\Models;

class Login_token_model extends MY_Model
{

    protected $table = 'login_token';

    public function __construct()
    {
        parent::__construct();
    }
}
