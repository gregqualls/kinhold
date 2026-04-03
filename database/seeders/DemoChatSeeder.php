<?php

namespace Database\Seeders;

use App\Models\ChatMessage;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DemoChatSeeder extends Seeder
{
    use DemoFamilyContext;

    public function run(): void
    {
        $this->loadDemoContext();

        $now = Carbon::now();

        // ─────────────────────────────────────────────
        //  CHAT MESSAGES (sample conversation)
        // ─────────────────────────────────────────────

        $chatConvo = [
            ['role' => 'user', 'user' => $this->mike, 'message' => 'What tasks are due this week?', 'days_ago' => 5, 'hour' => 8],
            ['role' => 'assistant', 'user' => $this->mike, 'message' => "Here are the tasks due this week for the Johnson family:\n\n• **Emma** — Sort & file school papers (10 pts) — due today\n• **Jake** — Sweep garage (10 pts) — due in 2 days\n• **Lily** — Practice piano recital piece (15 pts) — due in 3 days\n• **Sarah** — Meal plan for the week (10 pts) — due tomorrow\n\nWould you like me to add any new tasks or check on something else?", 'days_ago' => 5, 'hour' => 8],
            ['role' => 'user', 'user' => $this->sarah, 'message' => "Who's in the lead on the leaderboard?", 'days_ago' => 3, 'hour' => 19],
            ['role' => 'assistant', 'user' => $this->sarah, 'message' => "Here's the current weekly leaderboard:\n\n**1st — Emma** — 15 pts this week\n**2nd — Jake** — 10 pts this week\n**3rd — Lily** — 15 pts this week\n\nEmma and Lily are neck and neck! Emma has been on a roll with her SAT prep and chores.", 'days_ago' => 3, 'hour' => 19],
            ['role' => 'user', 'user' => $this->mike, 'message' => "What's the wifi password?", 'days_ago' => 2, 'hour' => 14],
            ['role' => 'assistant', 'user' => $this->mike, 'message' => "Your Wi-Fi password is: **B1scu1t-Loves-Treats!** (network: JohnsonFamily5G)\n\nGuest network: **JohnsonGuest** / Welcome2024", 'days_ago' => 2, 'hour' => 14],
            ['role' => 'user', 'user' => $this->emma, 'message' => 'How many points do I have?', 'days_ago' => 1, 'hour' => 16],
            ['role' => 'assistant', 'user' => $this->emma, 'message' => "You currently have **185 points** in your bank, Emma!\n\nYou've earned a total of 325 points and spent 140 on rewards. Your most recent reward was **Movie Night Pick** (40 pts) last week.\n\nYou're getting close to being able to afford a **Friend Sleepover** (100 pts)!", 'days_ago' => 1, 'hour' => 16],
            ['role' => 'user', 'user' => $this->sarah, 'message' => "When is Jake's next dentist appointment?", 'days_ago' => 0, 'hour' => 9],
            ['role' => 'assistant', 'user' => $this->sarah, 'message' => "Jake's next dentist appointment is **April 15** at Bright Smiles Family Dental with Dr. Maria Lopez. Lily also has an appointment the same day.\n\nNote from the vault: Jake may need a braces evaluation this summer.", 'days_ago' => 0, 'hour' => 9],
        ];

        foreach ($chatConvo as $msg) {
            $createdAt = $now->copy()->subDays($msg['days_ago'])->setHour($msg['hour'])->setMinute(rand(0, 30));
            ChatMessage::create([
                'family_id' => $this->familyId(),
                'user_id' => $msg['user']->id,
                'role' => $msg['role'],
                'message' => $msg['message'],
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
        }
    }
}
