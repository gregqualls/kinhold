<script setup>
import ComponentPage from '../../shared/ComponentPage.vue'
import VariantFrame from '../../shared/VariantFrame.vue'
import {
  HandThumbUpIcon, ArrowTopRightOnSquareIcon, UserPlusIcon, PencilSquareIcon,
} from '@heroicons/vue/24/outline'
import KinAuthorStrip from '@/components/design-system/KinAuthorStrip.vue'

const L = {
  surfaceApp:    '#FAF8F5',
  surfaceRaised: '#FFFFFF',
  inkTertiary:   '#9C9895',
  borderSubtle:  '#E8E4DF',
}
const D = {
  surfaceApp:    '#141311',
  surfaceRaised: '#1C1B19',
  inkTertiary:   '#6E6B67',
  borderSubtle:  '#2C2A27',
}
</script>

<template>
  <ComponentPage
    title="5.14 AuthorStrip"
    description="The attribution row for any 'created by' or 'shared by' moment. Avatar + name + role / meta + optional outline-pill action. Used on list rows, task headers, vault entry headers, recipe attribution, kudos cards, and AI activity. AI rows render a lavender sparkle avatar instead of initials."
    status="chosen"
  >
    <section class="mb-16">
      <VariantFrame label="KinAuthorStrip" caption="Inline strip · 32px tinted avatar · name + role/meta stacked · outline pill action far right · isAI swaps initials for a lavender sparkle.">
        <div class="w-full space-y-10">

          <!-- LIGHT PANEL -->
          <div class="rounded-2xl border" :style="{ background: L.surfaceApp, borderColor: L.borderSubtle }">
            <div class="px-5 py-3 border-b" :style="{ borderColor: L.borderSubtle }">
              <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: L.inkTertiary }">Light mode</p>
            </div>
            <div class="rounded-b-2xl overflow-hidden divide-y" :style="{ background: L.surfaceRaised, borderColor: L.borderSubtle }">
              <KinAuthorStrip initials="GK" avatar-color="#BA562E" name="Grandma's Kitchen"  role="Recipe author" meta="Contributed 12 recipes" action-label="Follow"       :action-icon="UserPlusIcon" />
              <KinAuthorStrip initials="GQ" avatar-color="#6856B2" name="Greg Qualls"        role="Parent"        meta="Created Mar 12"         action-label="View profile" :action-icon="ArrowTopRightOnSquareIcon" />
              <KinAuthorStrip initials="EQ" avatar-color="#2E8A62" name="Emma Qualls (12)"  role="Kid"           meta="Created this task"      action-label="Edit"         :action-icon="PencilSquareIcon" />
              <KinAuthorStrip initials="AQ" avatar-color="#A2780C" name="Ava (14)"          role="Sibling"       meta="Gave kudos 2h ago"      action-label="Thank"        :action-icon="HandThumbUpIcon" />
              <KinAuthorStrip is-ai                                  name="Kinhold Assistant" role="Generated"     meta="4.2s"                   action-label="View trace"   :action-icon="ArrowTopRightOnSquareIcon" />
            </div>
          </div>

          <!-- DARK PANEL -->
          <div class="dark rounded-2xl border" :style="{ background: D.surfaceApp, borderColor: D.borderSubtle }">
            <div class="px-5 py-3 border-b" :style="{ borderColor: D.borderSubtle }">
              <p class="text-xs font-semibold uppercase tracking-widest" :style="{ color: D.inkTertiary }">Dark mode</p>
            </div>
            <div class="rounded-b-2xl overflow-hidden divide-y" :style="{ background: D.surfaceRaised, borderColor: D.borderSubtle }">
              <KinAuthorStrip initials="GK" avatar-color="#F0A882" name="Grandma's Kitchen"  role="Recipe author" meta="Contributed 12 recipes" action-label="Follow"       :action-icon="UserPlusIcon" />
              <KinAuthorStrip initials="GQ" avatar-color="#B6A8E6" name="Greg Qualls"        role="Parent"        meta="Created Mar 12"         action-label="View profile" :action-icon="ArrowTopRightOnSquareIcon" />
              <KinAuthorStrip initials="EQ" avatar-color="#7CD6AE" name="Emma Qualls (12)"  role="Kid"           meta="Created this task"      action-label="Edit"         :action-icon="PencilSquareIcon" />
              <KinAuthorStrip initials="AQ" avatar-color="#E6C452" name="Ava (14)"          role="Sibling"       meta="Gave kudos 2h ago"      action-label="Thank"        :action-icon="HandThumbUpIcon" />
              <KinAuthorStrip is-ai                                  name="Kinhold Assistant" role="Generated"     meta="4.2s"                   action-label="View trace"   :action-icon="ArrowTopRightOnSquareIcon" />
            </div>
          </div>

        </div>
      </VariantFrame>
    </section>
  </ComponentPage>
</template>
