<?php
if (!function_exists('show_error')) {
    function show_error($message = '', $heading = 'Error')
    {
        // $response = service('response');
        // $response->setStatusCode($statusCode);
        
        echo view('errors/html/error_general', [
            'title'   => $heading,
            'message' => $message
        ]);
        exit;
    }
}

if (!function_exists('show_success')) {
    function show_success($message = '', $heading = 'Success')
    {
        echo view('errors/html/success_general', [
            'title'   => $heading,
            'message' => $message
        ]);
        exit;
    }
}