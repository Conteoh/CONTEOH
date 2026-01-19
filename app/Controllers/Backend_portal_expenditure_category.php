<?php

namespace App\Controllers;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class Backend_portal_expenditure_category extends MY_Backend
{
    protected $Expenditure_suggestion_model;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        $this->Main_model = model("App\Models\Expenditure_category_model");
        //Load Model
        $this->Expenditure_suggestion_model = model("App\Models\Expenditure_suggestion_model");
    }

    public function action($id = 0)
    {
        if ($id > 0) {
            //get result data
            $result_data = $this->Main_model->get_one([
                'is_deleted' => 0,
                'id' => $id
            ]);
            if (empty($result_data)) {
                show_error('Result not found');
            }
            $this->data['result_data'] = $result_data;

            $this->data['suggestion_list'] = $this->Expenditure_suggestion_model->get_all([
                'is_deleted' => 0,
                'expenditure_category_id' => $id
            ], [
                'id' => 'ASC'
            ]);
        }

        $this->data['id'] = $id;

        return view('backend/header', $this->data) . view('backend/' . $this->data['current_module'] . '/action', $this->data) . view('backend/footer', $this->data);
    }
}
