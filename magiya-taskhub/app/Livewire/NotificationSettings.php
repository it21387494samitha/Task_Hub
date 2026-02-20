<?php

namespace App\Livewire;

use App\Models\NotificationSetting;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

/**
 * Notification preferences page — allows each user to toggle
 * email / in-app notifications per event type.
 */
class NotificationSettings extends Component
{
    /** @var array<string, array{email: bool, database: bool}> */
    public array $settings = [];

    public function mount(): void
    {
        $user = Auth::user();

        // Load persisted preferences, falling back to "enabled" for everything
        foreach (NotificationSetting::EVENT_TYPES as $type => $label) {
            $existing = $user->notificationSettings()->where('event_type', $type)->first();

            $this->settings[$type] = [
                'email'    => $existing ? $existing->email_enabled : true,
                'database' => $existing ? $existing->database_enabled : true,
            ];
        }
    }

    /**
     * Toggle a single channel for a single event type — saves immediately.
     */
    public function toggle(string $eventType, string $channel): void
    {
        if (! array_key_exists($eventType, NotificationSetting::EVENT_TYPES)) {
            return;
        }
        if (! in_array($channel, ['email', 'database'])) {
            return;
        }

        $this->settings[$eventType][$channel] = ! $this->settings[$eventType][$channel];

        NotificationSetting::updateOrCreate(
            [
                'user_id'    => Auth::id(),
                'event_type' => $eventType,
            ],
            [
                'email_enabled'    => $this->settings[$eventType]['email'],
                'database_enabled' => $this->settings[$eventType]['database'],
            ]
        );

        $this->dispatch('toast', type: 'success', message: 'Preference saved.');
    }

    /**
     * Bulk toggle: enable all or disable all.
     */
    public function enableAll(): void
    {
        foreach (NotificationSetting::EVENT_TYPES as $type => $label) {
            $this->settings[$type] = ['email' => true, 'database' => true];

            NotificationSetting::updateOrCreate(
                ['user_id' => Auth::id(), 'event_type' => $type],
                ['email_enabled' => true, 'database_enabled' => true]
            );
        }

        $this->dispatch('toast', type: 'success', message: 'All notifications enabled.');
    }

    public function disableAll(): void
    {
        foreach (NotificationSetting::EVENT_TYPES as $type => $label) {
            $this->settings[$type] = ['email' => false, 'database' => false];

            NotificationSetting::updateOrCreate(
                ['user_id' => Auth::id(), 'event_type' => $type],
                ['email_enabled' => false, 'database_enabled' => false]
            );
        }

        $this->dispatch('toast', type: 'success', message: 'All notifications disabled.');
    }

    public function render()
    {
        return view('livewire.notification-settings')
            ->layout('layouts.app')
            ->title('Notification Settings');
    }
}
