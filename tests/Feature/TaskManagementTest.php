<?php

use Illuminate\Support\Facades\Storage;

test('a visitor can create, edit, complete, and delete tasks without a database', function () {
    Storage::fake('local');

    $this->get('/')
        ->assertOk()
        ->assertSee('A fresh start');

    $this->get(route('tasks.index'))
        ->assertOk()
        ->assertSee('Small steps.')
        ->assertSee('A fresh start');

    $this->post(route('tasks.store'), [
        'title' => 'Plan the project demo',
        'description' => 'Prepare a short walkthrough.',
    ])->assertRedirect(route('tasks.index'));

    $tasks = json_decode(Storage::disk('local')->get('tasks.json'), true, 512, JSON_THROW_ON_ERROR);
    expect($tasks)->toHaveCount(1);
    $task = array_values($tasks)[0];
    expect($task['completed'])->toBeFalse();

    $this->get(route('tasks.index'))
        ->assertOk()
        ->assertSee('Plan the project demo')
        ->assertSee('Prepare a short walkthrough.');

    $this->get(route('tasks.edit', $task['id']))
        ->assertOk()
        ->assertSee('Save changes');

    $this->put(route('tasks.update', $task['id']), [
        'title' => 'Present the project demo',
        'description' => 'Keep it under five minutes.',
    ])->assertRedirect(route('tasks.index'));

    $this->patch(route('tasks.toggle', $task['id']))
        ->assertRedirect(route('tasks.index'));

    $tasks = json_decode(Storage::disk('local')->get('tasks.json'), true, 512, JSON_THROW_ON_ERROR);
    $task = array_values($tasks)[0];
    expect($task['title'])->toBe('Present the project demo')
        ->and($task['description'])->toBe('Keep it under five minutes.')
        ->and($task['completed'])->toBeTrue();

    $this->delete(route('tasks.destroy', $task['id']))
        ->assertRedirect(route('tasks.index'));

    expect(json_decode(Storage::disk('local')->get('tasks.json'), true, 512, JSON_THROW_ON_ERROR))
        ->toBe([]);
});

test('task titles are required and limited to 120 characters', function () {
    Storage::fake('local');

    $this->from(route('tasks.index'))
        ->post(route('tasks.store'), ['title' => str_repeat('a', 121)])
        ->assertRedirect(route('tasks.index'))
        ->assertSessionHasErrors(['title']);

    expect(Storage::disk('local')->exists('tasks.json'))->toBeFalse();
});
