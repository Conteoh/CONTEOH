<?php

namespace App\Controllers;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class Backend_portal_user extends MY_Backend
{

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->Main_model = model("App\Models\User_model");

        //Load Kv List
        $this->data['status_kv_list'] = $this->User_model->status_kv_list();
        $this->data['status_kv_info'] = $this->User_model->kv_list_to_info($this->data['status_kv_list']);
        
    }
}
