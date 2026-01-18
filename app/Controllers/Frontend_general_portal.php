<?php

namespace App\Controllers;

class Frontend_general_portal extends BaseController
{
    protected $data = [];

    protected $Setting_model;

    public function __construct()
    {
        //Load Model
        $this->Setting_model = model('App\Models\Setting_model');

        //Site config
        $site_config = [];
        $result_list = $this->Setting_model->get_all([
            'is_deleted' => 0,
            'is_load_frontend' => 1,
        ]);
        foreach ($result_list as $k => $v) {
            $site_config[$v['variable']] = $v['value'];
        }
        $this->data['site_config'] = $site_config;
    }

    public function index()
    {
        return view('frontend/index');
    }
}