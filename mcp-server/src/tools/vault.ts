import { Q32HubClient } from "../api-client.js";

export function registerVaultTools(
  server: any,
  client: Q32HubClient
): Map<string, Function> {
  const tools = new Map<string, Function>();

  tools.set("list_vault_categories", async () => {
    try {
      const categories = await client.listVaultCategories();

      if (!categories || categories.length === 0) {
        return {
          content: [
            {
              type: "text",
              text: "No vault categories found. Create one to get started.",
            },
          ],
        };
      }

      const formatted = categories
        .map(
          (cat: any) =>
            `- **${cat.name}** (ID: ${cat.id})${cat.description ? ` - ${cat.description}` : ""}\n  ${cat.entry_count || 0} entries`
        )
        .join("\n");

      return {
        content: [
          {
            type: "text",
            text: `Vault categories:\n\n${formatted}`,
          },
        ],
      };
    } catch (error: any) {
      return {
        content: [
          {
            type: "text",
            text: `Error listing vault categories: ${error.message}`,
            isError: true,
          },
        ],
      };
    }
  });

  tools.set(
    "list_vault_entries",
    async (input: { category_id?: string }) => {
      try {
        const entries = await client.listVaultEntries(input.category_id);

        if (!entries || entries.length === 0) {
          return {
            content: [
              {
                type: "text",
                text: "No vault entries found.",
              },
            ],
          };
        }

        const formatted = entries
          .map(
            (entry: any) =>
              `- **${entry.title}** (ID: ${entry.id})\n  Category: ${entry.category}\n  Shared with: ${entry.shared_count || 0} members${entry.notes ? `\n  Notes: ${entry.notes}` : ""}`
          )
          .join("\n");

        return {
          content: [
            {
              type: "text",
              text: `Vault entries:\n\n${formatted}`,
            },
          ],
        };
      } catch (error: any) {
        return {
          content: [
            {
              type: "text",
              text: `Error listing vault entries: ${error.message}`,
              isError: true,
            },
          ],
        };
      }
    }
  );

  tools.set("get_vault_entry", async (input: { entry_id: string }) => {
    try {
      const entry = await client.getVaultEntry(input.entry_id);

      const dataDisplay = Object.entries(entry.data || {})
        .map(([key, value]) => `  **${key}**: ${value}`)
        .join("\n");

      return {
        content: [
          {
            type: "text",
            text: `**${entry.title}**\nCategory: ${entry.category}${entry.notes ? `\nNotes: ${entry.notes}` : ""}\n\nData:\n${dataDisplay}`,
          },
        ],
      };
    } catch (error: any) {
      return {
        content: [
          {
            type: "text",
            text: `Error getting vault entry: ${error.message}`,
            isError: true,
          },
        ],
      };
    }
  });

  tools.set(
    "create_vault_entry",
    async (input: {
      title: string;
      category_id: string;
      data: Record<string, any>;
      notes?: string;
    }) => {
      try {
        const entry = await client.createVaultEntry(input);
        return {
          content: [
            {
              type: "text",
              text: `Created vault entry: **${entry.title}** (ID: ${entry.id})`,
            },
          ],
        };
      } catch (error: any) {
        return {
          content: [
            {
              type: "text",
              text: `Error creating vault entry: ${error.message}`,
              isError: true,
            },
          ],
        };
      }
    }
  );

  tools.set(
    "update_vault_entry",
    async (input: {
      entry_id: string;
      title?: string;
      data?: Record<string, any>;
      notes?: string;
      [key: string]: any;
    }) => {
      try {
        const { entry_id, ...payload } = input;
        const entry = await client.updateVaultEntry(entry_id, payload);
        return {
          content: [
            {
              type: "text",
              text: `Updated vault entry: **${entry.title}** (ID: ${entry.id})`,
            },
          ],
        };
      } catch (error: any) {
        return {
          content: [
            {
              type: "text",
              text: `Error updating vault entry: ${error.message}`,
              isError: true,
            },
          ],
        };
      }
    }
  );

  tools.set("delete_vault_entry", async (input: { entry_id: string }) => {
    try {
      await client.deleteVaultEntry(input.entry_id);
      return {
        content: [
          {
            type: "text",
            text: "Vault entry deleted successfully.",
          },
        ],
      };
    } catch (error: any) {
      return {
        content: [
          {
            type: "text",
            text: `Error deleting vault entry: ${error.message}`,
            isError: true,
          },
        ],
      };
    }
  });

  tools.set(
    "grant_vault_access",
    async (input: {
      entry_id: string;
      family_member_id: string;
    }) => {
      try {
        await client.grantVaultAccess(input.entry_id, input.family_member_id);
        return {
          content: [
            {
              type: "text",
              text: `Access granted for vault entry ${input.entry_id}.`,
            },
          ],
        };
      } catch (error: any) {
        return {
          content: [
            {
              type: "text",
              text: `Error granting vault access: ${error.message}`,
              isError: true,
            },
          ],
        };
      }
    }
  );

  tools.set(
    "revoke_vault_access",
    async (input: {
      entry_id: string;
      family_member_id: string;
    }) => {
      try {
        await client.revokeVaultAccess(
          input.entry_id,
          input.family_member_id
        );
        return {
          content: [
            {
              type: "text",
              text: `Access revoked for vault entry ${input.entry_id}.`,
            },
          ],
        };
      } catch (error: any) {
        return {
          content: [
            {
              type: "text",
              text: `Error revoking vault access: ${error.message}`,
              isError: true,
            },
          ],
        };
      }
    }
  );

  return tools;
}

