import axios, { AxiosInstance, AxiosError } from "axios";

export interface ApiError {
  message: string;
  status: number;
  originalError?: Error;
}

export class KinholdClient {
  private client: AxiosInstance;
  private apiUrl: string;
  private apiToken: string;

  constructor(apiUrl: string, apiToken: string) {
    this.apiUrl = apiUrl;
    this.apiToken = apiToken;

    this.client = axios.create({
      baseURL: apiUrl,
      headers: {
        Authorization: `Bearer ${apiToken}`,
        Accept: "application/json",
        "Content-Type": "application/json",
      },
      validateStatus: () => true, // Handle all status codes
    });
  }

  private handleError(error: AxiosError): ApiError {
    if (error.response?.data && typeof error.response.data === "object") {
      const data = error.response.data as Record<string, any>;
      return {
        message:
          data.message ||
          `API Error: ${error.response.status} ${error.response.statusText}`,
        status: error.response.status || 500,
        originalError: error,
      };
    }

    return {
      message: error.message || "Unknown API error occurred",
      status: error.response?.status || 500,
      originalError: error,
    };
  }

  async request(
    method: string,
    endpoint: string,
    data?: any,
    params?: any
  ): Promise<any> {
    try {
      const response = await this.client({
        method,
        url: endpoint,
        data,
        params,
      });

      if (response.status >= 400) {
        throw this.handleError(response as any);
      }

      return response.data;
    } catch (error) {
      if (error instanceof AxiosError) {
        throw this.handleError(error);
      }
      throw error;
    }
  }

  // Task Lists
  async listTaskLists(): Promise<any[]> {
    try {
      const response = await this.request("GET", "/task-lists");
      return response.data || response;
    } catch (error) {
      throw this.handleError(error as AxiosError);
    }
  }

  async getTaskList(listId: string): Promise<any> {
    try {
      const response = await this.request("GET", `/task-lists/${listId}`);
      return response.data || response;
    } catch (error) {
      throw this.handleError(error as AxiosError);
    }
  }

  async createTaskList(payload: {
    name: string;
    description?: string;
    color?: string;
  }): Promise<any> {
    try {
      const response = await this.request("POST", "/task-lists", payload);
      return response.data || response;
    } catch (error) {
      throw this.handleError(error as AxiosError);
    }
  }

  // Tasks
  async listTasks(filters?: {
    assignee?: string;
    status?: string;
    due_date?: string;
    list_id?: string;
  }): Promise<any[]> {
    try {
      const response = await this.request("GET", "/tasks", undefined, filters);
      return response.data || response;
    } catch (error) {
      throw this.handleError(error as AxiosError);
    }
  }

  async createTask(payload: {
    title: string;
    description?: string;
    assigned_to?: string;
    due_date?: string;
    priority?: string;
    list_id?: string;
    is_family_task?: boolean;
  }): Promise<any> {
    try {
      const response = await this.request("POST", "/tasks", payload);
      return response.data || response;
    } catch (error) {
      throw this.handleError(error as AxiosError);
    }
  }

  async updateTask(taskId: string, payload: any): Promise<any> {
    try {
      const response = await this.request("PUT", `/tasks/${taskId}`, payload);
      return response.data || response;
    } catch (error) {
      throw this.handleError(error as AxiosError);
    }
  }

  async completeTask(taskId: string): Promise<any> {
    try {
      const response = await this.request("PATCH", `/tasks/${taskId}/complete`);
      return response.data || response;
    } catch (error) {
      throw this.handleError(error as AxiosError);
    }
  }

  async uncompleteTask(taskId: string): Promise<any> {
    try {
      const response = await this.request(
        "PATCH",
        `/tasks/${taskId}/uncomplete`
      );
      return response.data || response;
    } catch (error) {
      throw this.handleError(error as AxiosError);
    }
  }

  async deleteTask(taskId: string): Promise<void> {
    try {
      await this.request("DELETE", `/tasks/${taskId}`);
    } catch (error) {
      throw this.handleError(error as AxiosError);
    }
  }

