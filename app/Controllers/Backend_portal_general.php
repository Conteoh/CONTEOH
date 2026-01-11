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

    public function profile()
    {
        $this->set_module('profile');
        $this->set_page('my_profile');
        return view('backend/header', $this->data) . view('backend/profile', $this->data) . view('backend/footer', $this->data);
    }

    public function setting()
    {
        $this->set_module('setting');
        $this->set_page('setting');

        //Get "setting" List
        $setting_list = $this->Setting_model->get_all([
            'is_deleted' => 0,
            'is_display' => 1,
        ], [
            'priority' => 'DESC',
            'id' => 'ASC'
        ]);
        if (!empty($setting_list)) {
            foreach ($setting_list as $k => $v) {
                $setting_list[$k]['variable_title'] = ucwords(str_replace('_', ' ', $v['variable']));
                $setting_list[$k]['input_option'] = !empty($v['input_option']) ? json_decode($v['input_option'], true) : [];
            }
        }
        $this->data['setting_list'] = $setting_list;

        return view('backend/header', $this->data) . view('backend/setting', $this->data) . view('backend/footer', $this->data);
    }
}
