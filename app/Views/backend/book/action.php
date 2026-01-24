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
                                    <div class="col-md-12 mb-3">
                                        <div class="form-group">
                                            <label for="title">Title</label>
                                            <input type="text" class="form-control" id="title" placeholder="Title" ng-model="form_data.title" required>
                                        </div>
                                    </div>
                                    <div class="col-md-9 mb-3">
                                        <div class="form-group">
                                            <label for="description">Description</label>
                                            <textarea class="form-control" id="description" placeholder="Description" ng-model="form_data.description" rows="5"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <div class="form-group">
                                            <label for="photo">Photo (300x300PX)</label>
                                            <div>
                                                <div class="mb-1" ng-if="form_data.photo">
                                                    <a href="{{form_data.photo}}" target="_blank"><img ng-src="{{form_data.photo}}" id="photo_photo" class="img-fluid" /></a>
                                                </div>
                                                <input type="file" class="form-control" id="photo" ng-files="getTheFiles($files,'photo')" ng-disabled="form_data.is_uploading_photo==1" />
                                                <input type="text" style="display:none" ng-model="form_data.photo" />

                                                <div class="mt-1">
                                                    <button type="button" id="upload_btn_photo" class="btn btn-secondary btn-sm" ng-click="uploadFiles('300','300','photo')" ng-disabled="form_data.is_uploading_photo==1"><i class="fa fa-upload"></i> Upload <i class="fa fa-spinner fa-spin" ng-if="form_data.is_uploading_photo==1"></i></button>
                                                    <button type="button" class="btn btn-danger btn-sm" ng-click="form_data.photo=''" ng-if="form_data.photo"><i class="fa fa-trash"></i> Remove</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <div class="form-group">
                                            <label for="price">Price</label>
                                            <input type="number" step=".01" class="form-control" id="price" ng-model="form_data.price" required>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <div class="form-group">
                                            <label for="total_page">Total Page</label>
                                            <input type="number" step="1" class="form-control" id="total_page" ng-model="form_data.total_page" required>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <div class="form-group">
                                            <label for="bought_date">Bought Date</label>
                                            <input type="date" class="form-control" id="bought_date" ng-model="form_data.bought_date">
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <div class="form-group">
                                            <label for="completed_date">Completed Date</label>
                                            <input type="date" class="form-control" id="completed_date" ng-model="form_data.completed_date" required>
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


        $scope.id = <?= (isset($id) && !empty($id)) ? $id : 0 ?>;

        if ($scope.id && $scope.id > 0) {
            $scope.form_data = <?= isset($result_data) ? json_encode($result_data) : "[]" ?>;
            $scope.form_data.my_user_id = $scope.my_user_id;
            $scope.form_data.my_login_token = $scope.my_login_token;

            if ($scope.form_data.price) {
                $scope.form_data.price = parseFloat($scope.form_data.price);
            }
            if ($scope.form_data.total_page) {
                $scope.form_data.total_page = parseInt($scope.form_data.total_page);
            }
            if ($scope.form_data.bought_date) {
                $scope.form_data.bought_date = new Date($scope.form_data.bought_date);
            }
            if ($scope.form_data.completed_date) {
                $scope.form_data.completed_date = new Date($scope.form_data.completed_date);
            }


        } else {
            $scope.form_data = {
                "my_user_id": $scope.my_user_id,
                "my_login_token": $scope.my_login_token,

                "price": 0.00,
                "total_page": 0,

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


            if ($scope.form_data.bought_date) {
                $scope.form_data.bought_date_temp = $("#bought_date").val();
            }
            if ($scope.form_data.completed_date) {
                $scope.form_data.completed_date_temp = $("#completed_date").val();
            }


            var to_be_submit = angular.copy($scope.form_data);

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

        //image upload 
        var formdata = new FormData();
        $scope.getTheFiles = function($files, field_id) {
            formdata.append(field_id, $files[0]);
            $('#upload_btn_' + field_id).click();
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
                url: "<?= base_url(BACKEND_API . '/general/upload_image_now') ?>",
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
            restrict: 'A', // only activate on element attribute
            require: 'ngModel',
            link: function(scope, element, attr, ngModel, ngModelCtrl) {
                if (!ngModel) return; // do nothing if no ng-model you might want to remove this

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