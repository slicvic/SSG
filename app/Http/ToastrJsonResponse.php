<?php namespace App\Http;

use Illuminate\Http\JsonResponse;
use App\Session\Flash;

/**
 * ToastrJsonResponse
 *
 * @author Victor Lantigua <vmlantigua@gmail.com>
 */
class ToastrJsonResponse {

    /**
     * Sets a success response.
     *
     * @param  mixed  $message
     * @param  int    $status
     * @return JsonResponse
     */
    public static function success($message, $status = 200)
    {
        return response()->json([
            'title' => trans('messages.flash_success_title'),
            'message' => Flash::tidyMessage($message)
        ], $status);
    }

    /**
     * Sets an error response.
     *
     * @param  mixed  $message
     * @param  int    $status
     * @return JsonResponse
     */
    public static function error($message, $status)
    {
        return response()->json([
            'title' => trans('messages.flash_error_title'),
            'message' => Flash::tidyMessage($message)
        ], $status);
    }
}