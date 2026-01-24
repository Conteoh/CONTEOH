<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use Exception;

class Backend_api_car_maintenance extends BaseResourceController
{
    use ResponseTrait;

    protected $Tag_model;
    protected $Car_maintenance_attachment_model;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        $this->Main_model = model('App\Models\Car_maintenance_model');
        $this->Tag_model = model('App\Models\Tag_model');
        $this->current_module = 'car_maintenance';
        $this->current_module_name = ucwords(str_replace("_", " ", $this->current_module));

        //Load Model
        $this->Car_maintenance_attachment_model = model('App\Models\Car_maintenance_attachment_model');
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
                $date = $this->request->getVar("date_temp");
                $total_amount = $this->request->getVar("total_amount");
                $tags = $this->request->getVar("tags");
                $attachment_list = isset($_POST['attachment_list']) && !empty($_POST['attachment_list']) ? $_POST['attachment_list'] : [];

                $submit_data = [
                    "title" => !empty($title) ? $title : null,
                    "description" => !empty($description) ? $description : null,
                    "date" => !empty($date) ? $date : null,
                    "total_amount" => !empty($total_amount) ? $total_amount : 0,
                    "tags" => !empty($tags) ? $tags : null,
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

                // Process tags - create new tags in tag table if they don't exist
                if (!empty($tags)) {
                    $tags_array = explode(',', $tags);
                    foreach ($tags_array as $tag_name) {
                        $tag_name = trim($tag_name);
                        if (!empty($tag_name)) {
                            // Check if tag already exists
                            $existing_tag = $this->Tag_model->get_one([
                                "is_deleted" => 0,
                                "title" => $tag_name
                            ]);

                            // If tag doesn't exist, create it
                            if (empty($existing_tag)) {
                                $this->Tag_model->insert_data([
                                    "title" => $tag_name,
                                    "created_date" => date("Y-m-d H:i:s")
                                ]);
                            }
                        }
                    }
                }

                //Process "car_maintenance_attachment"
                if (!empty($attachment_list)) {
                    foreach ($attachment_list as $k => $v) {
                        if ($v['id'] == '0' && $v['is_deleted'] == '0') {
                            //Insert "car_maintenance_attachment"
                            $record_id = $this->Car_maintenance_attachment_model->insert_data([
                                "created_date" => date("Y-m-d H:i:s"),
                                "car_maintenance_id" => $ID,
                                "title" => !empty($v['title']) ? $v['title'] : null,
                                "document_path" => !empty($v['document_path']) ? $v['document_path'] : null,
                            ]);

                            //Insert 'audit_trail'
                            $after_data = $this->Car_maintenance_attachment_model->get_one(['id' => $record_id]);
                            $this->Audit_trail_model->insert_data([
                                "created_date" => date("Y-m-d H:i:s"),
                                "user_id" => $my_data["id"],
                                "ref_table" => $this->Car_maintenance_attachment_model->table,
                                "ref_id" => $record_id,
                                "after" => json_encode($after_data),
                                "origin" => $this->get_function_execution_origin()
                            ]);
                        } else if ($v['id'] != '0' && $v['is_deleted'] == '0') {
                            //Check if record exist
                            $record_data = $this->Car_maintenance_attachment_model->get_one([
                                "is_deleted" => 0,
                                "id" => $v['id'],
                                ($this->current_module . "_id") => $ID
                            ]);
                            if (!empty($record_data)) {
                                //Check if any changes made, if yes, update the record
                                $submit_data = [];
                                $field_list = ['title', 'document_path'];
                                foreach ($field_list as $field) {
                                    if ($v[$field] != $record_data[$field]) {
                                        $field_value = !empty($v[$field]) ? $v[$field] : null;
                                        $submit_data[$field] = $field_value;
                                    }
                                }
                                if (!empty($submit_data)) {
                                    $submit_data['modified_date'] = date("Y-m-d H:i:s");

                                    //Update "car_maintenance_attachment"
                                    $this->Car_maintenance_attachment_model->update_data([
                                        "id" => $v['id']
                                    ], $submit_data);

                                    //Insert 'audit_trail'
                                    $after_data = $this->Car_maintenance_attachment_model->get_one(['id' => $v['id']]);
                                    $this->Audit_trail_model->insert_data([
                                        "created_date" => date("Y-m-d H:i:s"),
                                        "user_id" => $my_data["id"],
                                        "ref_table" => $this->Car_maintenance_attachment_model->table,
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
                            $record_data = $this->Car_maintenance_attachment_model->get_one([
                                "is_deleted" => 0,
                                "id" => $v['id'],
                                ($this->current_module . "_id") => $ID
                            ]);
                            if (!empty($record_data)) {
                                //Soft delete "car_maintenance_attachment"
                                $this->Car_maintenance_attachment_model->delete_data(
                                    $v['id'],
                                    $my_data["id"],
                                    $this->get_function_execution_origin()
                                );
                            }
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

    public function get_tag_suggestions($my_user_id = null, $my_login_token = null)
    {
        try {
            //Authentication
            $my_data = $this->member_authentication($my_user_id, $my_login_token);

            //Permission check
            $this->user_permission_verification($my_data['id'], $this->current_module, 'view');

            $query = $this->request->getVar('query');

            $where = [
                'is_deleted' => 0,
            ];

            $like = [];
            if (!empty($query)) {
                $like['title'] = $query;
            }

            // Get all tags, optionally filtered by query
            // get_all($where, $order_by, $select, $where_in, $like, $or_like, $limit)
            $tags_list = $this->Tag_model->get_all($where, ['title' => 'ASC'], '*', [], $like);

            $suggestions = [];
            foreach ($tags_list as $tag) {
                $suggestions[] = $tag['title'];
            }

            return $this->respond([
                'status' => "SUCCESS",
                'result' => $suggestions
            ]);
        } catch (Exception $e) {
            return $this->fail([
                'status' => "ERROR",
                'result' => $e->getMessage()
            ]);
        }
    }
}
