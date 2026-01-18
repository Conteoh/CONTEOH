<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use Exception;

class Backend_api_general extends BaseResourceController
{

    use ResponseTrait;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
    }

    public function login_submit()
    {
        try {

            if (isset($_SERVER["CONTENT_TYPE"]) && strpos($_SERVER["CONTENT_TYPE"], "application/json") !== false) {
                $_POST = array_merge($_POST, (array) json_decode(trim(file_get_contents('php://input')), true));

                //verify recaptcha
                $recaptcha = $this->request->getVar('recaptcha');
                $secret_key = $this->data['site_config']['recaptcha_secret_key'];
                $ip = $_SERVER['REMOTE_ADDR'];

                $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=" . $secret_key . "&response=" . $recaptcha . "&remoteip=" . $ip);
                $responseKeys = json_decode($response, true);

                if (!isset($responseKeys['success']) || $responseKeys['success'] != 1) {
                    throw new Exception("Recaptcha verification failed.");
                }

                $email = $this->request->getVar('email');
                $password = $this->request->getVar('password');

                $user_data = $this->User_model->get_one([
                    'is_deleted' => 0,
                    'email' => $email,
                ]);

                if (empty($user_data)) {
                    throw new Exception('Invalid email, user account not found.');
                } else {

                    if (sha1($password) != $user_data['password']) {
                        throw new Exception('Invalid email or password.');
                    }

                    if ($user_data['status'] != 1) {
                        throw new Exception('User account is inactivated, please contact admin.');
                    }

                    if ($user_data['is_email_verified'] != 1) {
                        throw new Exception('User account is not verified, please contact admin.');
                    }
                }


                //define token 
                $login_token = md5(date('YmdHis') . rand(100, 999));

                //insert login_token record
                $this->Login_token_model->insert_data([
                    'created_date' => date('Y-m-d H:i:s'),
                    'user_id' => $user_data['id'],
                    'token' => $login_token,
                    'expiry_time' => date('Y-m-d H:i:s', strtotime('+7 day')),
                ]);

                set_cookie('login_token', $login_token, (60 * 60 * 24 * 365));

                return $this->respond([
                    'status' => "SUCCESS",
                    'result' => [
                        'login_token' => $login_token,
                    ]
                ]);
            } else {
                throw new Exception('Invalid param');
            }
        } catch (Exception $e) {
            return $this->fail([
                'status' => "ERROR",
                'result' => $e->getMessage()
            ]);
        }
    }

    public function sent_reset_password_link()
    {

        try {

            if (isset($_SERVER["CONTENT_TYPE"]) && strpos($_SERVER["CONTENT_TYPE"], "application/json") !== false) {
                $_POST = array_merge($_POST, (array) json_decode(trim(file_get_contents('php://input')), true));

                //verify recaptcha
                $recaptcha = $this->request->getVar('recaptcha');
                $secret_key = $this->data['site_config']['recaptcha_secret_key'];
                $ip = $_SERVER['REMOTE_ADDR'];

                $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=" . $secret_key . "&response=" . $recaptcha . "&remoteip=" . $ip);
                $responseKeys = json_decode($response, true);

                if (!isset($responseKeys['success']) || $responseKeys['success'] != 1) {
                    throw new Exception("Recaptcha verification failed.");
                }

                $email = $this->request->getVar('email');

                $user_data = $this->User_model->get_one([
                    'is_deleted' => 0,
                    'email' => $email,
                ]);
                if (empty($user_data)) {
                    throw new Exception('Invalid email, user account not found.');
                } else {

                    //防止user不滥用email发送
                    if (!empty($user_data['last_sent_time'])) {
                        if (date('Y-m-d H:i:s') < date('Y-m-d H:i:s', strtotime($user_data['last_sent_time'] . '+5 minute'))) {
                            // throw new Exception("You just sent email within 5 minutes, please try again later");
                        }
                    }
                }

                //Set verification token
                $verification_token = md5($user_data['id'] . date('ymdHis') . rand(10, 99));

                $this->User_model->update_data([
                    'id' => $user_data['id']
                ], [
                    'modified_date' => date('Y-m-d H:i:s'),
                    'verification_token' => $verification_token,
                    'last_sent_time' => date('Y-m-d H:i:s')
                ]);

                $mail_template_data = $this->Mail_template_model->get_one([
                    'is_deleted' => 0,
                    'template_name' => 'member_forget_password'
                ]);

                if (!empty($mail_template_data)) {

                    //define hyperlink
                    $hyperlink = base_url(BACKEND_PORTAL . '/reset_password/') . $verification_token;

                    $search_array = [
                        "[@website_name]",
                        "[@user_name]",
                        "[@hyperlink]",
                    ];

                    $replace_array = [
                        $this->data['site_config']['website_name'],
                        $user_data['name'],
                        $hyperlink
                    ];

                    $email_subject = str_replace($search_array, $replace_array, $mail_template_data['subject']);
                    $email_content = str_replace($search_array, $replace_array, $mail_template_data['content']);

                    $this->Emailer_library->send(
                        $user_data['email'],
                        $email_subject,
                        $email_content,
                        '',
                        [$this->data['site_config']['website_email']]
                    );
                }

                return $this->respond([
                    'status' => "SUCCESS",
                    'result' => [
                        'email' => $email,
                    ]
                ]);
            } else {
                throw new Exception('Invalid param');
            }
        } catch (Exception $e) {
            return $this->fail([
                'status' => "ERROR",
                'result' => $e->getMessage()
            ]);
        }
    }

    public function reset_password()
    {

        try {

            if (isset($_SERVER["CONTENT_TYPE"]) && strpos($_SERVER["CONTENT_TYPE"], "application/json") !== false) {
                $_POST = array_merge($_POST, (array) json_decode(trim(file_get_contents('php://input')), true));

                //verify recaptcha
                $recaptcha = $this->request->getVar('recaptcha');
                $secret_key = $this->data['site_config']['recaptcha_secret_key'];
                $ip = $_SERVER['REMOTE_ADDR'];

                $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=" . $secret_key . "&response=" . $recaptcha . "&remoteip=" . $ip);
                $responseKeys = json_decode($response, true);

                if (!isset($responseKeys['success']) || $responseKeys['success'] != 1) {
                    throw new Exception("Recaptcha verification failed.");
                }

                $new_password = $this->request->getVar('new_password');
                $confirm_password = $this->request->getVar('confirm_password');
                $verification_token = $this->request->getVar('verification_token');

                if (empty($verification_token)) {
                    throw new Exception("Verification token cannot be blank");
                }

                $user_data = $this->User_model->get_one([
                    'is_deleted' => 0,
                    'verification_token' => $verification_token,
                ]);
                if (empty($user_data)) {
                    throw new Exception("Invalid verification code or verification code is expired");
                }

                //check password
                if (empty($new_password)) {
                    throw new Exception("New password cannot blank");
                } else {
                    if (strlen($new_password) < 5) {
                        throw new Exception('Password length at least 5');
                    }

                    if ($confirm_password != $new_password) {
                        throw new Exception('New password and confirm password not the same, please check');
                    }
                }

                $this->User_model->update_data([
                    'id' => $user_data['id']
                ], [
                    'modified_date' => date('Y-m-d H:i:s'),
                    'password' => sha1($new_password),
                    'verification_token' => null,
                ]);

                return $this->respond([
                    'status' => "SUCCESS",
                    'result' => []
                ]);
            } else {
                throw new Exception('Invalid param');
            }
        } catch (Exception $e) {
            return $this->fail([
                'status' => "ERROR",
                'result' => $e->getMessage()
            ]);
        }
    }

    public function update_profile()
    {
        try {

            if (isset($_SERVER["CONTENT_TYPE"]) && strpos($_SERVER["CONTENT_TYPE"], "application/json") !== false) {
                $_POST = array_merge($_POST, (array) json_decode(trim(file_get_contents('php://input')), true));

                //authentication
                $my_user_id = $this->request->getVar('my_user_id');
                $my_login_token = $this->request->getVar('my_login_token');
                $my_data = $this->member_authentication($my_user_id, $my_login_token);

                //get 
                $result_data = $this->User_model->get_one([
                    'is_deleted' => 0,
                    'id' => $my_data['id']
                ]);
                if (empty($result_data)) {
                    throw new Exception('Result data not found');
                }

                $name = trim($this->request->getVar('name'));
                $dial_code = trim($this->request->getVar('dial_code'));
                $mobile = trim($this->request->getVar('mobile'));
                $old_password = trim($this->request->getVar('old_password'));
                $new_password = trim($this->request->getVar('new_password'));
                $confirm_password = trim($this->request->getVar('confirm_password'));

                $submit_data = [
                    'modified_date' => date('Y-m-d H:i:s'),
                    'name' => !empty($name) ? $name : null,
                    'dial_code' => !empty($dial_code) ? $dial_code : null,
                    'mobile' => !empty($mobile) ? $mobile : null,
                ];

                if (!empty($old_password)) {
                    if (sha1($old_password) != $result_data['password']) {
                        throw new Exception("Invalid old password, please check");
                    }

                    if (empty($new_password)) {
                        throw new Exception('New password cannot be blank');
                    } else {
                        if (strlen($new_password) < 5) {
                            throw new Exception('New password length at least 5');
                        }

                        if ($confirm_password != $new_password) {
                            throw new Exception('New password and confirm password not tally, please check');
                        }
                    }

                    $submit_data['password'] = sha1($new_password);
                }

                $this->User_model->transStart();

                $this->User_model->update_data([
                    'id' => $result_data['id']
                ], $submit_data);

                $after_data = $this->User_model->get_one(['id' => $result_data['id']]);

                $this->Audit_trail_model->insert_data([
                    'created_date' => date('Y-m-d H:i:s'),
                    'user_id' => $my_data['id'],
                    'ref_table' => $this->current_module,
                    'ref_id' => $result_data['id'],
                    'action_type' => 1,
                    'before' => json_encode($result_data),
                    'after' => json_encode($after_data),
                    "origin" => $this->get_function_execution_origin()
                ]);

                $this->User_model->transComplete();

                return $this->respond([
                    'status' => "SUCCESS",
                    'result' => []
                ]);
            } else {
                throw new Exception('Invalid param');
            }
        } catch (Exception $e) {
            return $this->fail([
                'status' => "ERROR",
                'result' => $e->getMessage()
            ]);
        }
    }

    public function upload_image_now()
    {
        try {

            //Authentication
            $my_user_id = $this->request->getVar('my_user_id');
            $my_login_token = $this->request->getVar('my_login_token');
            $my_data = $this->member_authentication($my_user_id, $my_login_token);

            $width = $this->request->getVar('width');
            $height = $this->request->getVar('height');
            $crop = $this->request->getVar('crop');
            $ori_file_ext = $this->request->getVar('ori_file_ext');
            $quality = "auto";

            $field_name = $this->request->getVar('field_name');
            if (empty($field_name)) {
                $field_name = 'image_path';
            }
            if (empty($width)) {
                $width = 500;
            }
            if (empty($height)) {
                $height = 500;
            }
            if (empty($crop)) {
                $crop = 'fill';
            }

            if (isset($_FILES[$field_name])) {

                if ($width == 'ori' && $height == 'ori') {
                    list($width, $height) = getimagesize($_FILES[$field_name]['tmp_name']);
                }

                if ($_FILES[$field_name]['error'] == 0 && $_FILES[$field_name]['name'] != '') {

                    $pathinfo = pathinfo($_FILES[$field_name]['name']);
                    $ext = $pathinfo['extension'];
                    $ext = strtolower($ext);

                    switch ($ext) {
                        case "jpeg":
                        case "jpg":
                        case "png":
                        case "gif":

                            if ($ori_file_ext) {
                                $format = $ext;
                            } else {
                                $format = 'jpg';
                            }

                            $remote_img_path = $this->Cloudinary_library->upload($_FILES[$field_name]['tmp_name'], array(
                                'width' => $width,
                                'height' => $height,
                                "crop" => $crop,
                                "quality" => $quality,
                                'format' => $format,
                            ), $my_data['id']);

                            break;
                        case "pdf":
                            $remote_img_path = $this->Cloudinary_library->upload($_FILES[$field_name]['tmp_name'], [], $my_data['id']);
                            break;
                        default:
                            throw new Exception('Invalid file format');
                            break;
                    }
                } else {
                    throw new Exception("file error");
                }
            } else {
                throw new Exception("no file has been uploaded");
            }

            return $this->respond([
                'status' => "SUCCESS",
                'result' => $remote_img_path
            ]);
        } catch (Exception $e) {
            return $this->fail([
                'status' => "ERROR",
                'result' => $e->getMessage()
            ]);
        }
    }
}
