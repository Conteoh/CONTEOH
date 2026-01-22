<?php

namespace App\Controllers;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class Backend_portal_expenditure extends MY_Backend
{
    protected $Expenditure_category_model;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        $this->Main_model = model("App\Models\Expenditure_model");
        $this->Expenditure_category_model = model("App\Models\Expenditure_category_model");


        $this->data["expenditure_category_kv_list"] = $this->Expenditure_category_model->get_kv_list(["is_deleted"=>0], "title");
		$this->data["expenditure_category_kv_info"] = $this->Expenditure_category_model->kv_list_to_info($this->data["expenditure_category_kv_list"]);


    }
}