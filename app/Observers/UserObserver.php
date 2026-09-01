<?php

namespace App\Observers;

use App\Models\User;
use App\Services\GoogleCalendarService;

class UserObserver
{
    protected GoogleCalendarService $calendarService;

    public function __construct(GoogleCalendarService $calendarService)
    {
        $this->calendarService = $calendarService;
    }

    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        // Avoid recursive saving loop or duplicate calendars
        if (empty($user->google_calendar_id)) {
            $calendarId = $this->calendarService->createCalendar("Agenda: {$user->name}", $user->email);
            
            if ($calendarId) {
                $user->google_calendar_id = $calendarId;
                $user->saveQuietly();
            }
        }
    }
}
