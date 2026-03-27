import { KinholdClient } from "../api-client.js";

export function registerTaskTools(
  server: any,
  client: KinholdClient
): Map<string, Function> {
  const tools = new Map<string, Function>();

  tools.set("list_task_lists", async () => {
    try {
      const lists = await client.listTaskLists();
      if (!lists || lists.length === 0) {
        return {
          content: [{ type: "text", text: "No task lists found." }],
        };
      }

      const formatted = lists
        .map(
          (list: any) =>
            `- **${list.name}** (ID: ${list.id})${list.description ? ` - ${list.description}` : ""}${list.color ? ` [${list.color}]` : ""}`
        )
        .join("\n");

      return {
        content: [
          {
            type: "text",
            text: `Found ${lists.length} task list(s):\n\n${formatted}`,
          },
        ],
      };
    } catch (error: any) {
      return {
        content: [
          {
            type: "text",
            text: `Error listing task lists: ${error.message}`,
            isError: true,
          },
        ],
      };
    }
  });

  tools.set("get_task_list", async (input: { list_id: string }) => {
    try {
      const list = await client.getTaskList(input.list_id);
      const taskCount = list.tasks ? list.tasks.length : 0;
      const completedCount = list.tasks
        ? list.tasks.filter((t: any) => t.completed_at).length
        : 0;

      const tasksList =
        list.tasks && list.tasks.length > 0
          ? list.tasks
              .map((task: any) => {
                const status = task.completed_at ? "✓" : "○";
                const dueDate = task.due_date
                  ? ` - Due: ${task.due_date}`
                  : "";
                const assigned = task.assigned_to ? ` - Assigned to: ${task.assigned_to}` : "";
                return `  ${status} ${task.title}${assigned}${dueDate}`;
              })
              .join("\n")
          : "  (no tasks)";

      return {
        content: [
          {
            type: "text",
            text: `**${list.name}**\n${list.description ? `${list.description}\n` : ""}\nProgress: ${completedCount}/${taskCount} completed\n\nTasks:\n${tasksList}`,
          },
        ],
      };
    } catch (error: any) {
      return {
        content: [
          {
            type: "text",
            text: `Error getting task list: ${error.message}`,
            isError: true,
          },
        ],
      };
    }
  });

  tools.set(
    "create_task_list",
    async (input: {
      name: string;
      description?: string;
      color?: string;
    }) => {
      try {
        const list = await client.createTaskList(input);
        return {
          content: [
            {
              type: "text",
              text: `Created task list: **${list.name}** (ID: ${list.id})`,
            },
          ],
        };
      } catch (error: any) {
        return {
          content: [
            {
              type: "text",
              text: `Error creating task list: ${error.message}`,
              isError: true,
            },
          ],
        };
      }
    }
  );

  tools.set(
    "list_tasks",
    async (input: {
      assignee?: string;
      status?: string;
      due_date?: string;
      list_id?: string;
    }) => {
      try {
        const tasks = await client.listTasks(input);
        if (!tasks || tasks.length === 0) {
          return {
            content: [{ type: "text", text: "No tasks found matching filters." }],
          };
        }

        const formatted = tasks
          .map((task: any) => {
            const status = task.completed_at ? "✓" : "○";
            const priority =
              task.priority && task.priority !== "medium"
                ? ` [${task.priority.toUpperCase()}]`
                : "";
            const assigned = task.assigned_to
              ? ` → ${task.assigned_to}`
              : "";
            const dueDate = task.due_date ? ` (Due: ${task.due_date})` : "";
            return `${status} ${task.title}${priority}${assigned}${dueDate}`;
          })
          .join("\n");

        return {
          content: [
            {
              type: "text",
              text: `Found ${tasks.length} task(s):\n\n${formatted}`,
            },
          ],
        };
      } catch (error: any) {
        return {
          content: [
            {
              type: "text",
              text: `Error listing tasks: ${error.message}`,
              isError: true,
            },
          ],
        };
      }
    }
  );

  tools.set(
    "create_task",
    async (input: {
      title: string;
      description?: string;
      assigned_to?: string;
      due_date?: string;
      priority?: string;
      list_id?: string;
      is_family_task?: boolean;
    }) => {
      try {
        const task = await client.createTask(input);
        const assigned = input.assigned_to
          ? ` assigned to ${input.assigned_to}`
          : "";
        const due = input.due_date ? ` due on ${input.due_date}` : "";
        return {
          content: [
            {
              type: "text",
              text: `Created task: **${task.title}** (ID: ${task.id})${assigned}${due}`,
            },
          ],
        };
      } catch (error: any) {
        return {
          content: [
            {
              type: "text",
              text: `Error creating task: ${error.message}`,
              isError: true,
            },
          ],
        };
      }
    }
  );

  tools.set(
    "update_task",
    async (input: { task_id: string; [key: string]: any }) => {
      try {
        const { task_id, ...payload } = input;
        const task = await client.updateTask(task_id, payload);
        return {
          content: [
            {
              type: "text",
              text: `Updated task: **${task.title}** (ID: ${task.id})`,
            },
          ],
        };
      } catch (error: any) {
        return {
          content: [
            {
              type: "text",
              text: `Error updating task: ${error.message}`,
              isError: true,
            },
          ],
        };
      }
    }
  );

  tools.set("complete_task", async (input: { task_id: string }) => {
    try {
      const task = await client.completeTask(input.task_id);
      return {
        content: [
          {
            type: "text",
            text: `Completed task: **${task.title}**`,
          },
        ],
      };
    } catch (error: any) {
      return {
        content: [
          {
            type: "text",
            text: `Error completing task: ${error.message}`,
            isError: true,
          },
        ],
      };
    }
  });

  tools.set("uncomplete_task", async (input: { task_id: string }) => {
    try {
      const task = await client.uncompleteTask(input.task_id);
      return {
        content: [
          {
            type: "text",
            text: `Marked as incomplete: **${task.title}**`,
          },
        ],
      };
    } catch (error: any) {
      return {
        content: [
          {
            type: "text",
            text: `Error uncompleting task: ${error.message}`,
            isError: true,
          },
        ],
      };
    }
  });

  tools.set("delete_task", async (input: { task_id: string }) => {
    try {
      await client.deleteTask(input.task_id);
      return {
        content: [
          {
            type: "text",
            text: `Task deleted successfully.`,
          },
        ],
      };
    } catch (error: any) {
      return {
        content: [
          {
            type: "text",
            text: `Error deleting task: ${error.message}`,
            isError: true,
          },
        ],
      };
    }
  });

  return tools;
}

