<main class="app-main" ng-app="myApp" ng-controller="myCtrl" ng-cloak>
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
                        <li class="breadcrumb-item"><a href="<?= base_url(BACKEND_PORTAL . '/dashboard') ?>">Dashboard</a></li>
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
                                <div class="col-md-4 mb-2">
                                    <div class="form-group">
                                        <label for="template_name">Template Name</label>
                                        <input type="text" class="form-control form-control-sm" id="template_name" name="template_name" placeholder="Template Name" ng-model="filter_data.template_name">
                                    </div>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <div class="form-group">
                                        <label for="subject">Subject</label>
                                        <input type="text" class="form-control form-control-sm" id="subject" name="subject" placeholder="Subject" ng-model="filter_data.subject">
                                    </div>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <div class="form-group">
                                        <label for="description">Description</label>
                                        <input type="text" class="form-control form-control-sm" id="description" name="description" placeholder="Description" ng-model="filter_data.description">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <button type="button" class="btn btn-secondary btn-sm" ng-click="reset_filter()"><i class="fa fa-refresh"></i> Reset Filter</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="row justify-content-between align-items-center">
                                <div class="col-md-6 mb-3">
                                    <h5 class="mb-0"><i class="fa fa-database"></i> Total Result : <span class="text-primary">{{config_data.total_record}}</span> Record</h5>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-floating">
                                        <select class="form-select" id="item_per_page" ng-model="config_data.item_per_page" ng-change="load_result_list_now()">
                                            <option value="10">10</option>
                                            <option value="20">20</option>
                                            <option value="50">50</option>
                                            <option value="100">100</option>
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
                                        <td title="'Action'">
                                            <div class="mb-1">
                                                <button type="button" class="btn btn-primary btn-sm list-act-btn" ng-click="edit_record(item)"><i class="fa fa-edit"></i></button>
                                            </div>
                                            <div class="mb-1">
                                                <button type="button" class="btn btn-danger btn-sm list-act-btn" ng-click="delete_record(item)"><i class="fa fa-trash"></i></button>
                                            </div>
                                        </td>
                                        <td title="'Created Date'"><span ng-bind-html="item.created_on"></span></td>
                                        <td title="'ID'">{{item.id}}</td>
                                        <td title="'Template Name'">{{item.template_name}}</td>
                                        <td title="'Subject'">{{item.subject}}</td>
                                        <td title="'Description'">{{item.description}}</td>
                                    </tr>
                                    <tr ng-if="config_data.total_record == 0">
                                        <td colspan="6" class="text-center">No data found</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <!-- <div class="card-footer">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-floating">
                                        <select class="form-select" id="item_per_page" aria-label="Floating label select example" ng-model="config_data.item_per_page" ng-change="load_result_list_now()">
                                            <option value="10">10</option>
                                            <option value="20">20</option>
                                            <option value="50">50</option>
                                            <option value="100">100</option>
                                        </select>
                                        <label for="item_per_page">Item Per Page</label>
                                    </div>
                                </div>
                            </div>
                        </div> -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    var app = angular.module("myApp", ["ngTable", "ngResource", "ui.bootstrap", "ngSanitize"]);
    app.controller("myCtrl", function($scope, $http, $timeout, NgTableParams, $resource) {

        $scope.my_user_id = '<?= $my_user_id ?? 0 ?>';
        $scope.my_login_token = '<?= $my_login_token ?? '' ?>';

        $scope.config_data = {
            'total_record': 0,
            'item_per_page': '<?= $site_config["item_per_page"] ?? BACKEND_ITEM_PER_PAGE ?>'
        };

        $scope.filter_data = {
            "id": "<?= $_GET['id'] ?? '' ?>"
        };

        var Api = $resource("<?= base_url(BACKEND_API . "/" . $current_module . "/list") ?>/" + $scope.my_user_id + "/" + $scope.my_login_token);
        $scope.load_result_list_now = function() {
            var tableConfig = {
                page: 1,
                count: $scope.config_data.item_per_page,
                filter: $scope.filter_data
            };

            //如果有Last Page Memory的话，则恢复Last Page Memory
            let last_page_memory = localStorage.getItem('last_page_memory_<?= $current_module ?>');
            if (last_page_memory) {
                tableConfig = JSON.parse(last_page_memory);
                localStorage.removeItem('last_page_memory_<?= $current_module ?>');
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
                        localStorage.setItem('last_page_memory_<?= $current_module ?>', table_parms);
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
            localStorage.setItem('last_page_memory_<?= $current_module ?>', table_parms);
            location.href = "<?= base_url(BACKEND_PORTAL . "/" . $current_module . "/edit/") ?>" + item.id;
        }

        $scope.reset_filter = function() {
            $scope.filter_data = {};
            $scope.load_result_list_now();
        }
    });
</script>