<?php

namespace App\Controllers;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class Backend_portal_car_maintenance extends MY_Backend
{
    protected $Tag_model;
    protected $Car_maintenance_attachment_model;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        $this->Main_model = model("App\Models\Car_maintenance_model");
        $this->Tag_model = model("App\Models\Tag_model");
        //Load Model
        $this->Car_maintenance_attachment_model = model("App\Models\Car_maintenance_attachment_model");

        // Load all tags for filter
        $tags_list = $this->Tag_model->get_all(["is_deleted" => 0], ["title" => "ASC"]);
        $tags_kv_list = [];
        foreach ($tags_list as $tag) {
            $tags_kv_list[$tag['title']] = $tag['title'];
        }
        $this->data["tags_kv_list"] = $tags_kv_list;
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

            $this->data['attachment_list'] = $this->Car_maintenance_attachment_model->get_all([
                'is_deleted' => 0,
                'car_maintenance_id' => $id
            ], [
                'id' => 'ASC'
            ]);
        }

        $this->data['id'] = $id;

        return view('backend/header', $this->data) . view('backend/' . $this->data['current_module'] . '/action', $this->data) . view('backend/footer', $this->data);
    }
}
