<?php

namespace App\Services;

use App\Models\CalendarConnection;
use Carbon\Carbon;
use Google\Client;
use Google\Service\Calendar;
use Google\Service\Calendar\Event;

class GoogleCalendarService
{
    private CalendarConnection $connection;
    private Client $client;
    private Calendar $service;

    public function __construct(CalendarConnection $connection)
    {
        $this->connection = $connection;
        $this->client = $this->initializeClient();
        $this->service = new Calendar($this->client);
    }

    /**
     * Initialize Google API client.
     *
     * @return Client
     */
    private function initializeClient(): Client
    {
        $client = new Client();
        $client->setApplicationName(config('app.name'));
        $client->setClientId(config('kinhold.google.client_id'));
        $client->setClientSecret(config('kinhold.google.client_secret'));
        $client->setAccessToken(json_decode($this->connection->access_token, true));

        // Assign client before refreshing so refreshToken() can use $this->client
        $this->client = $client;

        // Refresh token if expired
        if ($client->isAccessTokenExpired()) {
            $this->refreshToken();
            $client->setAccessToken(json_decode($this->connection->access_token, true));
        }

        return $client;
    }

    /**
     * Get events from Google Calendar.
     *
     * @param Carbon $start
     * @param Carbon $end
     * @return array
     */
    public function getEvents(Carbon $start, Carbon $end): array
    {
        try {
            $optParams = [
                'timeMin' => $start->toRfc3339String(),
                'timeMax' => $end->toRfc3339String(),
                'singleEvents' => true,
                'orderBy' => 'startTime',
                'maxResults' => 250,
            ];

            $results = $this->service->events->listEvents($this->connection->calendar_id, $optParams);
            $events = [];

            foreach ($results->getItems() as $event) {
                $events[] = $this->formatEvent($event);
            }

            return $events;
        } catch (\Exception $e) {
            \Log::error('Failed to fetch Google Calendar events: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Format Google Calendar event to standard format.
     *
     * @param Event $event
     * @return array
     */
    private function formatEvent(Event $event): array
    {
        $start = $event->getStart();
        $end = $event->getEnd();

        return [
            'id' => $event->getId(),
            'title' => $event->getSummary(),
            'description' => $event->getDescription(),
            'start' => $start->getDateTime() ?? $start->getDate(),
            'end' => $end->getDateTime() ?? $end->getDate(),
            'all_day' => !$start->getDateTime(),
            'location' => $event->getLocation(),
            'calendar_id' => $this->connection->calendar_id,
        ];
    }

    /**
     * Refresh expired OAuth token.
     *
     * @return void
     */
    public function refreshToken(): void
    {
        try {
            $this->client->fetchAccessTokenWithRefreshToken($this->connection->refresh_token);
            $newToken = $this->client->getAccessToken();

            $this->connection->update([
                'access_token' => json_encode($newToken),
                'last_synced_at' => now(),
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to refresh Google Calendar token: ' . $e->getMessage());
            $this->connection->update(['is_active' => false]);
        }
    }

    /**
     * Get Google OAuth authorization URL.
     *
     * @return string
     */
    public static function getAuthUrl(string $userId = null): string
    {
        $client = new Client();
        $client->setApplicationName(config('app.name'));
        $client->setClientId(config('kinhold.google.client_id'));
        $client->setClientSecret(config('kinhold.google.client_secret'));
        $client->setRedirectUri(config('kinhold.google.redirect_uri') ?: route('api.calendar.callback'));
        $client->setAccessType('offline');
        $client->setPrompt('consent');

        $client->addScope([
            'https://www.googleapis.com/auth/calendar.readonly',
            'https://www.googleapis.com/auth/calendar',
        ]);

        // Pass user ID in state so callback knows which user initiated
        if ($userId) {
            $client->setState($userId);
        }

        return $client->createAuthUrl();
    }

    /**
     * Handle OAuth callback and store connection.
     *
     * @param string $code
     * @param int $userId
     * @return CalendarConnection
     */
    /**
     * Handle OAuth callback — creates a connection for each owned calendar.
     *
     * @return CalendarConnection[] Array of created connections
     */
    public static function handleCallback(string $code, string $userId): array
    {
        $client = new Client();
        $client->setApplicationName(config('app.name'));
        $client->setClientId(config('kinhold.google.client_id'));
        $client->setClientSecret(config('kinhold.google.client_secret'));
        $client->setRedirectUri(config('kinhold.google.redirect_uri') ?: route('api.calendar.callback'));

        $token = $client->fetchAccessTokenWithAuthCode($code);
        $client->setAccessToken($token);

        // Get all calendars the user owns (primary + any others)
        $service = new Calendar($client);
        $calendarList = $service->calendarList->listCalendarList()->getItems();

        $connections = [];
        $tokenJson = json_encode($token);
        $refreshToken = $token['refresh_token'] ?? null;

        // Count existing connections to assign unique colors
        $existingCount = CalendarConnection::where('user_id', $userId)->count();
        $colorIndex = $existingCount;

        foreach ($calendarList as $cal) {
            // Only sync calendars the user owns (skip shared/subscribed ones)
            if ($cal->getAccessRole() !== 'owner') {
                continue;
            }

            // Skip if this calendar is already connected for this user
            $existing = CalendarConnection::where('user_id', $userId)
                ->where('calendar_id', $cal->getId())
                ->first();

            if ($existing) {
                // Update the token on the existing connection
                $existing->update([
                    'access_token' => $tokenJson,
                    'refresh_token' => $refreshToken ?? $existing->refresh_token,
                    'is_active' => true,
                ]);
                $connections[] = $existing;
                continue;
            }

            $calColors = ['#1166ee', '#7c49b6', '#0d9488', '#e11d48', '#d97706', '#059669', '#0284c7', '#c026d3', '#ea580c', '#65a30d'];

            $connections[] = CalendarConnection::create([
                'user_id' => $userId,
                'calendar_id' => $cal->getId(),
                'calendar_name' => $cal->getSummary(),
                'calendar_email' => $cal->getId(),
                'access_token' => $tokenJson,
                'refresh_token' => $refreshToken,
                'color' => $calColors[$colorIndex % count($calColors)],
                'is_active' => true,
            ]);

            $colorIndex++;
        }

        return $connections;
    }
}
