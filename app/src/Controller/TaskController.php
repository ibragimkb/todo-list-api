<?php declare(strict_types=1);

namespace App\Controller;

use App\Repository\TaskRepository;
use App\Core\Request;
use App\Core\Response;
use App\Validation\Validator;


class TaskController
{
    protected const INPUT_FIELDS = ['title', 'description', 'status'];
    protected TaskRepository $repo;

    public function __construct()
    {
        $this->repo = new TaskRepository();
    }

    public function index(): void
    {
        $tasks = $this->repo->getAllTasks();
        Response::json($tasks);
    }

    public function show(int $id): void
    {
        $task = $this->repo->getById($id);
        if (!$task) {
            Response::error('Task not found', 404);
            return;
        }

        Response::json($task);
    }

    public function create(): void
    {
        $request = new Request();

        $data = [];
        $rules = [];
        foreach (self::INPUT_FIELDS as $key) {
            $rule = $this->getRule($key);
            if (is_null($rule)) {
                continue;
            }
            $data[$key] = $request->get($key);
            $rules[$key] = $rule;
        }

        $validator = new Validator($data, $rules);

        $validator->validateOrFail();

        $id = $this->repo->create(
            $request->get('title'),
            $request->get('description'),
            $request->get('status')
        );

        Response::success('Task created', ['id' => $id], 201);
    }

    public function update(int $id): void
    {
        $task = $this->repo->getById($id);
        if (!$task) {
            Response::error('Task not found', 404);
            return;
        }

        $request = new Request();

        $data = [];
        $rules = [];
        foreach (self::INPUT_FIELDS as $key) {
            $rule = $this->getRule($key);
            if (is_null($rule)) {
                continue;
            }
            $value = $request->get($key);
            if (is_null($value)) {
                continue;
            }
            $data[$key] = $value;
            $rules[$key] = $rule;
        }

        $validator = new Validator($data, $rules);

        $validator->validateOrFail();

        $this->repo->update($id, $data);
        Response::success('Task updated', ['id' => $id], 201);
    }

    public function delete(int $id): void
    {

        $task = $this->repo->getById($id);
        if (!$task) {
            Response::error('Task not found', 404);
            return;
        }

        $this->repo->delete($id);
        Response::success('Task deleted', ['id' => $id], 201);
    }

    protected function getRule(string $key): ?string
    {
        return match($key) {
            'title'       => 'required|string|min:3|max:255',
            'description' => 'string',
            'status'      => 'required|string|in:pending,in_progress,done,archived',
            default => null
        };
    }
}

