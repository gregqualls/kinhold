<?php

namespace App\Services\Agent;

use App\Mcp\Servers\KinholdServer;
use Laravel\Mcp\Request as McpRequest;
use Laravel\Mcp\Server\Tool;
use ReflectionClass;

class ToolRegistry
{
    /** @var array<string, class-string<Tool>> */
    private array $toolMap = [];

    /** @var array<int, array{name: string, description: string, input_schema: array<string, mixed>}> */
    private ?array $cachedDefinitions = null;

    public function __construct()
    {
        $this->loadTools();
    }

    /**
     * Get tool definitions in Claude API tool_use format.
     *
     * @return array<int, array{name: string, description: string, input_schema: array<string, mixed>}>
     */
    public function getToolDefinitions(): array
    {
        if ($this->cachedDefinitions !== null) {
            return $this->cachedDefinitions;
        }

        $definitions = [];

        foreach ($this->toolMap as $name => $toolClass) {
            $tool = new $toolClass;
            $arr = $tool->toArray();

            $definitions[] = [
                'name' => $arr['name'],
                'description' => $arr['description'] ?? '',
                'input_schema' => $arr['inputSchema'] ?? ['type' => 'object', 'properties' => (object) []],
            ];
        }

        $this->cachedDefinitions = $definitions;

        return $definitions;
    }

    /**
     * Execute a tool by name with the given input.
     *
     * @param  array<string, mixed>  $input
     * @return array{content: string, is_error: bool}
     */
    public function execute(string $name, array $input): array
    {
        $toolClass = $this->toolMap[$name] ?? null;

        if (! $toolClass) {
            return [
                'content' => "Unknown tool: {$name}",
                'is_error' => true,
            ];
        }

        try {
            $tool = new $toolClass;
            $request = new McpRequest(arguments: $input);
            $response = $tool->handle($request);

            return [
                'content' => $response->content()->__toString(),
                'is_error' => $response->isError(),
            ];
        } catch (\Throwable $e) {
            return [
                'content' => "Tool execution failed: {$e->getMessage()}",
                'is_error' => true,
            ];
        }
    }

    /**
     * Get the list of available tool names.
     *
     * @return array<int, string>
     */
    public function getToolNames(): array
    {
        return array_keys($this->toolMap);
    }

    private function loadTools(): void
    {
        $reflection = new ReflectionClass(KinholdServer::class);
        $toolsProperty = $reflection->getProperty('tools');
        $toolClasses = $toolsProperty->getDefaultValue();

        foreach ($toolClasses as $toolClass) {
            $tool = new $toolClass;
            $this->toolMap[$tool->toArray()['name']] = $toolClass;
        }
    }
}
