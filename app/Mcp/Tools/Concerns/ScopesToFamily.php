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
        if (! $this->isParent()) {
            return Response::error('Only parents can perform this action.');
        }

        return null;
    }

    /**
     * Authorize an action using Laravel Policies.
     *
     * Delegates to the same policies that API controllers use,
     * ensuring MCP tools and API share identical authorization rules.
     *
     * @param  string  $ability  The policy method name (e.g., 'create', 'delete')
     * @param  mixed  $model  Model instance or class string (e.g., Tag::class)
     * @return Response|null Error response if denied, null if authorized
     */
    protected function authorize(string $ability, mixed $model): ?Response
    {
        if (! $this->user()->can($ability, $model)) {
            return Response::error('Only parents can perform this action.');
        }

        return null;
    }
}
