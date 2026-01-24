<?php


namespace App\Controllers;

use App\Libraries\Emailer;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Model;
use Exception;
use Psr\Log\LoggerInterface;
use CodeIgniter\Router\Router;

abstract class BaseResourceController extends \CodeIgniter\RESTful\ResourceController
{
    /**
     *
     * @var string
     */
    public $pageTitle;

    /**
     * Additional string to display after page title
     *
     * @var string
     */
    public $pageSubTitle;

    /**
     *
     * @var boolean
     */
    protected $usePageSubTitle = true;

    /**
     * Singular noun of primary object
     *
     * @var string
     */
    protected static $singularObjectName;

    /**
     * Plural form of primary object name
     *
     * @var string
     */
    protected static $pluralObjectName;

    /**
     * Path for the views directory for the extending view controller
     * 
     * @var string 
     */
    protected static $viewPath;

    //                              //
    //                              //
    //      Original REST API       //
    //                              //
    //                              //

    //model
    protected $Main_model;
    protected $current_module;
    protected $current_module_name;
    protected $User_model;
    protected $Login_token_model;
    protected $Audit_trail_model;
    protected $Setting_model;
    protected $Mail_template_model;
    protected $System_module_model;
    protected $User_permission_model;
    
    //library
    protected $Emailer_library;
    protected $Cloudinary_library;

    //service
    protected $Router_service;

    //other
    protected $item_per_page;
    protected $data;

    public function __construct()
    {
        //Load module's model
        $this->User_model = model('App\Models\User_model');
        $this->Login_token_model = model('App\Models\Login_token_model');
        $this->Audit_trail_model = model("App\Models\Audit_trail_model");
        $this->Setting_model = model("App\Models\Setting_model");
        $this->Mail_template_model = model("App\Models\Mail_template_model");
        $this->System_module_model = model("App\Models\System_module_model");
        $this->User_permission_model = model("App\Models\User_permission_model");
        $this->Cloudinary_library = model("App\Libraries\Cloudinary_client");
        
        //Other
        $this->item_per_page = 10;

        //service
        $this->Router_service = service('router');

        //helper 
        helper('cookie');
        helper('common_helper');

        //library
        $this->Emailer_library = new Emailer;

        //Site config
        $site_config = [];
        $result_list = $this->Setting_model->get_all([
            'is_deleted' => 0,
            'is_load_backend' => 1,
        ]);
        foreach ($result_list as $k => $v) {
            $site_config[$v['variable']] = $v['value'];
        }
        $this->data['site_config'] = $site_config;

        //kv list
        $this->data['user_level_kv_list'] = $this->User_model->level_kv_list();
    }

    public function member_authentication($user_id, $login_token)
    {

        //check login token
        if (empty($login_token)) {
            throw new Exception('Login token is missing');
        } else {
            $login_token_data = $this->Login_token_model->get_one([
                'is_deleted' => 0,
                'user_id' => $user_id,
                'token' => $login_token,
                'expiry_time >=' => date('Y-m-d H:i:s'),
            ]);
            if (empty($login_token_data)) {
                throw new Exception('Invalid login token or login token is expired');
            }
        }

        //get user data 
        $user_data = $this->User_model->get_one([
            'is_deleted' => 0,
            'id' => $login_token_data['user_id'],
            'level <=' => 1,
        ]);
        if (empty($user_data)) {
            throw new Exception("User data not found");
        } else {
            if ($user_data['status'] != 1) {
                throw new Exception('This user account is currently inactive');
            }
        }
        return $user_data;
    }

    public function get_function_execution_origin()
    {
        return class_basename($this->Router_service->controllerName()) . "/" . $this->Router_service->methodName();
    }

    public function list($my_user_id, $my_login_token)
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

            $start = ($page - 1) * $count;
            $total_record = $this->Main_model->record_count($where, $like);
            if ($is_export) { //如果是Export，就捞全部资料
                $count = $total_record;
                $start = 0;
            }
            $result_list = $this->Main_model->fetch($count, $start, $where, $like);

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

    public function delete($id = null)
    {
        try {

            //user authentication
            $my_user_id = $this->request->getVar('my_user_id');
            $my_login_token = $this->request->getVar('my_login_token');
            $my_data = $this->member_authentication($my_user_id, $my_login_token);

            $result_data = $this->Main_model->get_one([
                'is_deleted' => 0,
                'id' => $id,
            ]);
            if (empty($result_data)) {
                throw new Exception("Result data not found");
            }

            //Permission check
            $this->user_permission_verification($my_data['id'], $this->current_module, 'delete');

            $this->Main_model->transStart();

            $this->Main_model->update_data([
                'id' => $result_data['id'],
            ], [
                'modified_date' => date("Y-m-d H:i:s"),
                'is_deleted' => 1,
            ]);

            //Insert 'audit_trail'
            $this->Audit_trail_model->insert_data([
                'created_date' => date('Y-m-d H:i:s'),
                'user_id' => $my_data['id'],
                'ref_table' => $this->current_module,
                'ref_id' => $id,
                'action_type' => $this->Audit_trail_model::ACTION_DELETE,
                "origin" => $this->get_function_execution_origin()
            ]);

            $this->Main_model->transComplete();

            return $this->respond([
                'status' => "SUCCESS",
                'result' => []
            ]);
        } catch (Exception $e) {
            return $this->fail([
                'status' => "ERROR",
                'result' => $e->getMessage()
            ]);
        }
    }

