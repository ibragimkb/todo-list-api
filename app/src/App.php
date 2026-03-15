<?php declare(strict_types=1);

namespace App;

use App\Route\Router;
use App\Controller\TaskController;
use App\Core\Response;
use Exception;

class App {
    public function run(): void {
        try {
            $router = new Router();
            $tasks  = new TaskController();

            $router->add('GET', '/tasks', [$tasks, 'index']);
            $router->add('GET', '/tasks/(\d+)', [$tasks, 'show']);
            $router->add('POST', '/tasks', [$tasks, 'create']);
            $router->add('PUT', '/tasks/(\d+)', [$tasks, 'update']);
            $router->add('DELETE', '/tasks/(\d+)', [$tasks, 'delete']);

            $method = $_SERVER['REQUEST_METHOD'];
            $uri    = strtok($_SERVER['REQUEST_URI'], '?');

            $router->dispatch($method, $uri);
        } catch (Exception $e) {
            Response::error($e->getMessage());   
        }
    }
}
