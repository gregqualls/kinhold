<script setup>
import ComponentPage from '../../shared/ComponentPage.vue'
import VariantFrame from '../../shared/VariantFrame.vue'

// ── Dark-mode hex constants ──────────────────────────────────────────────────
const D = {
  surfaceApp:    '#141311',
  surfaceRaised: '#1C1B19',
  surfaceSunken: '#161513',
  inkPrimary:    '#F0EDE9',
  inkSecondary:  '#A09C97',
  inkTertiary:   '#6E6B67',
  borderSubtle:  '#2C2A27',
  borderStrong:  '#403E3A',
  skelBase:      '#2C2A27',   // skeleton base in dark — lighter than page (#141311) so it reads
  skelHighlight: '#3E3C38',   // highlight pass — clearly lighter than base
}

// ── Light-mode hex constants ─────────────────────────────────────────────────
const L = {
  surfaceApp:    '#FAF8F5',
  surfaceRaised: '#FFFFFF',
  surfaceSunken: '#F5F2EE',
  inkPrimary:    '#1C1C1E',
  inkSecondary:  '#6B6966',
  inkTertiary:   '#9C9895',
  borderSubtle:  '#E8E4DF',
  skelBase:      '#EDEAE5',   // slightly darker than surface-sunken so shimmer reads
  skelHighlight: '#F8F6F3',   // lighter pass for the shimmer gradient
}
</script>

