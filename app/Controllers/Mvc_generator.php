<?php

namespace App\Controllers;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class Mvc_generator extends BaseGenerator
{
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
    }

    public function module_role()
    {
        $module_name = "role";

        $field_list = [
            'id' => [
                'primary_key' => true,
                'data_type' => 'int',
                'length' => '11',
                'NULL' => false,
                'default_value' => '',
                'comment' => '',
                'label' => 'ID',
            ],
            'is_deleted' => [
                'primary_key' => false,
                'data_type' => 'tinyint',
                'length' => '1',
                'NULL' => false,
                'default_value' => '0',
                'comment' => '',
                'label' => 'Is Deleted',
            ],
            'created_date' => [
                'primary_key' => false,
                'data_type' => 'datetime',
                'length' => '',
                'NULL' => true,
                'default_value' => '',
                'comment' => '',
                'label' => 'Created Date',
            ],
            'title' => [
                'primary_key' => false,
                'data_type' => 'varchar',
                'length' => '255',
                'NULL' => true,
                'default_value' => '',
                'comment' => '',
                'label' => 'Title',
                'input_type' => 'text',
                'show_in_list_page' => true,
                'show_in_action_page' => true,
                'compulsory' => true,
            ],                         
            'description' => [
                'primary_key' => false,
                'data_type' => 'text',
                'length' => '',
                'NULL' => true,
                'default_value' => '',
                'comment' => '',
                'label' => 'Description',
                'input_type' => 'textarea',
                'show_in_list_page' => false,
                'show_in_action_page' => true,
                'compulsory' => false,
            ], 
            'priority' => [
                'primary_key' => false,
                'data_type' => 'int',
                'length' => '8',
                'NULL' => false,
                'default_value' => '0',
                'comment' => '',
                'label' => 'Priority',
                'input_type' => 'number',
                'show_in_list_page' => false,
                'show_in_action_page' => true,
                'compulsory' => true,
            ], 
            'modified_date' => [
                'primary_key' => false,
                'data_type' => 'datetime',
                'length' => '',
                'NULL' => true,
                'default_value' => '',
                'comment' => '',
                'label' => 'Modified Date',
            ],  
        ];

        $this->module_generation($module_name, $field_list);
    }

    public function module_enquiry()
    {
        $module_name = "enquiry";

        $field_list = [
            'id' => [
                'primary_key' => true,
                'data_type' => 'int',
                'length' => '11',
                'NULL' => false,
                'default_value' => '',
                'comment' => '',
                'label' => 'ID',
            ],
            'is_deleted' => [
                'primary_key' => false,
                'data_type' => 'tinyint',
                'length' => '1',
                'NULL' => false,
                'default_value' => '0',
                'comment' => '',
                'label' => 'Is Deleted',
            ],
            'created_date' => [
                'primary_key' => false,
                'data_type' => 'datetime',
                'length' => '',
                'NULL' => true,
                'default_value' => '',
                'comment' => '',
                'label' => 'Created Date',
            ],
            'name' => [
                'primary_key' => false,
                'data_type' => 'varchar',
                'length' => '255',
                'NULL' => true,
                'default_value' => '',
                'comment' => '',
                'label' => 'Name',
                'input_type' => 'text',
                'show_in_list_page' => true,
                'show_in_action_page' => true,
                'compulsory' => true,
            ],                         
            'email' => [
                'primary_key' => false,
                'data_type' => 'varchar',
                'length' => '255',
                'NULL' => true,
                'default_value' => '',
                'comment' => '',
                'label' => 'Email',
                'input_type' => 'text',
                'show_in_list_page' => true,
                'show_in_action_page' => true,
                'compulsory' => true,
            ], 
            'mobile' => [
                'primary_key' => false,
                'data_type' => 'varchar',
                'length' => '255',
                'NULL' => true,
                'default_value' => '',
                'comment' => '',
                'label' => 'Mobile',
                'input_type' => 'text',
                'show_in_list_page' => true,
                'show_in_action_page' => true,
                'compulsory' => true,
            ],
            'message' => [
                'primary_key' => false,
                'data_type' => 'text',
                'length' => '',
                'NULL' => true,
                'default_value' => '',
                'comment' => '',
                'label' => 'Message',
                'input_type' => 'textarea',
                'show_in_list_page' => false,
                'show_in_action_page' => true,
                'compulsory' => false,
            ],
            'status' => [
                'primary_key' => false,
                'data_type' => 'tinyint',
                'length' => '1',
                'NULL' => false,
                'default_value' => '0',
                'comment' => '0=Pending,1=Processing,2=Processed',
                'label' => 'Status',
                'input_type' => 'select',
                'option' => [
                    '0' => 'Pending',
                    '1' => 'Processing',
                    '2' => 'Processed',
                ],
                'show_in_list_page' => true,
                'show_in_action_page' => true,
                'compulsory' => true,
                'option_yes_no' => false
            ],
            'admin_remark' => [
                'primary_key' => false,
                'data_type' => 'text',
                'length' => '',
                'NULL' => true,
                'default_value' => '',
                'comment' => '',
                'label' => 'Admin Remark',
                'input_type' => 'textarea',
                'show_in_list_page' => false,
                'show_in_action_page' => true,
                'compulsory' => false,
            ],
            'modified_date' => [
                'primary_key' => false,
                'data_type' => 'datetime',
                'length' => '',
                'NULL' => true,
                'default_value' => '',
                'comment' => '',
                'label' => 'Modified Date',
            ],  
        ];

        $this->module_generation($module_name, $field_list);
    }
}
