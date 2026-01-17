<?php

namespace App\Controllers;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class Backend_portal_role extends MY_Backend
{
    protected $System_module_model;
    protected $Role_permission_model;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        $this->Main_model = model("App\Models\Role_model");
        $this->System_module_model = model("App\Models\System_module_model");
        $this->Role_permission_model = model("App\Models\Role_permission_model");
    }

    public function action($id = 0)
    {
        //Assembly permission
        $permission_list = [];
        $system_module_list = $this->System_module_model->get_all([
            'is_deleted' => 0,
        ], [
            'priority' => 'DESC',
            'id' => 'ASC'
        ]);
        foreach ($system_module_list as $k => $v) {
            $permission_list[] = [
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

            foreach ($permission_list as $k => $v) {

                $role_permission_data = $this->Role_permission_model->get_one([
                    'is_deleted' => 0,
                    'role_id' => $result_data['id'],
                    'system_module_id' => $v['system_module_id'],
                ], [
                    'id' => 'DESC'
                ]);
                if (!empty($role_permission_data)) {
                    $permission_list[$k]['can_view'] = (int)$role_permission_data['can_view'];
                    $permission_list[$k]['can_add'] = (int)$role_permission_data['can_add'];
                    $permission_list[$k]['can_edit'] = (int)$role_permission_data['can_edit'];
                    $permission_list[$k]['can_delete'] = (int)$role_permission_data['can_delete'];
                } else {
                    $permission_list[$k]['can_view'] = 0;
                    $permission_list[$k]['can_add'] = 0;
                    $permission_list[$k]['can_edit'] = 0;
                    $permission_list[$k]['can_delete'] = 0;
                }
            }
        }

        $this->data['id'] = $id;
        $this->data['permission_list'] = $permission_list;

        return view('backend/header', $this->data) . view('backend/' . $this->data['current_module'] . '/action', $this->data) . view('backend/footer', $this->data);
    }
}
