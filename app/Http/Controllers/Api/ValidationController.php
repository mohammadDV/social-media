<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ValidationController extends Controller
{
    /**
     * Constructor of ILiveRepository.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the base requests namespace
     *
     * @return string               The base requests namespace
     */
    protected function requestsNamespace() : string
    {
        return '\\App\Http\\Requests\\';
    }

    /**
     * Get all of lives.
     */
    public function index(string $requestName, Request $request)
    {
        return response()->json($this->validationRequestObject($requestName, $request), Response::HTTP_OK);
    }

    /**
     * Helper function to obtain the validation request object
     *
     * @param   string The request name
     * @return  array Eroor message
     */
    protected function validationRequestObject(string $requestName, Request $request) : array
    {
        // get the RequestValidator belongin to the requested class
        $requestNameFull = $this->requestsNamespace()  . $requestName;

        $requestClass = new $requestNameFull;

        $validator = app('validator')->make($requestClass->all(), $requestClass->rules());

        if ($validator->fails()) {
            // Validation failed
            $errors = $validator->errors()->getMessages();

            // Access the error message for the field
            if (isset($errors[$request->get('field')])) {
                return [
                    'status' => 1,
                    'massage' => reset($errors[$request->get('field')])
                ];
            }
        }

        return [
            'status' => 0,
            'message' => ''
        ];
    }

}

