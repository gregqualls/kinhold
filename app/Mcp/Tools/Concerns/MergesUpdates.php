<?php

namespace App\Mcp\Tools\Concerns;

use Laravel\Mcp\Request;

/**
 * Build update payloads from MCP requests without losing the ability to clear
 * nullable fields. Distinguishes "field absent from request" (don't update)
 * from "field explicitly null/empty" (clear it).
 *
 * The naive pattern this replaces:
 *
 *     array_filter([...], fn ($v) => $v !== null)
 *
 * silently drops null values, so an LLM can never *clear* a nullable column
 * via update — e.g., switching a meal plan entry from "preset Leftovers"
 * back to a plain custom-title entry by setting `meal_preset_id => null`.
 */
trait MergesUpdates
{
    /**
     * Merge an update payload from a Request, given the list of fields to consider.
     *
     * @param  Request  $request  The MCP request whose `all()` we read.
     * @param  array<int, string>  $simpleFields  Fields kept verbatim (including null) when present.
     * @param  array<int, string>  $refFields  Fields where empty string and null both normalize to null. Use for FK / ID columns.
     * @return array<string, mixed>
     */
    protected function mergeUpdates(Request $request, array $simpleFields, array $refFields = []): array
    {
        $input = $request->all();
        $updates = [];

        foreach ($simpleFields as $field) {
            if (array_key_exists($field, $input)) {
                $updates[$field] = $input[$field];
            }
        }

        foreach ($refFields as $field) {
            if (array_key_exists($field, $input)) {
                $value = $input[$field];
                $updates[$field] = ($value === '' || $value === null) ? null : $value;
            }
        }

        return $updates;
    }
}
