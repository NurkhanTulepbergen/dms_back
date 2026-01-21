<?php

if (! function_exists('result')) {
    function result($data = null,int $status_code = 200, $message = null)
    {
        $message = $message ?: match ($status_code){
            200 => trans('messages.http_errors.200'),
            201 => trans('messages.http_errors.201'),
            202 => trans('messages.http_errors.202'),
            400 => trans('messages.http_errors.400'),
            401 => trans('messages.http_errors.401'),
            403 => trans('messages.http_errors.403'),
            404 => trans('messages.http_errors.404'),
            429 => trans('messages.http_errors.429'),
            500 => trans('messages.http_errors.500'),
            default => null,
        };

        return response()->json([
            'status_code' => $status_code,
            'message' => $message,
            'data' => $data,
        ], $status_code, []);
    }
}
