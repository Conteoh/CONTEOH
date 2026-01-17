<?php

namespace App\Controllers;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class Backend_portal_user extends MY_Backend
{
    protected $System_module_model;
    protected $User_permission_model;
    protected $Role_model;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->Main_model = model("App\Models\User_model");

        //Load Model
        $this->System_module_model = model("App\Models\System_module_model");
        $this->User_permission_model = model("App\Models\User_permission_model");
        $this->Role_model = model("App\Models\Role_model");

        //Load Kv List
        $this->data['status_kv_list'] = $this->User_model->status_kv_list();
        $this->data['status_kv_info'] = $this->User_model->kv_list_to_info($this->data['status_kv_list']);
        $this->data['role_list'] = $this->Role_model->get_kv_list(['is_deleted' => 0]);
        $this->data['role_kv_info'] = $this->Role_model->kv_list_to_info($this->data['role_list']);
    }

    public function action($id = 0)
    {
        //Assembly permission
        $user_permission_list = [];
        $system_module_list = $this->System_module_model->get_all([
            'is_deleted' => 0,
        ], [
            'priority' => 'DESC',
            'id' => 'ASC'
        ]);
        foreach ($system_module_list as $k => $v) {
            $user_permission_list[] = [
                'system_module_id' => $v['id'],
                'title' => $v['title'],
                'description' => $v['description'],
                'can_view' => 0,
                'can_add' => 0,
                'can_edit' => 0,
                'can_delete' => 0,
            ];
        }

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

            //Assembly User Permission
            foreach ($user_permission_list as $k => $v) {
                $user_permission_data = $this->User_permission_model->get_one([
                    'is_deleted' => 0,
                    'user_id' => $result_data['id'],
                    'system_module_id' => $v['system_module_id'],
                ], [
                    'id' => 'DESC'
                ]);
                if (!empty($user_permission_data)) {
                    $user_permission_list[$k]['can_view'] = (int)$user_permission_data['can_view'];
                    $user_permission_list[$k]['can_add'] = (int)$user_permission_data['can_add'];
                    $user_permission_list[$k]['can_edit'] = (int)$user_permission_data['can_edit'];
                    $user_permission_list[$k]['can_delete'] = (int)$user_permission_data['can_delete'];
                } else {
                    $user_permission_list[$k]['can_view'] = 0;
                    $user_permission_list[$k]['can_add'] = 0;
                    $user_permission_list[$k]['can_edit'] = 0;
                    $user_permission_list[$k]['can_delete'] = 0;
                }
            }
        }

        $this->data['id'] = $id;
        $this->data['user_permission_list'] = $user_permission_list;

        return view('backend/header', $this->data) . view('backend/' . $this->data['current_module'] . '/action', $this->data) . view('backend/footer', $this->data);
    }
}
