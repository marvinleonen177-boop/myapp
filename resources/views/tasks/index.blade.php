<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f6f7f3">
    <title>Daylight — your to-do list</title>
    <style>
        :root {
            color-scheme: light;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: #242a24;
            background: #f6f7f3;
            font-synthesis: none;
            text-rendering: optimizeLegibility;
        }
        * { box-sizing: border-box; }
        body { margin: 0; min-width: 320px; }
        button, input, textarea { font: inherit; }
        button, a { -webkit-tap-highlight-color: transparent; }
        .sr-only { position: absolute; width: 1px; height: 1px; overflow: hidden; clip: rect(0, 0, 0, 0); white-space: nowrap; clip-path: inset(50%); }
        .page { width: min(100% - 40px, 760px); margin: 0 auto; padding: 48px 0 64px; }
        .topbar { display: flex; align-items: center; justify-content: space-between; margin-bottom: 60px; }
        .brand { display: inline-flex; gap: 10px; align-items: center; color: inherit; font-size: 15px; font-weight: 750; letter-spacing: -.03em; text-decoration: none; }
        .brand-mark { display: grid; width: 30px; height: 30px; place-items: center; border-radius: 10px; background: #526b4c; color: #fff; font-size: 17px; }
        .local-note { color: #858b82; font-size: 12px; }
        .eyebrow { margin: 0 0 12px; color: #74846e; font-size: 11px; font-weight: 750; letter-spacing: .15em; text-transform: uppercase; }
        h1 { margin: 0; font-size: clamp(34px, 7vw, 52px); font-weight: 650; letter-spacing: -.065em; line-height: 1.08; }
        .intro { max-width: 520px; margin: 14px 0 32px; color: #7a8078; font-size: 15px; line-height: 1.65; }
        .composer, .task-list { border: 1px solid #e8ebe4; border-radius: 16px; background: #fff; box-shadow: 0 10px 30px rgb(34 44 31 / 4%); }
        .composer { padding: 20px; }
        .field { display: block; width: 100%; border: 1px solid #e7eae3; border-radius: 10px; outline: none; background: #fcfdfa; color: #242a24; transition: border-color .15s, box-shadow .15s; }
        .field:focus { border-color: #83977d; box-shadow: 0 0 0 3px rgb(113 139 105 / 13%); }
        .title-field { padding: 12px 14px; font-size: 14px; }
        .description-field { min-height: 70px; margin-top: 10px; padding: 11px 14px; resize: vertical; font-size: 13px; }
        .form-footer { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-top: 12px; }
        .hint { color: #9a9f97; font-size: 11px; }
        .primary-button, .quiet-button, .delete-button { min-height: 38px; border: 0; border-radius: 9px; cursor: pointer; font-size: 12px; font-weight: 700; transition: background .15s, color .15s, transform .15s; }
        .primary-button { padding: 0 16px; background: #526b4c; color: #fff; }
        .primary-button:hover { background: #435b3d; transform: translateY(-1px); }
        .quiet-button { padding: 0 13px; background: #eff2ec; color: #596653; }
        .quiet-button:hover { background: #e5e9e1; }
        .delete-button { padding: 0 10px; background: transparent; color: #a26f68; }
        .delete-button:hover { background: #fbf1ef; color: #8c4f47; }
        .alert { margin: 16px 0 0; border-radius: 10px; padding: 11px 14px; background: #edf4e9; color: #4c6847; font-size: 13px; }
        .alert-error { background: #fbf1ef; color: #925249; }
        .alert-error ul { margin: 0; padding-left: 18px; }
        .list-heading { display: flex; align-items: end; justify-content: space-between; gap: 16px; margin: 42px 2px 14px; }
        .list-heading h2 { margin: 0; font-size: 17px; font-weight: 700; letter-spacing: -.03em; }
        .count { color: #92988f; font-size: 12px; }
        .task-list { overflow: hidden; }
        .task-row { display: flex; align-items: flex-start; gap: 13px; padding: 18px 20px; }
        .task-row + .task-row { border-top: 1px solid #eef0eb; }
        .check-form { padding-top: 1px; }
        .check-button { display: grid; width: 20px; height: 20px; place-items: center; border: 1.5px solid #cbd1c7; border-radius: 50%; background: #fff; color: transparent; cursor: pointer; }
        .check-button:hover { border-color: #71856a; }
        .is-complete .check-button { border-color: #71856a; background: #71856a; color: #fff; }
        .checkmark { font-size: 12px; font-weight: 800; line-height: 1; }
        .task-content { min-width: 0; flex: 1; }
        .task-title { margin: 0; overflow-wrap: anywhere; font-size: 14px; font-weight: 650; line-height: 1.5; }
        .is-complete .task-title { color: #9a9f97; text-decoration: line-through; }
        .task-description { margin: 4px 0 0; color: #8a9087; font-size: 12px; line-height: 1.55; white-space: pre-wrap; overflow-wrap: anywhere; }
        .task-actions { display: flex; align-items: center; gap: 2px; margin: -7px -9px 0 0; }
        .action-link { padding: 8px; color: #92988f; font-size: 11px; font-weight: 650; text-decoration: none; }
        .action-link:hover { color: #526b4c; }
        .edit-form { display: grid; width: 100%; gap: 9px; }
        .edit-actions { display: flex; justify-content: flex-end; gap: 8px; }
        .empty-state { padding: 40px 20px; text-align: center; }
        .empty-icon { display: grid; width: 42px; height: 42px; margin: 0 auto 13px; place-items: center; border-radius: 14px; background: #f0f3ed; color: #64785e; font-size: 19px; }
        .empty-state h3 { margin: 0; font-size: 14px; }
        .empty-state p { margin: 7px 0 0; color: #92988f; font-size: 12px; }
        .footer { margin-top: 28px; color: #9a9f97; font-size: 11px; text-align: center; }
        @media (max-width: 520px) {
            .page { width: min(100% - 28px, 760px); padding-top: 26px; }
            .topbar { margin-bottom: 45px; }
            .local-note { max-width: 125px; text-align: right; }
            .composer { padding: 14px; }
            .task-row { padding: 16px 14px; }
            .task-actions { flex-direction: column; align-items: flex-end; }
            .action-link { padding: 5px 8px; }
            .hint { max-width: 145px; line-height: 1.4; }
        }
    </style>
</head>
<body>
    @php
        $completedCount = count(array_filter($tasks, fn (array $task): bool => $task['completed']));
        $taskCount = count($tasks);
    @endphp
    <main class="page">
        <header class="topbar">
            <a class="brand" href="{{ route('tasks.index') }}">
                <span class="brand-mark" aria-hidden="true">d</span>
                <span>daylight</span>
            </a>
            <span class="local-note">A little more focus, one task at a time.</span>
        </header>

        <section aria-labelledby="page-title">
            <p class="eyebrow">Your personal task list</p>
            <h1 id="page-title">Small steps.<br>Big momentum.</h1>
            <p class="intro">Get your thoughts out of your head and into a calmer plan. Add a task, then take it one step at a time.</p>

            <form class="composer" action="{{ route('tasks.store') }}" method="POST">
                @csrf
                <label class="sr-only" for="new-title">Task title</label>
                <input class="field title-field" id="new-title" type="text" name="title" maxlength="120" placeholder="What needs to get done?" value="{{ old('title') }}" required>
                <label class="sr-only" for="new-description">Task details (optional)</label>
                <textarea class="field description-field" id="new-description" name="description" maxlength="500" placeholder="Add a few details if you like...">{{ old('description') }}</textarea>
                <div class="form-footer">
                    <span class="hint">Keep it simple. You can always edit it later.</span>
                    <button class="primary-button" type="submit">Add task <span aria-hidden="true">+</span></button>
                </div>
            </form>

            @if (session('status'))
                <p class="alert" role="status">{{ session('status') }}</p>
            @endif

            @if ($errors->any())
                <div class="alert alert-error" role="alert">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </section>

        <section aria-labelledby="tasks-heading">
            <div class="list-heading">
                <h2 id="tasks-heading">Your tasks</h2>
                <span class="count">{{ $completedCount }} of {{ $taskCount }} completed</span>
            </div>

            <div class="task-list">
                @forelse ($tasks as $task)
                    @if ($editingTask && $editingTask['id'] === $task['id'])
                        <article class="task-row">
                            <form class="edit-form" action="{{ route('tasks.update', $task['id']) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <label class="sr-only" for="edit-title">Task title</label>
                                <input class="field title-field" id="edit-title" type="text" name="title" maxlength="120" value="{{ old('title', $task['title']) }}" required>
                                <label class="sr-only" for="edit-description">Task details (optional)</label>
                                <textarea class="field description-field" id="edit-description" name="description" maxlength="500">{{ old('description', $task['description']) }}</textarea>
                                <div class="edit-actions">
                                    <a class="action-link" href="{{ route('tasks.index') }}">Cancel</a>
                                    <button class="primary-button" type="submit">Save changes</button>
                                </div>
                            </form>
                        </article>
                    @else
                        <article class="task-row {{ $task['completed'] ? 'is-complete' : '' }}">
                            <form class="check-form" action="{{ route('tasks.toggle', $task['id']) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button class="check-button" type="submit" aria-label="{{ $task['completed'] ? 'Mark '.$task['title'].' as incomplete' : 'Mark '.$task['title'].' as complete' }}">
                                    <span class="checkmark" aria-hidden="true">✓</span>
                                </button>
                            </form>
                            <div class="task-content">
                                <h3 class="task-title">{{ $task['title'] }}</h3>
                                @if ($task['description'] !== '')
                                    <p class="task-description">{{ $task['description'] }}</p>
                                @endif
                            </div>
                            <div class="task-actions">
                                <a class="action-link" href="{{ route('tasks.edit', $task['id']) }}">Edit</a>
                                <form action="{{ route('tasks.destroy', $task['id']) }}" method="POST" onsubmit="return confirm('Delete this task?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="delete-button" type="submit">Delete</button>
                                </form>
                            </div>
                        </article>
                    @endif
                @empty
                    <div class="empty-state">
                        <span class="empty-icon" aria-hidden="true">+</span>
                        <h3>A fresh start</h3>
                        <p>Your list is clear. Add a task above when you’re ready.</p>
                    </div>
                @endforelse
            </div>
        </section>

        <footer class="footer">Your tasks are saved privately on this device.</footer>
    </main>
</body>
</html>
