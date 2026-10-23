<?php

namespace Tests\Feature;

use App\Models\Family;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Laravel\Passport\Passport;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class McpServerTest extends TestCase
{
    use RefreshDatabase;

    private Family $family;

    private User $parent;

    protected function setUp(): void
    {
        parent::setUp();

        // The /mcp route tries the Passport guard first, which cannot be built without OAuth keys.
        Passport::loadKeysFrom(sys_get_temp_dir());
        if (! is_file(Passport::keyPath('oauth-public.key'))) {
            $this->artisan('passport:keys');
        }

        $this->family = Family::factory()->create(['name' => 'The Testers']);
        $this->parent = User::factory()->parent()->create(['family_id' => $this->family->id]);
    }

    public function test_unauthenticated_requests_are_rejected(): void
    {
        $this->rpc('tools/list')->assertUnauthorized();
    }

    public function test_tools_list_includes_the_family_and_module_tools(): void
    {
        Sanctum::actingAs($this->parent);

        $names = collect($this->rpc('tools/list')->assertOk()->json('result.tools'))->pluck('name');

        $this->assertContains('kinhold-family', $names);
        $this->assertContains('kinhold-tasks', $names);
    }

    public function test_tool_call_returns_family_scoped_data(): void
    {
        $otherFamily = Family::factory()->create();
        $otherParent = User::factory()->parent()->create(['family_id' => $otherFamily->id]);
        Task::create(['family_id' => $this->family->id, 'created_by' => $this->parent->id, 'title' => 'Mow the lawn']);
        Task::create(['family_id' => $otherFamily->id, 'created_by' => $otherParent->id, 'title' => 'Other family chore']);

        Sanctum::actingAs($this->parent);

        $info = $this->callTool('kinhold-family', ['action' => 'family_info']);
        $this->assertStringContainsString('The Testers', $info);

        $tasks = $this->callTool('kinhold-tasks', ['action' => 'task_list']);
        $this->assertStringContainsString('Mow the lawn', $tasks);
        $this->assertStringNotContainsString('Other family chore', $tasks);
    }

    private function callTool(string $name, array $arguments): string
    {
        $response = $this->rpc('tools/call', ['name' => $name, 'arguments' => $arguments])->assertOk();

        $this->assertFalse((bool) $response->json('result.isError'), (string) $response->json('result.content.0.text'));

        return (string) $response->json('result.content.0.text');
    }

    private function rpc(string $method, array $params = []): TestResponse
    {
        return $this->postJson('/mcp', [
            'jsonrpc' => '2.0',
            'id' => 1,
            'method' => $method,
            'params' => (object) $params,
        ]);
    }
}
