<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use Exception;

class Backend_api_role extends BaseResourceController
{
    use ResponseTrait;

    protected $Role_permission_model;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        $this->Main_model = model('App\Models\Role_model');
        $this->current_module = 'role';
        $this->current_module_name = ucwords(str_replace("_", " ", $this->current_module));

        //Load Model
        $this->Role_permission_model = model('App\Models\Role_permission_model');
    }

    public function submit()
    {
        try {

            if (isset($_SERVER["CONTENT_TYPE"]) && strpos($_SERVER["CONTENT_TYPE"], "application/json") !== false) {
                $_POST = array_merge($_POST, (array) json_decode(trim(file_get_contents('php://input')), true));

                //authentication
                $my_user_id = $this->request->getVar("my_user_id");
                $my_login_token = $this->request->getVar("my_login_token");
                $my_data = $this->member_authentication($my_user_id, $my_login_token);

                $id = $this->request->getVar("id");
                $title = $this->request->getVar("title");
                $description = $this->request->getVar("description");
                $priority = $this->request->getVar("priority");
                $permission_list = isset($_POST['permission_list']) && !empty($_POST['permission_list']) ? $_POST['permission_list'] : [];

                $submit_data = [
                    "title" => !empty($title) ? $title : null,
                    "description" => !empty($description) ? $description : null,
                    "priority" => !empty($priority) ? $priority : 0,
                ];

                $this->Main_model->transStart();

                if (!empty($id) && $id != "0") {

                    //Permission check
                    $this->user_permission_verification($my_data['id'], $this->current_module, 'edit');

                    $ID = $id;

                    //check if record exist
                    $result_data = $this->Main_model->get_one([
                        "is_deleted" => 0,
                        "id" => $ID
                    ]);
                    if (empty($result_data)) {
                        throw new Exception("Result data not found");
                    } else {
                        $submit_data["modified_date"] = date("Y-m-d H:i:s");
                    }

                    $this->Main_model->update_data([
                        "id" => $ID,
                    ], $submit_data);

                    $after_data = $this->Main_model->where(["id" => $ID])->first();

                    $this->Audit_trail_model->insert_data([
                        "created_date" => date("Y-m-d H:i:s"),
                        "user_id" => $my_data["id"],
                        "ref_table" => $this->current_module,
                        "ref_id" => $ID,
                        "action_type" => $this->Audit_trail_model::ACTION_EDIT,
                        "before" => json_encode($result_data),
                        "after" => json_encode($after_data),
                        "origin" => $this->get_function_execution_origin()
                    ]);
                } else {
                    //Permission check
                    $this->user_permission_verification($my_data['id'], $this->current_module, 'add');

                    $submit_data["created_date"] = date("Y-m-d H:i:s");

                    $ID = $this->Main_model->insert_data($submit_data);

                    $after_data = $this->Main_model->where("id", $ID)->first();

                    $this->Audit_trail_model->insert_data([
                        "created_date" => date("Y-m-d H:i:s"),
                        "user_id" => $my_data["id"],
                        "ref_table" => $this->current_module,
                        "ref_id" => $ID,
                        "after" => json_encode($after_data),
                        "origin" => $this->get_function_execution_origin()
                    ]);
                }

                //Process "user_permission"
                if (!empty($permission_list)) {
                    foreach ($permission_list as $k => $v) {
                        //Check if record exist
                        $record_data = $this->Role_permission_model->get_one([
                            'is_deleted' => 0,
                            'role_id' => $ID,
                            'system_module_id' => $v['system_module_id'],
                        ]);
                        if (!empty($record_data)) {

                            //Check if any changes made, if yes, update the record
                            $submit_data = [];
                            $field_list = ['can_view', 'can_add', 'can_edit', 'can_delete'];
                            foreach ($field_list as $field) {
                                if ($v[$field] != $record_data[$field]) {
                                    $submit_data[$field] = !empty($v[$field]) ? $v[$field] : 0;
                                }
                            }

                            if (!empty($submit_data)) {
                                //Update "role_permission"
                                $submit_data['modified_date'] = date('Y-m-d H:i:s');
                                $this->Role_permission_model->update_data([
                                    'id' => $record_data['id']
                                ], $submit_data);

                                //Insert 'audit_trail'
                                $after_data = $this->Role_permission_model->get_one(['id' => $record_data['id']]);
                                $this->Audit_trail_model->insert_data([
                                    'created_date' => date('Y-m-d H:i:s'),
                                    'user_id' => $my_data['id'],
                                    'ref_table' => $this->Role_permission_model->table,
                                    'ref_id' => $record_data['id'],
                                    'action_type' => $this->Audit_trail_model::ACTION_EDIT,
                                    'before' => json_encode($record_data),
                                    'after' => json_encode($after_data),
                                    'origin' => $this->get_function_execution_origin()
                                ]);
                            }
                        } else {
                            //Insert "role_permission"
                            $record_id = $this->Role_permission_model->insert_data([
                                'created_date' => date('Y-m-d H:i:s'),
                                'role_id' => $ID,
                                'system_module_id' => $v['system_module_id'],
                                'can_view' => !empty($v['can_view']) ? $v['can_view'] : 0,
                                'can_add' => !empty($v['can_add']) ? $v['can_add'] : 0,
                                'can_edit' => !empty($v['can_edit']) ? $v['can_edit'] : 0,
                                'can_delete' => !empty($v['can_delete']) ? $v['can_delete'] : 0,
                            ]);

                            //Insert 'audit_trail'
                            $after_data = $this->Role_permission_model->get_one(['id' => $record_id]);
                            $this->Audit_trail_model->insert_data([
                                "created_date" => date("Y-m-d H:i:s"),
                                "user_id" => $my_data["id"],
                                "ref_table" => $this->Role_permission_model->table,
                                "ref_id" => $record_id,
                                "after" => json_encode($after_data),
                                "origin" => $this->get_function_execution_origin()
                            ]);
                        }
                    }
                }

                $this->Main_model->transComplete();

                return $this->respond([
                    "status" => "SUCCESS",
                    "result" => [
                        "id" => $ID,
                    ]
                ]);
            } else {
                throw new Exception('Invalid param');
            }
        } catch (Exception $e) {
            return $this->fail([
                "status" => "ERROR",
                "result" => $e->getMessage()
            ]);
        }
    }

    public function load_role_permission()
    {
        try {

            if (isset($_SERVER["CONTENT_TYPE"]) && strpos($_SERVER["CONTENT_TYPE"], "application/json") !== false) {
                $_POST = array_merge($_POST, (array) json_decode(trim(file_get_contents('php://input')), true));

                //authentication
                $my_user_id = $this->request->getVar("my_user_id");
                $my_login_token = $this->request->getVar("my_login_token");
                $my_data = $this->member_authentication($my_user_id, $my_login_token);

                $role_id = $this->request->getVar("role_id");

                //Check if role exist
                $result_data = $this->Main_model->get_one([
                    'is_deleted' => 0,
                    'id' => $role_id
                ]);
                if (empty($result_data)) {
                    throw new Exception('User data not found');
                }

                //Load related roles' permissions
                $permission_list = $this->Role_permission_model->get_all([
                    'is_deleted' => 0,
                    'role_id' => $result_data['id']
                ], [
                    'system_module_id' => 'ASC'
                ]);

                return $this->respond([
                    "status" => "SUCCESS",
                    "result" => [
                        "permission_list" => $permission_list,
                    ]
                ]);
            } else {
                throw new Exception('Invalid param');
            }
        } catch (Exception $e) {
            return $this->fail([
                "status" => "ERROR",
                "result" => $e->getMessage()
            ]);
        }
    }
}
