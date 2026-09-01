<?php

namespace App\Observers;

use App\Models\Appointment;
use App\Services\GoogleCalendarService;

class AppointmentObserver
{
    protected GoogleCalendarService $calendarService;

    public function __construct(GoogleCalendarService $calendarService)
    {
        $this->calendarService = $calendarService;
    }

    /**
     * Handle the Appointment "created" event.
     */
    public function created(Appointment $appointment): void
    {
        $calendarId = $appointment->user->google_calendar_id;
        $createMeet = request()->boolean('create_meet', false);
        \Log::info('AppointmentObserver created triggered. createMeet: ' . ($createMeet ? 'true' : 'false'));
        
        if ($calendarId) {
            $result = $this->calendarService->createEvent(
                $calendarId,
                $appointment->title,
                $appointment->description ?? '',
                $appointment->start_time->toDateTimeString(),
                $appointment->end_time->toDateTimeString(),
                (bool) $createMeet
            );

            if ($result && isset($result['id'])) {
                // Save without triggering events again to prevent recursion
                $appointment->google_event_id = $result['id'];
                if (isset($result['meet_link'])) {
                    $appointment->meet_link = $result['meet_link'];
                }
                $appointment->saveQuietly();
            }
        }
    }

    /**
     * Handle the Appointment "updated" event.
     */
    public function updated(Appointment $appointment): void
    {
        $calendarId = $appointment->user->google_calendar_id;
        
        // Only update if there is a google event ID
        if ($appointment->google_event_id && $calendarId) {
            $this->calendarService->updateEvent(
                $calendarId,
                $appointment->google_event_id,
                $appointment->title,
                $appointment->description ?? '',
                $appointment->start_time->toDateTimeString(),
                $appointment->end_time->toDateTimeString()
            );
        }
    }

    /**
     * Handle the Appointment "deleted" event.
     */
    public function deleted(Appointment $appointment): void
    {
        $calendarId = $appointment->user->google_calendar_id;
        
        if ($appointment->google_event_id && $calendarId) {
            $this->calendarService->deleteEvent($calendarId, $appointment->google_event_id);
        }
    }
}
