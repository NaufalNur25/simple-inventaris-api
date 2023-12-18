<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Portofolio Naufal - Product API",
 *      description="Product API Documentation",
 * )
 *
 * @OA\Server(
 *      url=L5_SWAGGER_CONST_HOST,
 *      description="Demo API Server"
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * success response method.
     *
     * @param  array  $result
     * @param  string  $message
     * @param  int  $code
     * @return void
     */
    public function sendSuccess($result, $message, $code = 200)
    {
        $response = [];

        if (! empty($message)) {
            $response = [
                'success' => true,
                'message' => $message,
            ];
        }

        if (! empty($result)) {
            $response['data'] = $result;
        }

        return response()->json($response, $code);
    }

    /**
     * return error response.
     *
     * @param  string  $error
     * @param  array  $errorData
     * @param  int  $code
     * @return void
     */
    public function sendError($error, $errorData = [], $code = 404)
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];

        if (! empty($errorData)) {
            $response['data'] = $errorData;
        }

        return response()->json($response, $code);
    }
}