    public function export_to_csv($result_list = [])
    {
        $header_list = [];
        $body_list = [];

        //Table的所有栏位
        $table_field_list = $this->Main_model->get_table_fields(true);

        //找出应该被排除的栏位
        $exportation_exclused_fields = $this->Main_model->get_exportation_exclused_fields();

        if (!empty($table_field_list)) {
            foreach ($table_field_list as $k => $v) {
                if (!in_array($k, $exportation_exclused_fields)) {
                    $field_name = ucwords(str_replace("_", " ", $k));
                    $header_list[$k] = $field_name;
                }
            }
        }
        
        if (!empty($result_list)) {
            $bind_select_list = $this->Main_model->bind_select_list;

            foreach ($result_list as $k => $v) {
                $body_data = [];
                foreach ($header_list as $k2 => $v2) {
                    $field_value = $v[$k2] ?? '';

                    //查看是不是Yes No欄位
                    if (in_array($k2, $this->Main_model->yes_no_fields)) {
                        if ($v[$k2] == '1') {
                            $field_value = 'Yes';
                        } else {
                            $field_value = 'No';
                        }
                    } else if (method_exists($this->Main_model, ($k2 . '_kv_list'))) {
                        $field_kv_list = $this->Main_model->{$k2 . '_kv_list'}();
                        if (isset($field_kv_list[$v[$k2]])) {
                            $field_value = $field_kv_list[$v[$k2]];
                        }
                    } else if (isset($bind_select_list[$k2])) {
                        $bind_select_arr = explode("|", $bind_select_list[$k2]);
                        if (count($bind_select_arr) == 2) {
                            $bind_module = $bind_select_arr[0];
                            $bind_field = $bind_select_arr[1];

                            //Load Model
                            $Target_model = model('App\Models\\' . ucfirst($bind_module) . '_model');
                            if ($Target_model) {
                                $target_data = $Target_model->get_one([
                                    'id' => $v[$k2],
                                ]);
                                if (!empty($target_data)) {
                                    if (isset($target_data[$bind_field])) {
                                        $field_value = $v[$k2] . '. ' . $target_data[$bind_field];
                                    }
                                }
                            }
                        }
                    }

                    $body_data[$v2] = $field_value;
                }
                $body_list[] = $body_data;
            }
        }

        //Export to CSV
        $file_name = $this->current_module . "_" . date("YmdHis") . ".csv";
        $output = fopen("php://output", 'w') or die("Can't open php://output");
        header("Content-Type:application/csv");
        header("Content-Disposition:attachment;filename=" . $file_name);
        fputcsv($output, $header_list);
        foreach ($body_list as $body) {
            fputcsv($output, $body);
        }
        fclose($output) or die("Can't close php://output");

        exit;
    }

    //$action : view/add/edit/delete
    public function user_permission_verification($user_id, $module, $action)
    {
        if (isset($this->data['site_config']['backend_check_permission']) && $this->data['site_config']['backend_check_permission'] == "1") {
            //Get data of user to be verify
            $user_data = $this->User_model->get_one([
                'id' => $user_id,
            ]);
            if (empty($user_data)) {
                throw new Exception("User data not found");
            } else {
                //only check NON-Superadmin
                if ($user_data['level'] >= 1) {
                    $system_module_data = $this->System_module_model->get_one([
                        'is_deleted' => 0,
                        'title' => $module,
                    ]);
                    if (empty($system_module_data)) {
                        throw new Exception('Module (' . $module . ') data not found');
                    } else {

                        $user_permission_data = $this->User_permission_model->get_one([
                            'is_deleted' => 0,
                            'system_module_id' => $system_module_data['id'],
                            'user_id' => $user_id,
                            'can_view' => 1,
                        ]);

                        if (empty($user_permission_data)) {
                            throw new Exception('You are not authorized for current module : ' . $module);
                        } else {
                            //check user action
                            switch ($action) {
                                case "add":
                                    if ($user_permission_data['can_add'] != 1) {
                                        throw new Exception('You are not authorized to perform ADD in current module (' . $module . ')');
                                    }
                                    break;
                                case "edit":
                                    if ($user_permission_data['can_edit'] != 1) {
                                        throw new Exception('You are not authorized to perform EDIT in current module (' . $module . ')');
                                    }
                                    break;
                                case "delete":
                                    if ($user_permission_data['can_delete'] != 1) {
                                        throw new Exception('You are not authorized to perform DELETE in current module (' . $module . ')');
                                    }
                                    break;
                            }
                        }
                    }
                }
            }
        }
    }
}
