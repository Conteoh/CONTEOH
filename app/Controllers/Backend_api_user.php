<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use Exception;
use PDO;

class Backend_api_user extends BaseResourceController
{

    use ResponseTrait;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        $this->Main_model = model('App\Models\User_model');
        $this->current_module = 'user';
    }

    public function submit()
    {
        try {

            if (isset($_SERVER["CONTENT_TYPE"]) && strpos($_SERVER["CONTENT_TYPE"], "application/json") !== false) {
                $_POST = array_merge($_POST, (array) json_decode(trim(file_get_contents('php://input')), true));

                //Authentication
                $my_user_id = $this->request->getVar('my_user_id');
                $my_login_token = $this->request->getVar('my_login_token');
                $my_data = $this->member_authentication($my_user_id, $my_login_token);

                $id = $this->request->getVar('id');
                $name = $this->request->getVar('name');
                $level = $this->request->getVar('level');
                $status = $this->request->getVar('status');
                $email = $this->request->getVar('email');
                $is_email_verified = $this->request->getVar('is_email_verified');
                $dial_code = $this->request->getVar('dial_code');
                $mobile = $this->request->getVar('mobile');
                $role_id = $this->request->getVar('role_id');
                $new_password = $this->request->getVar('new_password');
                $user_permission_list = isset($_POST['user_permission_list']) && !empty($_POST['user_permission_list']) ? $_POST['user_permission_list'] : [];

                $submit_data = [
                    'name' => !empty($name) ? $name : null,
                    'email' => !empty($email) ? $email : null,
                    'level' => !empty($level) ? $level : 1,
                    'status' => isset($status) ? $status : 0,
                    'is_email_verified' => isset($is_email_verified) ? $is_email_verified : 0,
                    'dial_code' => !empty($dial_code) ? $dial_code : '+60',
                    'mobile' => !empty($mobile) ? $mobile : null,
                    'role_id' => !empty($role_id) ? $role_id : 0,
                ];

                // Only update password if provided
                if (!empty($new_password)) {
                    if (strlen($new_password) < 5) {
                        throw new Exception('New password length at least 5');
                    }
                    $submit_data['password'] = sha1($new_password);
                }

                //如果不是superadmin，就一定要有role
                if ($level != '-1' && (empty($role_id) || $role_id == '0')) {
                    throw new Exception('Role is required for non-superadmin user');
                }

                $this->Main_model->transStart();

                if (!empty($id) && $id != "0") {

                    //Permission check
                    $this->user_permission_verification($my_data['id'], $this->current_module, 'edit');

                    $ID = $id;

                    //check if record exist
                    $result_data = $this->Main_model->get_one([
                        'is_deleted' => 0,
                        'id' => $ID
                    ]);
                    if (empty($result_data)) {
                        throw new Exception('Result data not found');
                    } else {

                        //Check if email already exist
                        if ($email != $result_data['email']) {
                            $other_record = $this->Main_model->get_one([
                                'is_deleted' => 0,
                                'email' => $email,
                                'id !=' => $ID
                            ]);
                            if (!empty($other_record)) {
                                throw new Exception('This email(' . $email . ') already used by another user');
                            }
                        }

                        $submit_data['modified_date'] = date("Y-m-d H:i:s");
                    }

                    $this->Main_model->update_data([
                        'id' => $ID,
                    ], $submit_data);

                    //Insert 'audit_trail'
                    $after_data = $this->Main_model->where(['id' => $ID])->first();
                    $this->Audit_trail_model->insert_data([
                        'created_date' => date('Y-m-d H:i:s'),
                        'user_id' => $my_data['id'],
                        'ref_table' => $this->current_module,
                        'ref_id' => $ID,
                        'action_type' => $this->Audit_trail_model::ACTION_EDIT,
                        'before' => json_encode($result_data),
                        'after' => json_encode($after_data),
                        "origin" => $this->get_function_execution_origin()
                    ]);
                } else {
                    //Permission check
                    $this->user_permission_verification($my_data['id'], $this->current_module, 'add');

                    //New user must have password
                    if (empty($new_password)) {
                        throw new Exception('Password is required for new user');
                    }

                    //Check if email already exist
                    $other_record = $this->Main_model->get_one([
                        'is_deleted' => 0,
                        'email' => $email
                    ]);
                    if (!empty($other_record)) {
                        throw new Exception('This email(' . $email . ') already used by another user');
                    }

                    $submit_data['created_date'] = date("Y-m-d H:i:s");

                    $ID = $this->Main_model->insert_data($submit_data);

                    //Insert 'audit_trail'
                    $after_data = $this->Main_model->where('id', $ID)->first();
                    $this->Audit_trail_model->insert_data([
                        'created_date' => date('Y-m-d H:i:s'),
                        'user_id' => $my_data['id'],
                        'ref_table' => $this->current_module,
                        'ref_id' => $ID,
                        'action_type' => $this->Audit_trail_model::ACTION_ADD,
                        'after' => json_encode($after_data),
                        "origin" => $this->get_function_execution_origin()
                    ]);
                }

                //Process "user_permission"
                if (!empty($user_permission_list)) {
                    foreach ($user_permission_list as $k => $v) {
                        $record_data = $this->User_permission_model->get_one([
                            'is_deleted' => 0,
                            'user_id' => $ID,
                            'system_module_id' => $v['system_module_id'],
                        ]);
                        if (!empty($record_data)) {

                            $submit_data = [];
                            $field_list = ['can_view', 'can_add', 'can_edit', 'can_delete'];
                            foreach ($field_list as $field) {
                                if ($v[$field] != $record_data[$field]) {
                                    $submit_data[$field] = !empty($v[$field]) ? $v[$field] : 0;
                                }
                            }
                            if (!empty($submit_data)) {
                                $submit_data['modified_date'] = date('Y-m-d H:i:s');
                                //Update "user_permission"
                                $this->User_permission_model->update_data([
                                    'id' => $record_data['id']
                                ], $submit_data);

                                //Insert 'audit_trail'
                                $after_data = $this->User_permission_model->get_one(['id' => $record_data['id']]);
                                $this->Audit_trail_model->insert_data([
                                    "created_date" => date("Y-m-d H:i:s"),
                                    "user_id" => $my_data["id"],
                                    "ref_table" => $this->User_permission_model->table,
                                    "ref_id" => $record_data['id'],
                                    "action_type" => $this->Audit_trail_model::ACTION_EDIT,
                                    "before" => json_encode($record_data),
                                    "after" => json_encode($after_data),
                                    "origin" => $this->get_function_execution_origin()
                                ]);
                            }
                        } else {
                            //Insert "user_permission"
                            $record_id = $this->User_permission_model->insert_data([
                                'created_date' => date('Y-m-d H:i:s'),
                                'user_id' => $ID,
                                'system_module_id' => $v['system_module_id'],
                                'module' => $v['title'],
                                'can_view' => !empty($v['can_view']) ? $v['can_view'] : 0,
                                'can_add' => !empty($v['can_add']) ? $v['can_add'] : 0,
                                'can_edit' => !empty($v['can_edit']) ? $v['can_edit'] : 0,
                                'can_delete' => !empty($v['can_delete']) ? $v['can_delete'] : 0,
                            ]);

                            //Insert 'audit_trail'
                            $after_data = $this->User_permission_model->get_one(['id' => $record_id]);
                            $this->Audit_trail_model->insert_data([
                                "created_date" => date("Y-m-d H:i:s"),
                                "user_id" => $my_data["id"],
                                "ref_table" => $this->User_permission_model->table,
                                "ref_id" => $record_id,
                                "after" => json_encode($after_data),
                                "origin" => $this->get_function_execution_origin()
                            ]);
                        }
                    }
                }

                $this->Main_model->transComplete();

                return $this->respond([
                    'status' => "SUCCESS",
                    'result' => [
                        'id' => $ID,
                    ]
                ]);
            } else {
                throw new Exception('Invalid param');
            }
        } catch (Exception $e) {
            return $this->fail([
                'status' => "ERROR",
                'result' => $e->getMessage()
            ]);
        }
    }
}
