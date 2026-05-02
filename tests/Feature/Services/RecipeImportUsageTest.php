<?php

namespace Tests\Feature\Services;

use App\Enums\FamilyRole;
use App\Models\AiUsageDaily;
use App\Models\Family;
use App\Models\User;
use App\Services\RecipeImportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Regression: 70-D closed a gap from #137 where RecipeImportService's two
 * Anthropic calls didn't go through AiUsageService::recordMessage(), so recipe
 * AI usage was invisible to the daily cap. These tests pin the wiring so a
 * future refactor can't silently drop it.
 */
class RecipeImportUsageTest extends TestCase
{
    use RefreshDatabase;

    private Family $family;

    private User $parent;

    protected function setUp(): void
    {
        parent::setUp();

        // Platform key configured + self_hosted off → AiUsageService enforces.
        config(['kinhold.chatbot.api_key' => 'test-platform-key']);
        config(['kinhold.self_hosted' => false]);

        $this->family = Family::create([
            'name' => 'Test Family',
            'slug' => 'recipe-usage-fam',
            'invite_code' => 'RUF001',
        ]);

        $this->parent = User::create([
            'name' => 'Parent',
            'email' => 'recipe-usage-parent@test.com',
            'password' => bcrypt('password'),
            'family_id' => $this->family->id,
            'family_role' => FamilyRole::Parent,
        ]);
    }

    private function fakeAnthropicResponse(int $inputTokens = 123, int $outputTokens = 45): string
    {
        return json_encode([
            'content' => [[
                'type' => 'text',
                'text' => json_encode([
                    'title' => 'Test Recipe',
                    'description' => null,
                    'servings' => 2,
                    'prep_time' => 5,
                    'cook_time' => 10,
                    'ingredients' => [
                        ['name' => 'flour', 'quantity' => '1', 'unit' => 'cup', 'preparation' => null],
                    ],
                    'instructions' => ['Mix.', 'Cook.'],
                ]),
            ]],
            'stop_reason' => 'end_turn',
            'usage' => [
                'input_tokens' => $inputTokens,
                'output_tokens' => $outputTokens,
            ],
        ]);
    }

    public function test_photo_import_records_ai_usage(): void
    {
        Storage::fake('public');

        Http::fake([
            'api.anthropic.com/*' => Http::response($this->fakeAnthropicResponse(200, 80), 200),
        ]);

        $photo = UploadedFile::fake()->image('recipe.jpg', 400, 300);

        app(RecipeImportService::class)->importFromPhoto($photo, $this->family, $this->parent, preview: true);

        $row = AiUsageDaily::where('family_id', $this->family->id)->first();
        $this->assertNotNull($row, 'Expected an AiUsageDaily row to be created.');
        $this->assertSame(1, $row->message_count);
        $this->assertSame(200, $row->input_tokens);
        $this->assertSame(80, $row->output_tokens);
    }

    public function test_url_import_records_ai_usage_when_falling_back_to_llm(): void
    {
        // Plain HTML (no JSON-LD) forces extractViaLlm() — the path that
        // actually calls Anthropic. JSON-LD pages skip the LLM entirely and
        // legitimately don't record usage.
        $plainHtml = '<html><body><h1>Banana Bread</h1><p>Mash bananas. Mix flour. Bake.</p></body></html>';

        Http::fake([
            'example.com/*' => Http::response($plainHtml, 200),
            'api.anthropic.com/*' => Http::response($this->fakeAnthropicResponse(150, 60), 200),
        ]);

        app(RecipeImportService::class)->importFromUrl(
            'https://example.com/banana-bread',
            $this->family,
            $this->parent,
            preview: true,
        );

        $row = AiUsageDaily::where('family_id', $this->family->id)->first();
        $this->assertNotNull($row);
        $this->assertSame(1, $row->message_count);
        $this->assertSame(150, $row->input_tokens);
        $this->assertSame(60, $row->output_tokens);
    }

    public function test_byok_family_does_not_record_usage(): void
    {
        // BYOK families pay their own bill — Kinhold should never count those
        // requests toward the daily cap.
        $this->family->update([
            'settings' => [
                'ai_mode' => 'byok',
                'ai_api_key' => encrypt('sk-ant-fake-byok-key'),
            ],
        ]);
        // Drop the platform key entirely so AgentService::resolveApiKey
        // returns the BYOK key (not null), making usesPlatformKey false.
        config(['kinhold.chatbot.api_key' => null]);

        Storage::fake('public');

        Http::fake([
            'api.anthropic.com/*' => Http::response($this->fakeAnthropicResponse(), 200),
        ]);

        $photo = UploadedFile::fake()->image('recipe.jpg', 400, 300);

        app(RecipeImportService::class)->importFromPhoto($photo, $this->family, $this->parent, preview: true);

        $this->assertSame(
            0,
            AiUsageDaily::where('family_id', $this->family->id)->count(),
            'BYOK families should not have usage rows from recipe imports.',
        );
    }
}
