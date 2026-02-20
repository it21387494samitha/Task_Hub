<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Team extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'created_by',
    ];

    // ─── Boot ────────────────────────────────────────────

    protected static function boot(): void
    {
        parent::boot();

        // Auto-generate slug from name on creation
        static::creating(function (Team $team) {
            if (empty($team->slug)) {
                $team->slug = Str::slug($team->name);
            }
        });
    }

    // ─── Relationships ───────────────────────────────────

    /**
     * Members of this team.
     */
    public function members(): HasMany
    {
        return $this->hasMany(User::class, 'team_id');
    }

    /**
     * Active members of this team.
     */
    public function activeMembers(): HasMany
    {
        return $this->hasMany(User::class, 'team_id')->where('is_active', true);
    }

    /**
     * The user who created this team.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ─── Helper Methods ──────────────────────────────────

    /**
     * Get the team leader (first team_leader role in this team).
     */
    public function leader(): ?User
    {
        return $this->members()
            ->where('role', \App\Enums\Role::TEAM_LEADER)
            ->first();
    }

    /**
     * Get the count of open tasks for this team's members.
     */
    public function openTaskCount(): int
    {
        return Task::whereIn('assigned_to', $this->members()->pluck('id'))
            ->whereNotIn('status', [\App\Enums\TaskStatus::DONE])
            ->count();
    }
}
