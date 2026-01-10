<!-- /.login-logo -->
<div class="card" ng-app="myApp" ng-controller="myCtrl" ng-cloak>
    <div class="card-body login-card-body">
        <p class="login-box-msg">Sign in to start your session</p>

        <form name="result_form">
            <div class="input-group mb-3">
                <input type="email" class="form-control" placeholder="Email" id="email" name="email" ng-model="form_data.email" required />
                <div class="input-group-text">
                    <span class="bi bi-envelope"></span>
                </div>
            </div>
            <div class="input-group mb-3">
                <input type="password" class="form-control" placeholder="Password" id="password" name="password" ng-model="form_data.password" required />
                <div class="input-group-text">
                    <span class="bi bi-lock-fill"></span>
                </div>
            </div>
            <div class="input-group mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember_me" id="remember_me" ng-model="form_data.remember_me" />
                    <label class="form-check-label" for="remember_me"> Remember Me </label>
                </div>
            </div>
            <div class="input-group mb-3">
                <button type="button" class="btn btn-primary w-100" ng-disabled="result_form.$invalid || form_data.is_submitting" ng-click="submit_now()">
                    Sign In <i class="fa fa-spinner fa-spin" ng-show="form_data.is_submitting"></i>
                </button>
            </div>
            <!--end::Row-->
            <div class="alert alert-danger" ng-if="form_data.error_msg">
                <i class="fa fa-exclamation-triangle"></i> {{ form_data.error_msg }}
            </div>
        </form>

        <div class="text-center">
            <a href="<?= base_url(BACKEND_PORTAL . '/forget_password') ?>">I forgot my password</a>
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

                    $http.post("<?= base_url(BACKEND_API . '/general/login_submit') ?>", to_be_submit).then(function(response) {
                        console.log('owo response', response);
                        $scope.form_data.is_submitting = 0;

                        if (response.data.status == "SUCCESS") {

                            if ($scope.form_data.remember_me) {
                                localStorage.setItem('backend_login_email', $scope.form_data.email);
                            }

                            location.href = '<?= base_url(BACKEND_PORTAL . '/dashboard') ?>';
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