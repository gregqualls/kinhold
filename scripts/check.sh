#!/bin/bash
#
# Kinhold Quality Gate — runs all checks and outputs a structured results table.
# Used by /check (Claude skill) and can be run standalone or in CI.
#
# Usage: ./scripts/check.sh
#
# Compatible with macOS (BSD) and Linux (GNU).
#

cd "$(dirname "$0")/.."

# ── Ensure Homebrew tools are in PATH (macOS) ──────────────────
if [[ -d /opt/homebrew/bin ]]; then
  export PATH="/opt/homebrew/bin:$PATH"
elif [[ -d /usr/local/bin ]]; then
  export PATH="/usr/local/bin:$PATH"
fi

# ── Timing ──────────────────────────────────────────────────────
START_TIME=$(date +%s)

# ── Context detection ───────────────────────────────────────────
BRANCH=$(git branch --show-current 2>/dev/null || echo "detached")
CHANGED_PHP=""
CHANGED_VUE=""
ALL_CHANGED=""

if [[ "$BRANCH" != "main" && "$BRANCH" != "detached" ]]; then
  COMMITTED=$(git diff --name-only main...HEAD 2>/dev/null || true)
  UNCOMMITTED=$(git diff --name-only 2>/dev/null || true)
  ALL_CHANGED=$(printf '%s\n%s' "$COMMITTED" "$UNCOMMITTED" | sort -u | grep -v '^$')
  CHANGED_PHP=$(echo "$ALL_CHANGED" | grep '\.php$' || true)
  CHANGED_VUE=$(echo "$ALL_CHANGED" | grep '\.vue$' || true)
  CHANGED_COUNT=$(echo "$ALL_CHANGED" | grep -c . || echo 0)
  SCOPE="branch ($BRANCH — $CHANGED_COUNT changed files)"
else
  SCOPE="full (on $BRANCH)"
fi

# ── Result tracking ─────────────────────────────────────────────
RESULT_COUNT=0
declare -a R_NAMES
declare -a R_STATUSES
declare -a R_DETAILS
PASS=0; FAIL=0; WARN=0; SKIP=0

add_result() {
  R_NAMES[$RESULT_COUNT]="$1"
  R_STATUSES[$RESULT_COUNT]="$2"
  R_DETAILS[$RESULT_COUNT]="$3"
  case "$2" in
    PASS) PASS=$((PASS + 1)) ;;
    FAIL) FAIL=$((FAIL + 1)) ;;
    WARN) WARN=$((WARN + 1)) ;;
    SKIP) SKIP=$((SKIP + 1)) ;;
  esac
  RESULT_COUNT=$((RESULT_COUNT + 1))
}

# ── Check 1: PHP Syntax ────────────────────────────────────────
run_php_syntax() {
  local file_list=""
  if [[ -n "$CHANGED_PHP" ]]; then
    file_list="$CHANGED_PHP"
  else
    file_list=$(find app -name '*.php' -type f)
  fi

  local errors=0
  local count=0
  while IFS= read -r f; do
    [[ -z "$f" || ! -f "$f" ]] && continue
    count=$((count + 1))
    if ! php -l "$f" > /dev/null 2>&1; then
      errors=$((errors + 1))
    fi
  done <<< "$file_list"

  if [[ $errors -eq 0 ]]; then
    add_result "PHP Syntax" "PASS" "$count files checked"
  else
    add_result "PHP Syntax" "FAIL" "$errors files with syntax errors"
  fi
}

# ── Check 2: Pint (formatting) ─────────────────────────────────
run_pint() {
  if [[ ! -f ./vendor/bin/pint ]]; then
    add_result "Pint (formatting)" "SKIP" "pint not installed"
    return
  fi
  local output
  if output=$(./vendor/bin/pint --test 2>&1); then
    add_result "Pint (formatting)" "PASS" "No formatting issues"
  else
    local count
    count=$(echo "$output" | grep -c 'FAIL' 2>/dev/null || echo 0)
    count=$(echo "$count" | tr -d '[:space:]')
    add_result "Pint (formatting)" "FAIL" "$count files need formatting. Run /fix"
  fi
}

# ── Check 3: Larastan (static analysis) ────────────────────────
run_larastan() {
  if [[ ! -f ./vendor/bin/phpstan ]]; then
    add_result "Larastan (analysis)" "SKIP" "phpstan not installed"
    return
  fi
  local output
  if output=$(./vendor/bin/phpstan analyse --memory-limit=512M --no-progress 2>&1); then
    add_result "Larastan (analysis)" "PASS" "0 errors"
  else
    local err_msg
    err_msg=$(echo "$output" | grep -E '[0-9]+ error' | head -1 || echo "errors found")
    add_result "Larastan (analysis)" "FAIL" "$err_msg"
  fi
}

