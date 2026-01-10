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
}
