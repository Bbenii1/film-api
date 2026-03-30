<?php

namespace App\Html;

use App\Repositories\BaseRepository;
use App\Repositories\FilmRepository;
use App\Repositories\DirectorRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\StarRepository;
use App\Repositories\StudioRepository;

class Request
{
    private static array $acceptedRoutes = [
        'POST' => [
            '/films',
            '/users/login',
            '/users/logout',
            '/directors',
            '/categories',
            '/stars',
            '/studio',
        ],
        'GET' => [
            '/films',
            '/films/{id}',
            '/directors',
            '/directors/{id}',
            '/categories',
            '/categories/{id}',
            '/stars',
            '/stars/{id}',
            '/studio',
            '/studio/{id}'
        ],
        'PUT' => [
            '/films/{id}',
            '/directors/{id}',
            '/categories/{id}',
            '/stars/{id}',
            '/studio/{id}',
            '/studio'
        ],
        'PATCH' => [
            '/films/{id}',
            '/directors/{id}',
            '/categories/{id}',
            '/stars/{id}',
            '/studio/{id}'
        ],
        'DELETE' => [
            '/films/{id}',
            '/directors/{id}',
            '/categories/{id}',
            '/stars/{id}',
            '/studio/{id}'
        ],
    ];

    public static function handle(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

        if (!self::isRouteAllowed($method, $uri)) {
            Response::jsonResponse(['error' => 'Route not allowed'], 400);
            return;
        }

        $requestData = self::getRequestData();
        $uriParts = explode('/', $uri);

        $resource = $uriParts[0] ?? null;
        $id = isset($uriParts[1]) ? (int)$uriParts[1] : null;

        switch ($method) {
            case 'POST':
                self::post($resource, $requestData);
                break;

            case 'GET':
                self::get($resource, $id);
                break;

            case 'PUT':
                self::put($resource, $id, $requestData);
                break;

            case 'PATCH':
                self::patch($resource, $id, $requestData);
                break;

            case 'DELETE':
                self::delete($resource, $id);
                break;

            default:
                Response::errorResponse('Unsupported method', 405);
        }
    }

    private static function getRequestData(): array
    {
        $json = json_decode(file_get_contents("php://input"), true) ?? [];
        return array_merge($_GET ?? [], $json);
    }

    private static function isRouteAllowed(string $method, string $uri): bool
    {
        if (!isset(self::$acceptedRoutes[$method])) {
            return false;
        }

        foreach (self::$acceptedRoutes[$method] as $route) {
            if (self::routeMatches($route, $uri)) {
                return true;
            }
        }

        return false;
    }

    private static function routeMatches(string $route, string $uri): bool
    {
        $routeParts = explode('/', trim($route, '/'));
        $uriParts = explode('/', trim($uri, '/'));

        if (count($routeParts) !== count($uriParts)) {
            return false;
        }

        foreach ($routeParts as $i => $part) {
            if (preg_match('/^{.*}$/', $part)) {
                continue;
            }

            if ($part !== $uriParts[$i]) {
                return false;
            }
        }

        return true;
    }

    private static function getRepository(string $resource): ?BaseRepository
    {
        return match ($resource) {
            'films' => new FilmRepository(),
            'directors' => new DirectorRepository(),
            'categories' => new CategoryRepository(),
            'stars' => new StarRepository(),
            'studio' => new StudioRepository(),
            default => null
        };
    }

    /* =========================
       POST
    ========================== */

    private static function post(string $resource, array $data): void
    {
        if ($resource === 'users') {
            self::handleUserPost($data);
            return;
        }

        if (!self::isAuthorized()) {
            Response::errorResponse("Unauthorized", 401);
            return;
        }

        $repository = self::getRepository($resource);
        if (!$repository) {
            Response::errorResponse("Repository not found", 404);
            return;
        }

        $id = $repository->create($data);

        if ($id) {
            Response::jsonResponse(['id' => $id], 201);
            return;
        }

        Response::errorResponse("Create failed", 400);
    }

    private static function handleUserPost(array $data): void
    {
        $uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

        if ($uri === 'users/login') {
            if (!empty($data['username'])) {
                $_SESSION['user'] = $data['username'];
                Response::jsonResponse(['message' => 'Logged in'], 200);
                return;
            }

            Response::errorResponse("Username required", 400);
            return;
        }

        if ($uri === 'users/logout') {
            session_destroy();
            Response::jsonResponse(['message' => 'Logged out'], 200);
            return;
        }
    }

    /* =========================
       GET
    ========================== */

    private static function get(string $resource, ?int $id): void
    {
        $repository = self::getRepository($resource);

        if (!$repository) {
            Response::errorResponse("Repository not found", 404);
            return;
        }

        if ($id) {
            $entity = $repository->find($id);

            if (!$entity) {
                Response::jsonResponse([], 404);
                return;
            }

            Response::jsonResponse($entity, 200);
            return;
        }

        Response::jsonResponse($repository->getAll(), 200);
    }

    /* =========================
       PUT (full update)
    ========================== */

    private static function put(string $resource, ?int $id, array $data): void
    {
        if (!self::isAuthorized()) {
            Response::errorResponse("Unauthorized", 401);
            return;
        }

        if (!$id) {
            Response::errorResponse("ID required", 400);
            return;
        }

        $repository = self::getRepository($resource);

        if (!$repository) {
            Response::errorResponse("Repository not found", 404);
            return;
        }

        $result = $repository->update($id, $data);

        if ($result) {
            Response::jsonResponse($result, 200);
            return;
        }

        Response::errorResponse("Update failed", 400);
    }

    /* =========================
       PATCH (partial update)
    ========================== */

    private static function patch(string $resource, ?int $id, array $data): void
    {
        self::put($resource, $id, $data); // same logic for simplicity
    }

    /* =========================
       DELETE
    ========================== */

    private static function delete(string $resource, ?int $id): void
    {
        if (!self::isAuthorized()) {
            Response::errorResponse("Unauthorized", 401);
            return;
        }

        if (!$id) {
            Response::errorResponse("ID required", 400);
            return;
        }

        $repository = self::getRepository($resource);

        if (!$repository) {
            Response::errorResponse("Repository not found", 404);
            return;
        }

        if ($repository->delete($id)) {
            Response::jsonResponse(['id' => $id], 200);
            return;
        }

        Response::errorResponse("Delete failed", 400);
    }

    private static function isAuthorized(): bool
    {
        return isset($_SESSION['user']);
    }
}