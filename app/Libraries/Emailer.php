<?php

namespace App\Libraries;

use Exception;

class Emailer
{

    protected $data;
    protected $Setting_model;
    protected $Mail_log_model;

    public function __construct()
    {
        $this->Setting_model = model('App\Models\Setting_model');
        $this->Mail_log_model = model('App\Models\Mail_log_model');

        $this->data['website_name'] = "";

        $setting_data = $this->Setting_model->get_one([
            'is_deleted' => 0,
            'variable' => 'website_name',
        ]);
        if (!empty($setting_data)) {
            $this->data['website_name']  = $setting_data['value'];
        }
    }

    public function send($to_email, $subject, $content, $reply_to = "", $cc = [], $attachments = [], $bcc = [])
    {
        try {
            //Insert "mail_log"
            $mail_log_id = $this->Mail_log_model->insert_data([
                'email' => $to_email,
                'subject' => $subject,
                'content' => $content,
                'reply_to' => $reply_to,
                'cc' => !empty($cc) ? implode(",", $cc) : null,
                'bcc' => !empty($bcc) ? implode(",", $bcc) : null,
                'attachments' => !empty($attachments) ? json_encode($attachments) : null,
            ]);

            $email = \Config\Services::email();

            $email->initialize([
                'protocol' => 'smtp',
                'SMTPHost' => $_ENV['SMTP_HOST'],
                'SMTPUser' => $_ENV['SMTP_USER'],
                'SMTPPass' => $_ENV['SMTP_PASSWORD'],
                'SMTPPort' => 465,
                'SMTPCrypto' => 'ssl',
                'mailType' => 'html',
                'charset' => 'utf-8',
                'newline' => "\r\n",
                'wordWrap' => true,
                'SMTPTimeout' => 10,
            ]);

            $email->setFrom('info@conteoh.my', $this->data['website_name']);
            $email->setTo($to_email);
            $email->setSubject($subject);
            $email->setMessage($content);

            if (!empty($cc)) {
                $cc_str = implode(",", $cc);
                $email->setCC($cc_str);
            }
            if (!empty($bcc)) {
                $bcc_str = implode(",", $bcc);
                $email->setBCC($bcc_str);
            }
            if (!empty($reply_to)) {
                $email->setReplyTo($reply_to);
            }
            if (!empty($attachments)) {
                foreach ($attachments as $a) {
                    $email->attach($a);
                }
            }

            $sent_result = $email->send();

            if ($sent_result) {
                $this->Mail_log_model->update_data([
                    'id' => $mail_log_id
                ], [
                    'status' => 1,
                ]);
            } else {
                //Define Error Msg
                $error_msg = $email->printDebugger();
                throw new Exception($error_msg);
            }
        } catch (Exception $e) {
            $this->Mail_log_model->update_data([
                'id' => $mail_log_id
            ], [
                'status' => 2,
                'error_message' => $e->getMessage(),
            ]);
        } finally {
            return $sent_result;
        }
    }
}
