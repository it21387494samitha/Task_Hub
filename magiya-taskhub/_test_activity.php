<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\ActivityLog;
use App\Models\Task;
use App\Models\User;
use App\Services\ActivityService;
use App\Services\TaskService;

$pass = 0;
$fail = 0;

function test(string $label, bool $result, string $extra = ''): void {
    global $pass, $fail;
    if ($result) { echo "  PASS: {$label}\n"; $pass++; }
    else         { echo "  FAIL: {$label} {$extra}\n"; $fail++; }
}

$taskService = app(TaskService::class);
$activityService = app(ActivityService::class);

$admin = User::where('email', 'admin@taskhub.test')->first();
$lead  = User::where('email', 'lead@taskhub.test')->first();
$dev1  = User::where('email', 'alice@taskhub.test')->first();
$dev2  = User::where('email', 'bob@taskhub.test')->first();

ActivityLog::truncate();

echo "=== Test 1: Task creation ===\n";
$task = $taskService->createTask($lead, [
    'title' => 'Audit Trail Test',
    'priority' => 'high',
    'status' => 'todo',
    'assigned_to' => $dev1->id,
]);
$log = ActivityLog::latest('id')->first();
test('Activity log created', $log !== null);
test('Action is "created"', $log->action === 'created');
test('User is lead', $log->user_id === $lead->id);
test('Subject is Task', $log->subject_type === Task::class);
test('Subject ID matches', (int) $log->subject_id === $task->id);
test('Description OK', str_contains($log->description, 'Audit Trail Test'));

echo "\n=== Test 2: Task update with diff ===\n";
$taskService->updateTask($lead, $task, [
    'title' => 'Audit Trail Test',
    'status' => 'in_progress',
    'priority' => 'critical',
]);
$log = ActivityLog::latest('id')->first();
test('Update logged', $log->action === 'updated');
test('Changes JSON stored', $log->changes !== null);
test('Status old=todo', ($log->changes['status']['old'] ?? '') === 'todo');
test('Status new=in_progress', ($log->changes['status']['new'] ?? '') === 'in_progress');
test('Priority changed too', isset($log->changes['priority']));

echo "\n=== Test 3: Task assignment ===\n";
$taskService->assignTask($lead, $task->fresh(), $dev2);
$log = ActivityLog::latest('id')->first();
test('Assign logged', $log->action === 'assigned');
test('Previous assignee', ($log->changes['assigned_to']['old'] ?? '') === $dev1->name);
test('New assignee', ($log->changes['assigned_to']['new'] ?? '') === $dev2->name);

echo "\n=== Test 4: Task deletion ===\n";
$taskService->deleteTask($admin, $task->fresh());
$log = ActivityLog::latest('id')->first();
test('Delete logged', $log->action === 'deleted');
test('Deleted by admin', $log->user_id === $admin->id);

$totalLogs = ActivityLog::count();
echo "\n=== Test 5: Queries (total: {$totalLogs}) ===\n";
$recent = $activityService->getRecent(10);
test('getRecent count = 4', $recent->count() === 4, "(got {$recent->count()})");
test('Newest first', $recent->first()->action === 'deleted');
test('Oldest last', $recent->last()->action === 'created');

echo "\n=== Test 6: getForSubject ===\n";
$taskLogs = $activityService->getForSubject($task);
test('4 logs for this task', $taskLogs->count() === 4, "(got {$taskLogs->count()})");

echo "\n=== Test 7: Model scopes ===\n";
test('forModel = 4', ActivityLog::forModel(Task::class)->count() === 4);
test('byUser lead = 3', ActivityLog::byUser($lead)->count() === 3);
test('ofAction created = 1', ActivityLog::ofAction('created')->count() === 1);

echo "\n=== Test 8: Polymorphic ===\n";
$createdLog = ActivityLog::ofAction('created')->first();
test('subject() loads soft-deleted Task', $createdLog->subject instanceof Task);
test('user() loads User', $createdLog->user instanceof User);

echo "\n================================\n";
echo "Results: {$pass} passed, {$fail} failed\n";

unlink(__FILE__);
