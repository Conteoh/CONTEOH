<?php

namespace App\Controllers;

class Backend_portal_auth extends BaseController
{
    protected $data = [];
    protected $Setting_model;
    protected $Login_token_model;
    protected $User_model;

    public function __construct()
    {
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

        //Define Current Page
        $this->data['current_page'] = "list";
        $uri_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri_segments = explode('/', $uri_path);
        if (isset($uri_segments[2])) {
            $this->data['current_page'] = $uri_segments[2];
        }
        $this->data['current_page_name'] = ucwords(str_replace('_', ' ', $this->data['current_page']));
    }


    protected function check_is_logged_in()
    {
        $token = $_COOKIE['login_token'] ?? null;
        if (!$token) {
            return false;
        }
        $login_token_data = $this->Login_token_model->get_one([
            'is_deleted' => 0,
            'token' => $token,
        ]);
        if (empty($login_token_data)) {
            return false;
        }
        $user_data = $this->User_model->get_one([
            'is_deleted' => 0,
            'id' => $login_token_data['user_id'],
        ]);
        if (empty($user_data)) {
            return false;
        }
        return true;
    }

    public function login()
    {
        if ($this->check_is_logged_in()) {
            return redirect()->to(BACKEND_PORTAL . '/dashboard');
        }

        return
            view('backend/auth/header', $this->data) .
            view('backend/auth/login', $this->data) .
            view('backend/auth/footer', $this->data);
    }

    public function logout()
    {
        // 获取当前token
        $token = $_COOKIE['login_token'] ?? null;
        if ($token) {
            // 软删除token（设置deleted_at）
            $this->Login_token_model->update_data([
                'is_deleted' => 0,
                'token' => $token,
            ], [
                'modified_date' => date('Y-m-d H:i:s'),
                'is_deleted' => 1,
            ]);
            // 清除cookie
            setcookie('login_token', '', time() - 3600, "/");
        }
        // 跳转到登录页
        return redirect()->to(BACKEND_PORTAL . '/login');
    }

    public function forget_password()
    {
        if ($this->check_is_logged_in()) {
            return redirect()->to(BACKEND_PORTAL . '/dashboard');
        }

        return
            view('backend/auth/header', $this->data) .
            view('backend/auth/forget_password', $this->data) .
            view('backend/auth/footer', $this->data);
    }

    public function reset_password($verification_token)
    {
        $user_data = $this->User_model->get_one([
            'is_deleted' => 0,
            'verification_token' => $verification_token,
        ]);
        if (empty($user_data)) {
            return redirect()->to(BACKEND_PORTAL . '/forget_password');
        }

        $this->data['verification_token'] = $verification_token;

        return
            view('backend/auth/header', $this->data) .
            view('backend/auth/reset_password', $this->data) .
            view('backend/auth/footer', $this->data);
    }
}
