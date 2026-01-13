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
                $new_password = $this->request->getVar('new_password');

                $submit_data = [
                    'name' => !empty($name) ? $name : null,
                    'email' => !empty($email) ? $email : null,
                    'level' => !empty($level) ? $level : 1,
                    'status' => isset($status) ? $status : 0,
                    'is_email_verified' => isset($is_email_verified) ? $is_email_verified : 0,
                    'dial_code' => !empty($dial_code) ? $dial_code : '+60',
                    'mobile' => !empty($mobile) ? $mobile : null,
                ];

                // Only update password if provided
                if (!empty($new_password)) {
                    if (strlen($new_password) < 5) {
                        throw new Exception('New password length at least 5');
                    }
                    $submit_data['password'] = sha1($new_password);
                }

                $this->Main_model->transStart();

                if (!empty($id) && $id != "0") {
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
