<?php

namespace App\Controllers;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class Backend_portal_enquiry extends MY_Backend
{
    
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        $this->Main_model = model("App\Models\Enquiry_model");
        

        $this->data["status_kv_list"] = $this->Main_model->status_kv_list();
		$this->data["status_kv_info"] = $this->Main_model->kv_list_to_info($this->data["status_kv_list"]);


    }
}