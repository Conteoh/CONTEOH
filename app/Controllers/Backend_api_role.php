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

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        $this->Main_model = model('App\Models\Role_model');
        $this->current_module = 'role';
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
				$title = $this->request->getVar("title");
				$description = $this->request->getVar("description");
				$priority = $this->request->getVar("priority");


                $submit_data = [
					"title" => !empty($title)?$title:null,
					"description" => !empty($description)?$description:null,
					"priority" => !empty($priority)?$priority:0,
				];

                $this->Main_model->transStart();

                if (!empty($id) && $id != "0") {
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
