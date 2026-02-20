<?php

/**
 * Notification System Test Script
 * Verifies: TaskAssigned, TaskUpdated, TaskDeleted notifications
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Task;
use App\Models\User;
use App\Services\TaskService;

$passed = 0;
$failed = 0;

function check(bool $condition, string $label): void {
    global $passed, $failed;
    if ($condition) {
        echo "  PASS: {$label}\n";
        $passed++;
    } else {
        echo "  FAIL: {$label}\n";
        $failed++;
    }
}

\Illuminate\Support\Facades\Artisan::call('migrate:fresh');
echo "=== Notification System Tests ===\n\n";

$admin = User::create(['name' => 'Admin', 'email' => 'admin@test.com', 'password' => bcrypt('pw'), 'role' => 'admin']);
$lead  = User::create(['name' => 'Lead',  'email' => 'lead@test.com',  'password' => bcrypt('pw'), 'role' => 'team_leader']);
$dev1  = User::create(['name' => 'Dev1',  'email' => 'dev1@test.com',  'password' => bcrypt('pw'), 'role' => 'developer']);
$dev2  = User::create(['name' => 'Dev2',  'email' => 'dev2@test.com',  'password' => bcrypt('pw'), 'role' => 'developer']);

$taskService = app(TaskService::class);

// ─── Test 1: Create task with assignee → assignee notified ─────
echo "=== Test 1: Task assigned on create ===\n";
$task = $taskService->createTask($lead, [
    'title' => 'Build API', 'priority' => 'high', 'status' => 'todo',
    'assigned_to' => $dev1->id, 'due_date' => now()->addDays(3)->format('Y-m-d'),
]);
$dev1->refresh();
check($dev1->unreadNotifications->count() === 1, "Dev1 has 1 unread");
$first = $dev1->unreadNotifications->first();
check($first->data['type'] === 'task_assigned', "Type = task_assigned");
check(str_contains($first->data['message'], 'Lead assigned you'), "Message has assigner");
check(str_contains($first->data['message'], 'Build API'), "Message has title");
check($first->data['task_id'] === $task->id, "Correct task_id");

$lead->refresh();
check($lead->unreadNotifications->count() === 0, "Lead (assigner) has 0 notifications");

// ─── Test 2: Self-assign → no notification ─────
echo "\n=== Test 2: Self-assign → no notification ===\n";
$taskService->createTask($lead, [
    'title' => 'Self Task', 'priority' => 'low', 'status' => 'todo', 'assigned_to' => $lead->id,
]);
$lead->refresh();
check($lead->unreadNotifications->count() === 0, "Lead has 0 (self-assign)");

// ─── Test 3: Task updated → assignee notified ─────
echo "\n=== Test 3: Task updated → assignee notified ===\n";
$dev1->notifications()->delete();
$dev1->refresh();
$taskService->updateTask($lead, $task, ['status' => 'in_progress']);
$dev1->refresh();
check($dev1->unreadNotifications->count() === 1, "Dev1 has 1 after update");
$n = $dev1->unreadNotifications->first();
check($n->data['type'] === 'task_updated', "Type = task_updated");
check(str_contains($n->data['message'], 'Lead updated'), "Message mentions updater");
check(isset($n->data['changed_fields']['status']), "Changed fields has status");

// ─── Test 4: Self-update → no notification ─────
echo "\n=== Test 4: Self-update → no notification ===\n";
$dev1->notifications()->delete();
$dev1->refresh();
$taskService->updateTask($dev1, $task, ['status' => 'done']);
$dev1->refresh();
check($dev1->unreadNotifications->count() === 0, "Dev1 has 0 (self-update)");

// ─── Test 5: Task deleted → assignee notified ─────
echo "\n=== Test 5: Task deleted → assignee notified ===\n";
$task->update(['status' => 'todo']);
$dev1->notifications()->delete();
$dev1->refresh();
$taskService->deleteTask($admin, $task);
$dev1->refresh();
check($dev1->unreadNotifications->count() === 1, "Dev1 has 1 after delete");
$n = $dev1->unreadNotifications->first();
check($n->data['type'] === 'task_deleted', "Type = task_deleted");
check(str_contains($n->data['message'], 'Admin deleted'), "Message mentions deleter");

// ─── Test 6: Mark as read ─────
echo "\n=== Test 6: Mark as read ===\n";
$nid = $dev1->unreadNotifications->first()->id;
$dev1->unreadNotifications->where('id', $nid)->first()->markAsRead();
$dev1->refresh();
check($dev1->unreadNotifications->count() === 0, "0 unread after markAsRead");
check($dev1->notifications->count() === 1, "Total still 1 (read)");

// ─── Test 7: Mark all as read ─────
echo "\n=== Test 7: Mark all as read ===\n";
$taskService->createTask($lead, ['title' => 'A', 'priority' => 'high', 'status' => 'todo', 'assigned_to' => $dev2->id]);
$taskService->createTask($lead, ['title' => 'B', 'priority' => 'medium', 'status' => 'todo', 'assigned_to' => $dev2->id]);
$dev2->refresh();
check($dev2->unreadNotifications->count() === 2, "Dev2 has 2 unread");
$dev2->unreadNotifications->markAsRead();
$dev2->refresh();
check($dev2->unreadNotifications->count() === 0, "0 after markAllAsRead");
check($dev2->notifications->count() === 2, "Total still 2");

// ─── Test 8: Data structure ─────
echo "\n=== Test 8: Data structure ===\n";
$dev2->notifications()->delete();
$task5 = $taskService->createTask($lead, [
    'title' => 'Structured', 'priority' => 'critical', 'status' => 'todo',
    'assigned_to' => $dev2->id, 'due_date' => now()->addDay()->format('Y-m-d'),
]);
$dev2->refresh();
$data = $dev2->unreadNotifications->first()->data;
check(isset($data['type']), "Has type");
check(isset($data['task_id']), "Has task_id");
check(isset($data['task_title']), "Has task_title");
check(isset($data['assigner_name']), "Has assigner_name");
check(isset($data['message']), "Has message");
check($data['task_title'] === 'Structured', "task_title matches");

// ─── Test 9: Reassign → new assignee notified ─────
echo "\n=== Test 9: Reassign task ===\n";
$dev1->notifications()->delete();
$dev2->notifications()->delete();
$taskService->assignTask($lead, $task5, $dev1);
$dev1->refresh();
$dev2->refresh();
check($dev1->unreadNotifications->count() === 1, "Dev1 (new) has 1");
check($dev2->unreadNotifications->count() === 0, "Dev2 (old) has 0");
check($dev1->unreadNotifications->first()->data['type'] === 'task_assigned', "Reassign type correct");

// ─── Summary ─────
echo "\n================================\n";
echo "Results: {$passed} passed, {$failed} failed\n";

unlink(__FILE__);
exit($failed > 0 ? 1 : 0);
