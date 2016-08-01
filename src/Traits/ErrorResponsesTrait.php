<?php

namespace NavJobs\Transmit\Traits;

trait ErrorResponsesTrait
{
    /**
     * Returns a response that indicates an an error occurred.
     *
     * @param $message
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    private function respondWithError($message, $statusCode = 400)
    {
        return response()->json([
            'errors' => [
                'http_code' => $statusCode,
                'message'   => $message,
            ]
        ], $statusCode);
    }

    /**
     * Returns a response that indicates a 403 Forbidden.
     *
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    protected function errorForbidden($message = 'Forbidden')
    {
        return $this->respondWithError($message, 403);
    }

    /**
     * Returns a response that indicates an Internal Error has occurred.
     *
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    protected function errorInternalError($message = 'Internal Error')
    {
        return $this->respondWithError($message, 500);
    }

    /**
     * Returns a response that indicates a 404 Not Found.
     *
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    protected function errorNotFound($message = 'Resource Not Found')
    {
        return $this->respondWithError($message, 404);
    }

    /**
     * Returns a response that indicates a 401 Unauthorized.
     *
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    protected function errorUnauthorized($message = 'Unauthorized')
    {
        return $this->respondWithError($message, 401);
    }

    /**
     * Returns a response that indicates a 422 Unprocessable Entity.
     *
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    protected function errorUnprocessableEntity($message = 'Unprocessable Entity')
    {
        return $this->respondWithError($message, 422);
    }

    /**
     * Returns a response that indicates the wrong arguments were specified.
     *
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    protected function errorWrongArgs($message = 'Wrong Arguments')
    {
        return $this->respondWithError($message, 400);
    }

    /**
     * Returns a response that indicates custom error type.
     *
     * @param $message
     * @param $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    protected function errorCustomType($message, $statusCode = 400)
    {
        return $this->respondWithError($message, $statusCode);
    }

    /**
     * Returns a response that indicates multiple errors in an array.
     *
     * @param array $errors
     * @param $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    protected function errorArray(array $errors, $statusCode = 400)
    {
        return response()->json([
            'errors' => $errors
        ], $statusCode);
    }

    /**
     * Returns a response that mimics Laravel validation failure response.
     *
     * @param array $errors
     * @return \Illuminate\Http\JsonResponse
     */
    protected function errorValidation(array $errors)
    {
        return $this->errorArray($errors, 422);
    }
}