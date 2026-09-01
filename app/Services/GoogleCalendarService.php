<?php

namespace App\Services;

use Google\Client;
use Google\Service\Calendar;
use Google\Service\Calendar\Event;
use Google\Service\Calendar\EventDateTime;
use App\Models\UserSettings;
use Exception;

class GoogleCalendarService
{
    protected Client $client;

    public function __construct()
    {
        $this->client = new Client();
        $this->client->setApplicationName('CRM Calendar Integration');
        $this->client->setScopes(Calendar::CALENDAR);
        
        $credentialsJson = config('services.google.credentials_json');
        $credentialsPath = config('services.google.credentials_path');

        if (!empty($credentialsJson)) {
            $this->client->setAuthConfig(json_decode($credentialsJson, true));
        } elseif ($credentialsPath && file_exists($credentialsPath)) {
            $this->client->setAuthConfig($credentialsPath);
        }
    }

    /**
     * Create a new calendar for a user.
     */
    public function createCalendar(string $calendarName, ?string $userEmail = null): ?string
    {
        $service = new Calendar($this->client);
        $calendar = new \Google\Service\Calendar\Calendar();
        $calendar->setSummary($calendarName);
        $calendar->setTimeZone(config('app.timezone', 'America/Sao_Paulo'));

        try {
            $createdCalendar = $service->calendars->insert($calendar);
            $calendarId = $createdCalendar->getId();

            // Compartilhar a agenda criada com o email real do usuário
            if ($userEmail) {
                try {
                    $rule = new \Google\Service\Calendar\AclRule();
                    $scope = new \Google\Service\Calendar\AclRuleScope();
                    $scope->setType("user");
                    $scope->setValue($userEmail);
                    $rule->setScope($scope);
                    $rule->setRole("owner");
                    
                    $service->acl->insert($calendarId, $rule);
                } catch (Exception $e) {
                    \Log::warning('Google Calendar ACL Error (Email may not be a Google Account): ' . $e->getMessage());
                }
            }

            return $calendarId;
        } catch (Exception $e) {
            \Log::error('Google Calendar Create Calendar Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Create an event in Google Calendar.
     */
    public function createEvent(string $calendarId, string $title, string $description, string $startTime, string $endTime, bool $createMeet = false): ?array
    {
        if (!$calendarId) return null;

        $service = new Calendar($this->client);
        $eventData = [
            'summary' => $title,
            'description' => $description,
            'start' => [
                'dateTime' => date('c', strtotime($startTime)),
                'timeZone' => config('app.timezone', 'America/Sao_Paulo'),
            ],
            'end' => [
                'dateTime' => date('c', strtotime($endTime)),
                'timeZone' => config('app.timezone', 'America/Sao_Paulo'),
            ],
        ];

        if ($createMeet) {
            $eventData['conferenceData'] = [
                'createRequest' => [
                    'requestId' => uniqid(),
                    'conferenceSolutionKey' => ['type' => 'hangoutsMeet'],
                ]
            ];
        }

        $event = new Event($eventData);

        try {
            $options = $createMeet ? ['conferenceDataVersion' => 1] : [];
            $eventResult = $service->events->insert($calendarId, $event, $options);

            \Log::info('Event created in Google. HangoutLink: ' . ($eventResult->getHangoutLink() ?? 'NULL'));
            
            $meetLink = null;
            if ($createMeet && $eventResult->getHangoutLink()) {
                $meetLink = $eventResult->getHangoutLink();
            }
            return ['id' => $eventResult->getId(), 'meet_link' => $meetLink];
        } catch (\Google\Service\Exception $e) {
            $errors = $e->getErrors();
            $isInvalidConference = false;
            
            foreach ($errors as $error) {
                if (isset($error['reason']) && $error['reason'] === 'invalid' && strpos($error['message'], 'conference type') !== false) {
                    $isInvalidConference = true;
                    break;
                }
            }

            if ($isInvalidConference && $createMeet) {
                \Log::warning('Service account cannot create Meet links. Falling back to event without Meet.');
                // Tenta criar novamente sem o link do Meet
                unset($eventData['conferenceData']);
                $event = new Event($eventData);
                try {
                    $eventResult = $service->events->insert($calendarId, $event);
                    return ['id' => $eventResult->getId(), 'meet_link' => null];
                } catch (\Exception $fallbackException) {
                    \Log::error('Google Calendar Fallback Error: ' . $fallbackException->getMessage());
                    return null;
                }
            }

            \Log::error('Google Calendar Create Event API Error: ' . $e->getMessage());
            return null;
        } catch (\Exception $e) {
            \Log::error('Google Calendar Create Event General Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Update an event in Google Calendar.
     */
    public function updateEvent(string $calendarId, string $eventId, string $title, string $description, string $startTime, string $endTime): bool
    {
        if (!$calendarId || !$eventId) return false;

        $service = new Calendar($this->client);
        
        try {
            $event = $service->events->get($calendarId, $eventId);
            
            $event->setSummary($title);
            $event->setDescription($description);
            
            $start = new EventDateTime();
            $start->setDateTime(date('c', strtotime($startTime)));
            $start->setTimeZone(config('app.timezone', 'America/Sao_Paulo'));
            $event->setStart($start);
            
            $end = new EventDateTime();
            $end->setDateTime(date('c', strtotime($endTime)));
            $end->setTimeZone(config('app.timezone', 'America/Sao_Paulo'));
            $event->setEnd($end);
            
            $service->events->update($calendarId, $event->getId(), $event);
            return true;
        } catch (Exception $e) {
            \Log::error('Google Calendar Update Event Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete an event from Google Calendar.
     */
    public function deleteEvent(string $calendarId, string $eventId): bool
    {
        if (!$calendarId || !$eventId) return false;

        $service = new Calendar($this->client);
        try {
            $service->events->delete($calendarId, $eventId);
            return true;
        } catch (Exception $e) {
            \Log::error('Google Calendar Delete Event Error: ' . $e->getMessage());
            return false;
        }
    }
}
