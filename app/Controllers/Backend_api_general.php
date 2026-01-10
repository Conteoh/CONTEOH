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
}
