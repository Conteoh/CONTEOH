<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use Exception;

class Backend_api_expenditure_category extends BaseResourceController
{
    use ResponseTrait;

    protected $Expenditure_suggestion_model;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        $this->Main_model = model('App\Models\Expenditure_category_model');
        $this->current_module = 'expenditure_category';
        $this->current_module_name = ucwords(str_replace("_", " ", $this->current_module));

        //Load Model
        $this->Expenditure_suggestion_model = model('App\Models\Expenditure_suggestion_model');
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
                $is_favourite = $this->request->getVar("is_favourite");
                $suggestion_list = isset($_POST['suggestion_list']) && !empty($_POST['suggestion_list']) ? $_POST['suggestion_list'] : [];

                $submit_data = [
                    "title" => !empty($title) ? $title : null,
                    "description" => !empty($description) ? $description : null,
                    "priority" => !empty($priority) ? $priority : 0,
                    "is_favourite" => !empty($is_favourite) ? $is_favourite : 0,
                ];

                // $this->Main_model->transStart();

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

                //Process "expenditure_suggestion"
                if (!empty($suggestion_list)) {
                    foreach ($suggestion_list as $k => $v) {
                        if ($v['id'] == '0' && $v['is_deleted'] == '0') {
                            //Insert "expenditure_suggestion"
                            $record_id = $this->Expenditure_suggestion_model->insert_data([
                                "created_date" => date("Y-m-d H:i:s"),
                                "expenditure_category_id" => $ID,
                                "title" => !empty($v['title']) ? $v['title'] : null,
                                "price" => !empty($v['price']) ? $v['price'] : 0,
                                "day" => !empty($v['day']) ? $v['day'] : 0,
                                "priority" => !empty($v['priority']) ? $v['priority'] : 0,
                            ]);

                            //Insert 'audit_trail'
                            $after_data = $this->Expenditure_suggestion_model->get_one(['id' => $record_id]);
                            $this->Audit_trail_model->insert_data([
                                "created_date" => date("Y-m-d H:i:s"),
                                "user_id" => $my_data["id"],
                                "ref_table" => $this->Expenditure_suggestion_model->table,
                                "ref_id" => $record_id,
                                "after" => json_encode($after_data),
                                "origin" => $this->get_function_execution_origin()
                            ]);
                        } else if ($v['id'] != '0' && $v['is_deleted'] == '0') {
                            //Check if record exist
                            $record_data = $this->Expenditure_suggestion_model->get_one([
                                "is_deleted" => 0,
                                "id" => $v['id'],
                                ($this->current_module . "_id") => $ID
                            ]);
                            if (!empty($record_data)) {
                                //Check if any changes made, if yes, update the record
                                $submit_data = [];
                                $field_list = ['title', 'price', 'day', 'priority'];
                                foreach ($field_list as $field) {
                                    if ($v[$field] != $record_data[$field]) {
                                        $field_value = !empty($v[$field]) ? $v[$field] : null;
                                        if (!$field_value && $this->Expenditure_suggestion_model->check_is_number_field($field)) {
                                            $field_value = 0;
                                        }
                                        $submit_data[$field] = $field_value;
                                    }
                                }
                                if (!empty($submit_data)) {
                                    $submit_data['modified_date'] = date("Y-m-d H:i:s");

                                    //Update "expenditure_suggestion"
                                    $this->Expenditure_suggestion_model->update_data([
                                        "id" => $v['id']
                                    ], $submit_data);

                                    //Insert 'audit_trail'
                                    $after_data = $this->Expenditure_suggestion_model->get_one(['id' => $v['id']]);
                                    $this->Audit_trail_model->insert_data([
                                        "created_date" => date("Y-m-d H:i:s"),
                                        "user_id" => $my_data["id"],
                                        "ref_table" => $this->Expenditure_suggestion_model->table,
                                        "ref_id" => $v['id'],
                                        "action_type" => $this->Audit_trail_model::ACTION_EDIT,
                                        "before" => json_encode($record_data),
                                        "after" => json_encode($after_data),
                                        "origin" => $this->get_function_execution_origin()
                                    ]);
                                }
                            }
                        } else if ($v['id'] != '0' && $v['is_deleted'] == '1') {
                            //Check if record exist
                            $record_data = $this->Expenditure_suggestion_model->get_one([
                                "is_deleted" => 0,
                                "id" => $v['id'],
                                ($this->current_module . "_id") => $ID
                            ]);
                            if (!empty($record_data)) {
                                //Soft delete "expenditure_suggestion"
                                $this->Expenditure_suggestion_model->delete_data(
                                    $v['id'],
                                    $my_data["id"],
                                    $this->get_function_execution_origin()
                                );
                            }
                        }
                    }
                }

                // $this->Main_model->transComplete();

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
