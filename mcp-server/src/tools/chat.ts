import { KinholdClient } from "../api-client.js";

export function registerChatTools(
  server: any,
  client: KinholdClient
): Map<string, Function> {
  const tools = new Map<string, Function>();

  tools.set(
    "search_family_data",
    async (input: { query: string }) => {
      try {
        const results = await client.searchFamilyData(input.query);

        if (
          !results ||
          (Object.keys(results).length === 0 ||
            (results.tasks && results.tasks.length === 0 &&
              results.vault && results.vault.length === 0 &&
              results.events && results.events.length === 0))
        ) {
          return {
            content: [
              {
                type: "text",
                text: `No results found for query: "${input.query}"`,
              },
            ],
          };
        }

        const sections = [];

        if (results.tasks && results.tasks.length > 0) {
          const taskList = results.tasks
            .slice(0, 5)
            .map((task: any) => {
              const status = task.completed_at ? "✓" : "○";
              return `  ${status} ${task.title}${task.due_date ? ` (Due: ${task.due_date})` : ""}`;
            })
            .join("\n");
          sections.push(`**Tasks (${results.tasks.length}):**\n${taskList}`);
        }

        if (results.vault && results.vault.length > 0) {
          const vaultList = results.vault
            .slice(0, 5)
            .map((entry: any) => `  - ${entry.title}`)
            .join("\n");
          sections.push(
            `**Vault Entries (${results.vault.length}):**\n${vaultList}`
          );
        }

        if (results.events && results.events.length > 0) {
          const eventList = results.events
            .slice(0, 5)
            .map(
              (event: any) =>
                `  - ${event.title}${event.start_date ? ` on ${event.start_date}` : ""}`
            )
            .join("\n");
          sections.push(`**Events (${results.events.length}):**\n${eventList}`);
        }

        return {
          content: [
            {
              type: "text",
              text: `Search results for "${input.query}":\n\n${sections.join("\n\n")}`,
            },
          ],
        };
      } catch (error: any) {
        return {
          content: [
            {
              type: "text",
              text: `Error searching family data: ${error.message}`,
              isError: true,
            },
          ],
        };
      }
    }
  );

  return tools;
}

export const chatToolDefinitions = [
  {
    name: "search_family_data",
    description:
      "Search across all family data including tasks, vault entries, and calendar events. Useful for finding information about the family.",
    inputSchema: {
      type: "object",
      properties: {
        query: {
          type: "string",
          description: "Search query (keywords, names, topics)",
        },
      },
      required: ["query"],
    },
  },
];
