<?php

namespace Framework\Foundation;

use Framework\Http\Request;
use Framework\Http\Response;
use Framework\Validation\Validator;

/**
 * Base controller class
 */
abstract class Controller
{
    /**
     * Current request
     *
     * @var Request
     */
    protected Request $request;

    /**
     * Middleware
     *
     * @var array
     */
    protected array $middleware = [];

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->request = app(Request::class);
    }

    /**
     * Get middleware
     *
     * @return array
     */
    public function getMiddleware(): array
    {
        return $this->middleware;
    }

    /**
     * Validate request data
     *
     * @param array $rules
     * @return array
     */
    public function validate(array $rules): array
    {
        $validator = new Validator($this->request->all(), $rules);
        
        if ($validator->fails()) {
            session()->put('errors', $validator->errors());
            session()->put('old_input', $this->request->all());
            abort(422, 'Validation failed');
        }

        return $validator->validated();
    }

    /**
     * Render a view
     *
     * @param string $name
     * @param array $data
     * @return Response
     */
    public function view(string $name, array $data = []): Response
    {
        $content = view($name, $data)->render();
        return response($content);
    }

    /**
     * Return JSON response
     *
     * @param mixed $data
     * @param int $status
     * @return Response
     */
    public function json($data, int $status = 200): Response
    {
        return json_response($data, $status);
    }

    /**
     * Redirect to route
     *
     * @param string $route
     * @param array $parameters
     * @return Response
     */
    public function redirectToRoute(string $route, array $parameters = []): Response
    {
        return redirect(route($route, $parameters));
    }

    /**
     * Redirect back
     *
     * @return Response
     */
    public function back(): Response
    {
        return back();
    }

    /**
     * Get current user
     *
     * @return mixed
     */
    public function user()
    {
        return auth()->user();
    }

    /**
     * Check if user is authenticated
     *
     * @return bool
     */
    public function isAuthenticated(): bool
    {
        return auth()->check();
    }
}
