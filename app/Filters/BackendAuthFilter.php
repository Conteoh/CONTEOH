<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class BackendAuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $User_model = model('App\Models\User_model');
        $Login_token_model = model('App\Models\Login_token_model');
        
        //Check if login token valid 
        $token = $_COOKIE['login_token'] ?? null;
        if (!$token) {
            return redirect()->to(BACKEND_PORTAL . '/login');
        } else {
            $login_token_data = $Login_token_model->get_one([
                'is_deleted' => 0,
                'expiry_time >=' => date('Y-m-d H:i:s'),
                'token' => $token,
            ]);
            if (empty($login_token_data)) {
                setcookie('login_token', '', time() - 3600, "/");
                return redirect()->to(BACKEND_PORTAL . '/login');
            }
        }

        //Check if user exist
        $user_data = $User_model->get_one([
            'is_deleted' => 0,
            'status' => 1,
            'is_email_verified' => 1,
            'id' => $login_token_data['user_id'],
            'level <=' => 2,
        ]);
        if (empty($user_data)) {
            setcookie('login_token', '', time() - 3600, "/");
            return redirect()->to(BACKEND_PORTAL . '/login');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // 无需处理
    }
}
