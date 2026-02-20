<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

/**
 * NotificationBell — a small Livewire component embedded in the navbar.
 *
 * MERN comparison:
 *   In React you'd have a <NotificationBell /> component that fetches
 *   from GET /api/notifications and shows a badge with the count.
 *   Clicking it opens a dropdown, and clicking a notification fires
 *   PATCH /api/notifications/:id/read.
 *
 *   In Livewire the pattern is the same, but the "API calls" are
 *   wire:click="markAsRead('id')" — Livewire sends an AJAX request
 *   behind the scenes and re-renders the component.
 *
 * Polling:
 *   wire:poll.30s on the view means Livewire re-fetches every 30 seconds
 *   so the badge updates without a full page reload. In MERN you'd use
 *   setInterval or WebSockets for this.
 */
class NotificationBell extends Component
{
    public bool $showDropdown = false;

    /**
     * Toggle the dropdown open/closed.
     */
    public function toggleDropdown(): void
    {
        $this->showDropdown = ! $this->showDropdown;
    }

    /**
     * Mark a single notification as read.
     */
    public function markAsRead(string $notificationId): void
    {
        $notification = Auth::user()
            ->unreadNotifications
            ->where('id', $notificationId)
            ->first();

        $notification?->markAsRead();
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(): void
    {
        Auth::user()->unreadNotifications->markAsRead();
    }

    public function render()
    {
        $user = Auth::user();

        return view('livewire.notification-bell', [
            'unreadCount'   => $user->unreadNotifications()->count(),
            'notifications' => $user->unreadNotifications()->latest()->take(10)->get(),
        ]);
    }
}