# ── Check 4: PHPUnit (tests + optional coverage) ──────────────
run_phpunit() {
  if [[ ! -f ./vendor/bin/phpunit ]]; then
    add_result "PHPUnit (tests)" "SKIP" "phpunit not installed"
    add_result "Test Coverage" "SKIP" "phpunit not installed"
    return
  fi

  local has_coverage=false
  if php -m 2>/dev/null | grep -qi pcov || php -m 2>/dev/null | grep -qi xdebug; then
    has_coverage=true
  fi

  local output exit_code=0
  if $has_coverage; then
    output=$(./vendor/bin/phpunit --coverage-text --coverage-filter=app 2>&1) || exit_code=$?
  else
    output=$(./vendor/bin/phpunit 2>&1) || exit_code=$?
  fi

  if [[ $exit_code -eq 0 ]]; then
    local summary
    summary=$(echo "$output" | grep -E 'Tests:' | tail -1 || echo "passed")
    summary=$(echo "$summary" | sed 's/^[[:space:]]*//')
    add_result "PHPUnit (tests)" "PASS" "$summary"
  else
    local summary
    summary=$(echo "$output" | grep -E 'FAILURES|ERRORS|Tests:' | tail -1 || echo "failed")
    add_result "PHPUnit (tests)" "FAIL" "$summary"
  fi

  if $has_coverage; then
    local coverage_pct
    coverage_pct=$(echo "$output" | grep 'Lines:' | head -1 | sed 's/[^0-9.]//g' | cut -d. -f1 || echo "0")
    [[ -z "$coverage_pct" ]] && coverage_pct=0

    if [[ $coverage_pct -ge 40 ]]; then
      add_result "Test Coverage" "PASS" "${coverage_pct}% line coverage (target: 40%)"
    elif [[ $coverage_pct -ge 20 ]]; then
      add_result "Test Coverage" "WARN" "${coverage_pct}% line coverage (target: 40%)"
    else
      add_result "Test Coverage" "FAIL" "${coverage_pct}% line coverage (target: 40%)"
    fi
  else
    add_result "Test Coverage" "SKIP" "Install pcov (pecl install pcov) for coverage"
  fi
}

# ── Check 5: Vite (frontend build) ─────────────────────────────
VITE_OUTPUT=""
run_vite() {
  local output exit_code=0
  output=$(npx vite build 2>&1) || exit_code=$?
  VITE_OUTPUT="$output"

  if [[ $exit_code -eq 0 ]]; then
    local modules
    modules=$(echo "$output" | grep -o '[0-9]* modules' | head -1 || echo "built")
    add_result "Vite (build)" "PASS" "$modules"
  else
    add_result "Vite (build)" "FAIL" "Build failed"
  fi
}

# ── Check 6: ESLint (frontend lint) ────────────────────────────
run_eslint() {
  local target="resources/js/"

  local output exit_code=0
  output=$(npx eslint $target 2>&1) || exit_code=$?

  local errors warnings
  errors=$(echo "$output" | grep -o '[0-9]* error' | head -1 | grep -o '[0-9]*' || echo "0")
  warnings=$(echo "$output" | grep -o '[0-9]* warning' | head -1 | grep -o '[0-9]*' || echo "0")

  if [[ $exit_code -eq 0 ]]; then
    if [[ "${warnings:-0}" -gt 0 ]]; then
      add_result "ESLint (lint)" "WARN" "$warnings warnings, 0 errors"
    else
      add_result "ESLint (lint)" "PASS" "No issues"
    fi
  else
    if [[ "${errors:-0}" -gt 0 ]]; then
      add_result "ESLint (lint)" "FAIL" "$errors errors, $warnings warnings. Run /fix"
    else
      add_result "ESLint (lint)" "WARN" "$warnings warnings"
    fi
  fi
}

# ── Check 7: Dependency Audit ──────────────────────────────────
run_audit() {
  local php_result=0 npm_result=0

  local php_output npm_output
  php_output=$(composer audit 2>&1) || php_result=$?
  npm_output=$(npm audit --omit=dev 2>&1) || npm_result=$?

  local has_high=false
  if echo "$php_output" | grep -qi 'high\|critical'; then has_high=true; fi
  if echo "$npm_output" | grep -qi 'high\|critical'; then has_high=true; fi

  if [[ $php_result -eq 0 && $npm_result -eq 0 ]]; then
    add_result "Dependency Audit" "PASS" "0 vulnerabilities"
  elif $has_high; then
    add_result "Dependency Audit" "FAIL" "High/critical vulnerabilities found"
  else
    add_result "Dependency Audit" "WARN" "Low/moderate vulnerabilities found"
  fi
}

