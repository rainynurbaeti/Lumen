<?php

namespace App\Helpers;

class ApiFormatter{

    protected static $response =[
        "status" => NULL,
        "massage" => NULL,
        "data" => NULL,
    ];

    public static function sendResponse($status = NULL, $message = NULL, $data = [])
    {
        self::$response['status'] = $status;
        self::$response['$message'] = $message;
        self::$response['data'] = $data;
        return response()->json(self::$response, self::$response['status']);
    }
}

