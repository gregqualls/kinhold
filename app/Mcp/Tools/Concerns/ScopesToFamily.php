<?php

namespace App\Mcp\Tools\Concerns;

use App\Models\Family;
use App\Models\User;
use Laravel\Mcp\Response;

trait ScopesToFamily
{
    protected function user(): User
    {
        return request()->user();
    }

    protected function family(): Family
    {
        return $this->user()->family;
    }

    protected function familyId(): string
    {
        return $this->user()->family_id;
    }

    protected function isParent(): bool
    {
        return $this->user()->isParent();
    }

    protected function requireParent(): ?Response
    {
        if (!$this->isParent()) {
            return Response::error('Only parents can perform this action.');
        }

        return null;
    }
}
