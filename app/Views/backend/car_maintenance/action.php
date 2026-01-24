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
                                            <label for="date">Date</label>
                                            <input type="date" class="form-control" id="date" ng-model="form_data.date" required>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <div class="form-group">
                                            <label for="total_amount">Price</label>
                                            <input type="number" step=".01" class="form-control" id="total_amount" ng-model="form_data.total_amount" required>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <div class="form-group">
                                            <label for="description">Description</label>
                                            <textarea class="form-control" id="description" placeholder="Description" ng-model="form_data.description" rows="5"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <div class="form-group">
                                            <label for="tags">Tags</label>
                                            <div class="tags-input-container" style="border: 1px solid #ced4da; border-radius: 0.25rem; padding: 0.375rem 0.75rem; min-height: 38px; display: flex; flex-wrap: wrap; align-items: center; gap: 0.5rem; position: relative;">
                                                <span ng-repeat="tag in form_data.tags_array" class="badge badge-primary" style="background-color: #007bff; color: white; padding: 0.25rem 0.5rem; border-radius: 0.25rem; display: inline-flex; align-items: center; gap: 0.5rem;">
                                                    {{tag}}
                                                    <span ng-click="removeTag($index)" style="cursor: pointer; font-weight: bold;">&times;</span>
                                                </span>
                                                <div style="flex: 1; min-width: 150px; position: relative;">
                                                    <input type="text" class="form-control border-0" id="tags" placeholder="Type to search tags..." ng-model="form_data.tag_input" ng-keydown="handleTagInput($event)" ng-keyup="searchTagSuggestions()" ng-focus="onTagInputFocus()" ng-blur="onTagInputBlur()" style="border: none !important; outline: none; box-shadow: none;">
                                                    <div ng-if="showSuggestions && tagSuggestions.length > 0" class="tag-suggestions-dropdown" style="position: absolute; top: 100%; left: 0; right: 0; background: white; border: 1px solid #ced4da; border-top: none; border-radius: 0 0 0.25rem 0.25rem; max-height: 200px; overflow-y: auto; z-index: 1000; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                                        <div ng-repeat="suggestion in tagSuggestions" ng-click="selectTagSuggestion(suggestion)" ng-mousedown="$event.preventDefault()" style="padding: 0.5rem 0.75rem; cursor: pointer; border-bottom: 1px solid #f0f0f0; color: #212529; background-color: white;" ng-mouseenter="$event.target.style.backgroundColor='#e9ecef'; $event.target.style.color='#212529';" ng-mouseleave="$event.target.style.backgroundColor='white'; $event.target.style.color='#212529';">
                                                            {{suggestion}}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fa fa-paperclip"></i> <b>Attachment Information</b></h3>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm text-center b-result-list-table">
                                        <tr>
                                            <th width="10%">Action</th>
                                            <th width="10%">ID</th>
                                            <th width="20%">Created Date</th>
                                            <th width="30%">Title</th>
                                            <th width="30%">Document Path (IMG/PDF)</th>
                                        </tr>
                                        <tr ng-if="!attachment_list || attachment_list.length == 0">
                                            <td colspan="5" class="text-center">No data found</td>
                                        </tr>
                                        <tr ng-repeat="item in attachment_list" ng-if="item.is_deleted == '0'">
                                            <td>
                                                <button type="button" class="btn btn-danger btn-sm list-act-btn" ng-click="remove_attachment($index)"><i class="fa fa-trash"></i></button>
                                            </td>
                                            <td>{{item.id}}</td>
                                            <td>{{item.created_date}}</td>
                                            <td>
                                                <input type="text" class="form-control" ng-model="item.title" placeholder="Title">
                                            </td>
                                            <td>
                                                <div class="input-group">
                                                    <input type="file" class="form-control" id="document_path_{{ $index }}" ng-files="getTheFiles($files, 'document_path_' + $index)" accept="image/*,.pdf" placeholder="No file chosen">
                                                    <input type="text" class="form-control" ng-model="item.document_path" placeholder="No file chosen" readonly style="display: none;">
                                                    <div class="input-group-append">
                                                        <button type="button" class="btn btn-danger attachment-upload-delete-btn" ng-click="item.document_path = ''; result_form.$setDirty()" ng-disabled="!item.document_path || item.is_uploading_document_path == 1">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </div>

                                                    <button type="button" id="upload_btn_document_path_{{ $index }}" ng-click="uploadFiles(0, 0, 'document_path_' + $index, 'attachment_list', true, $index)" style="display: none;"></button>
                                                </div>
                                                <div ng-if="item.document_path || item.is_uploading_document_path" class="mt-2 text-start">
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <!--Show loading spinner when uploading-->
                                                            <div ng-if="item.is_uploading_document_path" class="attachment-upload-loading text-center">
                                                                <i class="fa fa-spinner fa-spin text-primary"></i>
                                                            </div>
                                                            <!--Show Image thumbnail or document icon based on the file type-->
                                                            <a ng-if="!item.is_uploading_document_path && isImageFile(item.document_path)" ng-href="{{item.document_path}}" target="_blank">
                                                                <img ng-src="{{item.document_path}}" class="attachment-upload-image" />
                                                            </a>
                                                            <a ng-if="!item.is_uploading_document_path && !isImageFile(item.document_path)" ng-href="{{item.document_path}}" target="_blank" class="attachment-upload-icon-wrapper text-center">
                                                                <i class="fa fa-file text-secondary"></i>
                                                            </a>
                                                        </div>
                                                        <input type="text" class="form-control" placeholder="No file chosen" value="{{item.document_path ? item.document_path.split('/').pop() : ''}}" readonly>
                                                        <div class="input-group-append">
                                                            <button type="button" class="btn btn-secondary attachment-upload-copy-btn" ng-click="copy_document_path(item.document_path)" ng-disabled="item.is_uploading_document_path"><i class="fa fa-copy"></i></button>
                                                        </div>
                                                    </div>

                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                    <button type="button" class="btn btn-primary btn-sm" ng-click="add_attachment()"><i class="fa fa-plus"></i> Add </button>
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
        $scope.tags_kv_list = <?= isset($tags_kv_list) ? json_encode($tags_kv_list) : '[]' ?>;

        $scope.id = <?= (isset($id) && !empty($id)) ? $id : 0 ?>;
        $scope.attachment_list = [];

        // Tag suggestions
        $scope.tagSuggestions = [];
        $scope.showSuggestions = false;

        if ($scope.id && $scope.id > 0) {
            $scope.form_data = <?= isset($result_data) ? json_encode($result_data) : "[]" ?>;
            $scope.form_data.my_user_id = $scope.my_user_id;
            $scope.form_data.my_login_token = $scope.my_login_token;

            if ($scope.form_data.date) {
                $scope.form_data.date = new Date($scope.form_data.date);
            }
            if ($scope.form_data.total_amount) {
                $scope.form_data.total_amount = parseFloat($scope.form_data.total_amount);
            }

            // Initialize tags array from tags string
            if ($scope.form_data.tags) {
                try {
                    // Try to parse as JSON array first
                    var parsed = JSON.parse($scope.form_data.tags);
                    if (Array.isArray(parsed)) {
                        $scope.form_data.tags_array = parsed;
                    } else {
                        // If not JSON, treat as comma-separated string
                        $scope.form_data.tags_array = $scope.form_data.tags.split(',').map(function(tag) {
                            return tag.trim();
                        }).filter(function(tag) {
                            return tag.length > 0;
                        });
                    }
                } catch (e) {
                    // If parsing fails, treat as comma-separated string
                    $scope.form_data.tags_array = $scope.form_data.tags.split(',').map(function(tag) {
                        return tag.trim();
                    }).filter(function(tag) {
                        return tag.length > 0;
                    });
                }
            } else {
                $scope.form_data.tags_array = [];
            }
            $scope.form_data.tag_input = '';

            // Load attachment list
            $scope.attachment_list = <?= isset($attachment_list) ? json_encode($attachment_list) : "[]" ?>;

        } else {
            $scope.form_data = {
                "my_user_id": $scope.my_user_id,
                "my_login_token": $scope.my_login_token,

                "total_amount": 0.00,
                "date": new Date(),
                "tags_array": [],
                "tag_input": ""
            };
        }

        // On tag input focus
        $scope.onTagInputFocus = function() {
            var query = $scope.form_data.tag_input.trim();
            if (query.length > 0) {
                $scope.searchTagSuggestions();
            } else {
                // Show all available tags when input is empty
                $scope.loadAllTagSuggestions();
            }
        };

        // Track if suggestion is being clicked to prevent blur from closing dropdown
        $scope.isClickingSuggestion = false;

        // On tag input blur (with delay to allow click events)
        $scope.onTagInputBlur = function() {
            // Use timeout to allow click events on suggestions to fire first
            $timeout(function() {
                if (!$scope.isClickingSuggestion) {
                    $scope.showSuggestions = false;
                }
                $scope.isClickingSuggestion = false;
            }, 200);
        };

        // Load all tag suggestions
        $scope.loadAllTagSuggestions = function() {
            var allSuggestions = [];
            angular.forEach($scope.tags_kv_list, function(value, key) {
                // Check if tag is not already added
                if ($scope.form_data.tags_array.indexOf(value) === -1) {
                    allSuggestions.push(value);
                }
            });
            $scope.tagSuggestions = allSuggestions.slice(0, 20); // Limit to 20 suggestions
            $scope.showSuggestions = $scope.tagSuggestions.length > 0;
        };

        // Search tag suggestions
        $scope.searchTagSuggestions = function() {
            var query = $scope.form_data.tag_input.trim();

            if (query.length === 0) {
                $scope.loadAllTagSuggestions();
                return;
            }

            // Filter from local tags list first
            var localSuggestions = [];
            angular.forEach($scope.tags_kv_list, function(value, key) {
                if (value.toLowerCase().indexOf(query.toLowerCase()) !== -1) {
                    // Check if tag is not already added
                    if ($scope.form_data.tags_array.indexOf(value) === -1) {
                        localSuggestions.push(value);
                    }
                }
            });

            // Also fetch from API for more suggestions
            $http.get("<?= base_url(BACKEND_API . '/' . $current_module . '/get_tag_suggestions') ?>/" + $scope.my_user_id + "/" + $scope.my_login_token + "?query=" + encodeURIComponent(query)).then(function(response) {
                if (response.data.status == "SUCCESS") {
                    var apiSuggestions = response.data.result || [];
                    // Merge and deduplicate
                    var allSuggestions = localSuggestions.concat(apiSuggestions);
                    var uniqueSuggestions = [];
                    var seen = {};
                    angular.forEach(allSuggestions, function(suggestion) {
                        if (!seen[suggestion] && $scope.form_data.tags_array.indexOf(suggestion) === -1) {
                            seen[suggestion] = true;
                            uniqueSuggestions.push(suggestion);
                        }
                    });
                    $scope.tagSuggestions = uniqueSuggestions.slice(0, 10); // Limit to 10 suggestions
                    $scope.showSuggestions = $scope.tagSuggestions.length > 0;
                }
            }, function(response) {
                // If API fails, use local suggestions
                $scope.tagSuggestions = localSuggestions.slice(0, 10);
                $scope.showSuggestions = $scope.tagSuggestions.length > 0;
            });
        };

        // Select tag from suggestion
        $scope.selectTagSuggestion = function(suggestion) {
            $scope.isClickingSuggestion = true;
            if (suggestion && suggestion.trim().length > 0) {
                if ($scope.form_data.tags_array.indexOf(suggestion) === -1) {
                    $scope.form_data.tags_array.push(suggestion);
                }
                $scope.form_data.tag_input = '';
                $scope.tagSuggestions = [];
                $scope.showSuggestions = false;
                // Remove focus from input
                var inputElement = document.getElementById('tags');
                if (inputElement) {
                    inputElement.blur();
                }
            }
        };

        // Handle tag input (Enter key and arrow keys)
        $scope.handleTagInput = function(event) {
            if (event.keyCode === 13) { // Enter key
                event.preventDefault();
                var tagValue = $scope.form_data.tag_input.trim();
                if (tagValue && tagValue.length > 0) {
                    // Check if tag already exists
                    if ($scope.form_data.tags_array.indexOf(tagValue) === -1) {
                        $scope.form_data.tags_array.push(tagValue);
                    }
                    $scope.form_data.tag_input = '';
                    $scope.tagSuggestions = [];
                    $scope.showSuggestions = false;
                }
            } else if (event.keyCode === 27) { // Escape key
                $scope.showSuggestions = false;
            }
        };

        // Add tag function (kept for backward compatibility)
        $scope.addTag = function(event) {
            $scope.handleTagInput(event);
        };

        // Hide suggestions when clicking outside
        var clickHandler = function(e) {
            var target = e.target;
            var container = null;

            // Use native DOM closest method
            if (target && target.closest) {
                container = target.closest('.tags-input-container');
            } else {
                // Fallback: traverse up the DOM tree
                var element = target;
                while (element && element !== document.body) {
                    if (element.classList && element.classList.contains('tags-input-container')) {
                        container = element;
                        break;
                    }
                    element = element.parentElement;
                }
            }

            // If click is outside the container, hide suggestions
            if (!container) {
                if (!$scope.$$phase && !$scope.$root.$$phase) {
                    $scope.$apply(function() {
                        $scope.showSuggestions = false;
                    });
                } else {
                    $scope.showSuggestions = false;
                }
            }
        };

        // Use timeout to ensure DOM is ready
        $timeout(function() {
            document.addEventListener('click', clickHandler);
        }, 0);

        // Clean up on scope destroy
        $scope.$on('$destroy', function() {
            document.removeEventListener('click', clickHandler);
        });

        // Remove tag function
        $scope.removeTag = function(index) {
            $scope.form_data.tags_array.splice(index, 1);
        };

        // Attachment functions
        $scope.add_attachment = function() {
            $scope.attachment_list.push({
                "id": 0,
                "is_deleted": "0",
                "title": "",
                "document_path": "",
                "created_date": ""
            });
        }

        $scope.remove_attachment = function(index) {
            if ($scope.attachment_list[index].id > 0) {
                let ans = confirm("<?= BACKEND_REMOVE_ACTION_REMINDER ?>");
                if (!ans) {
                    return false;
                }
            }
            $scope.attachment_list[index].is_deleted = "1";
            $scope.result_form.$setDirty();
        }

        /*
        $scope.upload_attachment_document = function(index) {
            $("#document_path_" + index).click();
        }
        */

        // Check if file is an image
        $scope.isImageFile = function(filePath) {
            if (!filePath) return false;
            var imageExtensions = ['.jpg', '.jpeg', '.png', '.gif', '.bmp', '.webp', '.svg'];
            var lowerPath = filePath.toLowerCase();
            // Check if path contains image extension
            return imageExtensions.some(function(ext) {
                return lowerPath.indexOf(ext) !== -1;
            });
        }

        // Copy document path to clipboard
        $scope.copy_document_path = function(documentPath) {
            if (!documentPath) return;
            
            // Create a temporary textarea element
            var textarea = document.createElement('textarea');
            textarea.value = documentPath;
            textarea.style.position = 'fixed';
            textarea.style.opacity = '0';
            document.body.appendChild(textarea);
            textarea.select();
            
            try {
                document.execCommand('copy');
                alert('Document path copied to clipboard!');
            } catch (err) {
                alert('Failed to copy document path');
            }
            
            document.body.removeChild(textarea);
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


            if ($scope.form_data.date) {
                $scope.form_data.date_temp = $("#date").val();
            }

            // Convert tags array to comma-separated string
            if ($scope.form_data.tags_array && $scope.form_data.tags_array.length > 0) {
                $scope.form_data.tags = $scope.form_data.tags_array.join(',');
            } else {
                $scope.form_data.tags = '';
            }

            var to_be_submit = angular.copy($scope.form_data);
            to_be_submit.attachment_list = $scope.attachment_list;

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

            // Extract base field name for list items (e.g., "document_path_0" -> "document_path")
            var base_field_id = field_id;
            var upload_status_field = "is_uploading_" + field_id;
            if (is_list) {
                // For list items, extract base field name by removing the index suffix
                var match = field_id.match(/^(.+)_(\d+)$/);
                if (match && match[1]) {
                    base_field_id = match[1];
                    upload_status_field = "is_uploading_" + base_field_id;
                }
            }

            if (is_list) {
                $scope[fieldName][index][upload_status_field] = 1;
            } else {
                $scope[fieldName][upload_status_field] = 1;
            }

            // SEND THE FILES.        
            $http(request).then(function(res) {
                if (is_list) {
                    $scope[fieldName][index][upload_status_field] = 0;
                } else {
                    $scope[fieldName][upload_status_field] = 0;
                }
                if (res.data.status == "SUCCESS") {
                    if (is_list) {
                        $scope[fieldName][index][base_field_id] = res.data.result;
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
                // Extract base field name for list items
                var upload_status_field = "is_uploading_" + field_id;
                if (is_list) {
                    var match = field_id.match(/^(.+)_(\d+)$/);
                    if (match && match[1]) {
                        upload_status_field = "is_uploading_" + match[1];
                    }
                }
                if (is_list) {
                    $scope[fieldName][index][upload_status_field] = 0;
                } else {
                    $scope[fieldName][upload_status_field] = 0;
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