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

if (!function_exists('js_date_to_php_date')) {
    function js_date_to_php_date($js_date)
    {
        // Remove "(Malaysia Time)"
        $clean = preg_replace('/\s*\(.*\)$/', '', $js_date);

        try {
            $date = new DateTime($clean);
            return $date->format('Y-m-d');
        } catch (Exception $e) {
            return null; // or throw / log error
        }
    }
}
