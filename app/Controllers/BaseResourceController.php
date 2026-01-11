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
    //library
    protected $Emailer_library;

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

            //filtering
            if (!empty($filter)) {
                $filter = array_map('urldecode', $filter);
            }

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

            $start = ($page - 1) * $count;
            $total_record = $this->Main_model->record_count($where, $like);
            $result_list = $this->Main_model->fetch($count, $start, $where, $like);

            foreach ($result_list as $k => $v) {
                $created_date = date('Y-m-d h:i:sA', strtotime($v['created_date']));
                $result_list[$k]['created_on'] = str_replace(" ", "<br>", $created_date);
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
}