<template>
  <ComponentPage
    title="1.7 Skeleton loader"
    description="Shimmer placeholders for any loading state — text lines, circles, pills, fields, and card blocks. Respects prefers-reduced-motion: shimmer stops and falls back to a static muted base color. Single canonical treatment."
    status="chosen"
  >

    <!-- ══════════════════════════════════════════════════════════════
         SECTION 1 — SHAPE PRIMITIVES
         The five canonical skeleton shapes, each in light + dark panels.
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-20">
      <VariantFrame label="Shapes" caption="Five canonical skeleton shapes for any loading state">
        <div class="w-full space-y-10">

          <!-- LIGHT PANEL -->
          <div class="rounded-2xl border p-6 space-y-10" :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode</p>

            <!-- 1. Text lines -->
            <div>
              <p class="text-xs mb-4" :style="{ color: L.inkTertiary }">Text lines — for headlines, body text, metadata</p>
              <div class="space-y-3">
                <div class="skel-lt h-3 w-3/4 rounded-[6px]" />
                <div class="skel-lt h-3 w-1/2 rounded-[6px]" />
                <div class="skel-lt h-3 w-2/3 rounded-[6px]" />
              </div>
            </div>

            <!-- 2. Circle avatars -->
            <div>
              <p class="text-xs mb-4" :style="{ color: L.inkTertiary }">Circle (avatar) — xs / sm / md / lg / xl sizes matching Avatar tokens</p>
              <div class="flex items-end gap-4">
                <div class="flex flex-col items-center gap-2">
                  <div class="skel-lt rounded-full flex-shrink-0" style="width:24px; height:24px" />
                  <span class="text-[10px]" :style="{ color: L.inkTertiary }">xs</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                  <div class="skel-lt rounded-full flex-shrink-0" style="width:32px; height:32px" />
                  <span class="text-[10px]" :style="{ color: L.inkTertiary }">sm</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                  <div class="skel-lt rounded-full flex-shrink-0" style="width:40px; height:40px" />
                  <span class="text-[10px]" :style="{ color: L.inkTertiary }">md</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                  <div class="skel-lt rounded-full flex-shrink-0" style="width:56px; height:56px" />
                  <span class="text-[10px]" :style="{ color: L.inkTertiary }">lg</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                  <div class="skel-lt rounded-full flex-shrink-0" style="width:80px; height:80px" />
                  <span class="text-[10px]" :style="{ color: L.inkTertiary }">xl</span>
                </div>
              </div>
            </div>

            <!-- 3. Pill / button / chip -->
            <div>
              <p class="text-xs mb-4" :style="{ color: L.inkTertiary }">Pill (button / chip) — sm / md / lg heights with varying widths</p>
              <div class="flex items-end gap-4">
                <div class="flex flex-col items-center gap-2">
                  <div class="skel-lt rounded-full" style="height:24px; width:60px" />
                  <span class="text-[10px]" :style="{ color: L.inkTertiary }">sm</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                  <div class="skel-lt rounded-full" style="height:32px; width:80px" />
                  <span class="text-[10px]" :style="{ color: L.inkTertiary }">md</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                  <div class="skel-lt rounded-full" style="height:40px; width:120px" />
                  <span class="text-[10px]" :style="{ color: L.inkTertiary }">lg</span>
                </div>
              </div>
            </div>

            <!-- 4. Field / input -->
            <div>
              <p class="text-xs mb-4" :style="{ color: L.inkTertiary }">Field / input — h-10, rounded-[12px], full width</p>
              <div class="skel-lt w-full rounded-[12px]" style="height:40px" />
            </div>

            <!-- 5. Card block -->
            <div>
              <p class="text-xs mb-4" :style="{ color: L.inkTertiary }">Card block — rounded-[16px], full width, 120px tall</p>
              <div class="skel-lt w-full rounded-[16px]" style="height:120px" />
            </div>
          </div><!-- /light shapes -->

          <!-- DARK PANEL -->
          <div class="rounded-2xl border p-6 space-y-10" :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode</p>

            <!-- 1. Text lines -->
            <div>
              <p class="text-xs mb-4" :style="{ color: D.inkTertiary }">Text lines — for headlines, body text, metadata</p>
              <div class="space-y-3">
                <div class="skel-dk h-3 w-3/4 rounded-[6px]" />
                <div class="skel-dk h-3 w-1/2 rounded-[6px]" />
                <div class="skel-dk h-3 w-2/3 rounded-[6px]" />
              </div>
            </div>

            <!-- 2. Circle avatars -->
            <div>
              <p class="text-xs mb-4" :style="{ color: D.inkTertiary }">Circle (avatar) — xs / sm / md / lg / xl</p>
              <div class="flex items-end gap-4">
                <div class="flex flex-col items-center gap-2">
                  <div class="skel-dk rounded-full flex-shrink-0" style="width:24px; height:24px" />
                  <span class="text-[10px]" :style="{ color: D.inkTertiary }">xs</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                  <div class="skel-dk rounded-full flex-shrink-0" style="width:32px; height:32px" />
                  <span class="text-[10px]" :style="{ color: D.inkTertiary }">sm</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                  <div class="skel-dk rounded-full flex-shrink-0" style="width:40px; height:40px" />
                  <span class="text-[10px]" :style="{ color: D.inkTertiary }">md</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                  <div class="skel-dk rounded-full flex-shrink-0" style="width:56px; height:56px" />
                  <span class="text-[10px]" :style="{ color: D.inkTertiary }">lg</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                  <div class="skel-dk rounded-full flex-shrink-0" style="width:80px; height:80px" />
                  <span class="text-[10px]" :style="{ color: D.inkTertiary }">xl</span>
                </div>
              </div>
            </div>

            <!-- 3. Pill -->
            <div>
              <p class="text-xs mb-4" :style="{ color: D.inkTertiary }">Pill (button / chip) — sm / md / lg</p>
              <div class="flex items-end gap-4">
                <div class="flex flex-col items-center gap-2">
                  <div class="skel-dk rounded-full" style="height:24px; width:60px" />
                  <span class="text-[10px]" :style="{ color: D.inkTertiary }">sm</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                  <div class="skel-dk rounded-full" style="height:32px; width:80px" />
                  <span class="text-[10px]" :style="{ color: D.inkTertiary }">md</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                  <div class="skel-dk rounded-full" style="height:40px; width:120px" />
                  <span class="text-[10px]" :style="{ color: D.inkTertiary }">lg</span>
                </div>
              </div>
            </div>

            <!-- 4. Field -->
            <div>
              <p class="text-xs mb-4" :style="{ color: D.inkTertiary }">Field / input — h-10, rounded-[12px], full width</p>
              <div class="skel-dk w-full rounded-[12px]" style="height:40px" />
            </div>

            <!-- 5. Card block -->
            <div>
              <p class="text-xs mb-4" :style="{ color: D.inkTertiary }">Card block — rounded-[16px], full width, 120px tall</p>
              <div class="skel-dk w-full rounded-[16px]" style="height:120px" />
            </div>
          </div><!-- /dark shapes -->

        </div>
      </VariantFrame>
    </section>


    <!-- ══════════════════════════════════════════════════════════════
         SECTION 2 — COMPOSED PATTERNS
         Real Kinhold loading states assembled from shape primitives.
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-20">
      <VariantFrame label="Patterns" caption="Composed skeletons — real loading states for Kinhold UI">
        <div class="w-full space-y-10">

          <!-- LIGHT PANEL -->
          <div class="rounded-2xl border p-6 space-y-10" :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode</p>

            <!-- Task row skeleton × 3 -->
            <div>
              <p class="text-xs mb-4" :style="{ color: L.inkTertiary }">Task row skeleton — avatar + text stack + pill (×3)</p>
              <div class="space-y-3">
                <div v-for="i in 3" :key="i" class="flex items-center gap-3">
                  <!-- avatar circle sm -->
                  <div
                    class="skel-lt rounded-full flex-shrink-0"
                    style="width:32px; height:32px"
                    :class="i === 2 ? 'skel-delay-1' : i === 3 ? 'skel-delay-2' : ''"
                  />
                  <!-- text stack -->
                  <div class="flex-1 space-y-2">
                    <div
                      class="skel-lt rounded-[6px] h-3"
                      style="width:60%"
                      :class="i === 2 ? 'skel-delay-1' : i === 3 ? 'skel-delay-2' : ''"
                    />
                    <div
                      class="skel-lt rounded-[6px] h-2.5"
                      style="width:35%"
                      :class="i === 2 ? 'skel-delay-1' : i === 3 ? 'skel-delay-2' : ''"
                    />
                  </div>
                  <!-- pill right -->
                  <div
                    class="skel-lt rounded-full flex-shrink-0"
                    style="height:22px; width:52px"
                    :class="i === 2 ? 'skel-delay-1' : i === 3 ? 'skel-delay-2' : ''"
                  />
                </div>
              </div>
            </div>

            <!-- Vault entry card skeleton -->
            <div>
              <p class="text-xs mb-4" :style="{ color: L.inkTertiary }">Vault entry card skeleton — title + subtitle + tag row</p>
              <div class="rounded-[16px] border p-4 space-y-3" :style="{ borderColor: L.borderSubtle }">
                <!-- title line -->
                <div class="skel-lt rounded-[6px] h-4" style="width:66%" />
                <!-- subtitle line -->
                <div class="skel-lt rounded-[6px] h-3 mt-2" style="width:33%" />
                <!-- tag row -->
                <div class="flex gap-2 mt-3">
                  <div class="skel-lt rounded-full" style="height:22px; width:56px" />
                  <div class="skel-lt rounded-full skel-delay-1" style="height:22px; width:64px" />
                  <div class="skel-lt rounded-full skel-delay-2" style="height:22px; width:48px" />
                </div>
              </div>
            </div>

            <!-- Profile header skeleton -->
            <div>
              <p class="text-xs mb-4" :style="{ color: L.inkTertiary }">Profile header skeleton — xl avatar + name + role + status pill</p>
              <div class="flex items-center gap-4">
                <!-- xl circle -->
                <div class="skel-lt rounded-full flex-shrink-0" style="width:80px; height:80px" />
                <!-- text stack -->
                <div class="flex-1 space-y-2">
                  <div class="skel-lt rounded-[6px] h-5" style="width:50%" />
                  <div class="skel-lt rounded-[6px] skel-delay-1 h-4" style="width:33%" />
                </div>
                <!-- status pill -->
                <div class="skel-lt rounded-full flex-shrink-0 skel-delay-2" style="height:28px; width:72px" />
              </div>
            </div>
          </div><!-- /light patterns -->

          <!-- DARK PANEL -->
          <div class="rounded-2xl border p-6 space-y-10" :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode</p>

            <!-- Task row skeleton × 3 -->
            <div>
              <p class="text-xs mb-4" :style="{ color: D.inkTertiary }">Task row skeleton (×3)</p>
              <div class="space-y-3">
                <div v-for="i in 3" :key="i" class="flex items-center gap-3">
                  <div
                    class="skel-dk rounded-full flex-shrink-0"
                    style="width:32px; height:32px"
                    :class="i === 2 ? 'skel-delay-1' : i === 3 ? 'skel-delay-2' : ''"
                  />
                  <div class="flex-1 space-y-2">
                    <div
                      class="skel-dk rounded-[6px] h-3"
                      style="width:60%"
                      :class="i === 2 ? 'skel-delay-1' : i === 3 ? 'skel-delay-2' : ''"
                    />
                    <div
                      class="skel-dk rounded-[6px] h-2.5"
                      style="width:35%"
                      :class="i === 2 ? 'skel-delay-1' : i === 3 ? 'skel-delay-2' : ''"
                    />
                  </div>
                  <div
                    class="skel-dk rounded-full flex-shrink-0"
                    style="height:22px; width:52px"
                    :class="i === 2 ? 'skel-delay-1' : i === 3 ? 'skel-delay-2' : ''"
                  />
                </div>
              </div>
            </div>

            <!-- Vault entry card skeleton -->
            <div>
              <p class="text-xs mb-4" :style="{ color: D.inkTertiary }">Vault entry card skeleton</p>
              <div class="rounded-[16px] border p-4 space-y-3" :style="{ borderColor: D.borderSubtle }">
                <div class="skel-dk rounded-[6px] h-4" style="width:66%" />
                <div class="skel-dk rounded-[6px] h-3 mt-2" style="width:33%" />
                <div class="flex gap-2 mt-3">
                  <div class="skel-dk rounded-full" style="height:22px; width:56px" />
                  <div class="skel-dk rounded-full skel-delay-1" style="height:22px; width:64px" />
                  <div class="skel-dk rounded-full skel-delay-2" style="height:22px; width:48px" />
                </div>
              </div>
            </div>

            <!-- Profile header skeleton -->
            <div>
              <p class="text-xs mb-4" :style="{ color: D.inkTertiary }">Profile header skeleton</p>
              <div class="flex items-center gap-4">
                <div class="skel-dk rounded-full flex-shrink-0" style="width:80px; height:80px" />
                <div class="flex-1 space-y-2">
                  <div class="skel-dk rounded-[6px] h-5" style="width:50%" />
                  <div class="skel-dk rounded-[6px] skel-delay-1 h-4" style="width:33%" />
                </div>
                <div class="skel-dk rounded-full flex-shrink-0 skel-delay-2" style="height:28px; width:72px" />
              </div>
            </div>
          </div><!-- /dark patterns -->

        </div>
      </VariantFrame>
    </section>


    <!-- ══════════════════════════════════════════════════════════════
         SECTION 3 — REDUCED MOTION BEHAVIOR
         Side-by-side comparison: forced motion vs static fallback.
         Visible regardless of Greg's OS reduced-motion setting.
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-20">
      <VariantFrame label="Motion" caption="Always honors prefers-reduced-motion; static fallback is just the muted base color">
        <div class="w-full space-y-10">

          <!-- LIGHT PANEL -->
          <div class="rounded-2xl border p-6 space-y-6" :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode</p>
            <p class="text-[13px]" :style="{ color: L.inkSecondary }">
              These two pills are hardcoded to always-animated and always-static so you can compare both behaviors regardless of your OS setting.
            </p>
            <div class="flex items-end gap-10">
              <!-- Forced motion — always animates, ignores OS reduced-motion -->
              <div class="flex flex-col items-center gap-3">
                <div class="skel-forced-motion-lt rounded-full" style="height:32px; width:100px" />
                <span class="text-[11px] font-medium" :style="{ color: L.inkSecondary }">With motion</span>
                <span class="text-[10px] text-center" :style="{ color: L.inkTertiary }">shimmer always active</span>
              </div>
              <!-- Static — always the base color, never animates -->
              <div class="flex flex-col items-center gap-3">
                <div class="skel-static-lt rounded-full" style="height:32px; width:100px" />
                <span class="text-[11px] font-medium" :style="{ color: L.inkSecondary }">Reduced motion (static)</span>
                <span class="text-[10px] text-center" :style="{ color: L.inkTertiary }">muted base, no shimmer</span>
              </div>
            </div>
            <p class="text-[11px]" :style="{ color: L.inkTertiary }">
              Skeletons auto-disable shimmer when the user has <code class="font-mono text-[10px] px-1 py-0.5 rounded" :style="{ background: L.surfaceSunken }">prefers-reduced-motion: reduce</code> active.
              Static bg is the fallback — no animation, no pulse.
            </p>
          </div><!-- /light motion -->

          <!-- DARK PANEL -->
          <div class="rounded-2xl border p-6 space-y-6" :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode</p>
            <p class="text-[13px]" :style="{ color: D.inkSecondary }">
              Same comparison in dark. Base color is <code class="font-mono text-[10px] px-1 py-0.5 rounded" :style="{ background: D.borderSubtle, color: D.inkSecondary }">#2C2A27</code> — clearly lighter than the page bg <code class="font-mono text-[10px] px-1 py-0.5 rounded" :style="{ background: D.borderSubtle, color: D.inkSecondary }">#141311</code>.
            </p>
            <div class="flex items-end gap-10">
              <div class="flex flex-col items-center gap-3">
                <div class="skel-forced-motion-dk rounded-full" style="height:32px; width:100px" />
                <span class="text-[11px] font-medium" :style="{ color: D.inkSecondary }">With motion</span>
                <span class="text-[10px] text-center" :style="{ color: D.inkTertiary }">shimmer always active</span>
              </div>
              <div class="flex flex-col items-center gap-3">
                <div class="skel-static-dk rounded-full" style="height:32px; width:100px" />
                <span class="text-[11px] font-medium" :style="{ color: D.inkSecondary }">Reduced motion (static)</span>
                <span class="text-[10px] text-center" :style="{ color: D.inkTertiary }">muted base, no shimmer</span>
              </div>
            </div>
            <p class="text-[11px]" :style="{ color: D.inkTertiary }">
              Skeletons auto-disable shimmer when the user has <code class="font-mono text-[10px] px-1 py-0.5 rounded" :style="{ background: D.borderSubtle }">prefers-reduced-motion: reduce</code> active.
              Static bg is the fallback — no animation, no pulse.
            </p>
          </div><!-- /dark motion -->

        </div>
      </VariantFrame>
    </section>


    <!-- ══════════════════════════════════════════════════════════════
         SECTION 4 — USAGE GUIDE
         ═══════════════════════════════════════════════════════════════ -->
    <section class="mb-8">
      <div class="rounded-2xl border p-6 space-y-4" :style="{ background: L.surfaceRaised, borderColor: L.borderSubtle }">
        <h2 class="text-[17px] font-semibold" :style="{ color: L.inkPrimary }">When to use skeleton vs spinner</h2>
        <ul class="space-y-3 text-[14px]" :style="{ color: L.inkSecondary }">
          <li>
            <strong :style="{ color: L.inkPrimary }">Skeleton vs spinner:</strong>
            Use skeleton when you know the shape of content that is about to appear (task row, card, profile). Use a spinner (<code class="font-mono text-[12px]">ArrowPathIcon animate-spin</code>) when the operation is brief and shapeless — a form submission, a button click.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Duration:</strong>
            Skeletons should appear immediately for any content fetch that exceeds ~150ms. For sub-150ms responses, skip the skeleton — it causes a flicker rather than helping.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Count:</strong>
            Match the real content count if known (3 task skeletons → 3 real tasks load in). If unknown, show 3–5 as a reasonable default.
          </li>
          <li>
            <strong :style="{ color: L.inkPrimary }">Stagger (optional):</strong>
            Add <code class="font-mono text-[12px]">skel-delay-1</code> / <code class="font-mono text-[12px]">skel-delay-2</code> / <code class="font-mono text-[12px]">skel-delay-3</code> to successive skeleton elements for a gentle wave effect. Stagger delays are 150ms each. Skip on reduced-motion systems (animation is off anyway).
          </li>
        </ul>
      </div>
    </section>

  </ComponentPage>
