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
 *
 * THREE FIELD-LIST PARAMS, three semantics:
 *
 * - simpleFields    — pass-through. Includes null when present, but `""` is
 *                     kept as `""` (not normalized to null). Use for columns
 *                     where empty string is a meaningful value distinct from
 *                     null (rare).
 *
 * - refFields       — FK / ID columns. `null` and `""` both normalize to null.
 *                     The LLM-friendly convention so models can drop a
 *                     reference with either form.
 *
 * - nullableFields  — Nullable text columns where the LLM might pass `""`
 *                     intending "clear it" (because that's what an empty form
 *                     input would send). Same `null`/`""` → null normalization
 *                     as refFields. Opt-in per call so simpleFields stays
 *                     strict by default.
 */
trait MergesUpdates
{
    /**
     * Merge an update payload from a Request, given the list of fields to consider.
     *
     * @param  Request  $request  The MCP request whose `all()` we read.
     * @param  array<int, string>  $simpleFields  Fields kept verbatim (including null) when present. `""` is preserved as `""`.
     * @param  array<int, string>  $refFields  FK/ID columns where `""` and `null` both normalize to null.
     * @param  array<int, string>  $nullableFields  Nullable scalar columns where `""` and `null` both normalize to null (opt-in).
     * @return array<string, mixed>
     */
    protected function mergeUpdates(
        Request $request,
        array $simpleFields,
        array $refFields = [],
        array $nullableFields = [],
    ): array {
        $input = $request->all();
        $updates = [];

        foreach ($simpleFields as $field) {
            if (array_key_exists($field, $input)) {
                $updates[$field] = $input[$field];
            }
        }

        foreach (array_merge($refFields, $nullableFields) as $field) {
            if (array_key_exists($field, $input)) {
                $value = $input[$field];
                $updates[$field] = ($value === '' || $value === null) ? null : $value;
            }
        }

        return $updates;
    }
}
