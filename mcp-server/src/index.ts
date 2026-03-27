import { Server } from "@modelcontextprotocol/sdk/server/index.js";
import {
  StdioServerTransport,
  StdioClientTransport,
} from "@modelcontextprotocol/sdk/server/stdio.js";
import {
  CallToolRequestSchema,
  ListToolsRequestSchema,
} from "@modelcontextprotocol/sdk/types.js";
import { KinholdClient } from "./api-client.js";
import {
  registerTaskTools,
  taskToolDefinitions,
} from "./tools/tasks.js";
import {
  registerCalendarTools,
  calendarToolDefinitions,
} from "./tools/calendar.js";
import { registerVaultTools, vaultToolDefinitions } from "./tools/vault.js";
import {
  registerFamilyTools,
  familyToolDefinitions,
} from "./tools/family.js";
import {
  registerChatTools,
  chatToolDefinitions,
} from "./tools/chat.js";

const apiUrl = process.env.KINHOLD_API_URL || "http://localhost/api/v1";
const apiToken = process.env.KINHOLD_API_TOKEN || "";

if (!apiToken) {
  console.error(
    "KINHOLD_API_TOKEN environment variable is required"
  );
  process.exit(1);
}

const client = new KinholdClient(apiUrl, apiToken);

const server = new Server(
  {
    name: "kinhold",
    version: "1.0.0",
  },
  {
    capabilities: {
      tools: {},
    },
  }
);

// Register all tool handlers
const allTools = new Map<string, Function>();

allTools.forEach((handler, name) => {
  // Will be populated below
});

// Register tool categories
const taskTools = registerTaskTools(server, client);
const calendarTools = registerCalendarTools(server, client);
const vaultTools = registerVaultTools(server, client);
const familyTools = registerFamilyTools(server, client);
const chatTools = registerChatTools(server, client);

// Merge all tools
const tools = new Map([
  ...taskTools,
  ...calendarTools,
  ...vaultTools,
  ...familyTools,
  ...chatTools,
]);

// Define all tools
const allToolDefinitions = [
  ...taskToolDefinitions,
  ...calendarToolDefinitions,
  ...vaultToolDefinitions,
  ...familyToolDefinitions,
  ...chatToolDefinitions,
];

// Handle list_tools
server.setRequestHandler(ListToolsRequestSchema, async () => ({
  tools: allToolDefinitions,
}));

// Handle call_tool
server.setRequestHandler(
  CallToolRequestSchema,
  async (request) => {
    const handler = tools.get(request.params.name);

    if (!handler) {
      return {
        content: [
          {
            type: "text",
            text: `Unknown tool: ${request.params.name}`,
            isError: true,
          },
        ],
      };
    }

    try {
      return await handler(request.params.arguments || {});
    } catch (error) {
      const errorMsg = error instanceof Error ? error.message : String(error);
      return {
        content: [
          {
            type: "text",
            text: `Error executing tool: ${errorMsg}`,
            isError: true,
          },
        ],
      };
    }
  }
);

// Start the server
async function main() {
  const transport = new StdioServerTransport();
  await server.connect(transport);
  console.error("[Kinhold MCP] Server started successfully");
}

main().catch((error) => {
  console.error("[Kinhold MCP] Fatal error:", error);
  process.exit(1);
});
