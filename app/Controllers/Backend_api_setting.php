<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use Exception;

class Backend_api_setting extends BaseResourceController
{

    use ResponseTrait;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        $this->Main_model = model('App\Models\Setting_model');
        $this->current_module = 'setting';
    }

    public function batch_update()
    {

        try {

            if (isset($_SERVER["CONTENT_TYPE"]) && strpos($_SERVER["CONTENT_TYPE"], "application/json") !== false) {
                $_POST = array_merge($_POST, (array) json_decode(trim(file_get_contents('php://input')), true));

                //authentication
                $my_user_id = $this->request->getVar('my_user_id');
                $my_login_token = $this->request->getVar('my_login_token');
                $my_data = $this->member_authentication($my_user_id, $my_login_token);

                $result_list = isset($_POST['result_list']) ? $_POST['result_list'] : [];

                //Permission check
                $this->user_permission_verification($my_data['id'], $this->current_module, 'edit');

                $this->Setting_model->transStart();
                foreach ($result_list as $k => $v) {
                    $result_data = $this->Setting_model->get_one([
                        'id' => $v['id'],
                        'is_deleted' => 0,
                    ]);
                    if (!empty($result_data)) {
                        if ($v['input_type'] == '5') {
                            $v['value'] = !empty($v['value_temp']) ? date("Y-m-d") : '';
                        }
                        if ($v['value'] != $result_data['value']) {

                            $this->Setting_model->update_data([
                                'id' => $result_data['id']
                            ], [
                                'modified_date' => date("Y-m-d H:i:s"),
                                'value' => $v['value'],
                            ]);

                            //Insert 'audit_trail'
                            $after_data = $this->Setting_model->where('id', $result_data['id'])->first();
                            $this->Audit_trail_model->insert_data([
                                'created_date' => date("Y-m-d H:i:s"),
                                'user_id' => $my_data['id'],
                                'ref_table' => 'setting',
                                'ref_id' => $result_data['id'],
                                'action_type' => $this->Audit_trail_model::ACTION_EDIT,
                                'before' => json_encode($result_data),
                                'after' => json_encode($after_data),
                                'origin' => $this->get_function_execution_origin()
                            ]);
                        }
                    }
                }
                $this->Setting_model->transComplete();

                return $this->respond([
                    'status' => "SUCCESS",
                    'result' => []
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
