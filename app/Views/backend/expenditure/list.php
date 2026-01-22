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
                                    <div class="col-md-4 mb-2">
                                <div class="form-group">
                                    <label for="expenditure_category_id">Expenditure Category</label>
                                    <select class="form-control form-control-sm" id="expenditure_category_id" name="expenditure_category_id" ng-model="filter_data.expenditure_category_id">
                                        <option value="">All</option>
                                        <?php if (isset($expenditure_category_kv_list) && !empty($expenditure_category_kv_list)): ?>
                                            <?php foreach ($expenditure_category_kv_list as $k => $v): ?>
                                                <option value="<?= $k ?>"><?= $v ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
									<div class="col-md-4 mb-2">
                                <div class="form-group">
                                    <label for="date">Date</label>
                                    <input type="date" class="form-control form-control-sm" id="date" name="date" ng-model="filter_data.date">
                                </div>
                            </div>
									<div class="col-md-4 mb-2">
                                <div class="form-group">
                                    <label for="title">Title</label>
                                    <input type="text" class="form-control form-control-sm" id="title" name="title" placeholder="Title" ng-model="filter_data.title">
                                </div>
                            </div>
									<div class="col-md-4 mb-2">
                                <div class="form-group">
                                    <label for="total_amount">Total Amount</label>
                                    <input type="text" class="form-control form-control-sm" id="total_amount" name="total_amount" placeholder="Total Amount" ng-model="filter_data.total_amount">
                                </div>
                            </div>
									<div class="col-md-4 mb-2">
                                <div class="form-group">
                                    <label for="location">Location</label>
                                    <input type="text" class="form-control form-control-sm" id="location" name="location" placeholder="Location" ng-model="filter_data.location">
                                </div>
                            </div>
									<div class="col-md-12 mb-2">
                                <div class="form-group">
                                    <label for="tags">Tags</label>
                                    <div class="tags-filter-container" style="border: 1px solid #ced4da; border-radius: 0.25rem; padding: 0.5rem; min-height: 60px; max-height: 200px; overflow-y: auto; display: flex; flex-wrap: wrap; gap: 1rem; align-items: flex-start;">
                                        <div ng-repeat="(tagKey, tagValue) in tags_kv_list" class="form-check" style="flex: 0 0 auto; margin-bottom: 0; display: flex; align-items: center;">
                                            <input class="form-check-input" type="checkbox" id="tag_{{tagKey}}" ng-model="filter_data.tags_array[tagKey]" ng-true-value="'{{tagKey}}'" ng-false-value="undefined" ng-change="load_result_list_now()" style="margin-right: 0.25rem; margin-top: 0;">
                                            <label class="form-check-label" for="tag_{{tagKey}}" style="margin-bottom: 0; padding-left: 0;">
                                                {{tagValue}}
                                            </label>
                                        </div>
                                        <div ng-if="!tags_kv_list || Object.keys(tags_kv_list).length == 0" class="text-muted" style="width: 100%;">
                                            No tags available
                                        </div>
                                    </div>
                                </div>
                            </div>

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
                        <a href="<?= base_url(BACKEND_PORTAL . '/' . $current_module . '/add') ?>" class="btn btn-primary btn-sm"><i class="fa fa-plus-circle"></i> Add New</a>
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
                                        <td title="'Expenditure Category'">{{expenditure_category_kv_list[item.expenditure_category_id]}}</td>
                                        <td title="'Date'">{{item.date}}</td>
										<td title="'Title'">{{item.title}}</td>
										<td title="'Total Amount'">{{item.total_amount}}</td>
										<td title="'Location'">{{item.location}}</td>
										<td title="'Tags'">
											<span ng-if="item.tags" ng-repeat="tag in getTagsArray(item.tags)" class="badge badge-primary me-1" style="background-color: #007bff; color: white; padding: 0.25rem 0.5rem; border-radius: 0.25rem; display: inline-block; margin-right: 0.25rem;">
												{{tag}}
											</span>
										</td>
                                    </tr>
                                    <tr ng-if="config_data.total_record == 0">
                                        <td colspan="9" class="text-center">No data found</td>
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

        $scope.my_user_id = '<?= $my_user_id ?? 0 ?>';
        $scope.my_login_token = '<?= $my_login_token ?? '' ?>';

        $scope.expenditure_category_kv_list = <?= isset($expenditure_category_kv_list) ? json_encode($expenditure_category_kv_list) : '[]' ?>;
        $scope.tags_kv_list = <?= isset($tags_kv_list) ? json_encode($tags_kv_list) : '[]' ?>;
								
        // Function to convert tags string to array
        $scope.getTagsArray = function(tags) {
            if (!tags || tags === '') {
                return [];
            }
            try {
                // Try to parse as JSON array first
                var parsed = JSON.parse(tags);
                if (Array.isArray(parsed)) {
                    return parsed;
                }
            } catch (e) {
                // If parsing fails, treat as comma-separated string
            }
            // Treat as comma-separated string
            return tags.split(',').map(function(tag) {
                return tag.trim();
            }).filter(function(tag) {
                return tag.length > 0;
            });
        };

        $scope.config_data = {
            'total_record': 0,
            'item_per_page': '<?= $site_config["item_per_page"] ?? BACKEND_ITEM_PER_PAGE ?>'
        };

        $scope.filter_data = {
            "id": "<?= $_GET['id'] ?? '' ?>",
            "tags_array": {}
        };

        var Api = $resource("<?= base_url(BACKEND_API . "/" . $current_module . "/list") ?>/" + $scope.my_user_id + "/" + $scope.my_login_token);
        $scope.load_result_list_now = function() {
            // Convert tags_array object to array of selected tags
            var filterDataCopy = angular.copy($scope.filter_data);
            if (filterDataCopy.tags_array) {
                var selectedTags = [];
                angular.forEach(filterDataCopy.tags_array, function(value, key) {
                    if (value) {
                        selectedTags.push(value);
                    }
                });
                filterDataCopy.tags = selectedTags.length > 0 ? selectedTags.join(',') : '';
                delete filterDataCopy.tags_array;
            }
            
            var tableConfig = {
                page: 1,
                count: $scope.config_data.item_per_page,
                filter: filterDataCopy
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
            $scope.filter_data = {
                "tags_array": {}
            };
            $scope.load_result_list_now();
        }

        $scope.export_now = () => {
            let to_be_submit = angular.copy($scope.filter_data);
            to_be_submit['is_export'] = 1;
            
            // Convert tags_array object to tags string for export
            if (to_be_submit.tags_array) {
                var selectedTags = [];
                angular.forEach(to_be_submit.tags_array, function(value, key) {
                    if (value) {
                        selectedTags.push(value);
                    }
                });
                to_be_submit.tags = selectedTags.length > 0 ? selectedTags.join(',') : '';
                delete to_be_submit.tags_array;
            }

            let params = [];
            angular.forEach(to_be_submit, function(value, key) {
                params.push(key + '=' + encodeURIComponent(value));
            });

            let query_str = params.join('&');

            let url = "<?= base_url(BACKEND_API . "/" . $current_module . "/list") ?>" + "/" + $scope.my_user_id + "/" + $scope.my_login_token + '?' + query_str;

            window.open(url, '_blank');

        }
    });
</script>
