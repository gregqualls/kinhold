<?php

namespace App\Services;

use Illuminate\Support\Facades\Crypt;

class VaultEncryptionService
{
    /**
     * Encrypt sensitive data.
     */
    public function encrypt(array $data): string
    {
        $json = json_encode($data);

        return Crypt::encryptString($json);
    }

    /**
     * Decrypt sensitive data.
     */
    public function decrypt(string $encrypted): array
    {
        try {
            $json = Crypt::decryptString($encrypted);

            return json_decode($json, true) ?? [];
        } catch (\Exception $e) {
            \Log::error('Failed to decrypt vault entry: '.$e->getMessage());

            return [];
        }
    }
}