</template>

<style scoped>
/*
  ═══════════════════════════════════════════════════════════════
  SKELETON SHIMMER KEYFRAME
  background-position slides from -100% to 200% so the highlight
  band enters from the left edge and fully exits the right edge.
  ═══════════════════════════════════════════════════════════════
*/
@keyframes skel-shimmer {
  from { background-position: -100% 0; }
  to   { background-position: 200% 0; }
}

/*
  ═══════════════════════════════════════════════════════════════
  SKELETON — LIGHT MODE
  Base: #EDEAE5 (slightly darker than surface-sunken #F5F2EE)
  Highlight: #F8F6F3 (lighter gradient pass)
  Duration: 1.8s linear infinite — hardcoded, not from var()
  ═══════════════════════════════════════════════════════════════
*/
.skel-lt {
  background-color: #EDEAE5;
  background-image: linear-gradient(
    90deg,
    transparent 0%,
    #F8F6F3 50%,
    transparent 100%
  );
  background-size: 200% 100%;
  background-repeat: no-repeat;
  background-position: -100% 0;
  animation: skel-shimmer 1.8s linear infinite;
}

@media (prefers-reduced-motion: reduce) {
  .skel-lt {
    animation: none;
    background-image: none;
    background-color: #EDEAE5;
  }
}

