<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use Exception;

class Backend_api_jogging_target extends BaseResourceController
{
    use ResponseTrait;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        $this->Main_model = model('App\Models\Jogging_target_model');
        $this->current_module = 'jogging_target';
        $this->current_module_name = ucwords(str_replace("_", " ", $this->current_module));        
    }

    public function submit(){
        try {

            if (isset($_SERVER["CONTENT_TYPE"]) && strpos($_SERVER["CONTENT_TYPE"], "application/json") !== false) {
                $_POST = array_merge($_POST, (array) json_decode(trim(file_get_contents('php://input')), true));

                //authentication
                $my_user_id = $this->request->getVar("my_user_id");
                $my_login_token = $this->request->getVar("my_login_token");
                $my_data = $this->member_authentication($my_user_id, $my_login_token);
                
                $id = $this->request->getVar("id");
				$year = $this->request->getVar("year");
				$month_01 = $this->request->getVar("month_01");
				$month_02 = $this->request->getVar("month_02");
				$month_03 = $this->request->getVar("month_03");
				$month_04 = $this->request->getVar("month_04");
				$month_05 = $this->request->getVar("month_05");
				$month_06 = $this->request->getVar("month_06");
				$month_07 = $this->request->getVar("month_07");
				$month_08 = $this->request->getVar("month_08");
				$month_09 = $this->request->getVar("month_09");
				$month_10 = $this->request->getVar("month_10");
				$month_11 = $this->request->getVar("month_11");
				$month_12 = $this->request->getVar("month_12");


                $submit_data = [
					"year" => !empty($year)?$year:null,
					"month_01" => !empty($month_01)?$month_01:null,
					"month_02" => !empty($month_02)?$month_02:null,
					"month_03" => !empty($month_03)?$month_03:null,
					"month_04" => !empty($month_04)?$month_04:null,
					"month_05" => !empty($month_05)?$month_05:null,
					"month_06" => !empty($month_06)?$month_06:null,
					"month_07" => !empty($month_07)?$month_07:null,
					"month_08" => !empty($month_08)?$month_08:null,
					"month_09" => !empty($month_09)?$month_09:null,
					"month_10" => !empty($month_10)?$month_10:null,
					"month_11" => !empty($month_11)?$month_11:null,
					"month_12" => !empty($month_12)?$month_12:null,
				];

                $this->Main_model->transStart();

                if (!empty($id) && $id != "0") {
                    //Permission check
                    $this->user_permission_verification($my_data['id'], $this->current_module ,'edit');

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
                    $this->user_permission_verification($my_data['id'], $this->current_module ,'add');

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
}
