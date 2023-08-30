<?php

namespace App\Utils\Traits;

use Illuminate\Http\JsonResponse;

trait ResponseTrait
{
    public function responseData($data, $message = null): JsonResponse
    {
        if ($message != null) {
            return new JsonResponse([
                'result' => true,
                'message' => $message,
                'data' => $data,
            ], 200);
        }
        return new JsonResponse([
            'result' => true,
            'data' => $data,
        ], 200);
    }

    public function responseError($message = null, $status = 500): JsonResponse
    {
        if ($message != null) {
            return new JsonResponse([
                'result' => false,
                'message' => $message,
            ], $status);
        }
        return new JsonResponse([
            'result' => false,
            'mesage' => $message,
        ], $status);
    }


    public
    function responseDataCount($data, $count = null): JsonResponse
    {
        if ($count == null) {
            return new JsonResponse([
                'result' => true,
                'count' => count($data),
                'data' => $data
            ], 200);
        } else {
            return new JsonResponse([
                'result' => true,
                'count' => $count - 1,
                'data' => $data
            ], 200);
        }
    }

    public
    function responseValidation($validation, $data = null): JsonResponse
    {
        return new JsonResponse([
            'result' => false,
            'data' => $data,
            'message' => $validation,
        ], 422);
    }

    public
    function responseDataNotFound($customMessage = "", $detail = "", $lang = ""): JsonResponse
    {
        $statusCode = 400;
        if ($customMessage == "") {
            $info = match ($lang) {
                "en" => "Data not found",
                default => "Data tidak ditemukan",
            };
        } else {
            $info = $customMessage;
        }
        if ($detail == "") {
            return new JsonResponse([
                'info' => $info,
            ], $statusCode);
        } else {
            return new JsonResponse([
                'info' => $info,
                'detail' => $detail,
            ], $statusCode);
        }
    }
}
