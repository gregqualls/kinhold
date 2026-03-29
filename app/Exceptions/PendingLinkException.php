<?php

namespace App\Exceptions;

use Exception;

class PendingLinkException extends Exception
{
    public function __construct(
        public readonly string $pendingCode,
        public readonly string $email,
    ) {
        parent::__construct('Account exists with password — pending link confirmation');
    }
}
