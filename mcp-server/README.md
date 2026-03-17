# Q32 Hub MCP Server

Model Context Protocol server for Q32 Hub, enabling Claude to interact with your family hub application.

## What it is

This MCP server allows Claude (via Claude Desktop or other MCP-compatible clients) to:
- View and manage family tasks and task lists
- Check calendar events and sync connected calendars
- Access the family vault
- Get family member information
- Search across all family data

## Installation

### Prerequisites
- Node.js 18 or higher
- A running Q32 Hub instance (local or remote)
- A Sanctum API token from Q32 Hub

### Setup

1. Install dependencies:
```bash
npm install
```

2. Build the TypeScript:
```bash
npm run build
```

3. Generate an API token in Q32 Hub:
   - Log in to Q32 Hub
   - Go to Settings > API Tokens
   - Create a new token (or use an existing one)
   - Copy the token value

## Configuration

### Environment Variables

Set these before running the server:

- `Q32HUB_API_URL`: Full URL to your Q32 Hub API (default: `http://localhost/api/v1`)
- `Q32HUB_API_TOKEN`: Your Sanctum API token (required)

### Example

```bash
export Q32HUB_API_URL="http://q32hub.local/api/v1"
export Q32HUB_API_TOKEN="your-sanctum-token-here"
npm start
```

## Claude Desktop Configuration

Add this to your Claude Desktop configuration file (`~/.claude_desktop_config.json` on macOS/Linux, or `%APPDATA%\Claude\claude_desktop_config.json` on Windows):

```json
{
  "mcpServers": {
    "q32hub": {
      "command": "node",
      "args": ["/path/to/mcp-server/dist/index.js"],
      "env": {
        "Q32HUB_API_URL": "http://localhost/api/v1",
        "Q32HUB_API_TOKEN": "your-sanctum-token-here"
      }
    }
  }
}
```

Replace `/path/to/mcp-server/` with the actual path to this directory.

## Usage

Once configured, you can ask Claude questions like:

- "What tasks are due this week?"
- "Show me today's calendar events"
- "Create a new task to buy groceries and assign it to Mom"
- "Search for all password-related vault entries"
- "List all family members"
- "What's on Sarah's calendar this month?"

## Development

For development with hot reload:

```bash
npm run dev
```

Build for production:

```bash
npm run build
```

## Available Tools

### Tasks
- `list_task_lists` - Show all task lists
- `get_task_list` - Get tasks in a specific list
- `create_task_list` - Create a new task list
- `list_tasks` - List tasks with filters
- `create_task` - Create a new task
- `update_task` - Update a task
- `complete_task` - Mark task as complete
- `uncomplete_task` - Mark task as incomplete
- `delete_task` - Delete a task

### Calendar
- `list_calendar_events` - Get events for a date range
- `list_calendar_connections` - Show connected calendars
- `sync_calendars` - Force calendar sync
- `get_todays_events` - Quick access to today's events
- `get_weeks_events` - Quick access to this week's events

### Vault
- `list_vault_categories` - Show vault categories
- `list_vault_entries` - List vault entries
- `get_vault_entry` - View a vault entry (decrypted)
- `create_vault_entry` - Create a new entry
- `update_vault_entry` - Update an entry
- `delete_vault_entry` - Delete an entry
- `grant_vault_access` - Share entry with family member
- `revoke_vault_access` - Unshare entry

### Family
- `get_family_info` - Get family details and members
- `get_family_member` - View a family member's info
- `list_family_members` - List all family members

### Search
- `search_family_data` - Search across all family data

## Troubleshooting

### Connection refused
- Ensure Q32 Hub is running and accessible at `Q32HUB_API_URL`
- Check network connectivity and firewall rules

### Invalid API token
- Verify the token is correct in Q32 Hub settings
- The token should start with a specific prefix (e.g., `sanctum_`)
- Regenerate the token if needed

### Unauthorized errors
- Confirm you're logged in to Q32 Hub
- Check that the token has appropriate permissions
- Ensure the token hasn't expired

### Tool calls fail silently
- Check the server logs for error messages
- Enable debug output in Claude
- Verify the API response format matches expectations

## License

MIT - See LICENSE file in the project root

## Support

For issues or questions:
1. Check Q32 Hub logs for API errors
2. Verify environment variables are set correctly
3. Review the tool definitions above for usage patterns
4. Report issues with full error messages and steps to reproduce
