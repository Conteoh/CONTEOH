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
                            <div class="card-header bg-secondary text-white">
                                <h3 class="card-title"><i class="bi bi-info-circle"></i> <b>General Information</b></h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12 mb-3" ng-repeat="item in result_list">

                                        <!-- text-->
                                        <div class="form-group" ng-if="item.input_type=='0'">
                                            <label for="{{item.variable}}" class="floating-label mb-1">{{item.variable_title}}</label>
                                            <input type="text" class="form-control form-control-sm" id="{{item.variable}}" name="{{item.variable}}" placeholder="{{(item.description)?item.description:item.variable_title}}" ng-model="item.value" ng-required="item.is_compulsory" ng-disabled="item.is_readonly=='1'">
                                        </div>

                                        <!--Textrea-->
                                        <div class="form-group" ng-if="item.input_type=='1'">
                                            <label for="{{item.variable}}" class="floating-label mb-1">{{item.variable_title}}</label>
                                            <textarea class="form-control form-control-sm" id="{{item.variable}}" name="{{item.variable}}" placeholder="{{(item.description)?item.description:item.variable_title}}" ng-model="item.value" ng-disabled="item.is_readonly=='1'" rows="5"></textarea>
                                        </div>

                                        <!--CKEditor-->
                                        <div class="form-group" ng-if="item.input_type=='2'">
                                            <label for="{{item.variable}}" class="floating-label mb-1">{{item.variable_title}}</label>
                                            <textarea class="form-control form-control-sm" id="{{item.variable}}" name="{{item.variable}}" placeholder="{{(item.description)?item.description:item.variable_title}}" ng-model="item.value" ck-editor ng-required="item.compulsory==1" ng-disabled="item.readonly==1"></textarea>
                                        </div>

                                        <!--Radio-->
                                        <div class="form-group" ng-if="item.input_type=='3'">
                                            <label for="{{item.variable}}" class="floating-label mb-1">{{item.variable_title}}</label>
                                            <div class="row py-2">
                                                <div class="col-auto" ng-repeat="(k,v) in item.input_option">
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" class="custom-control-input" id="{{item.variable}}{{k}}" name="{{item.variable}}" ng-model="item.value" value="{{k}}" required>
                                                        <label class="custom-control-label" for="{{item.variable}}{{k}}">{{v}}</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!--Select-->
                                        <div class="form-group" ng-if="item.input_type=='4'">
                                            <label for="{{item.variable}}" class="floating-label mb-1">{{item.variable_title}}</label>
                                            <select class="form-control form-control-sm" id="{{item.variable}}" ng-model="item.value" ng-required="item.is_compulsory" ng-disabled="item.is_readonly=='1'">
                                                <option value="{{k}}" ng-repeat="(k,v) in item.input_option">{{v}}</option>
                                            </select>
                                        </div>

                                        <!--Date-->
                                        <div class="form-group" ng-if="item.input_type=='5'">
                                            <label for="{{item.variable}}" class="floating-label mb-1">{{item.variable_title}}</label>
                                            <input type="date" class="form-control form-control-sm" id="{{item.variable}}" name="{{item.variable}}" ng-model="item.value" ng-required="item.is_compulsory" ng-disabled="item.is_readonly=='1'">
                                        </div>

                                        <!-- Number -->
                                        <div class="form-group" ng-if="item.input_type=='6'">
                                            <label for="{{item.variable}}" class="floating-label mb-1">{{item.variable_title}}</label>
                                            <input type="number" class="form-control form-control-sm" id="{{item.variable}}" name="{{item.variable}}" placeholder="{{(item.description)?item.description:item.variable_title}}" ng-model="item.value" ng-required="item.is_compulsory" ng-disabled="item.is_readonly=='1'">
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
        };

        $scope.result_list = <?= isset($setting_list) ? json_encode($setting_list) : '[]' ?>;
        $scope.result_list.forEach(function(v, k) {
            if (v.input_type == '5' && v.value) {
                $scope.result_list[k]['value'] = new Date(v.value);
            }
            if (v.input_type == '6' && v.value) {
                $scope.result_list[k]['value'] = parseFloat(v.value);
            }
        });

        $scope.submit_now = function() {
            $scope.form_data.error_msg = "";

            $scope.result_list.forEach(function(v, k) {
                if (v.input_type == '5' && v.value) {
                    $scope.result_list[k]['value_temp'] = v.value.getTime() / 1000;
                }
            });

            let to_be_submit = $scope.form_data;
            to_be_submit.result_list = $scope.result_list;

            $scope.form_data.is_submitting = 1;
            $http.post("<?= base_url(BACKEND_API . '/setting/batch_update') ?>", to_be_submit).then(function(response) {
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
    }).directive("ckEditor", function($timeout) {
        return {
            restrict: 'A', // only activate on element attribute
            require: 'ngModel',
            link: function(scope, element, attr, ngModel, ngModelCtrl) {
                if (!ngModel) return; // do nothing if no ng-model you might want to remove this

                //for(name in CKEDITOR.instances)
                //CKEDITOR.instances[name].destroy();
                //CKEDITOR.replace(element[0],{allowedContent: true,height: 300});

                var ck = CKEDITOR.replace(element[0], {
                    allowedContent: true,
                    height: 200,
                    toolbar: [{
                            name: 'document',
                            items: ['Source', '-', 'Save', 'NewPage', 'Preview', 'Print', '-', 'Templates']
                        },
                        {
                            name: 'clipboard',
                            items: ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo']
                        },
                        {
                            name: 'editing',
                            items: ['Find', 'Replace', '-', 'SelectAll', '-', 'Scayt']
                        },
                        {
                            name: 'forms',
                            items: ['Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField']
                        },
                        '/',
                        {
                            name: 'basicstyles',
                            items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'CopyFormatting', 'RemoveFormat']
                        },
                        {
                            name: 'paragraph',
                            items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl', 'Language']
                        },
                        {
                            name: 'links',
                            items: ['Link', 'Unlink', 'Anchor']
                        },
                        {
                            name: 'insert',
                            items: ['Image', 'Flash', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak', 'Iframe']
                        },
                        '/',
                        {
                            name: 'styles',
                            items: ['Styles', 'Format', 'Font', 'FontSize']
                        },
                        {
                            name: 'colors',
                            items: ['TextColor', 'BGColor']
                        },
                        {
                            name: 'tools',
                            items: ['Maximize', 'ShowBlocks']
                        },
                        {
                            name: 'about',
                            items: ['About']
                        }
                    ]
                });

                function updateModel() {
                    scope.$apply(function() {
                        ngModel.$setViewValue(ck.getData());
                    });
                }

                ck.on('instanceReady', function() {
                    $timeout(function() {
                        ck.setData(ngModel.$viewValue);
                    }, 350);
                });

                ck.on('pasteState', updateModel);
                ck.on('change', updateModel);
                ck.on('key', updateModel);

                ngModel.$render = function(value) {
                    $timeout(function() {
                        ck.setData(ngModel.$viewValue);
                    }, 350);
                };

            }
        }
    });
</script>