/*
  ═══════════════════════════════════════════════════════════════
  SKELETON — DARK MODE
  Base: #2C2A27 (lighter than page #141311 so it reads)
  Highlight: #3E3C38 (clearly lighter — visible gradient pass)
  ═══════════════════════════════════════════════════════════════
*/
.skel-dk {
  background-color: #2C2A27;
  background-image: linear-gradient(
    90deg,
    transparent 0%,
    #3E3C38 50%,
    transparent 100%
  );
  background-size: 200% 100%;
  background-repeat: no-repeat;
  background-position: -100% 0;
  animation: skel-shimmer 1.8s linear infinite;
}

@media (prefers-reduced-motion: reduce) {
  .skel-dk {
    animation: none;
    background-image: none;
    background-color: #2C2A27;
  }
}

/*
  ═══════════════════════════════════════════════════════════════
  STAGGER DELAYS
  Optional wave effect for composed patterns with multiple rows.
  Each class adds 150ms of delay to the shimmer.
  Delay only matters when animation is active — on reduced-motion
  systems animation is already none, so stagger has no effect.
  ═══════════════════════════════════════════════════════════════
*/
.skel-delay-1 { animation-delay: 0.15s; }
.skel-delay-2 { animation-delay: 0.30s; }
.skel-delay-3 { animation-delay: 0.45s; }

