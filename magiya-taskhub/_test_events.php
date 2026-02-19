<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Enums\Role;
use App\Models\Task;
use App\Models\User;
use App\Services\TaskService;

$pass = 0;
$fail = 0;

function test(string $label, bool $result): void {
    global $pass, $fail;
    if ($result) { echo "  PASS: {$label}\n"; $pass++; }
    else         { echo "  FAIL: {$label}\n"; $fail++; }
}

$logFile = storage_path('logs/laravel.log');
file_put_contents($logFile, '');

$service = app(TaskService::class);

$admin = User::where('email', 'admin@taskhub.test')->first();
$lead  = User::where('email', 'lead@taskhub.test')->first();
$dev1  = User::where('email', 'alice@taskhub.test')->first();
$dev2  = User::where('email', 'bob@taskhub.test')->first();

echo "=== Test 1: TaskCreated event ===\n";
$task = $service->createTask($lead, [
    'title' => 'Event Test Task',
    'priority' => 'high',
    'status' => 'todo',
    'assigned_to' => $dev1->id,
]);
$log = file_get_contents($logFile);
test('TaskCreated event logged', str_contains($log, 'Task created'));
test('Log contains task title', str_contains($log, 'Event Test Task'));
test('Log contains creator name', str_contains($log, $lead->name));

echo "\n=== Test 2: TaskUpdated event (status change) ===\n";
$service->updateTask($lead, $task, [
    'title' => 'Event Test Task',
    'status' => 'in_progress',
    'priority' => 'high',
]);
$log = file_get_contents($logFile);
test('TaskUpdated event logged', str_contains($log, 'Task updated'));
test('Log shows changed fields', str_contains($log, 'changed_fields'));

echo "\n=== Test 3: No event when nothing changed ===\n";
file_put_contents($logFile, '');
$service->updateTask($lead, $task->fresh(), [
    'title' => 'Event Test Task',
    'status' => 'in_progress',
    'priority' => 'high',
]);
$log = file_get_contents($logFile);
test('No TaskUpdated when nothing changed', !str_contains($log, 'Task updated'));

echo "\n=== Test 4: TaskAssigned event ===\n";
file_put_contents($logFile, '');
$service->assignTask($lead, $task->fresh(), $dev2);
$log = file_get_contents($logFile);
test('TaskAssigned event logged', str_contains($log, 'Task assigned'));
test('Log shows new assignee', str_contains($log, $dev2->name));

echo "\n=== Test 5: TaskDeleted event ===\n";
file_put_contents($logFile, '');
$service->deleteTask($admin, $task->fresh());
$log = file_get_contents($logFile);
test('TaskDeleted event logged', str_contains($log, 'Task deleted'));
test('Log shows who deleted', str_contains($log, $admin->name));

echo "\n================================\n";
echo "Results: {$pass} passed, {$fail} failed\n";

unlink(__FILE__);
