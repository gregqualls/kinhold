import { Q32HubClient } from "../api-client.js";

export function registerFamilyTools(
  server: any,
  client: Q32HubClient
): Map<string, Function> {
  const tools = new Map<string, Function>();

  tools.set("get_family_info", async () => {
    try {
      const family = await client.getFamilyInfo();

      const membersList = family.members
        .map(
          (member: any) =>
            `  - **${member.name}** (${member.role})${member.email ? ` - ${member.email}` : ""}`
        )
        .join("\n");

      return {
        content: [
          {
            type: "text",
            text: `**${family.name}**\n${family.description ? `${family.description}\n` : ""}\nMembers (${family.members.length}):\n${membersList}`,
          },
        ],
      };
    } catch (error: any) {
      return {
        content: [
          {
            type: "text",
            text: `Error getting family info: ${error.message}`,
            isError: true,
          },
        ],
      };
    }
  });

  tools.set("get_family_member", async (input: { member_id: string }) => {
    try {
      const member = await client.getFamilyMember(input.member_id);

      const details = [
        `Name: ${member.name}`,
        `Role: ${member.role}`,
        member.email ? `Email: ${member.email}` : null,
        member.phone ? `Phone: ${member.phone}` : null,
        member.birthday ? `Birthday: ${member.birthday}` : null,
        member.avatar ? `Has avatar` : null,
      ]
        .filter(Boolean)
        .join("\n");

      return {
        content: [
          {
            type: "text",
            text: details,
          },
        ],
      };
    } catch (error: any) {
      return {
        content: [
          {
            type: "text",
            text: `Error getting family member: ${error.message}`,
            isError: true,
          },
        ],
      };
    }
  });

  tools.set("list_family_members", async () => {
    try {
      const members = await client.listFamilyMembers();

      if (!members || members.length === 0) {
        return {
          content: [
            {
              type: "text",
              text: "No family members found.",
            },
          ],
        };
      }

      const formatted = members
        .map((member: any) => {
          const email = member.email ? ` - ${member.email}` : "";
          const status = member.is_active ? "✓" : "✗";
          return `  ${status} **${member.name}** (${member.role})${email}`;
        })
        .join("\n");

      return {
        content: [
          {
            type: "text",
            text: `Family members (${members.length}):\n\n${formatted}`,
          },
        ],
      };
    } catch (error: any) {
      return {
        content: [
          {
            type: "text",
            text: `Error listing family members: ${error.message}`,
            isError: true,
          },
        ],
      };
    }
  });

  return tools;
}

export const familyToolDefinitions = [
  {
    name: "get_family_info",
    description: "Get family details and all members",
    inputSchema: {
      type: "object",
      properties: {},
      required: [],
    },
  },
  {
    name: "get_family_member",
    description: "Get details about a specific family member",
    inputSchema: {
      type: "object",
      properties: {
        member_id: {
          type: "string",
          description: "The family member ID",
        },
      },
      required: ["member_id"],
    },
  },
  {
    name: "list_family_members",
    description: "List all family members with their roles",
    inputSchema: {
      type: "object",
      properties: {},
      required: [],
    },
  },
];