/*
  ═══════════════════════════════════════════════════════════════
  SECTION 3 ESCAPE-HATCH CLASSES
  These ignore the OS reduced-motion preference on purpose.
  Used ONLY in the comparison demo so Greg can see both states
  regardless of his system setting. Never use these in app code.
  ═══════════════════════════════════════════════════════════════
*/

/* Always animates — ignores prefers-reduced-motion */
.skel-forced-motion-lt {
  background-color: #EDEAE5;
  background-image: linear-gradient(
    90deg,
    transparent 0%,
    #F8F6F3 50%,
    transparent 100%
  );
  background-size: 200% 100%;
  background-repeat: no-repeat;
  background-position: -100% 0;
  animation: skel-shimmer 1.8s linear infinite;
  /* No @media (prefers-reduced-motion) override — always animates */
}

/* Always static — just the muted base color, no shimmer */
.skel-static-lt {
  background-color: #EDEAE5;
  /* No animation, no gradient. The reduced-motion fallback look. */
}

/* Dark forced-motion */
.skel-forced-motion-dk {
  background-color: #2C2A27;
  background-image: linear-gradient(
    90deg,
    transparent 0%,
    #3E3C38 50%,
    transparent 100%
  );
  background-size: 200% 100%;
  background-repeat: no-repeat;
  background-position: -100% 0;
  animation: skel-shimmer 1.8s linear infinite;
}

/* Dark always static */
.skel-static-dk {
  background-color: #2C2A27;
}
</style>
