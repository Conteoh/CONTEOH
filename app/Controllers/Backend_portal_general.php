<?php

namespace App\Controllers;

class Backend_portal_general extends MY_Backend
{
    protected $Jogging_target_model;
    protected $Jogging_model;
    protected $Expenditure_model;

    public function __construct()
    {
        parent::__construct();

        //Load Model
        $this->Jogging_target_model = model('App\Models\Jogging_target_model');
        $this->Jogging_model = model('App\Models\Jogging_model');
        $this->Expenditure_model = model('App\Models\Expenditure_model');
    }

    public function index()
    {
        $current_year = (int) date('Y');

        $jogging_target = $this->Jogging_target_model->get_one(['year' => $current_year, 'is_deleted' => 0]);
        $jogging_monthly_totals = $this->Jogging_model->get_monthly_totals_by_year($current_year);

        $expenditure_totals = $this->Expenditure_model->get_dashboard_totals();

        $this->data['current_year'] = $current_year;
        $this->data['jogging_target'] = $jogging_target;
        $this->data['jogging_monthly_totals'] = $jogging_monthly_totals;
        $this->data['expenditure_totals'] = $expenditure_totals;

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
