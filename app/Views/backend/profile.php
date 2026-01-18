<main class="app-main" ng-app="myApp" ng-controller="myCtrl" ng-cloak>
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0"><?= $current_page_name ?></h3>
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
            <form name="result_form" was-validate>
                <div class="row">

                    <!-- General Information -->
                    <div class="col-lg-12">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fa fa-info-circle"></i> <b>General Information</b></h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <div class="form-group">
                                            <label for="name">Name</label>
                                            <input type="text" class="form-control" id="name" name="name" placeholder="Name" ng-model="form_data.name" required />
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <div class="form-group">
                                            <label for="email">Email (Login Username)</label>
                                            <input type="text" class="form-control" id="email" name="email" placeholder="Email (Login Username)" ng-model="form_data.email" readonly />
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-3">
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
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Change Password -->
                    <div class="col-lg-12">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h3 class="card-title"> <i class="fa fa-lock"></i> <b>Change Password</b></h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <div class="form-group">
                                            <label for="old_password">Old Password</label>
                                            <div class="input-group mb-1">
                                                <input type="{{ form_data.show_old_password ? 'text' : 'password' }}" class="form-control" id="old_password" name="old_password" placeholder="Old Password" ng-model="form_data.old_password" />
                                                <button class="btn btn-primary" type="button" ng-click="form_data.show_old_password = !form_data.show_old_password">
                                                    <span class="bi bi-eye" ng-if="!form_data.show_old_password"></span>
                                                    <span class="bi bi-eye-slash" ng-if="form_data.show_old_password"></span>
                                                </button>
                                            </div>
                                            <div class="text-muted small"><i class="fa fa-info-circle"></i> Leave it blank if you don't want to change the password</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <label for="new_password">New Password</label>
                                            <div class="input-group">
                                                <input type="{{ form_data.show_password ? 'text' : 'password' }}" class="form-control" id="new_password" name="new_password" placeholder="New Password" ng-model="form_data.new_password" ng-required="form_data.old_password != ''" />
                                                <button class="btn btn-primary" type="button" ng-click="form_data.show_password = !form_data.show_password">
                                                    <span class="bi bi-eye" ng-if="!form_data.show_password"></span>
                                                    <span class="bi bi-eye-slash" ng-if="form_data.show_password"></span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <label for="confirm_password">Confirm Password</label>
                                            <div class="input-group">
                                                <input type="{{ form_data.show_confirm_password ? 'text' : 'password' }}" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm Password" ng-model="form_data.confirm_password" ng-required="form_data.old_password != ''" />
                                                <button class="btn btn-primary" type="button" ng-click="form_data.show_confirm_password = !form_data.show_confirm_password">
                                                    <span class="bi bi-eye" ng-if="!form_data.show_confirm_password"></span>
                                                    <span class="bi bi-eye-slash" ng-if="form_data.show_confirm_password"></span>
                                                </button>
                                            </div>
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
                        <button type="button" class="btn btn-primary" ng-click="submit_now()" ng-disabled="result_form.$invalid || result_form.$pristine || form_data.is_submitting">
                            <i class="fa fa-save"></i> Submit Changes <i class="fa fa-spinner fa-spin" ng-show="form_data.is_submitting"></i>
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

        $scope.form_data = {
            'my_user_id': $scope.my_user_id,
            'my_login_token': $scope.my_login_token,

            'is_submitting': 0,
            'error_msg': '',
            'success_msg': '',

            //General Information
            'name': "<?= $my_user_data['name'] ?? '' ?>",
            'email': "<?= $my_user_data['email'] ?? '' ?>",
            'dial_code': "<?= $my_user_data['dial_code'] ?? '' ?>",
            'mobile': "<?= $my_user_data['mobile'] ?? '' ?>",

            //Change Password
            'old_password': '',
            'new_password': '',
            'confirm_password': '',
            'show_old_password': false,
            'show_password': false,
            'show_confirm_password': false,

        };

        $scope.submit_now = function() {
            $scope.form_data.error_msg = "";
            let to_be_submit = $scope.form_data;
            $scope.form_data.is_submitting = 1;
            $http.post("<?= base_url(BACKEND_API . '/general/update_profile') ?>", to_be_submit).then(function(response) {
                if (response.data.status == "SUCCESS") {
                    alert('Successfully updated');
                    location.reload();
                } else {
                    $scope.form_data.error_msg = response.data.result;
                }
            }, function(response) {
                $scope.form_data.error_msg = response.data.messages.result;
            }).finally(function() {
                $scope.form_data.is_submitting = 0;
            });

        }
    });
</script>