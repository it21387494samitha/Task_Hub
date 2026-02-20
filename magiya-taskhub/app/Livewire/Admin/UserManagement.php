<?php

namespace App\Livewire\Admin;

use App\Enums\Role;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

/**
 * UserManagement — Admin panel for managing users.
 *
 * Features: list all users, toggle active/inactive, change roles,
 * assign teams, create new users.
 */
class UserManagement extends Component
{
    // ─── Filters ─────────────────────────────────────────
    public string $search = '';
    public string $roleFilter = '';
    public string $teamFilter = '';
    public string $statusFilter = '';

    // ─── Create User Modal ───────────────────────────────
    public bool $showCreateModal = false;
    public string $newName = '';
    public string $newEmail = '';
    public string $newPassword = 'password';
    public string $newRole = 'developer';
    public string $newTeamId = '';

    // ─── Edit User Modal ─────────────────────────────────
    public bool $showEditModal = false;
    public ?int $editUserId = null;
    public string $editName = '';
    public string $editEmail = '';
    public string $editRole = '';
    public string $editTeamId = '';

    // ─── Flash ───────────────────────────────────────────
    public string $successMessage = '';

    // ─── Actions ─────────────────────────────────────────

    public function toggleActive(int $userId): void
    {
        $user = User::findOrFail($userId);

        // Can't deactivate yourself
        if ($user->id === Auth::id()) {
            return;
        }

        $user->update(['is_active' => ! $user->is_active]);
        $this->successMessage = $user->is_active
            ? "'{$user->name}' has been activated."
            : "'{$user->name}' has been deactivated.";
        $this->dispatch('toast', message: $this->successMessage, type: 'success');
    }

    public function changeRole(int $userId, string $role): void
    {
        $user = User::findOrFail($userId);
        $roleEnum = Role::tryFrom($role);

        if (! $roleEnum || $user->id === Auth::id()) {
            return;
        }

        $user->update(['role' => $roleEnum]);
        $this->successMessage = "'{$user->name}' role changed to {$roleEnum->label()}.";
        $this->dispatch('toast', message: $this->successMessage, type: 'success');
    }

    public function assignTeam(int $userId, string $teamId): void
    {
        $user = User::findOrFail($userId);
        $user->update(['team_id' => $teamId ?: null]);
        $teamName = $teamId ? Team::find($teamId)?->name : 'None';
        $this->successMessage = "'{$user->name}' assigned to team: {$teamName}.";
        $this->dispatch('toast', message: $this->successMessage, type: 'success');
    }

    public function openCreateModal(): void
    {
        $this->reset(['newName', 'newEmail', 'newRole', 'newTeamId']);
        $this->newPassword = 'password';
        $this->newRole = 'developer';
        $this->showCreateModal = true;
    }

    public function createUser(): void
    {
        $this->validate([
            'newName'  => 'required|string|max:255',
            'newEmail' => 'required|email|unique:users,email',
            'newRole'  => 'required|in:admin,team_leader,developer',
        ]);

        User::create([
            'name'     => $this->newName,
            'email'    => $this->newEmail,
            'password' => Hash::make($this->newPassword),
            'role'     => Role::from($this->newRole),
            'team_id'  => $this->newTeamId ?: null,
        ]);

        $this->showCreateModal = false;
        $this->successMessage = "User '{$this->newName}' created successfully.";
        $this->dispatch('toast', message: $this->successMessage, type: 'success');
    }

    public function openEditModal(int $userId): void
    {
        $user = User::findOrFail($userId);
        $this->editUserId = $user->id;
        $this->editName = $user->name;
        $this->editEmail = $user->email;
        $this->editRole = $user->role->value;
        $this->editTeamId = (string) ($user->team_id ?? '');
        $this->showEditModal = true;
    }

    public function updateUser(): void
    {
        $this->validate([
            'editName'  => 'required|string|max:255',
            'editEmail' => 'required|email|unique:users,email,' . $this->editUserId,
            'editRole'  => 'required|in:admin,team_leader,developer',
        ]);

        $user = User::findOrFail($this->editUserId);
        $user->update([
            'name'    => $this->editName,
            'email'   => $this->editEmail,
            'role'    => Role::from($this->editRole),
            'team_id' => $this->editTeamId ?: null,
        ]);

        $this->showEditModal = false;
        $this->successMessage = "User '{$this->editName}' updated.";
        $this->dispatch('toast', message: $this->successMessage, type: 'success');
    }

    public function render()
    {
        $query = User::query()->with('team');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%");
            });
        }
        if ($this->roleFilter) {
            $query->where('role', $this->roleFilter);
        }
        if ($this->teamFilter) {
            $query->where('team_id', $this->teamFilter);
        }
        if ($this->statusFilter === 'active') {
            $query->where('is_active', true);
        } elseif ($this->statusFilter === 'inactive') {
            $query->where('is_active', false);
        }

        $users = $query->orderBy('name')->get();

        return view('livewire.admin.user-management', [
            'users' => $users,
            'teams' => Team::all(),
            'roles' => Role::cases(),
        ]);
    }
}
