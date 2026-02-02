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
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <label for="title">Title</label>
                                            <input type="text" class="form-control" id="title" placeholder="Title" ng-model="form_data.title" required>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <div class="form-group">
                                            <label for="priority">Priority</label>
                                            <input type="number" step="1" class="form-control" id="priority" ng-model="form_data.priority" required>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <div class="form-group">
                                            <label for="is_favourite">Is Favourite</label>
                                            <select class="form-control" id="is_favourite" ng-model="form_data.is_favourite" required>
                                                <option value="{{item.id}}" ng-repeat="item in yes_no_kv_info">{{item.title}}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <div class="form-group">
                                            <label for="description">Description</label>
                                            <textarea class="form-control" id="description" placeholder="Description" ng-model="form_data.description" rows="5"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="form-group">
                                            <label for="track_expenses">Track Expenses</label>
                                            <select class="form-control" id="track_expenses" ng-model="form_data.track_expenses" required>
                                                <option value="{{item.id}}" ng-repeat="item in yes_no_kv_info">{{item.title}}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="form-group">
                                            <label for="monthly_limit_amount">Monthly Limit Amount</label>
                                            <input type="number" step="0.01" min="0" class="form-control" id="monthly_limit_amount" ng-model="form_data.monthly_limit_amount" placeholder="0">
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="form-group">
                                            <label for="weekly_limit_amount">Weekly Limit Amount</label>
                                            <input type="number" step="0.01" min="0" class="form-control" id="weekly_limit_amount" ng-model="form_data.weekly_limit_amount" placeholder="0">
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-2"><label class="mb-0"><b>Daily Limit Amount</b></label></div>
                                    <div class="col-md-12 mb-3">
                                        <div class="row">
                                            <div class="col mb-2">
                                                <label for="daily_limit_amount_mon" class="small">Mon</label>
                                                <input type="number" step="0.01" min="0" class="form-control form-control-sm" id="daily_limit_amount_mon" ng-model="form_data.daily_limit_amount_mon" placeholder="0">
                                            </div>
                                            <div class="col mb-2">
                                                <label for="daily_limit_amount_tue" class="small">Tue</label>
                                                <input type="number" step="0.01" min="0" class="form-control form-control-sm" id="daily_limit_amount_tue" ng-model="form_data.daily_limit_amount_tue" placeholder="0">
                                            </div>
                                            <div class="col mb-2">
                                                <label for="daily_limit_amount_wed" class="small">Wed</label>
                                                <input type="number" step="0.01" min="0" class="form-control form-control-sm" id="daily_limit_amount_wed" ng-model="form_data.daily_limit_amount_wed" placeholder="0">
                                            </div>
                                            <div class="col mb-2">
                                                <label for="daily_limit_amount_thu" class="small">Thu</label>
                                                <input type="number" step="0.01" min="0" class="form-control form-control-sm" id="daily_limit_amount_thu" ng-model="form_data.daily_limit_amount_thu" placeholder="0">
                                            </div>
                                            <div class="col mb-2">
                                                <label for="daily_limit_amount_fri" class="small">Fri</label>
                                                <input type="number" step="0.01" min="0" class="form-control form-control-sm" id="daily_limit_amount_fri" ng-model="form_data.daily_limit_amount_fri" placeholder="0">
                                            </div>
                                            <div class="col mb-2">
                                                <label for="daily_limit_amount_sat" class="small">Sat</label>
                                                <input type="number" step="0.01" min="0" class="form-control form-control-sm" id="daily_limit_amount_sat" ng-model="form_data.daily_limit_amount_sat" placeholder="0">
                                            </div>
                                            <div class="col mb-2">
                                                <label for="daily_limit_amount_sun" class="small">Sun</label>
                                                <input type="number" step="0.01" min="0" class="form-control form-control-sm" id="daily_limit_amount_sun" ng-model="form_data.daily_limit_amount_sun" placeholder="0">
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fa fa-info-circle"></i> <b>Suggestion List</b></h3>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm text-center b-result-list-table">
                                        <tr>
                                            <th width="5%">Action</th>
                                            <th width="5%">ID</th>
                                            <th width="15%">Created Date</th>
                                            <th width="30%">Title</th>
                                            <th width="15%">Price</th>
                                            <th width="15%">Day</th>
                                            <th width="15%">Priority</th>
                                        </tr>
                                        <tr ng-if="!suggestion_list || suggestion_list.length == 0">
                                            <td colspan="100%" class="text-center">No data found</td>
                                        </tr>
                                        <tr ng-repeat="item in suggestion_list" ng-if="item.is_deleted == '0'">
                                            <td>
                                                <button type="button" class="btn btn-danger btn-sm list-act-btn" ng-click="remove_suggestion($index)"><i class="fa fa-trash"></i></button>
                                            </td>
                                            <td>{{item.id}}</td>
                                            <td>{{item.created_date}}</td>
                                            <td>
                                                <input type="text" class="form-control" ng-model="item.title" placeholder="Title" required>
                                            </td>
                                            <td>
                                                <input type="number" class="form-control" ng-model="item.price" placeholder="Price" min='0' required>
                                            </td>
                                            <td>
                                                <input type="number" class="form-control" ng-model="item.day" placeholder="Day" min='0' required>
                                            </td>
                                            <td>
                                                <input type="number" class="form-control" ng-model="item.priority" placeholder="Priority" min='0' required>
                                            </td>
                                        </tr>
                                    </table>
                                    <button type="button" class="btn btn-primary btn-sm" ng-click="add_suggestion()"><i class="fa fa-plus"></i> Add </button>
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
        $scope.yes_no_kv_info = <?= isset($yes_no_kv_info) ? json_encode($yes_no_kv_info) : "[]" ?>;


        $scope.id = <?= (isset($id) && !empty($id)) ? $id : 0 ?>;
        $scope.suggestion_list = [];

        if ($scope.id && $scope.id > 0) {
            $scope.form_data = <?= isset($result_data) ? json_encode($result_data) : "[]" ?>;
            $scope.form_data.my_user_id = $scope.my_user_id;
            $scope.form_data.my_login_token = $scope.my_login_token;

            if ($scope.form_data.priority) {
                $scope.form_data.priority = parseInt($scope.form_data.priority);
            }
            var limit_amount_fields = ['monthly_limit_amount', 'weekly_limit_amount', 'daily_limit_amount_mon', 'daily_limit_amount_tue', 'daily_limit_amount_wed', 'daily_limit_amount_thu', 'daily_limit_amount_fri', 'daily_limit_amount_sat', 'daily_limit_amount_sun'];
            limit_amount_fields.forEach(function(f) {
                if ($scope.form_data[f] !== undefined && $scope.form_data[f] !== null && $scope.form_data[f] !== '') {
                    $scope.form_data[f] = parseFloat($scope.form_data[f]);
                } else {
                    $scope.form_data[f] = 0;
                }
            });

            $scope.suggestion_list = <?= isset($suggestion_list) ? json_encode($suggestion_list) : "[]" ?>;
            if ($scope.suggestion_list && $scope.suggestion_list.length != 0) {
                $scope.suggestion_list.forEach(function(v, k) {
                    $scope.suggestion_list[k].price = parseFloat($scope.suggestion_list[k].price);
                    $scope.suggestion_list[k].day = parseInt($scope.suggestion_list[k].day);
                    $scope.suggestion_list[k].priority = parseInt($scope.suggestion_list[k].priority);
                });
            }

        } else {
            $scope.form_data = {
                "my_user_id": $scope.my_user_id,
                "my_login_token": $scope.my_login_token,

                "priority": 0,
                "is_favourite": "0",
                "track_expenses": "0",

                "monthly_limit_amount": 0,
                "weekly_limit_amount": 0,
                "daily_limit_amount_mon": 0,
                "daily_limit_amount_tue": 0,
                "daily_limit_amount_wed": 0,
                "daily_limit_amount_thu": 0,
                "daily_limit_amount_fri": 0,
                "daily_limit_amount_sat": 0,
                "daily_limit_amount_sun": 0
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
            to_be_submit.suggestion_list = $scope.suggestion_list;

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

        $scope.add_suggestion = function() {
            $scope.suggestion_list.push({
                "id": 0,
                "is_deleted": "0",
                "title": "",
                "price": 0,
                "day": 0,
                "priority": 0
            });
        }

        $scope.remove_suggestion = function(index) {
            if ($scope.suggestion_list[index].id > 0) {
                let ans = confirm("<?= BACKEND_REMOVE_ACTION_REMINDER ?>");
                if (!ans) {
                    return false;
                }
            }
            $scope.suggestion_list[index].is_deleted = "1";
            $scope.result_form.$setDirty();
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