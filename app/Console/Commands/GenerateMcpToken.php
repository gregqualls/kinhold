<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class GenerateMcpToken extends Command
{
    protected $signature = 'mcp:token {email : The user email to generate a token for}';

    protected $description = 'Generate a Sanctum token for MCP access';

    public function handle(): int
    {
        $user = User::where('email', $this->argument('email'))->first();

        if (!$user) {
            $this->error("User not found: {$this->argument('email')}");
            return 1;
        }

        $token = $user->createToken('mcp')->plainTextToken;

        $this->info("MCP token for {$user->name}:");
        $this->line($token);

        return 0;
    }
}
