<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    /**
     * Activity Logs table — the audit trail for every important action.
     *
     * MERN comparison:
     *   Like a MongoDB "audit" collection with polymorphic references.
     *   In Mongoose you'd store { subjectModel: 'Task', subjectId: '...' }.
     *   Laravel uses subject_type + subject_id (morph columns) for the same thing.
     *
     * Schema design:
     *   user_id      → WHO did it
     *   action       → WHAT they did (created, updated, deleted, assigned)
     *   subject_type → WHICH model (App\Models\Task, App\Models\User, etc.)
     *   subject_id   → WHICH record
     *   changes      → JSON diff of old/new values  { "status": {"old":"todo","new":"done"} }
     *   description  → Human-readable summary
     */
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action');                    // created, updated, deleted, assigned
            $table->string('subject_type');               // Morph: App\Models\Task
            $table->unsignedBigInteger('subject_id');     // Morph: task id
            $table->json('changes')->nullable();          // {"field": {"old": x, "new": y}}
            $table->string('description');                // "Team Leader created task: Fix login bug"
            $table->timestamps();

            // Indexes for fast queries
            $table->index(['subject_type', 'subject_id']); // Find all logs for a specific model
            $table->index('user_id');                       // Find all logs by a user
            $table->index('action');                        // Filter by action type
            $table->index('created_at');                    // Sort by newest first
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
