---
description: Single-command quality gate — runs all automated checks and reports pass/fail
model: haiku
---

Run the quality gate script and present the results.

## Steps

1. Run `./scripts/check.sh` and capture the full output.
2. Present the output to the user as-is — it already contains a formatted results table.
3. If the script exits with code 0, confirm: **"All checks passed. Ready for `/pr`."**
4. If the script exits with non-zero, state: **"Checks failed. Fix issues before creating a PR."** and note that `/fix` can auto-fix formatting and lint issues.

## Rules

- Do NOT interpret, summarize, or reformat the script output — show it verbatim.
- Do NOT fix anything — that's `/fix`'s job. This is diagnosis only.
- If the script itself fails to run (missing permissions, bash error), show the error and suggest `chmod +x scripts/check.sh`.
