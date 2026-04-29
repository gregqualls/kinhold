<?php

namespace App\Services;

use App\Models\Badge;
use App\Models\CalendarConnection;
use App\Models\ChatMessage;
use App\Models\MealPlan;
use App\Models\PointRequest;
use App\Models\PointTransaction;
use App\Models\Rating;
use App\Models\Recipe;
use App\Models\RewardPurchase;
use App\Models\ShoppingList;
use App\Models\Task;
use App\Models\User;
use App\Models\VaultEntry;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use ZipArchive;

class UserDataExportService
{
    public function buildExport(User $user): Response
    {
        // Future: switch to ZipStream-PHP for true streaming if memory becomes a concern.
        set_time_limit(120);
        ini_set('memory_limit', '512M');

        $tmp = tempnam(sys_get_temp_dir(), 'kinhold-export-');
        $zip = new ZipArchive;
        $zip->open($tmp, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        $files = [
            'user.json' => $this->exportUser($user),
            'tasks.json' => $this->exportTasks($user),
            'vault.json' => $this->exportVault($user, $zip),
            'points.json' => $this->exportPoints($user),
            'badges.json' => $this->exportBadges($user),
            'chat.json' => $this->exportChat($user),
            'calendar.json' => $this->exportCalendar($user),
            'food.json' => $this->exportFood($user, $zip),
        ];

        foreach ($files as $name => $payload) {
            $zip->addFromString($name, json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        }

        $zip->addFromString('manifest.json', json_encode(
            $this->buildManifest($user, array_merge(['manifest.json'], array_keys($files))),
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        ));

        $this->bundleAvatar($user, $zip);

        $zip->close();

        $bytes = file_get_contents($tmp);
        @unlink($tmp);

        $filename = 'kinhold-export-'.$user->id.'-'.now()->format('Y-m-d').'.zip';

        return response($bytes, 200, [
            'Content-Type' => 'application/zip',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Content-Length' => (string) strlen($bytes),
        ]);
    }

    private function buildManifest(User $user, array $files): array
    {
        return [
            'version' => '1.0',
            'exported_at' => now()->toIso8601String(),
            'user_id' => $user->id,
            'family_id' => $user->family_id,
            'app_version' => config('version.current'),
            'files' => array_values($files),
        ];
    }

    private function exportUser(User $user): array
    {
        return $user->makeHidden([
            'password',
            'remember_token',
            'google_refresh_token',
            'google_access_token',
            'two_factor_secret',
            'two_factor_recovery_codes',
        ])->toArray();
    }

    private function exportTasks(User $user): array
    {
        $userId = (string) $user->id;

        return Task::query()
            ->where(function ($q) use ($userId) {
                $q->where('created_by', $userId)->orWhere('assigned_to', $userId);
            })
            ->with('tags')
            ->get()
            ->map(function (Task $task) use ($userId) {
                $isCreator = (string) $task->created_by === $userId;
                $isAssignee = (string) $task->assigned_to === $userId;
                $role = match (true) {
                    $isCreator && $isAssignee => 'both',
                    $isCreator => 'creator',
                    default => 'assignee',
                };

                return $task->toArray() + ['_role' => $role];
            })
            ->all();
    }

    private function exportVault(User $user, ZipArchive $zip): array
    {
        $entries = VaultEntry::query()
            ->where(function ($q) use ($user) {
                $q->where('created_by', $user->id)
                    ->orWhereHas('permissions', fn ($p) => $p->where('user_id', $user->id));
            })
            ->with('documents')
            ->get();

        return $entries->map(function (VaultEntry $entry) use ($zip) {
            $row = $entry->toArray();
            $row['encrypted_data'] = null;
            // Mirror VaultEncryptionService: encryptString + json_encode pair.
            // VaultEntry::getDecryptedData() calls plain decrypt() which unserializes
            // and crashes on the production JSON-only payloads.
            if ($entry->encrypted_data) {
                try {
                    $row['data'] = json_decode(Crypt::decryptString($entry->encrypted_data), true) ?? [];
                } catch (DecryptException) {
                    $row['data'] = null;
                    $row['_error'] = 'decryption_failed';
                }
            } else {
                $row['data'] = [];
            }

            $row['documents'] = $entry->documents->map(function ($doc) use ($zip) {
                $archivePath = 'vault-documents/'.$doc->id.'/'.$doc->original_filename;

                $disk = Storage::disk($doc->disk ?: 'local');
                $archived = $disk->exists($doc->path);
                if ($archived) {
                    $zip->addFromString($archivePath, $disk->get($doc->path));
                }

                $docRow = [
                    'id' => $doc->id,
                    'original_filename' => $doc->original_filename,
                    'mime_type' => $doc->mime_type,
                    'size' => $doc->size,
                    'uploaded_by' => $doc->uploaded_by,
                    'created_at' => $doc->created_at?->toIso8601String(),
                ];

                if ($archived) {
                    $docRow['archive_path'] = $archivePath;
                }

                return $docRow;
            })->all();

            return $row;
        })->all();
    }

    private function exportPoints(User $user): array
    {
        return [
            'transactions' => PointTransaction::where('user_id', $user->id)->get()->toArray(),
            'requests' => PointRequest::where('user_id', $user->id)->get()->toArray(),
            'purchases' => RewardPurchase::where('user_id', $user->id)->with('reward')->get()->toArray(),
        ];
    }

    private function exportBadges(User $user): array
    {
        $pivots = DB::table('user_badges')
            ->where('user_id', $user->id)
            ->get(['badge_id', 'earned_at', 'awarded_by'])
            ->keyBy('badge_id');

        return Badge::whereIn('id', $pivots->keys())->get()->map(function (Badge $badge) use ($pivots) {
            $pivot = $pivots[$badge->id];

            return $badge->toArray() + [
                'earned_at' => $pivot->earned_at,
                'awarded_by' => $pivot->awarded_by,
            ];
        })->all();
    }

    private function exportChat(User $user): array
    {
        return ChatMessage::where('user_id', $user->id)
            ->orderBy('created_at')
            ->get(['id', 'role', 'message', 'metadata', 'created_at'])
            ->toArray();
    }

    private function exportCalendar(User $user): array
    {
        return CalendarConnection::where('user_id', $user->id)
            ->get()
            ->each->makeHidden(['access_token', 'refresh_token'])
            ->toArray();
    }

    private function exportFood(User $user, ZipArchive $zip): array
    {
        $recipes = Recipe::where('created_by', $user->id)
            ->with(['ingredients', 'tags'])
            ->get()
            ->map(function (Recipe $recipe) use ($zip) {
                $row = $recipe->toArray();

                if ($recipe->image_path) {
                    $disk = Storage::disk('public');
                    if ($disk->exists($recipe->image_path)) {
                        $archivePath = 'recipe-images/'.$recipe->id.'/'.basename($recipe->image_path);
                        $zip->addFromString($archivePath, $disk->get($recipe->image_path));
                        $row['image_archive_path'] = $archivePath;
                    }
                }

                return $row;
            })
            ->all();

        $shoppingLists = ShoppingList::where('created_by', $user->id)
            ->with('items')
            ->get()
            ->toArray();

        $mealPlans = MealPlan::where('created_by', $user->id)
            ->with('entries')
            ->get()
            ->toArray();

        $recipeRatings = Rating::where('user_id', $user->id)
            ->where('rateable_type', Recipe::class)
            ->get()
            ->toArray();

        return [
            'recipes' => $recipes,
            'shopping_lists' => $shoppingLists,
            'meal_plans' => $mealPlans,
            'recipe_ratings' => $recipeRatings,
        ];
    }

    private function bundleAvatar(User $user, ZipArchive $zip): void
    {
        if (! $user->avatar || ! str_starts_with($user->avatar, 'avatars/')) {
            return;
        }

        $disk = Storage::disk('local');
        if ($disk->exists($user->avatar)) {
            $zip->addFromString('avatar/'.basename($user->avatar), $disk->get($user->avatar));
        }
    }
}
