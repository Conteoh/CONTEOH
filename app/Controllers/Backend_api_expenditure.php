<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use Exception;

class Backend_api_expenditure extends BaseResourceController
{
    use ResponseTrait;

    protected $Tag_model;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        $this->Main_model = model('App\Models\Expenditure_model');
        $this->Tag_model = model('App\Models\Tag_model');
        $this->current_module = 'expenditure';
        $this->current_module_name = ucwords(str_replace("_", " ", $this->current_module));        
    }

    public function list($my_user_id = null, $my_login_token = null)
    {
        try {
            //Authentication
            $my_data = $this->member_authentication($my_user_id, $my_login_token);

            $filter = $this->request->getVar('filter');
            $count = $this->request->getVar('count');
            $page = $this->request->getVar('page');
            $is_export = $this->request->getVar('is_export');

            //filtering
            if (!empty($filter)) {
                $filter = array_map('urldecode', $filter);
            }
            if ($is_export) {
                //从网址取得filter
                $filter = $this->request->getGet();                
            }

            //Permission check
            $this->user_permission_verification($my_data['id'], $this->current_module, 'view');
            
            $where = [
                'is_deleted' => 0,
            ];
            $like = [];

            //filtering
            $response = $this->Main_model->get_table_fields();
            if (isset($response['where']) && !empty($response['where'])) {
                foreach ($response['where'] as $field) {
                    if (isset($filter[$field]) && $filter[$field] != "") {
                        $where[$field] = $filter[$field];
                    }
                }
            }
            if (isset($response['like']) && !empty($response['like'])) {
                foreach ($response['like'] as $field) {
                    if (isset($filter[$field]) && !empty($filter[$field])) {
                        $like[$field] = $filter[$field];
                    }
                }
            }

            if (isset($filter['created_date_from']) && !empty($filter['created_date_from'])) {
                $created_date_from = js_date_to_php_date($filter['created_date_from']);
                if (!empty($created_date_from)) {
                    $where['created_date >='] = $created_date_from;
                }
            }
            if (isset($filter['created_date_to']) && !empty($filter['created_date_to'])) {
                $created_date_to = js_date_to_php_date($filter['created_date_to']);
                if (!empty($created_date_to)) {
                    $where['created_date <='] = $created_date_to;
                }
            }

            // Handle tags filter - if any selected tag matches expenditure's tags
            $tags_filter = null;
            if (isset($filter['tags']) && !empty($filter['tags'])) {
                $selected_tags = explode(',', $filter['tags']);
                $selected_tags = array_map('trim', $selected_tags);
                $selected_tags = array_filter($selected_tags);
                
                if (!empty($selected_tags)) {
                    $tags_filter = $selected_tags;
                }
            }

            $start = ($page - 1) * $count;
            
            // Custom fetch with tags filter
            if ($tags_filter !== null) {
                $total_record = $this->Main_model->record_count_with_tags($where, $like, $tags_filter);
                if ($is_export) {
                    $count = $total_record;
                    $start = 0;
                }
                $result_list = $this->Main_model->fetch_with_tags($count, $start, $where, $like, $tags_filter);
            } else {
                $total_record = $this->Main_model->record_count($where, $like);
                if ($is_export) { //如果是Export，就捞全部资料
                    $count = $total_record;
                    $start = 0;
                }
                $result_list = $this->Main_model->fetch($count, $start, $where, $like);
            }

            foreach ($result_list as $k => $v) {
                if (!$is_export) {
                    $created_date = date('Y-m-d h:i:sA', strtotime($v['created_date']));
                    $result_list[$k]['created_on'] = str_replace(" ", "<br>", $created_date);
                }
            }

            if ($is_export) {
                $this->export_to_csv($result_list);
            }

            return $this->respond([
                'status' => "SUCCESS",
                'result' => [
                    'total_record' => $total_record,
                    'result_list' => $result_list
                ]
            ]);
        } catch (Exception $e) {
            return $this->fail([
                'status' => "ERROR",
                'result' => $e->getMessage()
            ]);
        }
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
				$expenditure_category_id = $this->request->getVar("expenditure_category_id");
				$date = $this->request->getVar("date_temp");
				$title = $this->request->getVar("title");
				$description = $this->request->getVar("description");
				$total_amount = $this->request->getVar("total_amount");
				$location = $this->request->getVar("location");
				$photo = $this->request->getVar("photo");
				$tags = $this->request->getVar("tags");


                $submit_data = [
					"expenditure_category_id" => !empty($expenditure_category_id)?$expenditure_category_id:0,
					"date" => !empty($date)?$date:null,
					"title" => !empty($title)?$title:null,
					"description" => !empty($description)?$description:null,
					"total_amount" => !empty($total_amount)?$total_amount:0,
					"location" => !empty($location)?$location:null,
					"photo" => !empty($photo)?$photo:null,
					"tags" => !empty($tags)?$tags:null,
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
