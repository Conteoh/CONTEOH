<!-- /.login-logo -->
<div class="card" ng-app="myApp" ng-controller="myCtrl" ng-cloak>
    <div class="card-body login-card-body">
        <p class="login-box-msg">Keyin your new password. New password length at least 5 characters.</p>

        <form name="result_form">
            <div class="input-group mb-3">
                <input type="{{ form_data.show_password ? 'text' : 'password' }}" class="form-control" placeholder="New Password" id="new_password" name="new_password" ng-model="form_data.new_password" minlength="5" required />
                <button class="btn btn-primary" type="button" ng-click="form_data.show_password = !form_data.show_password">
                    <span class="bi bi-eye" ng-if="!form_data.show_password"></span>
                    <span class="bi bi-eye-slash" ng-if="form_data.show_password"></span>
                </button>
            </div>
            <div class="mb-3">
                <input type="{{ form_data.show_password ? 'text' : 'password' }}" class="form-control" placeholder="Re-enter New Password" id="confirm_password" name="confirm_password" ng-model="form_data.confirm_password" minlength="5" required />
            </div>

            <div class="input-group mb-3">
                <button type="button" class="btn btn-primary w-100" ng-disabled="result_form.$invalid || form_data.is_submitting" ng-click="submit_now()">
                    Reset Password <i class="fa fa-spinner fa-spin" ng-show="form_data.is_submitting"></i>
                </button>
            </div>

            <!--end::Row-->
            <div class="alert alert-danger" ng-if="form_data.error_msg">
                <i class="fa fa-exclamation-triangle"></i> {{ form_data.error_msg }}
            </div>
            <div class="alert alert-success" ng-if="form_data.success_msg">
                <i class="fa fa-check-circle"></i> {{ form_data.success_msg }}
            </div>
        </form>
    </div>
    <!-- /.login-card-body -->
</div>

<script>
    var app = angular.module("myApp", []);
    app.controller("myCtrl", function($scope, $http, $timeout) {

        $scope.form_data = {
            'is_submitting': 0,
            'error_msg': '',
            'success_msg': '',
            'show_password': false,
            'verification_token': "<?= $verification_token ?? '' ?>",
        };

        $scope.submit_now = function() {

            $scope.form_data.error_msg = "";

            if ($scope.result_form.$invalid) {
                return;
            }

            $scope.form_data.is_submitting = 1;

            event.preventDefault();
            grecaptcha.ready(function() {
                grecaptcha.execute('<?= $site_config['recaptcha_site_key'] ?>', {
                    action: 'submit'
                }).then(function(token) {

                    var to_be_submit = $scope.form_data;
                    to_be_submit['recaptcha'] = token;

                    $http.post("<?= base_url(BACKEND_API . '/general/reset_password') ?>", to_be_submit).then(function(response) {
                        $scope.form_data.is_submitting = 0;

                        if (response.data.status == "SUCCESS") {
                            let success_msg = "Successfully reset password, you may login with your new password now";
                            alert(success_msg);
                            location.href = '<?= base_url(BACKEND_PORTAL . '/login') ?>';
                        } else {
                            alert(response.data.result);
                            $scope.form_data.error_msg = response.data.result;
                        }

                    }, function(response) {
                        $scope.form_data.is_submitting = 0;
                        alert(response.data.messages.result);
                        $scope.form_data.error_msg = response.data.messages.result;
                    });
                });
            });

        }

        $(document).on('keypress', function(e) {
            if (e.which == 13) {
                $scope.submit_now();
            }
        });
    });
</script>