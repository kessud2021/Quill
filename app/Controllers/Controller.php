<?php

namespace App\Controllers;

class Controller {
    protected $request;
    protected $response;

    public function __construct() {
        $this->request = app('request') ?? request();
    }

    protected function view($view, $data = []) {
        return app('view')->make($view, $data);
    }

    protected function json($data, $status = 200) {
        return json_response($data, $status);
    }

    protected function redirect($url, $status = 302) {
        $response = new \Framework\Http\Response('', $status);
        return $response->redirect($url, $status);
    }

    protected function validate($data, $rules) {
        $validator = new \Framework\Security\Validator($data, $rules);

        if (!$validator->validate()) {
            throw new \Framework\Exceptions\ValidationException($validator->errors());
        }

        return true;
    }

    protected function authorize($ability, $model = null) {
        if (!auth()->check()) {
            abort(403, 'Unauthorized');
        }

        return true;
    }

    protected function abort($code, $message = null) {
        abort($code, $message);
    }
}