  // Calendar
  async listCalendarEvents(params: {
    start: string;
    end: string;
  }): Promise<any[]> {
    try {
      const response = await this.request(
        "GET",
        "/calendar/events",
        undefined,
        params
      );
      return response.data || response;
    } catch (error) {
      throw this.handleError(error as AxiosError);
    }
  }

  async listCalendarConnections(): Promise<any[]> {
    try {
      const response = await this.request("GET", "/calendar/connections");
      return response.data || response;
    } catch (error) {
      throw this.handleError(error as AxiosError);
    }
  }

  async syncCalendars(): Promise<any> {
    try {
      const response = await this.request("POST", "/calendar/sync");
      return response.data || response;
    } catch (error) {
      throw this.handleError(error as AxiosError);
    }
  }

  // Vault
  async listVaultCategories(): Promise<any[]> {
    try {
      const response = await this.request("GET", "/vault/categories");
      return response.data || response;
    } catch (error) {
      throw this.handleError(error as AxiosError);
    }
  }

  async listVaultEntries(categoryId?: string): Promise<any[]> {
    try {
      const params = categoryId ? { category_id: categoryId } : undefined;
      const response = await this.request(
        "GET",
        "/vault/entries",
        undefined,
        params
      );
      return response.data || response;
    } catch (error) {
      throw this.handleError(error as AxiosError);
    }
  }

  async getVaultEntry(entryId: string): Promise<any> {
    try {
      const response = await this.request("GET", `/vault/entries/${entryId}`);
      return response.data || response;
    } catch (error) {
      throw this.handleError(error as AxiosError);
    }
  }

  async createVaultEntry(payload: {
    title: string;
    category_id: string;
    data: Record<string, any>;
    notes?: string;
  }): Promise<any> {
    try {
      const response = await this.request("POST", "/vault/entries", payload);
      return response.data || response;
    } catch (error) {
      throw this.handleError(error as AxiosError);
    }
  }

  async updateVaultEntry(entryId: string, payload: any): Promise<any> {
    try {
      const response = await this.request(
        "PUT",
        `/vault/entries/${entryId}`,
        payload
      );
      return response.data || response;
    } catch (error) {
      throw this.handleError(error as AxiosError);
    }
  }

  async deleteVaultEntry(entryId: string): Promise<void> {
    try {
      await this.request("DELETE", `/vault/entries/${entryId}`);
    } catch (error) {
      throw this.handleError(error as AxiosError);
    }
  }

  async grantVaultAccess(
    entryId: string,
    familyMemberId: string
  ): Promise<any> {
    try {
      const response = await this.request(
        "POST",
        `/vault/entries/${entryId}/access`,
        { family_member_id: familyMemberId }
      );
      return response.data || response;
    } catch (error) {
      throw this.handleError(error as AxiosError);
    }
  }

  async revokeVaultAccess(
    entryId: string,
    familyMemberId: string
  ): Promise<void> {
    try {
      await this.request(
        "DELETE",
        `/vault/entries/${entryId}/access/${familyMemberId}`
      );
    } catch (error) {
      throw this.handleError(error as AxiosError);
    }
  }

  // Family
  async getFamilyInfo(): Promise<any> {
    try {
      const response = await this.request("GET", "/family");
      return response.data || response;
    } catch (error) {
      throw this.handleError(error as AxiosError);
    }
  }

  async getFamilyMember(memberId: string): Promise<any> {
    try {
      const response = await this.request("GET", `/family/members/${memberId}`);
      return response.data || response;
    } catch (error) {
      throw this.handleError(error as AxiosError);
    }
  }

  async listFamilyMembers(): Promise<any[]> {
    try {
      const response = await this.request("GET", "/family/members");
      return response.data || response;
    } catch (error) {
      throw this.handleError(error as AxiosError);
    }
  }

  // Search
  async searchFamilyData(query: string): Promise<any> {
    try {
      const response = await this.request("GET", "/search", undefined, {
        q: query,
      });
      return response.data || response;
    } catch (error) {
      throw this.handleError(error as AxiosError);
    }
  }
}
