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
                        <li class="breadcrumb-item">
                            <a href="<?= base_url(BACKEND_PORTAL . '/' . $current_module . '/list') ?>"><?= $current_module_name ?></a>
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
                                    <div class="col-md-4 mb-3">
                                        <div class="form-group">
                                            <label for="name">Name</label>
                                            <input type="text" class="form-control" id="name" name="name" placeholder="Name" ng-model="form_data.name" required />
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="form-group">
                                            <label for="level">Level</label>
                                            <select class="form-control" id="level" name="level" ng-model="form_data.level" required>
                                                <option value="{{x.id}}" ng-repeat="x in user_level_kv_info">{{x.title}}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="form-group">
                                            <label for="status">Status</label>
                                            <select class="form-control" id="status" name="status" ng-model="form_data.status" required>
                                                <option value="{{x.id}}" ng-repeat="x in status_kv_info">{{x.title}}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="form-group">
                                            <label for="email">Email</label>
                                            <input type="email" class="form-control" id="email" name="email" placeholder="Email" ng-model="form_data.email" required />
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="form-group">
                                            <label for="is_email_verified">Email Verified</label>
                                            <select class="form-control" id="is_email_verified" name="is_email_verified" ng-model="form_data.is_email_verified" required>
                                                <option value="{{x.id}}" ng-repeat="x in yes_no_kv_info">{{x.title}}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="form-group">
                                            <label for="mobile">Mobile</label>
                                            <div class="input-group">
                                                <select name="dial_code" id="dial_code" class="form-control" ng-model="form_data.dial_code" style="width: 20%" required>
                                                    <option value="+60">+60</option>
                                                    <option value="+65">+65</option>
                                                </select>
                                                <input type="text" class="form-control" id="mobile" name="mobile" placeholder="Mobile" ng-model="form_data.mobile" minlength="8" maxlength="10" required style="width: 80%" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <div class="form-group">
                                            <label for="new_password">New Password</label>
                                            <input type="password" class="form-control" id="new_password" name="new_password" placeholder="New Password" ng-model="form_data.new_password" ng-required="id == 0" />
                                            <small class="form-text text-muted" ng-if="id > 0">Leave it blank if not changing password</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Permission Configuration -->
                    <div class="col-lg-12" ng-if="form_data.level != -1">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fa fa-info-circle"></i> <b>Permission Configuration</b></h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12 mb-4">
                                        <div class="form-group">
                                            <label for="role">Role</label>
                                            <select class="form-control" id="role_id" name="role_id" ng-model="form_data.role_id" ng-change="load_permission()" required>
                                                <option value="0">None</option>
                                                <option value="{{x.id}}" ng-repeat="x in role_kv_info">{{x.title}}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered long-table text-center">
                                                <tr>
                                                    <th>
                                                        <div>#</div>
                                                        <div>
                                                            <input type="checkbox" class="form-check-input" ng-change="select_column_all('all')" ng-model="config_data.all" ng-true-value="1" ng-false-value="0">
                                                        </div>
                                                    </th>
                                                    <th>ID</th>
                                                    <th>Name</th>
                                                    <th>
                                                        Can View
                                                        <div>
                                                            <input type="checkbox" class="form-check-input" ng-change="select_column_all('view')" ng-model="config_data.view" ng-true-value="1" ng-false-value="0">
                                                        </div>
                                                    </th>
                                                    <th>
                                                        Can Add
                                                        <div>
                                                            <input type="checkbox" class="form-check-input" ng-change="select_column_all('add')" ng-model="config_data.add" ng-true-value="1" ng-false-value="0">
                                                        </div>
                                                    </th>
                                                    <th>
                                                        Can Edit
                                                        <div>
                                                            <input type="checkbox" class="form-check-input" ng-change="select_column_all('edit')" ng-model="config_data.edit" ng-true-value="1" ng-false-value="0">
                                                        </div>
                                                    </th>
                                                    <th>
                                                        Can Delete
                                                        <div>
                                                            <input type="checkbox" class="form-check-input" ng-change="select_column_all('delete')" ng-model="config_data.delete" ng-true-value="1" ng-false-value="0">
                                                        </div>
                                                    </th>
                                                </tr>
                                                <tr ng-repeat="x in user_permission_list">
                                                    <td>
                                                        {{$index+1}}
                                                        <input type="checkbox" ng-true-value="1" ng-false-value="0" ng-model="x.select_all" ng-change="select_row_all($index)" style="position:relative;top:1px;margin-left:5px;" class="form-check-input">
                                                    </td>
                                                    <td>{{x.system_module_id}}</td>
                                                    <td class="text-left">{{x.description}}</td>
                                                    <td>
                                                        <input type="checkbox" class="form-check-input" ng-true-value="1" ng-false-value="0" ng-model="x.can_view">
                                                    </td>
                                                    <td>
                                                        <input type="checkbox" class="form-check-input" ng-true-value="1" ng-false-value="0" ng-model="x.can_add">
                                                    </td>
                                                    <td>
                                                        <input type="checkbox" class="form-check-input" ng-true-value="1" ng-false-value="0" ng-model="x.can_edit">
                                                    </td>
                                                    <td>
                                                        <input type="checkbox" class="form-check-input" ng-true-value="1" ng-false-value="0" ng-model="x.can_delete">
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
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

        $scope.my_user_id = '<?= $my_user_id ?? 0 ?>';
        $scope.my_login_token = '<?= $my_login_token ?? '' ?>';

        //Load Kv List
        $scope.status_kv_info = <?= isset($status_kv_info) ? json_encode($status_kv_info) : "[]" ?>;
        $scope.user_level_kv_info = <?= isset($user_level_kv_info) ? json_encode($user_level_kv_info) : "[]" ?>;
        $scope.yes_no_kv_info = <?= isset($yes_no_kv_info) ? json_encode($yes_no_kv_info) : "[]" ?>;
        $scope.role_kv_info = <?= isset($role_kv_info) ? json_encode($role_kv_info) : "[]" ?>;

        $scope.id = <?= (isset($id) && !empty($id)) ? $id : 0 ?>;
        $scope.user_permission_list = <?= isset($user_permission_list) ? json_encode($user_permission_list) : "[]" ?>;

        $scope.config_data = {
            'all': 0,
            'view': 0,
            'add': 0,
            'edit': 0,
            'delete': 0,
        };

        if ($scope.id && $scope.id > 0) {
            $scope.form_data = <?= isset($result_data) ? json_encode($result_data) : "[]" ?>;
            $scope.form_data.my_user_id = $scope.my_user_id;
            $scope.form_data.my_login_token = $scope.my_login_token;
            // Remove password from form data for edit mode
            delete $scope.form_data.password;
        } else {
            $scope.form_data = {
                "my_user_id": $scope.my_user_id,
                "my_login_token": $scope.my_login_token,

                "status": '1',
                'level': '1',
                'is_email_verified': '0',
                'dial_code': '+60',
                'mobile': '',
                'role_id': '0',
            };
        }

        $scope.back_now = function() {
            if ($scope.result_form.$dirty) {
                var ans = confirm("Leave this page without saving");
                if (!ans) {
                    return false;
                }
            }
            let url = "<?= base_url(BACKEND_PORTAL . '/' . $current_module . '/list') ?>";
            location.href = url;
        }

        $scope.submit_now = function() {

            $scope.form_data.error_msg = "";

            let ans = confirm("<?= BACKEND_SUBMIT_ACTION_REMINDER ?>");
            if (!ans) {
                return false;
            }

            var to_be_submit = angular.copy($scope.form_data);
            to_be_submit.user_permission_list = $scope.user_permission_list;

            // Only include password if it's provided (for edit) or required (for new)
            if ($scope.id > 0 && !to_be_submit.password) {
                delete to_be_submit.password;
            }

            $scope.form_data.is_submitting = 1;

            $http.post("<?= base_url(BACKEND_API . '/' . $current_module . '/submit') ?>", to_be_submit).then(function(response) {

                $scope.form_data.is_submitting = 0;

                if (response.data.status == "SUCCESS") {
                    alert("<?= BACKEND_SUBMIT_SUCCESS_MSG ?>");
                    location.href = "<?= base_url(BACKEND_PORTAL . "/" . $current_module . '/list') ?>";
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

        $scope.select_column_all = function(type) {
            $scope.user_permission_list.forEach(function(v, k) {
                switch (type) {
                    case "view":
                        $scope.user_permission_list[k]['can_view'] = $scope.config_data.view;
                        break;
                    case "add":
                        $scope.user_permission_list[k]['can_add'] = $scope.config_data.add;
                        break;
                    case "edit":
                        $scope.user_permission_list[k]['can_edit'] = $scope.config_data.edit;
                        break;
                    case "delete":
                        $scope.user_permission_list[k]['can_delete'] = $scope.config_data.delete;
                        break;
                    case 'all':
                        $scope.user_permission_list[k]['can_view'] = $scope.config_data.all;
                        $scope.user_permission_list[k]['can_add'] = $scope.config_data.all;
                        $scope.user_permission_list[k]['can_edit'] = $scope.config_data.all;
                        $scope.user_permission_list[k]['can_delete'] = $scope.config_data.all;
                        break;
                }
            });
        }

        $scope.select_row_all = function(index) {
            if ($scope.user_permission_list[index]['select_all']) {
                $scope.user_permission_list[index]['can_view'] = 1;
                $scope.user_permission_list[index]['can_add'] = 1;
                $scope.user_permission_list[index]['can_edit'] = 1;
                $scope.user_permission_list[index]['can_delete'] = 1;
            } else {
                $scope.user_permission_list[index]['can_view'] = 0;
                $scope.user_permission_list[index]['can_add'] = 0;
                $scope.user_permission_list[index]['can_edit'] = 0;
                $scope.user_permission_list[index]['can_delete'] = 0;
            }
        }

        $scope.load_permission = function() {

            $scope.config_data.all = 0;
            $scope.config_data.view = 0;
            $scope.config_data.add = 0;
            $scope.config_data.edit = 0;
            $scope.config_data.delete = 0;
            $scope.select_column_all('all');

            if ($scope.form_data.role_id && $scope.form_data.role_id != '0') {

                var to_be_submit = {
                    "my_user_id": $scope.my_user_id,
                    "my_login_token": $scope.my_login_token,
                    "role_id": $scope.form_data.role_id
                }

                $http.post("<?= base_url(BACKEND_API . '/role/load_role_permission') ?>", to_be_submit).then(function(response) {

                    if (response.data.status == "SUCCESS") {

                        if (response.data.result.permission_list) {
                            $scope.copy_from_role_permission(response.data.result.permission_list);
                        }

                    } else {
                        console.log(response.data.result);
                    }

                }, function(response) {
                    console.log(response.data.messages.result);
                });
            } else {
                $scope.reset_user_permission();
            }
        }

        $scope.copy_from_role_permission = function(role_permission_list) {
            $scope.user_permission_list.forEach(function(v, k) {
                role_permission_list.forEach(function(v2, k2) {
                    if (v.system_module_id == v2.system_module_id) {
                        $scope.user_permission_list[k]['can_view'] = parseInt(v2.can_view);
                        $scope.user_permission_list[k]['can_add'] = parseInt(v2.can_add);
                        $scope.user_permission_list[k]['can_edit'] = parseInt(v2.can_edit);
                        $scope.user_permission_list[k]['can_delete'] = parseInt(v2.can_delete);
                    }
                });
            });
        }
    });
</script>