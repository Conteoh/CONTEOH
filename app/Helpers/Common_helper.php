<?php
if (!function_exists('show_error')) {
    function show_error($message = '', $title = 'Error', $redirect_url = '')
    {
        // $response = service('response');
        // $response->setStatusCode($statusCode);

        echo view('errors/html/error_general', [
            'title'   => $title,
            'message' => $message,
            'redirect_url' => $redirect_url
        ]);
        exit;
    }
}

if (!function_exists('show_success')) {
    function show_success($message = '', $title = 'Success', $redirect_url = '')
    {
        echo view('errors/html/success_general', [
            'title'   => $title,
            'message' => $message,
            'redirect_url' => $redirect_url
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
