<?php

namespace App\Controllers;

class Backend_portal_general extends MY_Backend
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        return view('backend/header', $this->data) . view('backend/index', $this->data) . view('backend/footer', $this->data);
    }
}