export const taskToolDefinitions = [
  {
    name: "list_task_lists",
    description: "List all task lists in the family",
    inputSchema: {
      type: "object",
      properties: {},
      required: [],
    },
  },
  {
    name: "get_task_list",
    description: "Get a specific task list with all its tasks",
    inputSchema: {
      type: "object",
      properties: {
        list_id: {
          type: "string",
          description: "The ID of the task list",
        },
      },
      required: ["list_id"],
    },
  },
  {
    name: "create_task_list",
    description: "Create a new task list",
    inputSchema: {
      type: "object",
      properties: {
        name: {
          type: "string",
          description: "Name of the task list",
        },
        description: {
          type: "string",
          description: "Optional description",
        },
        color: {
          type: "string",
          description: "Optional color (e.g., 'blue', 'red', 'green')",
        },
      },
      required: ["name"],
    },
  },
  {
    name: "list_tasks",
    description: "List tasks with optional filters",
    inputSchema: {
      type: "object",
      properties: {
        assignee: {
          type: "string",
          description: "Filter by assignee",
        },
        status: {
          type: "string",
          enum: ["pending", "completed"],
          description: "Filter by status",
        },
        due_date: {
          type: "string",
          description: "Filter by due date",
        },
        list_id: {
          type: "string",
          description: "Filter by task list ID",
        },
      },
      required: [],
    },
  },
  {
    name: "create_task",
    description: "Create a new task",
    inputSchema: {
      type: "object",
      properties: {
        title: {
          type: "string",
          description: "Task title",
        },
        description: {
          type: "string",
          description: "Task description",
        },
        assigned_to: {
          type: "string",
          description: "Assign task to family member (by name or ID)",
        },
        due_date: {
          type: "string",
          description: "Due date (YYYY-MM-DD format)",
        },
        priority: {
          type: "string",
          enum: ["low", "medium", "high"],
          description: "Task priority",
        },
        list_id: {
          type: "string",
          description: "Task list ID",
        },
        is_family_task: {
          type: "boolean",
          description: "Whether this is a family task (shared)",
        },
      },
      required: ["title"],
    },
  },
  {
    name: "update_task",
    description: "Update an existing task",
    inputSchema: {
      type: "object",
      properties: {
        task_id: {
          type: "string",
          description: "The task ID",
        },
        title: {
          type: "string",
          description: "New title",
        },
        description: {
          type: "string",
          description: "New description",
        },
        assigned_to: {
          type: "string",
          description: "New assignee",
        },
        due_date: {
          type: "string",
          description: "New due date",
        },
        priority: {
          type: "string",
          enum: ["low", "medium", "high"],
        },
      },
      required: ["task_id"],
    },
  },
  {
    name: "complete_task",
    description: "Mark a task as complete",
    inputSchema: {
      type: "object",
      properties: {
        task_id: {
          type: "string",
          description: "The task ID",
        },
      },
      required: ["task_id"],
    },
  },
  {
    name: "uncomplete_task",
    description: "Mark a task as incomplete",
    inputSchema: {
      type: "object",
      properties: {
        task_id: {
          type: "string",
          description: "The task ID",
        },
      },
      required: ["task_id"],
    },
  },
  {
    name: "delete_task",
    description: "Delete a task",
    inputSchema: {
      type: "object",
      properties: {
        task_id: {
          type: "string",
          description: "The task ID",
        },
      },
      required: ["task_id"],
    },
  },
];
