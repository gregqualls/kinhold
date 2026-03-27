import { KinholdClient } from "../api-client.js";

function getDateRange(
  days: number = 7
): { start: string; end: string } {
  const today = new Date();
  const start = new Date(today);
  start.setDate(start.getDate() - today.getDay()); // Start of current week (Sunday)
  const end = new Date(start);
  end.setDate(end.getDate() + days);

  return {
    start: start.toISOString().split("T")[0],
    end: end.toISOString().split("T")[0],
  };
}

function formatEventTime(event: any): string {
  if (event.all_day) {
    return "All day";
  }
  const start = event.start_time ? event.start_time.substring(0, 5) : "";
  const end = event.end_time ? event.end_time.substring(0, 5) : "";
  return `${start} - ${end}`;
}

export function registerCalendarTools(
  server: any,
  client: KinholdClient
): Map<string, Function> {
  const tools = new Map<string, Function>();

  tools.set(
    "list_calendar_events",
    async (input: { start?: string; end?: string }) => {
      try {
        const { start, end } = input.start && input.end
          ? input
          : getDateRange(7);

        const events = await client.listCalendarEvents({ start, end });

        if (!events || events.length === 0) {
          return {
            content: [
              {
                type: "text",
                text: `No events found between ${start} and ${end}.`,
              },
            ],
          };
        }

        // Group by date
        const grouped = events.reduce(
          (acc: Record<string, any[]>, event: any) => {
            const date = event.start_date || event.date;
            if (!acc[date]) acc[date] = [];
            acc[date].push(event);
            return acc;
          },
          {}
        );

        const formatted = Object.entries(grouped)
          .map(
            ([date, dayEvents]) =>
              `**${new Date(date).toLocaleDateString("en-US", {
                weekday: "short",
                month: "short",
                day: "numeric",
              })}**\n${(dayEvents as any[])
                .map((e) => {
                  const calendar = e.calendar_name ? ` [${e.calendar_name}]` : "";
                  const attendees = e.attendee_count
                    ? ` (${e.attendee_count} attendees)`
                    : "";
                  return `  • ${formatEventTime(e)} - ${e.title}${calendar}${attendees}`;
                })
                .join("\n")}`
          )
          .join("\n\n");

        return {
          content: [
            {
              type: "text",
              text: `Calendar events from ${start} to ${end}:\n\n${formatted}`,
            },
          ],
        };
      } catch (error: any) {
        return {
          content: [
            {
              type: "text",
              text: `Error listing calendar events: ${error.message}`,
              isError: true,
            },
          ],
        };
      }
    }
  );

  tools.set("list_calendar_connections", async () => {
    try {
      const connections = await client.listCalendarConnections();

      if (!connections || connections.length === 0) {
        return {
          content: [
            {
              type: "text",
              text: "No calendar connections found. Connect a calendar in Kinhold settings.",
            },
          ],
        };
      }

      const formatted = connections
        .map((conn: any) => {
          const status = conn.is_active ? "✓ Active" : "✗ Inactive";
          const lastSync = conn.last_synced_at
            ? ` - Last synced: ${new Date(conn.last_synced_at).toLocaleDateString()}`
            : "";
          return `  **${conn.calendar_name}** (${conn.provider})${lastSync}\n    ${status}`;
        })
        .join("\n");

      return {
        content: [
          {
            type: "text",
            text: `Connected calendars:\n\n${formatted}`,
          },
        ],
      };
    } catch (error: any) {
      return {
        content: [
          {
            type: "text",
            text: `Error listing calendar connections: ${error.message}`,
            isError: true,
          },
        ],
      };
    }
  });

  tools.set("sync_calendars", async () => {
    try {
      const result = await client.syncCalendars();
      return {
        content: [
          {
            type: "text",
            text: `Calendar sync initiated. ${result.message || "Sync completed."}`,
          },
        ],
      };
    } catch (error: any) {
      return {
        content: [
          {
            type: "text",
            text: `Error syncing calendars: ${error.message}`,
            isError: true,
          },
        ],
      };
    }
  });

  tools.set("get_todays_events", async () => {
    try {
      const today = new Date().toISOString().split("T")[0];
      const tomorrow = new Date();
      tomorrow.setDate(tomorrow.getDate() + 1);
      const tomorrowStr = tomorrow.toISOString().split("T")[0];

      const events = await client.listCalendarEvents({
        start: today,
        end: tomorrowStr,
      });

      if (!events || events.length === 0) {
        return {
          content: [{ type: "text", text: "No events scheduled for today." }],
        };
      }

      const formatted = events
        .map((e: any) => {
          const calendar = e.calendar_name ? ` [${e.calendar_name}]` : "";
          const attendees = e.attendee_count
            ? ` (${e.attendee_count} attendees)`
            : "";
          return `  • ${formatEventTime(e)} - ${e.title}${calendar}${attendees}`;
        })
        .join("\n");

      return {
        content: [
          {
            type: "text",
            text: `Today's events:\n\n${formatted}`,
          },
        ],
      };
    } catch (error: any) {
      return {
        content: [
          {
            type: "text",
            text: `Error getting today's events: ${error.message}`,
            isError: true,
          },
        ],
      };
    }
  });

  tools.set("get_weeks_events", async () => {
    try {
      const { start, end } = getDateRange(7);
      const events = await client.listCalendarEvents({ start, end });

      if (!events || events.length === 0) {
        return {
          content: [
            {
              type: "text",
              text: `No events scheduled for this week (${start} to ${end}).`,
            },
          ],
        };
      }

      // Group by date
      const grouped = events.reduce(
        (acc: Record<string, any[]>, event: any) => {
          const date = event.start_date || event.date;
          if (!acc[date]) acc[date] = [];
          acc[date].push(event);
          return acc;
        },
        {}
      );

      const formatted = Object.entries(grouped)
        .map(
          ([date, dayEvents]) =>
            `**${new Date(date).toLocaleDateString("en-US", {
              weekday: "short",
              month: "short",
              day: "numeric",
            })}**\n${(dayEvents as any[])
              .map((e) => {
                const calendar = e.calendar_name ? ` [${e.calendar_name}]` : "";
                const attendees = e.attendee_count
                  ? ` (${e.attendee_count} attendees)`
                  : "";
                return `  • ${formatEventTime(e)} - ${e.title}${calendar}${attendees}`;
              })
              .join("\n")}`
        )
        .join("\n\n");

      return {
        content: [
          {
            type: "text",
            text: `This week's events (${start} to ${end}):\n\n${formatted}`,
          },
        ],
      };
    } catch (error: any) {
      return {
        content: [
          {
            type: "text",
            text: `Error getting week's events: ${error.message}`,
            isError: true,
          },
        ],
      };
    }
  });

  return tools;
}

export const calendarToolDefinitions = [
  {
    name: "list_calendar_events",
    description:
      "List calendar events for a date range. Defaults to current week if not specified.",
    inputSchema: {
      type: "object",
      properties: {
        start: {
          type: "string",
          description: "Start date (YYYY-MM-DD format)",
        },
        end: {
          type: "string",
          description: "End date (YYYY-MM-DD format)",
        },
      },
      required: [],
    },
  },
  {
    name: "list_calendar_connections",
    description: "List all connected calendars and their sync status",
    inputSchema: {
      type: "object",
      properties: {},
      required: [],
    },
  },
  {
    name: "sync_calendars",
    description: "Force a sync of all connected calendars",
    inputSchema: {
      type: "object",
      properties: {},
      required: [],
    },
  },
  {
    name: "get_todays_events",
    description: "Get all events scheduled for today",
    inputSchema: {
      type: "object",
      properties: {},
      required: [],
    },
  },
  {
    name: "get_weeks_events",
    description: "Get all events scheduled for this week",
    inputSchema: {
      type: "object",
      properties: {},
      required: [],
    },
  },
];