export const vaultToolDefinitions = [
  {
    name: "list_vault_categories",
    description: "List all vault categories with entry counts",
    inputSchema: {
      type: "object",
      properties: {},
      required: [],
    },
  },
  {
    name: "list_vault_entries",
    description: "List vault entries, optionally filtered by category",
    inputSchema: {
      type: "object",
      properties: {
        category_id: {
          type: "string",
          description: "Filter by category ID",
        },
      },
      required: [],
    },
  },
  {
    name: "get_vault_entry",
    description:
      "Get a specific vault entry with decrypted data",
    inputSchema: {
      type: "object",
      properties: {
        entry_id: {
          type: "string",
          description: "The vault entry ID",
        },
      },
      required: ["entry_id"],
    },
  },
  {
    name: "create_vault_entry",
    description: "Create a new vault entry",
    inputSchema: {
      type: "object",
      properties: {
        title: {
          type: "string",
          description: "Entry title",
        },
        category_id: {
          type: "string",
          description: "Category ID",
        },
        data: {
          type: "object",
          description: "Key-value pairs of data to store",
        },
        notes: {
          type: "string",
          description: "Optional notes",
        },
      },
      required: ["title", "category_id", "data"],
    },
  },
  {
    name: "update_vault_entry",
    description: "Update a vault entry",
    inputSchema: {
      type: "object",
      properties: {
        entry_id: {
          type: "string",
          description: "The vault entry ID",
        },
        title: {
          type: "string",
          description: "New title",
        },
        data: {
          type: "object",
          description: "Updated data",
        },
        notes: {
          type: "string",
          description: "New notes",
        },
      },
      required: ["entry_id"],
    },
  },
  {
    name: "delete_vault_entry",
    description: "Delete a vault entry",
    inputSchema: {
      type: "object",
      properties: {
        entry_id: {
          type: "string",
          description: "The vault entry ID",
        },
      },
      required: ["entry_id"],
    },
  },
  {
    name: "grant_vault_access",
    description: "Grant a family member access to a vault entry",
    inputSchema: {
      type: "object",
      properties: {
        entry_id: {
          type: "string",
          description: "The vault entry ID",
        },
        family_member_id: {
          type: "string",
          description: "Family member ID",
        },
      },
      required: ["entry_id", "family_member_id"],
    },
  },
  {
    name: "revoke_vault_access",
    description: "Revoke a family member's access to a vault entry",
    inputSchema: {
      type: "object",
      properties: {
        entry_id: {
          type: "string",
          description: "The vault entry ID",
        },
        family_member_id: {
          type: "string",
          description: "Family member ID",
        },
      },
      required: ["entry_id", "family_member_id"],
    },
  },
];
