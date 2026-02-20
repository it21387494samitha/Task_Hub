<?php

namespace App\Livewire\Admin;

use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

/**
 * TeamManagement — Admin panel for managing teams.
 *
 * Features: list teams with stats, create, edit, delete teams,
 * view members.
 */
class TeamManagement extends Component
{
    // ─── Create Team Modal ───────────────────────────────
    public bool $showCreateModal = false;
    public string $newName = '';
    public string $newDescription = '';

    // ─── Edit Team Modal ─────────────────────────────────
    public bool $showEditModal = false;
    public ?int $editTeamId = null;
    public string $editName = '';
    public string $editDescription = '';

    // ─── Members View ────────────────────────────────────
    public ?int $viewingTeamId = null;

    // ─── Flash ───────────────────────────────────────────
    public string $successMessage = '';

    // ─── Actions ─────────────────────────────────────────

    public function openCreateModal(): void
    {
        $this->reset(['newName', 'newDescription']);
        $this->showCreateModal = true;
    }

    public function createTeam(): void
    {
        $this->validate([
            'newName' => 'required|string|max:255|unique:teams,name',
        ]);

        Team::create([
            'name'        => $this->newName,
            'description' => $this->newDescription ?: null,
            'created_by'  => Auth::id(),
        ]);

        $this->showCreateModal = false;
        $this->successMessage = "Team '{$this->newName}' created.";
        $this->dispatch('toast', message: $this->successMessage, type: 'success');
    }

    public function openEditModal(int $teamId): void
    {
        $team = Team::findOrFail($teamId);
        $this->editTeamId = $team->id;
        $this->editName = $team->name;
        $this->editDescription = $team->description ?? '';
        $this->showEditModal = true;
    }

    public function updateTeam(): void
    {
        $this->validate([
            'editName' => 'required|string|max:255|unique:teams,name,' . $this->editTeamId,
        ]);

        $team = Team::findOrFail($this->editTeamId);
        $team->update([
            'name'        => $this->editName,
            'description' => $this->editDescription ?: null,
        ]);

        $this->showEditModal = false;
        $this->successMessage = "Team '{$this->editName}' updated.";
        $this->dispatch('toast', message: $this->successMessage, type: 'success');
    }

    public function deleteTeam(int $teamId): void
    {
        $team = Team::findOrFail($teamId);
        $name = $team->name;

        // Unassign all members first
        User::where('team_id', $teamId)->update(['team_id' => null]);

        $team->delete();

        $this->successMessage = "Team '{$name}' deleted. Members unassigned.";
        $this->dispatch('toast', message: $this->successMessage, type: 'success');
    }

    public function viewMembers(?int $teamId): void
    {
        $this->viewingTeamId = $this->viewingTeamId === $teamId ? null : $teamId;
    }

    public function removeMember(int $userId): void
    {
        $user = User::findOrFail($userId);
        $user->update(['team_id' => null]);
        $this->successMessage = "'{$user->name}' removed from team.";
        $this->dispatch('toast', message: $this->successMessage, type: 'success');
    }

    public function render()
    {
        $teams = Team::withCount('members')
            ->with(['creator', 'members' => fn ($q) => $q->orderBy('name')])
            ->orderBy('name')
            ->get();

        // Get unassigned users for potential team assignment
        $unassignedUsers = User::whereNull('team_id')->where('is_active', true)->get();

        return view('livewire.admin.team-management', [
            'teams'           => $teams,
            'unassignedUsers' => $unassignedUsers,
        ]);
    }
}
