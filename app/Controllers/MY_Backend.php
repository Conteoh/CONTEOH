<?php

namespace App\Controllers;

class MY_Backend extends BaseController
{
    public $data = [];
    public $Main_model;
    protected $Setting_model;
    protected $Login_token_model;
    protected $User_model;

    public function __construct()
    {
        //Load Model
        $this->Main_model = model('Setting_model');
        $this->Setting_model = model('App\Models\Setting_model');
        $this->Login_token_model = model('App\Models\Login_token_model');
        $this->User_model = model('App\Models\User_model');

        //Load Site Config
        $site_config = [];
        $result_list = $this->Setting_model->get_all([
            'is_deleted' => 0,
            'is_load_backend' => 1,
        ]);
        if (!empty($result_list)) {
            foreach ($result_list as $k => $v) {
                $site_config[$v['variable']] = $v['value'];
            }
        }
        $this->data['site_config'] = $site_config;

        //Load My User Data
        $this->data['my_user_id'] = 0;
        $this->data['my_user_data'] = [];
        $this->get_my_data();

        //Define Current Module & Page
        $uri_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri_segments = explode('/', $uri_path);

        $current_module = isset($uri_segments[2]) && !empty($uri_segments[2]) ? $uri_segments[2] : 'dashboard';
        $this->set_module($current_module);

        $current_page = isset($uri_segments[3]) && !empty($uri_segments[3]) ? $uri_segments[3] : 'list';
        $this->set_page($current_page);

        //Load Kv List
        $this->data['user_level_kv_list'] = $this->User_model->level_kv_list();
        $this->data['user_level_kv_info'] = $this->User_model->kv_list_to_info($this->data['user_level_kv_list']);
    }

    protected function set_module($module)
    {
        $this->data['current_module'] = $module;
        $this->data['current_module_name'] = ucwords(str_replace('_', ' ', $this->data['current_module']));
    }

    protected function set_page($page)
    {
        $this->data['current_page'] = $page;
        $this->data['current_page_name'] = ucwords(str_replace('_', ' ', $this->data['current_page']));
    }

    protected function get_my_data()
    {
        if (get_cookie('login_token')) {
            $login_token_data = $this->Login_token_model->get_one([
                'is_deleted' => 0,
                'token' => get_cookie('login_token'),
                'expiry_time >=' => date('Y-m-d H:i:s'),
            ]);
            if (!empty($login_token_data)) {
                $user_data = $this->User_model->get_one([
                    'is_deleted' => 0,
                    'status' => 1,
                    'is_email_verified' => 1,
                    'id' => $login_token_data['user_id'],
                    'level <=' => 1,
                ]);
                if (!empty($user_data)) {
                    $this->data['my_user_id'] = $user_data['id'];
                    $this->data['my_user_data'] = $user_data;
                    $this->data['my_login_token'] = $login_token_data['token'];
                }
            }
        }
    }

    public function list()
    {
        return view('backend/header', $this->data) .
            view('backend/' . $this->data['current_module'] . '/list', $this->data) .
            view('backend/footer', $this->data);
    }

    public function action($id = 0)
    {
        if ($id > 0) {
            //get result data
            $result_data = $this->Main_model->get_one([
                'is_deleted' => 0,
                'id' => $id
            ]);
            if (empty($result_data)) {
                show_error('Result not found');
            }
            $this->data['result_data'] = $result_data;
        }

        $this->data['id'] = $id;

        return view('backend/header', $this->data) . view('backend/' . $this->data['current_module'] . '/action', $this->data) . view('backend/footer', $this->data);
    }
}
