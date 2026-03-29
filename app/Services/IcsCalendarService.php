<?php

namespace App\Services;

use App\Models\CalendarConnection;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Sabre\VObject\Reader;

class IcsCalendarService
{
    private CalendarConnection $connection;

    public function __construct(CalendarConnection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Fetch and parse events from an ICS URL.
     */
    public function getEvents(Carbon $start, Carbon $end): array
    {
        try {
            $response = Http::timeout(15)->get($this->connection->calendar_id);

            if (!$response->successful()) {
                Log::error("Failed to fetch ICS calendar: HTTP {$response->status()} for {$this->connection->calendar_id}");
                return [];
            }

            $icsData = $response->body();
            $vcalendar = Reader::read($icsData);

            if (!$vcalendar || !isset($vcalendar->VEVENT)) {
                return [];
            }

            $events = [];

            foreach ($vcalendar->VEVENT as $vevent) {
                $event = $this->parseEvent($vevent);

                if (!$event) {
                    continue;
                }

                // Filter to date range
                $eventStart = Carbon::parse($event['start']);
                $eventEnd = $event['end'] ? Carbon::parse($event['end']) : $eventStart;

                if ($eventEnd >= $start && $eventStart <= $end) {
                    $events[] = $event;
                }
            }

            // Update last synced timestamp
            $this->connection->update(['last_synced_at' => now()]);

            return $events;
        } catch (\Exception $e) {
            Log::error("Failed to parse ICS calendar {$this->connection->id}: {$e->getMessage()}");
            return [];
        }
    }

    /**
     * Parse a single VEVENT into our standard format.
     */
    private function parseEvent($vevent): ?array
    {
        try {
            $dtstart = $vevent->DTSTART;
            if (!$dtstart) {
                return null;
            }

            $startDt = $dtstart->getDateTime();
            $allDay = !$dtstart->hasTime();

            $endDt = null;
            if (isset($vevent->DTEND)) {
                $endDt = $vevent->DTEND->getDateTime();
            } elseif (isset($vevent->DURATION)) {
                $endDt = clone $startDt;
                $endDt = $endDt->add(new \DateInterval((string) $vevent->DURATION));
            }

            return [
                'id' => isset($vevent->UID) ? (string) $vevent->UID : uniqid('ics_'),
                'title' => isset($vevent->SUMMARY) ? (string) $vevent->SUMMARY : 'Untitled Event',
                'description' => isset($vevent->DESCRIPTION) ? (string) $vevent->DESCRIPTION : null,
                'start' => $allDay ? $startDt->format('Y-m-d') : $startDt->format('c'),
                'end' => $endDt ? ($allDay ? $endDt->format('Y-m-d') : $endDt->format('c')) : null,
                'all_day' => $allDay,
                'location' => isset($vevent->LOCATION) ? (string) $vevent->LOCATION : null,
                'calendar_id' => $this->connection->calendar_id,
            ];
        } catch (\Exception $e) {
            Log::warning("Failed to parse ICS event: {$e->getMessage()}");
            return null;
        }
    }

    /**
     * Create a calendar connection from an ICS URL.
     */
    public static function subscribe(string $url, string $userId, ?string $name = null): CalendarConnection
    {
        // SECURITY: Validate URL scheme and block internal/private IP ranges (SSRF protection)
        $parsed = parse_url($url);
        $scheme = strtolower($parsed['scheme'] ?? '');
        if (!in_array($scheme, ['http', 'https'])) {
            throw new \Exception('Only HTTP and HTTPS URLs are supported.');
        }

        $host = $parsed['host'] ?? '';
        $ip = gethostbyname($host);
        if ($ip === $host && !filter_var($host, FILTER_VALIDATE_IP)) {
            throw new \Exception('Could not resolve hostname.');
        }
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
            throw new \Exception('Internal or private URLs are not allowed.');
        }

        $response = Http::timeout(15)->get($url);

        if (!$response->successful()) {
            throw new \Exception("Could not fetch calendar URL. HTTP status: {$response->status()}");
        }

        $icsData = $response->body();
        $vcalendar = Reader::read($icsData);

        if (!$vcalendar) {
            throw new \Exception('Invalid calendar data. The URL must point to a valid .ics file.');
        }

        // Try to get the calendar name from the ICS data
        $calendarName = $name;
        if (!$calendarName && isset($vcalendar->{'X-WR-CALNAME'})) {
            $calendarName = (string) $vcalendar->{'X-WR-CALNAME'};
        }
        if (!$calendarName) {
            $calendarName = 'Subscribed Calendar';
        }

        // Check if already subscribed
        $existing = CalendarConnection::where('user_id', $userId)
            ->where('calendar_id', $url)
            ->first();

        if ($existing) {
            $existing->update([
                'calendar_name' => $calendarName,
                'is_active' => true,
                'last_synced_at' => now(),
            ]);
            return $existing;
        }

        $calColors = ['#1166ee', '#7c49b6', '#0d9488', '#e11d48', '#d97706', '#059669', '#0284c7', '#c026d3', '#ea580c', '#65a30d'];
        $existingCount = CalendarConnection::where('user_id', $userId)->count();

        return CalendarConnection::create([
            'user_id' => $userId,
            'provider' => 'ics',
            'calendar_id' => $url,
            'calendar_name' => $calendarName,
            'access_token' => json_encode([]),
            'color' => $calColors[$existingCount % count($calColors)],
            'is_active' => true,
            'last_synced_at' => now(),
        ]);
    }
}