# ── Check 8: Bundle Size ──────────────────────────────────────
run_bundle_size() {
  if [[ -z "$VITE_OUTPUT" ]]; then
    add_result "Bundle Size" "SKIP" "No Vite build output"
    return
  fi

  # Parse JS sizes from Vite output
  local largest=0 total=0
  while IFS= read -r line; do
    # Match lines with kB values for .js files
    local size
    size=$(echo "$line" | sed -n 's/.*[[:space:]]\([0-9]*\)\.[0-9]* kB.*/\1/p')
    if [[ -n "$size" && "$size" -gt 0 ]] 2>/dev/null; then
      total=$((total + size))
      if [[ $size -gt $largest ]]; then
        largest=$size
      fi
    fi
  done <<< "$(echo "$VITE_OUTPUT" | grep '\.js')"

  if [[ $largest -gt 500 ]]; then
    add_result "Bundle Size" "WARN" "Largest chunk: ${largest}kB exceeds 500kB"
  elif [[ $total -gt 1000 ]]; then
    add_result "Bundle Size" "WARN" "Total JS: ${total}kB exceeds 1MB"
  else
    add_result "Bundle Size" "PASS" "${total}kB JS (largest: ${largest}kB)"
  fi
}

# ── Check 9: Accessibility (basic) ─────────────────────────────
run_a11y() {
  local file_list=""
  if [[ -n "$CHANGED_VUE" && "$BRANCH" != "main" ]]; then
    file_list="$CHANGED_VUE"
  else
    file_list=$(find resources/js -name '*.vue' -type f)
  fi

  local file_count=0
  local issues=0
  while IFS= read -r f; do
    [[ -z "$f" || ! -f "$f" ]] && continue
    file_count=$((file_count + 1))
    # img without alt
    local img_total img_alt
    img_total=$(grep -c '<img' "$f" 2>/dev/null || true)
    img_alt=$(grep -c '<img[^>]*alt' "$f" 2>/dev/null || true)
    img_total=${img_total:-0}; img_alt=${img_alt:-0}
    local diff=$((img_total - img_alt))
    [[ $diff -gt 0 ]] && issues=$((issues + diff))
  done <<< "$file_list"

  if [[ $issues -eq 0 ]]; then
    add_result "Accessibility" "PASS" "No issues in $file_count files"
  else
    add_result "Accessibility" "WARN" "$issues potential issues in $file_count files"
  fi
}

# ── Run all checks ─────────────────────────────────────────────
echo "Running quality checks ($SCOPE)..."
echo ""

run_php_syntax
run_pint
run_larastan
run_phpunit
run_vite
run_eslint
run_audit
run_bundle_size
run_a11y

# ── Output results table ───────────────────────────────────────
END_TIME=$(date +%s)
ELAPSED=$((END_TIME - START_TIME))

echo ""
echo "## Quality Check Results"
echo ""
printf "| %-3s | %-20s | %-7s | %-50s |\n" "#" "Check" "Status" "Details"
printf "|%-5s|%-22s|%-9s|%-52s|\n" "-----" "----------------------" "---------" "----------------------------------------------------"

i=1
idx=0
while [[ $idx -lt $RESULT_COUNT ]]; do
  printf "| %-3s | %-20s | %-7s | %-50s |\n" "$i" "${R_NAMES[$idx]}" "${R_STATUSES[$idx]}" "${R_DETAILS[$idx]}"
  i=$((i + 1))
  idx=$((idx + 1))
done

echo ""
echo "**$PASS passed, $FAIL failed, $WARN warnings, $SKIP skipped** (${ELAPSED}s)"

# ── Hard gate check ────────────────────────────────────────────
# Array indices: 0=syntax, 1=pint, 2=larastan, 3=phpunit, 4=coverage, 5=vite, 6=eslint, 7=audit, 8=bundle, 9=a11y
# Hard gates: checks 0-3 (PHP), 5 (Vite), 6 (ESLint)
HARD_FAIL=false
for gi in 0 1 2 3 5 6; do
  if [[ "${R_STATUSES[$gi]}" == "FAIL" ]]; then
    HARD_FAIL=true
    break
  fi
done

# Also fail on high/critical audit vulns
if [[ "${R_STATUSES[7]}" == "FAIL" ]]; then
  HARD_FAIL=true
fi

echo ""
if $HARD_FAIL; then
  echo "RESULT: FAIL — Fix issues before creating a PR."
  exit 1
else
  echo "RESULT: PASS — All checks passed. Ready for /pr."
  exit 0
fi
