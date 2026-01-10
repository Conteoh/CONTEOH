<!-- /.login-logo -->
<div class="card" ng-app="myApp" ng-controller="myCtrl" ng-cloak>
    <div class="card-body login-card-body">
        <p class="login-box-msg">Keyin your email address to receive a password reset link</p>

        <form name="result_form">
            <div class="input-group mb-3">
                <input type="email" class="form-control" placeholder="Email" id="email" name="email" ng-model="form_data.email" required />
                <div class="input-group-text">
                    <span class="bi bi-envelope"></span>
                </div>
            </div>
            <div class="input-group mb-3">
                <button type="button" class="btn btn-primary w-100" ng-disabled="result_form.$invalid || form_data.is_submitting" ng-click="submit_now()">
                    Send Reset Link <i class="fa fa-spinner fa-spin" ng-show="form_data.is_submitting"></i>
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

        <div class="text-center">
            <a href="<?= base_url(BACKEND_PORTAL . '/login') ?>">Back to login</a>
        </div>
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
        };

        if (localStorage.getItem('backend_login_email')) {
            $scope.form_data.email = localStorage.getItem('backend_login_email');
        }

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

                    $http.post("<?= base_url(BACKEND_API . '/general/sent_reset_password_link') ?>", to_be_submit).then(function(response) {
                        console.log('owo response', response);
                        $scope.form_data.is_submitting = 0;

                        if (response.data.status == "SUCCESS") {
                            let user_email = response.data.result.email;

                            $scope.form_data.success_msg = "Successfully sent to " + user_email + ", please check the your email for reset password link";
                            alert($scope.form_data.success_msg);

                            $scope.form_data.email = "";
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