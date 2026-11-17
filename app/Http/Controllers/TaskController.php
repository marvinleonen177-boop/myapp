<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use RuntimeException;

class TaskController extends Controller
{
    private const string FILE = 'tasks.json';

    public function index(): View
    {
        return view('tasks.index', [
            'tasks' => array_values($this->readTasks()),
            'editingTask' => null,
        ]);
    }

    public function edit(string $task): View
    {
        $tasks = $this->readTasks();
        abort_unless(isset($tasks[$task]), 404);

        return view('tasks.index', [
            'tasks' => array_values($tasks),
            'editingTask' => $tasks[$task],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $tasks = $this->readTasks();
        $id = Str::uuid()->toString();
        $tasks[$id] = [
            'id' => $id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? '',
            'completed' => false,
        ];

        $this->writeTasks($tasks);

        return to_route('tasks.index')->with('status', 'Task added to your list.');
    }

    public function update(Request $request, string $task): RedirectResponse
    {
        $tasks = $this->readTasks();
        abort_unless(isset($tasks[$task]), 404);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $tasks[$task]['title'] = $validated['title'];
        $tasks[$task]['description'] = $validated['description'] ?? '';

        $this->writeTasks($tasks);

        return to_route('tasks.index')->with('status', 'Task updated.');
    }

    public function toggle(string $task): RedirectResponse
    {
        $tasks = $this->readTasks();
        abort_unless(isset($tasks[$task]), 404);

        $tasks[$task]['completed'] = ! $tasks[$task]['completed'];
        $this->writeTasks($tasks);

        return to_route('tasks.index')->with('status', 'Task progress updated.');
    }

    public function destroy(string $task): RedirectResponse
    {
        $tasks = $this->readTasks();
        abort_unless(isset($tasks[$task]), 404);

        unset($tasks[$task]);
        $this->writeTasks($tasks);

        return to_route('tasks.index')->with('status', 'Task deleted.');
    }

    /**
     * @return array<string, array{id: string, title: string, description: string, completed: bool}>
     */
    private function readTasks(): array
    {
        $disk = Storage::disk('local');

        if (! $disk->exists(self::FILE)) {
            return [];
        }

        $tasks = json_decode($disk->get(self::FILE), true, 512, JSON_THROW_ON_ERROR);

        if (! is_array($tasks)) {
            throw new RuntimeException('The task file must contain a JSON object.');
        }

        foreach ($tasks as $id => $task) {
            if (
                ! is_string($id)
                || ! is_array($task)
                || ($task['id'] ?? null) !== $id
                || ! is_string($task['title'] ?? null)
                || ! is_string($task['description'] ?? null)
                || ! is_bool($task['completed'] ?? null)
            ) {
                throw new RuntimeException('The task file contains an invalid task.');
            }
        }

        return $tasks;
    }

    /**
     * @param  array<string, array{id: string, title: string, description: string, completed: bool}>  $tasks
     */
    private function writeTasks(array $tasks): void
    {
        $saved = Storage::disk('local')->put(
            self::FILE,
            json_encode($tasks, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
        );

        if (! $saved) {
            throw new RuntimeException('Unable to save the task file.');
        }
    }
}
