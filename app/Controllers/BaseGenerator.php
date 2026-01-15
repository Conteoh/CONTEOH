<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class BaseGenerator extends Controller
{
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
    }

    protected function module_generation($module_name, $field_list)
    {
        $this->create_table($module_name, $field_list);
        $this->create_model($module_name, $field_list);
        $this->create_controller($module_name, $field_list);
        $this->create_api($module_name, $field_list);
        $this->create_page_list($module_name, $field_list);
        $this->create_page_action($module_name, $field_list);
        $this->generate_navigation_text($module_name);
    }

    private function create_table($module_name, $field_list)
    {
        $query_substr = "";

        foreach ($field_list as $k => $v) {

            $substr_current = "";

            $substr_current .= "`" . $k . "` " . $v['data_type'];

            if (in_array($v['data_type'], ['tinyint', 'int', 'decimal', 'varchar']) && isset($v['length'])) {
                $substr_current .= "(" . $v['length'] . ")";
            }

            if (in_array($v['data_type'], ['varchar', 'text'])) {
                $substr_current .= " COLLATE utf8_unicode_ci";
            }

            // Special handling for created_date and modified_date
            if ($k == 'created_date' && in_array($v['data_type'], ['datetime', 'timestamp'])) {
                $substr_current .= " NOT NULL DEFAULT CURRENT_TIMESTAMP";
            } else if ($k == 'modified_date' && in_array($v['data_type'], ['datetime', 'timestamp'])) {
                $substr_current .= " NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP";
            } else {
                if ($v['NULL']) {
                    $substr_current .= " DEFAULT NULL";
                } else {
                    $substr_current .= " NOT NULL";

                    if (isset($v['default_value']) && $v['default_value'] != '') {
                        $substr_current .= " DEFAULT '" . $v['default_value'] . "'";
                    }
                }
            }

            if (isset($v['comment']) && !empty($v['comment'])) {
                $substr_current .= " COMMENT '" . $v['comment'] . "'";
            }

            if ($v['primary_key']) {
                $substr_current .= " AUTO_INCREMENT";
            }

            $query_substr .= $substr_current . ",\n";
        }

        //define primary key
        $query_substr .= " PRIMARY KEY (`id`)";

        $query_str = "CREATE TABLE IF NOT EXISTS `" . $module_name . "` (\n" . $query_substr . "\n) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;\n\n";

        $db = db_connect();
        $result = $db->query($query_str);

        if ($result) {
            echo "CREATE Table SUCCESS <br/>\n";
        } else {
            echo "CREATE Table FAILED <br/>\n";
        }
    }

    private function create_model($module_name, $field_list)
    {
        $file_path = APPPATH . 'Models/' . ucfirst($module_name) . '_model.php';

        //Kv list function
        $functions_substr = "";
        $yes_no_fields = [];
        foreach ($field_list as $k => $v) {
            if (isset($v['input_type'])) {
                if (
                    (!isset($v['option_yes_no']) || isset($v['option_yes_no']) && $v['option_yes_no'] != true) &&
                    ($v['input_type'] == 'radio' || $v['input_type'] == 'select') &&
                    (isset($v['option']) && !empty($v['option']))
                ) {
                    $current_options_str = "";

                    $counter = 0;
                    foreach ($v['option'] as $k2 => $v2) {

                        if ($counter != 0) {
                            $current_options_str .= "\n\t\t\t";
                        }
                        $current_options_str .= '"' . $k2 . '" => "' . $v2 . '",';
                        $counter++;
                    }

                    $functions_substr .= 'public function ' . ($k) . '_kv_list()
    {
        return [
            ' . $current_options_str . '
        ];
    }' . "\n\n\t";
                }
            }

            if (isset($v['option_yes_no']) && $v['option_yes_no'] == true) {
                $yes_no_fields[] = $k;
            }
        }

        //Define content of yes_no_fields
        $substr_yes_no_fields = "";
        $substr_yes_no_fields .= '$this->yes_no_fields = [';
        if (!empty($yes_no_fields)) {
            foreach ($yes_no_fields as $field_name) {
                $substr_yes_no_fields .= '"' . $field_name . '",';
            }
        }
        $substr_yes_no_fields .= '];' . "\n\n\t";

        //Main Content
        $page_content = '<?php

namespace App\Models;

class ' . ucfirst($module_name) . '_model extends MY_Model
{

    protected $table = "' . $module_name . '";

    public function __construct()
    {
        parent::__construct();

        $this->exportation_exclused_fields = [
            "is_deleted",
            "modified_date",
        ];
        ' . $substr_yes_no_fields . '
    }

    ' . $functions_substr . '
}';

        $fp = fopen($file_path, "w");
        fwrite($fp, $page_content);
        fclose($fp);

        echo "CREATE Model SUCCESS <br/>\n";
    }

    private function create_controller($module_name, $field_list)
    {
        $file_path = APPPATH . 'Controllers/Backend_portal_' . $module_name . '.php';

        $content_access_modifier = '';
        $content_kv_list = '';
        $content_model_load = '';
        $counter = 0;
        foreach ($field_list as $k => $v) {
            if (isset($v['input_type'])) {
                if (
                    (!isset($v['option_yes_no']) || isset($v['option_yes_no']) && $v['option_yes_no'] != true) &&
                    ($v['input_type'] == 'radio' || $v['input_type'] == 'select') &&
                    (isset($v['option']) && !empty($v['option']))
                ) {
                    if ($counter > 0) {
                        $content_kv_list .= "\t\t";
                    }
                    $content_kv_list .= '$this->data["' . $k . '_kv_list"] = $this->Main_model->' . $k . '_kv_list();' . "\n\t\t";
                    $content_kv_list .= '$this->data["' . $k . '_kv_info"] = $this->Main_model->kv_list_to_info($this->data["' . $k . '_kv_list"]);' . "\n\n";

                    $counter += 1;
                } else if (isset($v['bind_select']) && $v['bind_select'] == true && !empty($v['bind_module'])) {

                    if ($counter > 0) {
                        $content_access_modifier .= "\t";
                        $content_model_load .= "\t\t";
                        $content_kv_list .= "\t\t";
                    }
                    $content_access_modifier .= 'protected $' . (ucfirst($v['bind_module'])) . '_model;' . "\n";

                    $content_model_load .= '$this->' . (ucfirst($v['bind_module'])) . '_model = model("App\Models\\' . (ucfirst($v['bind_module'])) . '_model");' . "\n";

                    //define bind label
                    $bind_label = isset($v['bind_label']) && !empty($v['bind_label']) ? $v['bind_label'] : 'id,title';

                    $content_kv_list .= '$this->data["' . ($v['bind_module']) . '_kv_list"] = $this->' . (ucfirst($v['bind_module'])) . '_model->get_kv_list(["is_deleted"=>0], "' . ($bind_label) . '");' . "\n";
                    $content_kv_list .=  "\t\t" . '$this->data["' . ($v['bind_module']) . '_kv_info"] = $this->' . (ucfirst($v['bind_module'])) . '_model->kv_list_to_info($this->data["' . $v['bind_module'] . '_kv_list"]);' . "\n\n";

                    $counter += 1;
                }
            }
        }

        $page_content = '<?php

namespace App\Controllers;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class Backend_portal_' . ($module_name) . ' extends MY_Backend
{
    ' . ($content_access_modifier) . '
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        $this->Main_model = model("App\Models\\' . (ucfirst($module_name)) . '_model");
        ' . $content_model_load . '

        ' . $content_kv_list . '
    }
}';

        $fp = fopen($file_path, "w");
        fwrite($fp, $page_content);
        fclose($fp);

        echo "CREATE Controller SUCCESS <br/>\n";
    }

    private function create_api($module_name, $field_list)
    {
        $file_path = APPPATH . 'Controllers/Backend_api_' . $module_name . '.php';

        //get value from frontend
        $substr_post_value = '';
        $counter = 0;
        $content_filtering = '';
        $substr_get_sql = '';
        $skipped_field = ['is_deleted', 'created_date', 'modified_date'];

        foreach ($field_list as $k => $v) {
            if (!in_array($k, $skipped_field)) {

                $field_name = $k;
                if ($v['data_type'] == 'date') {
                    $field_name = $k . "_temp";
                }

                if ($counter > 0) {
                    $substr_post_value .= "\t\t\t\t";
                }
                $substr_post_value .= '$' . $k . ' = $this->request->getVar("' . $field_name . '");' . "\n";
                $counter++;

                if ($k != 'id') {
                    if (in_array($v['data_type'], ['int', 'tinyint', 'decimal'])) {
                        $substr_get_sql .= "\t\t\t\t\t" . '"' . $k . '" => !empty($' . $k . ')?$' . $k . ':0,' . "\n";
                    } else if (in_array($v['data_type'], ['date', 'datetime'])) {
                        $substr_get_sql .= "\t\t\t\t\t" . '"' . $k . '" => !empty($' . $k . ')?$' . $k . ':null,' . "\n";
                    } else {
                        $substr_get_sql .= "\t\t\t\t\t" . '"' . $k . '" => !empty($' . $k . ')?$' . $k . ':null,' . "\n";
                    }
                }

                if (
                    (isset($v['show_in_list_page']) && $v['show_in_list_page'] == true) &&
                    ($k != 'id')
                ) {

                    if (in_array($v['data_type'], ['int', 'tinyint', 'decimal'])) {
                        $content_filtering .= "\t\t\t\t\t" . 'if(isset($filter[\'' . $k . '\'])){
                        $where[\'' . $k . '\'] = $filter[\'' . $k . '\'];
                    }' . "\n";
                    } else {
                        $content_filtering .= "\t\t\t\t\t" . 'if(isset($filter[\'' . $k . '\']) && !empty($filter[\'' . $k . '\'])){
                        $like[\'' . $k . '\'] = $filter[\'' . $k . '\'];
                    }' . "\n";
                    }
                }
            }
        }

        $page_content = '<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use Exception;

class Backend_api_' . ($module_name) . ' extends BaseResourceController
{
    use ResponseTrait;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        $this->Main_model = model(\'App\Models\\' . (ucfirst($module_name)) . '_model\');
        $this->current_module = \'' . ($module_name) . '\';
        $this->current_module_name = ucwords(str_replace("_", " ", $this->current_module));        
    }

    public function submit(){
        try {

            if (isset($_SERVER["CONTENT_TYPE"]) && strpos($_SERVER["CONTENT_TYPE"], "application/json") !== false) {
                $_POST = array_merge($_POST, (array) json_decode(trim(file_get_contents(\'php://input\')), true));

                //authentication
                $my_user_id = $this->request->getVar("my_user_id");
                $my_login_token = $this->request->getVar("my_login_token");
                $my_data = $this->member_authentication($my_user_id, $my_login_token);
                
                ' . $substr_post_value . '

                $submit_data = [' . "\n" . $substr_get_sql . "\t\t\t\t" . '];

                $this->Main_model->transStart();

                if (!empty($id) && $id != "0") {
                    //Permission check
                    $this->member_permission_verification($my_data[\'id\'], $this->current_module ,\'edit\');

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
                    $this->member_permission_verification($my_data[\'id\'], $this->current_module ,\'add\');

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
                throw new Exception(\'Invalid param\');
            }
        } catch (Exception $e) {
            return $this->fail([
                "status" => "ERROR",
                "result" => $e->getMessage()
            ]);
        }
    }
}
';

        $fp = fopen($file_path, "w");
        fwrite($fp, $page_content);
        fclose($fp);

        echo "CREATE API SUCCESS <br/>\n";
    }

    private function create_page_list($module_name, $field_list)
    {
        $folder_name = APPPATH . 'Views/backend/' . $module_name;
        if (!file_exists($folder_name)) {
            mkdir($folder_name);
        }

        $file_path = APPPATH . 'Views/backend/' . $module_name . '/list.php';

        $content_table_column = "";
        $content_filter_fields = "";
        $substr_kv_list = "";
        $substr_kv_list_yes_no = "";
        $skipped_field = ['id', 'is_deleted', 'modified_date'];
        $counter1 = 0;
        $filter_counter = 0;

        foreach ($field_list as $k => $v) {
            if (!in_array($k, $skipped_field)) {
                // Generate filter fields
                if (isset($v['show_in_list_page']) && $v['show_in_list_page'] == true) {
                    if ($filter_counter > 0) {
                        $content_filter_fields .= "\t\t\t\t\t\t\t\t\t";
                    }

                    switch ($v['input_type']) {
                        case "radio":
                        case "select":
                            if (isset($v['option_yes_no']) && $v['option_yes_no'] == true) {
                                $content_filter_fields .= '<div class="col-md-4 mb-2">
                                    <div class="form-group">
                                        <label for="' . $k . '">' . ($v['label']) . '</label>
                                        <select class="form-control form-control-sm" id="' . $k . '" name="' . $k . '" ng-model="filter_data.' . $k . '">
                                            <option value="">All</option>
                                            <?php if (isset($yes_no_kv_list) && !empty($yes_no_kv_list)): ?>
                                                <?php foreach ($yes_no_kv_list as $k => $v): ?>
                                                    <option value="<?= $k ?>"><?= $v ?></option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </div>' . "\n";
                            } else {
                                $content_filter_fields .= '<div class="col-md-4 mb-2">
                                    <div class="form-group">
                                        <label for="' . $k . '">' . ($v['label']) . '</label>
                                        <select class="form-control form-control-sm" id="' . $k . '" name="' . $k . '" ng-model="filter_data.' . $k . '">
                                            <option value="">All</option>
                                            <?php if (isset($' . $k . '_kv_list) && !empty($' . $k . '_kv_list)): ?>
                                                <?php foreach ($' . $k . '_kv_list as $k => $v): ?>
                                                    <option value="<?= $k ?>"><?= $v ?></option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </div>' . "\n";
                            }
                            break;
                        case "bind_select":
                            $content_filter_fields .= '<div class="col-md-4 mb-2">
                                <div class="form-group">
                                    <label for="' . $k . '">' . ($v['label']) . '</label>
                                    <select class="form-control form-control-sm" id="' . $k . '" name="' . $k . '" ng-model="filter_data.' . $k . '">
                                        <option value="">All</option>
                                        <?php if (isset($' . $v['bind_module'] . '_kv_list) && !empty($' . $v['bind_module'] . '_kv_list)): ?>
                                            <?php foreach ($' . $v['bind_module'] . '_kv_list as $k => $v): ?>
                                                <option value="<?= $k ?>"><?= $v ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>' . "\n";
                            break;
                        case "date":
                        case "datetime":
                            $content_filter_fields .= '<div class="col-md-4 mb-2">
                                <div class="form-group">
                                    <label for="' . $k . '">' . ($v['label']) . '</label>
                                    <input type="date" class="form-control form-control-sm" id="' . $k . '" name="' . $k . '" ng-model="filter_data.' . $k . '">
                                </div>
                            </div>' . "\n";
                            break;
                        default:
                            $content_filter_fields .= '<div class="col-md-4 mb-2">
                                <div class="form-group">
                                    <label for="' . $k . '">' . ($v['label']) . '</label>
                                    <input type="text" class="form-control form-control-sm" id="' . $k . '" name="' . $k . '" placeholder="' . ($v['label']) . '" ng-model="filter_data.' . $k . '">
                                </div>
                            </div>' . "\n";
                            break;
                    }
                    $filter_counter++;
                }

                // Generate table columns
                if (isset($v['input_type']) && (isset($v['show_in_list_page']) && $v['show_in_list_page'] == true)) {

                    if ($counter1 > 0) {
                        $content_table_column .= "\t\t\t\t\t\t\t\t\t\t";
                        $substr_kv_list .= "\t\t";
                    }

                    switch ($v['input_type']) {
                        case "radio":
                        case "select":

                            if (isset($v['option_yes_no']) && $v['option_yes_no'] == true) {

                                $content_table_column .= '<td title="\'' . $v['label'] . '\'">
                                            <span ng-if="item.' . $k . ' == 1" class="badge bg-success">{{yes_no_kv_list[item.' . $k . ']}}</span>
                                            <span ng-if="item.' . $k . ' == 0" class="badge bg-warning">{{yes_no_kv_list[item.' . $k . ']}}</span>
                                        </td>' . "\n";

                                if (empty($substr_kv_list_yes_no)) {
                                    $substr_kv_list_yes_no .= '$scope.yes_no_kv_list = <?= isset($yes_no_kv_list) ? json_encode($yes_no_kv_list) : \'[]\' ?>;' . "\n\t\t";
                                }
                            } else {
                                $content_table_column .= '<td title="\'' . $v['label'] . '\'">{{' . $k . '_kv_list[item.' . $k . ']}}</td>' . "\n";

                                $substr_kv_list .= '$scope.' . $k . '_kv_list = <?= isset($' . $k . '_kv_list) ? json_encode($' . $k . '_kv_list) : \'[]\' ?>;' . "\n";
                            }

                            break;
                        case "bind_select":
                            $content_table_column .= '<td title="\'' . $v['label'] . '\'">{{' . $v['bind_module'] . '_kv_list[item.' . $k . ']}}</td>' . "\n";

                            $substr_kv_list .= '$scope.' . $v['bind_module'] . '_kv_list = <?= isset($' . $v['bind_module'] . '_kv_list) ? json_encode($' . $v['bind_module'] . '_kv_list) : \'[]\' ?>;' . "\n";
                            break;
                        case "file":
                            $content_table_column .= '<td title="\'' . $v['label'] . '\'"><img ng-src="{{item.' . ($k) . '}}" class="img-fluid" width="150px"></td>' . "\n";
                            break;
                        default:
                            $content_table_column .= '<td title="\'' . $v['label'] . '\'">{{item.' . $k . '}}</td>' . "\n";
                            break;
                    }

                    $counter1++;
                }
            }
        }

        $page_content = '<main class="app-main" ng-app="myApp" ng-controller="myCtrl" ng-cloak>
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">
                        <span class=""><?= $current_module_name ?></span> - <?= $current_page_name ?>
                    </h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="<?= base_url(BACKEND_PORTAL . \'/dashboard\') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?= $current_module_name ?></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="app-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fa fa-filter"></i> Filter Criteria</h5>
                        </div>
                        <div class="card-body">
                            <form name="filter_form">
                                <div class="row mb-2">
                                    <div class="col-md-4 mb-2">
                                        <div class="form-group">
                                            <label for="created_date_from">Created Date From</label>
                                            <input type="date" class="form-control form-control-sm" id="created_date_from" name="created_date_from" placeholder="Created Date From" ng-model="filter_data.created_date_from">
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <div class="form-group">
                                            <label for="created_date_to">Created Date To</label>
                                            <input type="date" class="form-control form-control-sm" id="created_date_to" name="created_date_to" placeholder="Created Date To" ng-model="filter_data.created_date_to">
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <div class="form-group">
                                            <label for="id">ID</label>
                                            <input type="text" class="form-control form-control-sm" id="id" name="id" placeholder="ID" ng-model="filter_data.id">
                                        </div>
                                    </div>
                                    ' . $content_filter_fields . '
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <button type="button" class="btn btn-secondary btn-sm" ng-click="reset_filter()" ng-disabled="filter_form.$pristine"><i class="fa fa-refresh"></i> Reset Filter</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="text-end mb-2">
                        <button type="button" class="btn btn-secondary btn-sm" ng-click="export_now()"><i class="fa fa-download"></i> Export</button>
                        <a href="<?= base_url(BACKEND_PORTAL . \'/\' . $current_module . \'/add\') ?>" class="btn btn-primary btn-sm"><i class="fa fa-plus-circle"></i> Add New</a>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <div class="row justify-content-between align-items-center">
                                <div class="col-md-6 d-flex align-items-center">
                                    <h5 class="mb-0"><i class="fa fa-database"></i> Total Result : <span class="text-primary">{{config_data.total_record}}</span> Record</h5>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-floating">
                                        <select class="form-select" id="item_per_page" ng-model="config_data.item_per_page" ng-change="load_result_list_now()">
                                            <?php if (isset($item_per_page_kv_list) && !empty($item_per_page_kv_list)): ?>
                                                <?php foreach ($item_per_page_kv_list as $k => $v): ?>
                                                    <option value="<?= $k ?>"><?= $v ?></option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                        <label for="item_per_page">Item Per Page</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table ng-table="tableParams" class="table table-bordered table-sm b-result-list-table" show-filter="false">
                                    <tr ng-repeat="item in $data" class="text-center">
                                        <td title="\'Action\'">
                                            <div class="mb-1">
                                                <button type="button" class="btn btn-primary btn-sm list-act-btn" ng-click="edit_record(item)"><i class="fa fa-edit"></i></button>
                                            </div>
                                            <div class="mb-1">
                                                <button type="button" class="btn btn-danger btn-sm list-act-btn" ng-click="delete_record(item)"><i class="fa fa-trash"></i></button>
                                            </div>
                                        </td>
                                        <td title="\'Created Date\'"><span ng-bind-html="item.created_on"></span></td>
                                        <td title="\'ID\'">{{item.id}}</td>
                                        ' . $content_table_column . '
                                    </tr>
                                    <tr ng-if="config_data.total_record == 0">
                                        <td colspan="' . (3 + $counter1) . '" class="text-center">No data found</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<script>
    var app = angular.module("myApp", ["ngTable", "ngResource", "ui.bootstrap", "ngSanitize"]);
    app.controller("myCtrl", function($scope, $http, $timeout, NgTableParams, $resource) {

        $scope.my_user_id = \'<?= $my_user_id ?? 0 ?>\';
        $scope.my_login_token = \'<?= $my_login_token ?? \'\' ?>\';

        ' . $substr_kv_list . '
        ' . $substr_kv_list_yes_no . '

        $scope.config_data = {
            \'total_record\': 0,
            \'item_per_page\': \'<?= $site_config["item_per_page"] ?? BACKEND_ITEM_PER_PAGE ?>\'
        };

        $scope.filter_data = {
            "id": "<?= $_GET[\'id\'] ?? \'\' ?>"
        };

        var Api = $resource("<?= base_url(BACKEND_API . "/" . $current_module . "/list") ?>/" + $scope.my_user_id + "/" + $scope.my_login_token);
        $scope.load_result_list_now = function() {
            var tableConfig = {
                page: 1,
                count: $scope.config_data.item_per_page,
                filter: $scope.filter_data
            };

            //如果有Last Page Memory的话，则恢复Last Page Memory
            let last_page_memory = localStorage.getItem(\'last_page_memory_<?= $current_module ?>\');
            if (last_page_memory) {
                tableConfig = JSON.parse(last_page_memory);
                localStorage.removeItem(\'last_page_memory_<?= $current_module ?>\');
            }

            $scope.tableParams = new NgTableParams(tableConfig, {
                counts: [],
                getData: function(params) {
                    // ajax request to api
                    return Api.get(params.url()).$promise.then(function(data) {
                        params.total(data.result.total_record); // recal. page nav controls
                        $scope.config_data.total_record = data.result.total_record;
                        return data.result.result_list;
                    });
                }
            });
        }
        $scope.load_result_list_now();

        $scope.delete_record = function(item) {

            var ans = confirm("<?= BACKEND_DELETE_ACTION_REMINDER ?>");
            if (ans) {

                var to_be_submit = {
                    "my_user_id": $scope.my_user_id,
                    "my_login_token": $scope.my_login_token,
                };
                var query_string = Object.keys(to_be_submit).map(key => key + "=" + encodeURIComponent(to_be_submit[key])).join("&");

                $http.delete("<?= base_url(BACKEND_API . "/" . $current_module . "/delete/") ?>" + item.id + "?" + query_string).then(function(response) {

                    if (response.data.status == "SUCCESS") {
                        alert("<?= BACKEND_DELETE_SUCCESS_MSG ?>");
                        let table_parms = JSON.stringify($scope.tableParams.url());
                        localStorage.setItem(\'last_page_memory_<?= $current_module ?>\', table_parms);
                        $scope.load_result_list_now();
                    } else {
                        alert(response.data.result);
                    }

                }, function(response) {
                    alert(response.data.messages.result);
                });
            }

        }

        $scope.edit_record = function(item) {
            let table_parms = JSON.stringify($scope.tableParams.url());
            localStorage.setItem(\'last_page_memory_<?= $current_module ?>\', table_parms);
            location.href = "<?= base_url(BACKEND_PORTAL . "/" . $current_module . "/edit/") ?>" + item.id;
        }

        $scope.reset_filter = function() {
            $scope.filter_data = {};
            $scope.load_result_list_now();
        }

        $scope.export_now = () => {
            let to_be_submit = angular.copy($scope.filter_data);
            to_be_submit[\'is_export\'] = 1;

            let params = [];
            angular.forEach(to_be_submit, function(value, key) {
                params.push(key + \'=\' + encodeURIComponent(value));
            });

            let query_str = params.join(\'&\');

            let url = "<?= base_url(BACKEND_API . "/" . $current_module . "/list") ?>" + "/" + $scope.my_user_id + "/" + $scope.my_login_token + \'?\' + query_str;

            window.open(url, \'_blank\');

        }
    });
</script>
';

        $fp = fopen($file_path, "w");
        fwrite($fp, $page_content);
        fclose($fp);

        echo "CREATE LIST SUCCESS <br/>\n";
    }

    private function create_page_action($module_name, $field_list)
    {
        $folder_name = APPPATH . 'Views/backend/' . $module_name;
        if (!file_exists($folder_name)) {
            mkdir($folder_name);
        }

        $content_form_body = "";
        $content_default_value_add = "";
        $content_default_value_edit = "";
        $content_kv_info = "";
        $content_yes_no_list = "";
        $content_before_submit = "";
        $counter = 0;

        $skipped_field = ['id', 'is_deleted', 'created_date', 'modified_date'];
        foreach ($field_list as $k => $v) {
            if (!in_array($k, $skipped_field)) {

                if (isset($v['input_type']) && (isset($v['show_in_action_page']) && $v['show_in_action_page'] == true)) {
                    switch ($v['input_type']) {
                        case "radio":
                        case "select":
                            if ($counter > 0) {
                                $content_kv_info .= "\t\t";
                            }
                            if (!isset($v['option_yes_no']) || $v['option_yes_no'] == false) {
                                $content_kv_info .= '$scope.' . $k . '_kv_info = <?= isset($' . $k . '_kv_info) ? json_encode($' . $k . '_kv_info) : \'[]\' ?>;' . "\n";
                            }
                            break;
                        case "bind_select":
                            if ($counter > 0) {
                                $content_kv_info .= "\t\t";
                            }
                            $content_kv_info .= '$scope.' . $v['bind_module'] . '_kv_info = <?= isset($' . $v['bind_module'] . '_kv_info) ? json_encode($' . $v['bind_module'] . '_kv_info) : \'[]\' ?>;' . "\n";
                            break;
                        default:
                            break;
                    }

                    $counter++;
                }

                if (isset($v['input_type']) && (isset($v['show_in_action_page']) && $v['show_in_action_page'] == true)) {
                    switch ($v['input_type']) {
                        case "radio":

                            $temp = "";
                            if (isset($v['option_yes_no']) && $v['option_yes_no'] == true) {
                                $temp = "yes_no";
                            } else {
                                $temp = $k;
                            }

                            $content_form_body .= '<div class="col-md-4 mb-3">
                                <div class="form-group">
                                    <label for="' . ($k) . '">' . ($v['label']) . '</label>
                                    <div class="row py-2">
                                        <div class="col-auto" ng-repeat="(k,v) in ' . ($temp) . '_kv_info">
                                            <div class="custom-control custom-radio">
                                                <input type="radio" class="custom-control-input" id="' . ($k) . '{{k}}" name="' . ($k) . '" ng-model="form_data.' . ($k) . '" value="{{v.id}}" ' . ((isset($v['compulsory']) && $v['compulsory'] == true) ? 'required' : '') . '>
                                                <label class="custom-control-label" for="' . ($k) . '{{k}}">{{v.title}}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>';
                            break;
                        case "select":
                            $content_form_body .= '<div class="col-md-4 mb-3">
                                <div class="form-group">
                                    <label for="' . ($k) . '">' . ($v['label']) . '</label>
                                    <select class="form-control" id="' . ($k) . '" ng-model="form_data.' . ($k) . '" ' . ((isset($v['compulsory']) && $v['compulsory'] == true) ? 'required' : '') . ' ' . ((isset($v['disabled']) && $v['disabled'] == true) ? 'disabled' : '') . '>
                                        <option value="{{item.id}}" ng-repeat="item in ' . ($k) . '_kv_info">{{item.title}}</option>
                                    </select>
                                </div>
                            </div>';
                            break;
                        case "bind_select":
                            $content_form_body .= '<div class="col-md-4 mb-3">
                                <div class="form-group">
                                    <label for="' . ($k) . '">' . ($v['label']) . '</label>
                                    <select class="form-control" id="' . ($k) . '" ng-model="form_data.' . ($k) . '" ' . ((isset($v['compulsory']) && $v['compulsory'] == true) ? 'required' : '') . ' ' . ((isset($v['disabled']) && $v['disabled'] == true) ? 'disabled' : '') . '>
                                        <option value="{{item.id}}" ng-repeat="item in ' . $v['bind_module'] . '_kv_info">{{item.title}}</option>
                                    </select>
                                </div>
                            </div>';
                            break;
                        case "textarea":
                            $content_form_body .= '<div class="col-md-12 mb-3">
                                <div class="form-group">
                                    <label for="' . ($k) . '">' . ($v['label']) . '</label>
                                    <textarea class="form-control" id="' . ($k) . '" placeholder="' . ($v['label']) . '" ng-model="form_data.' . ($k) . '" ' . ((isset($v['compulsory']) && $v['compulsory'] == true) ? 'required' : '') . ' rows="5" ' . ((isset($v['ckeditor']) && $v['ckeditor'] == true) ? 'ck-editor' : '') . ' ' . ((isset($v['disabled']) && $v['disabled'] == true) ? 'disabled' : '') . '></textarea>
                                </div>
                            </div>';
                            break;
                        case "file":

                            $upload_reminder = "";
                            if (!empty($v['width']) && !empty($v['height'])) {
                                if ($v['width'] != 'ori' || $v['height'] != 'ori') {
                                    $upload_reminder = " (" . $v['width'] . 'x' . $v['height'] . "PX)";
                                }
                            }

                            $content_form_body .= '<div class="col-md-12 mb-3">
                                <div class="form-group">
                                    <label for="' . ($k) . '">' . ($v['label']) . $upload_reminder . '</label>
                                    <div>
                                        <div class="mb-1" ng-if="form_data.' . ($k) . '">
                                            <a href="{{form_data.' . ($k) . '}}" target="_blank"><img ng-src="{{form_data.' . ($k) . '}}" id="' . ($k) . '_photo" class="img-fluid" /></a>
                                        </div>
                                        <input type="file" class="form-control" id="' . ($k) . '" ng-files="getTheFiles($files,\'' . ($k) . '\')" ng-disabled="form_data.is_uploading_' . ($k) . '==1" />
                                        <input type="text" style="display:none" ng-model="form_data.' . ($k) . '" />

                                        <div class="mt-1">
                                            <button type="button" id="upload_btn_' . ($k) . '" class="btn btn-secondary btn-sm" ng-click="uploadFiles(\'' . ($v['width']) . '\',\'' . ($v['height']) . '\',\'' . ($k) . '\')" ng-disabled="form_data.is_uploading_' . ($k) . '==1"><i class="fa fa-upload"></i> Upload <i class="fa fa-spinner fa-spin" ng-if="form_data.is_uploading_' . ($k) . '==1"></i></button>
                                            <button type="button" class="btn btn-danger btn-sm" ng-click="form_data.' . ($k) . '=\'\'" ng-if="form_data.' . ($k) . '"><i class="fa fa-trash"></i> Remove</button>
                                        </div>
                                    </div>
                                </div>
                            </div>';
                            break;
                        case "email":
                            $content_form_body .= '<div class="col-md-4 mb-3">
                                <div class="form-group">
                                    <label for="' . ($k) . '">' . ($v['label']) . '</label>
                                    <input type="email" class="form-control" id="' . ($k) . '" placeholder="' . ($v['label']) . '" ng-model="form_data.' . ($k) . '" ' . ((isset($v['compulsory']) && $v['compulsory'] == true) ? 'required' : '') . ' ' . ((isset($v['disabled']) && $v['disabled'] == true) ? 'disabled' : '') . '>
                                </div>
                            </div>';
                            break;
                        case "date":
                        case "datetime":

                            $current_input_type = "date";
                            if ($v['input_type'] == "datetime") {
                                $current_input_type = "datetime-local";
                            }

                            $content_form_body .= '<div class="col-md-4 mb-3">
                                <div class="form-group">
                                    <label for="' . ($k) . '">' . ($v['label']) . '</label>
                                    <input type="' . $current_input_type . '" class="form-control" id="' . ($k) . '" ng-model="form_data.' . ($k) . '" ' . ((isset($v['compulsory']) && $v['compulsory'] == true) ? 'required' : '') . ' ' . ((isset($v['disabled']) && $v['disabled'] == true) ? 'disabled' : '') . '>
                                </div>
                            </div>';
                            $content_default_value_edit .= 'if($scope.form_data.' . $k . '){
                                $scope.form_data.' . $k . ' = new Date($scope.form_data.' . $k . ');
                            }' . "\n";

                            $content_before_submit .= 'if($scope.form_data.' . $k . '){
                                $scope.form_data.' . $k . '_temp = $("#' . $k . '").val();
                            }' . "\n";

                            break;
                        case "number":
                            $content_form_body .= '<div class="col-md-4 mb-3">
                                <div class="form-group">
                                    <label for="' . ($k) . '">' . ($v['label']) . '</label>
                                    <input type="number" step="' . ($v['data_type'] == 'decimal' ? '.01' : '1') . '" class="form-control" id="' . ($k) . '" ng-model="form_data.' . ($k) . '" ' . ((isset($v['compulsory']) && $v['compulsory'] == true) ? 'required' : '') . ' ' . ((isset($v['disabled']) && $v['disabled'] == true) ? 'disabled' : '') . '>
                                </div>
                            </div>';

                            $parseType = "parseFloat";
                            if ($v['data_type'] != 'decimal') {
                                $parseType = "parseInt";
                            }

                            $content_default_value_edit .= 'if($scope.form_data.' . $k . '){
                                $scope.form_data.' . $k . ' = ' . $parseType . '($scope.form_data.' . $k . ');
                            }' . "\n";
                            break;
                        default:
                            $content_form_body .= '<div class="col-md-4 mb-3">
                                <div class="form-group">
                                    <label for="' . ($k) . '">' . ($v['label']) . '</label>
                                    <input type="text" class="form-control" id="' . ($k) . '" placeholder="' . ($v['label']) . '" ng-model="form_data.' . ($k) . '" ' . ((isset($v['compulsory']) && $v['compulsory'] == true) ? 'required' : '') . ' ' . ((isset($v['disabled']) && $v['disabled'] == true) ? 'disabled' : '') . '>
                                </div>
                            </div>';
                            break;
                    }
                }

                if (isset($v['default_value']) && $v['default_value'] != '') {
                    if (!in_array($k, ['is_deleted'])) {
                        $value = "''";
                        if ($v['data_type'] == 'decimal' || $v['data_type'] == 'int' && ($v['input_type'] != 'bind_select')) {
                            $value = $v['default_value'];
                        } else {
                            $value = '"' . $v['default_value'] . '"';
                        }

                        $content_default_value_add .= '"' . $k . '" : ' . $value . ",\n";
                    }
                }

                if (isset($v['option_yes_no']) && $v['option_yes_no'] == true) {
                    if (empty($content_yes_no_list)) {
                        $content_yes_no_list = '$scope.yes_no_kv_info = <?=isset($yes_no_kv_info)?json_encode($yes_no_kv_info):"[]"?>;' . "\n";
                    }
                }
            }
        }

        $page_content = '<main class="app-main" ng-app="myApp" ng-controller="myCtrl" ng-cloak>
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">
                        <span class=""><?= $current_module_name ?></span> - <?= $current_page_name ?>
                    </h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="<?= base_url(BACKEND_PORTAL . \'/dashboard\') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item">
                            <a href="<?= base_url(BACKEND_PORTAL . \'/\' . $current_module . \'/list\') ?>"><?= $current_module_name ?></a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page"><?= $current_page_name ?></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="app-content">
        <div class="container-fluid">
            <form name="result_form" was-validate>
                <div class="row">
                    <!-- General Information -->
                    <div class="col-lg-12">

                        <div class="d-flex justify-content-end gap-2 mb-2">
                            <button type="button" class="btn btn-sm btn-secondary" ng-click="back_now()">
                                <i class="fa fa-arrow-left"></i> Back
                            </button>
                            <button type="button" class="btn btn-sm btn-primary" ng-click="submit_now()" ng-disabled="result_form.$invalid || result_form.$pristine || form_data.is_submitting">
                                <i class="fa fa-save"></i> Save Changes <i class="fa fa-spinner fa-spin" ng-show="form_data.is_submitting"></i>
                            </button>
                        </div>

                        <div class="card mb-4">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fa fa-info-circle"></i> <b>General Information</b></h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    ' . ($content_form_body) . '
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-12" ng-if="form_data.error_msg">
                        <div class="alert alert-danger">
                            <i class="fa fa-exclamation-triangle"></i> {{form_data.error_msg}}
                        </div>
                    </div>

                    <div>
                        <button type="button" class="btn btn-sm btn-primary" ng-click="submit_now()" ng-disabled="result_form.$invalid || result_form.$pristine || form_data.is_submitting">
                            <i class="fa fa-save"></i> Save Changes <i class="fa fa-spinner fa-spin" ng-show="form_data.is_submitting"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</main>
<script>
    var app = angular.module("myApp", []);

    app.controller("myCtrl", function($scope, $http, $timeout) {

        $scope.my_user_id = \'<?= $my_user_id ?? 0 ?>\';
        $scope.my_login_token = \'<?= $my_login_token ?? \'\' ?>\';

        //Load Kv List
        ' . ($content_yes_no_list) . '
        ' . ($content_kv_info) . '
        $scope.id = <?= (isset($id) && !empty($id)) ? $id : 0 ?>;

        if ($scope.id && $scope.id > 0) {
            $scope.form_data = <?= isset($result_data) ? json_encode($result_data) : "[]" ?>;
            $scope.form_data.my_user_id = $scope.my_user_id;
            $scope.form_data.my_login_token = $scope.my_login_token;

            ' . ($content_default_value_edit) . '

        } else {
            $scope.form_data = {
                "my_user_id": $scope.my_user_id,
                "my_login_token": $scope.my_login_token,

                ' . ($content_default_value_add) . '
            };
        }

        $scope.back_now = function() {
            if ($scope.result_form.$dirty) {
                var ans = confirm("Leave this page without saving");
                if (!ans) {
                    return false;
                }
            }
            let url = "<?= base_url(BACKEND_PORTAL . \'/\' . $current_module . \'/list\') ?>";
            location.href = url;
        }

        $scope.submit_now = function() {

            $scope.form_data.error_msg = "";

            let ans = confirm("<?= BACKEND_SUBMIT_ACTION_REMINDER ?>");
            if (!ans) {
                return false;
            }

            ' . "\n" . $content_before_submit . '

            var to_be_submit = angular.copy($scope.form_data);

            $scope.form_data.is_submitting = 1;

            $http.post("<?= base_url(BACKEND_API . \'/\' . $current_module . \'/submit\') ?>", to_be_submit).then(function(response) {

                $scope.form_data.is_submitting = 0;

                if (response.data.status == "SUCCESS") {
                    alert("<?= BACKEND_SUBMIT_SUCCESS_MSG ?>");
                    location.href = "<?= base_url(BACKEND_PORTAL . "/" . $current_module . \'/list\') ?>";
                } else {
                    alert(response.data.result);
                    $scope.form_data.error_msg = response.data.result;
                }

            }, function(response) {

                $scope.form_data.is_submitting = 0;
                alert(response.data.messages.result);
                $scope.form_data.error_msg = response.data.messages.result;
            });
        }

        //image upload 
        var formdata = new FormData();
        $scope.getTheFiles = function($files, field_id) {
            formdata.append(field_id, $files[0]);
            $(\'#upload_btn_\' + field_id).click();
        };

        $scope.uploadFiles = function(width, height, field_id, fieldName, is_list, index) {

            if (typeof fieldName == undefined || typeof fieldName == "undefined") {
                fieldName = "form_data";
            }
            if (typeof index == undefined || typeof index == "undefined") {
                index = 0;
            }

            formdata.append("width", width);
            formdata.append("height", height);
            formdata.append("crop", "lpad");
            formdata.append("field_name", field_id);
            formdata.append("my_user_id", $scope.my_user_id);
            formdata.append("my_login_token", $scope.my_login_token);

            var request = {
                method: "POST",
                url: "<?= base_url(BACKEND_API.\'/general/upload_image_now\') ?>",
                data: formdata,
                headers: {
                    "Content-Type": undefined
                }
            };

            if (is_list) {
                $scope[fieldName][index]["is_uploading_" + field_id] = 1;
            } else {
                $scope[fieldName]["is_uploading_" + field_id] = 1;
            }

            // SEND THE FILES.        
            $http(request).then(function(res) {
                if (is_list) {
                    $scope[fieldName][index]["is_uploading_" + field_id] = 0;
                } else {
                    $scope[fieldName]["is_uploading_" + field_id] = 0;
                }
                if (res.data.status == "SUCCESS") {
                    if (is_list) {
                        $scope[fieldName][index][field_id.replace(index, "")] = res.data.result;
                    } else {
                        $scope[fieldName][field_id] = res.data.result;
                    }

                    //clear input of file
                    $("#" + field_id).val("");
                    formdata.delete(field_id);
                } else {
                    alert(res.data.result);
                }
            }, function(res) {
                alert(res.data.messages.result);
                if (is_list) {
                    $scope[fieldName][index]["is_uploading_" + field_id] = 0;
                } else {
                    $scope[fieldName]["is_uploading_" + field_id] = 0;
                }
            })
        }
    }).directive("ngFiles", ["$parse", function($parse) {

        function fn_link(scope, element, attrs) {
            var onChange = $parse(attrs.ngFiles);
            element.on("change", function(event) {
                onChange(scope, {
                    $files: event.target.files
                });
            });
        };

        return {
            link: fn_link
        }
    }]).directive("ckEditor", function($timeout) {
        return {
            restrict: \'A\', // only activate on element attribute
            require: \'ngModel\',
            link: function(scope, element, attr, ngModel, ngModelCtrl) {
                if (!ngModel) return; // do nothing if no ng-model you might want to remove this

                var ck = CKEDITOR.replace(element[0], {
                    allowedContent: true,
                    height: 200,
                    toolbar: [{
                            name: \'document\',
                            items: [\'Source\', \'-\', \'Save\', \'NewPage\', \'Preview\', \'Print\', \'-\', \'Templates\']
                        },
                        {
                            name: \'clipboard\',
                            items: [\'Cut\', \'Copy\', \'Paste\', \'PasteText\', \'PasteFromWord\', \'-\', \'Undo\', \'Redo\']
                        },
                        {
                            name: \'editing\',
                            items: [\'Find\', \'Replace\', \'-\', \'SelectAll\', \'-\', \'Scayt\']
                        },
                        {
                            name: \'forms\',
                            items: [\'Form\', \'Checkbox\', \'Radio\', \'TextField\', \'Textarea\', \'Select\', \'Button\', \'ImageButton\', \'HiddenField\']
                        },
                        \'/\',
                        {
                            name: \'basicstyles\',
                            items: [\'Bold\', \'Italic\', \'Underline\', \'Strike\', \'Subscript\', \'Superscript\', \'-\', \'CopyFormatting\', \'RemoveFormat\']
                        },
                        {
                            name: \'paragraph\',
                            items: [\'NumberedList\', \'BulletedList\', \'-\', \'Outdent\', \'Indent\', \'-\', \'Blockquote\', \'CreateDiv\', \'-\', \'JustifyLeft\', \'JustifyCenter\', \'JustifyRight\', \'JustifyBlock\', \'-\', \'BidiLtr\', \'BidiRtl\', \'Language\']
                        },
                        {
                            name: \'links\',
                            items: [\'Link\', \'Unlink\', \'Anchor\']
                        },
                        {
                            name: \'insert\',
                            items: [\'Image\', \'Flash\', \'Table\', \'HorizontalRule\', \'Smiley\', \'SpecialChar\', \'PageBreak\', \'Iframe\']
                        },
                        \'/\',
                        {
                            name: \'styles\',
                            items: [\'Styles\', \'Format\', \'Font\', \'FontSize\']
                        },
                        {
                            name: \'colors\',
                            items: [\'TextColor\', \'BGColor\']
                        },
                        {
                            name: \'tools\',
                            items: [\'Maximize\', \'ShowBlocks\']
                        },
                        {
                            name: \'about\',
                            items: [\'About\']
                        }
                    ]
                });

                function updateModel() {
                    scope.$apply(function() {
                        ngModel.$setViewValue(ck.getData());
                    });
                }

                ck.on(\'instanceReady\', function() {
                    $timeout(function() {
                        ck.setData(ngModel.$viewValue);
                    }, 350);
                });

                ck.on(\'pasteState\', updateModel);
                ck.on(\'change\', updateModel);
                ck.on(\'key\', updateModel);

                ngModel.$render = function(value) {
                    $timeout(function() {
                        ck.setData(ngModel.$viewValue);
                    }, 350);
                };

            }
        }
    });
</script>
';

        $file_path = APPPATH . 'Views/backend/' . $module_name . '/action.php';

        $fp = fopen($file_path, "w");
        fwrite($fp, $page_content);
        fclose($fp);

        echo "CREATE ACTION SUCCESS <br/>\n";
    }

    private function generate_navigation_text($module_name)
    {
        echo "CREATE NAVIGATION SUCCESS <br/>\n\n";

        $module_display_name = ucwords(str_replace('_', ' ', $module_name));
        
        $content = '<!--' . $module_display_name . '-->
<?php if ($this->data[\'site_config\'][\'backend_check_permission\']!=\'1\' || $this->data[\'my_user_data\'][\'level\'] == \'-1\' || (isset($my_permission_list[\'' . $module_name . '\']) && $my_permission_list[\'' . $module_name . '\'][\'can_view\'] == \'1\')) { ?>
<li class="nav-item <?= $current_module == \'' . $module_name . '\' ? \'menu-open\' : \'\' ?>">
    <a href="#" class="nav-link">
        <i class="nav-icon bi bi-people"></i>
        <p>
            ' . $module_display_name . '
            <i class="nav-arrow bi bi-chevron-right"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">
        <li class="nav-item">
            <a href="<?= base_url(BACKEND_PORTAL . \'/' . $module_name . '/list\') ?>" class="nav-link <?= $current_module == \'' . $module_name . '\' && $current_page == \'list\' ? \'active\' : \'\' ?>">
                <i class="nav-icon bi bi-list"></i>
                <p>List</p>
            </a>
        </li>
        <?php if ($this->data[\'site_config\'][\'backend_check_permission\']!=\'1\' || $this->data[\'my_user_data\'][\'level\'] == \'-1\' || (isset($my_permission_list[\'' . $module_name . '\']) && $my_permission_list[\'' . $module_name . '\'][\'can_add\'] == \'1\')) { ?>
        <li class="nav-item">
            <a href="<?= base_url(BACKEND_PORTAL . \'/' . $module_name . '/add\') ?>" class="nav-link <?= $current_module == \'' . $module_name . '\' && ($current_page == \'add\' || $current_page == \'edit\') ? \'active\' : \'\' ?>">
                <i class="nav-icon bi bi-plus"></i>
                <p>Add</p>
            </a>
        </li>
        <?php } ?>
    </ul>
</li>
<?php } ?>' . "\n";

        echo $content;
    }
}
