<!--
  KinToastStack — fixed-position host for KinToast notifications.
  @see /design-system/toast (docs/design/COMPONENT_ROADMAP.md §4.8)

  Canonical placement:
    Desktop (≥md, ≥768px): top-right corner, 16px from edges.
    Mobile  (<md):         top-center, full-width within safe insets.

  Stacking: most recent toast on top of the stack. Hard cap at `maxVisible`
  (default 3). Older toasts in the queue auto-promote as ones above dismiss.

  Imperative API (recommended): import { useToasts } from this file.
    const { push, dismiss, toasts } = useToasts()
    push({ status, title, body, actionLabel, durationMs })

  Or render `<KinToastStack :toasts="myArray" @dismiss="..." />` and manage
  the array yourself.
-->
<script>
import { ref, defineComponent, h, Transition, TransitionGroup, Teleport } from 'vue'
import KinToast from './KinToast.vue'

// Shared, app-level store. Multiple consumers can push; one host renders.
const _toasts = ref([])
let _id = 0

export function useToasts() {
  function push(opts = {}) {
    const id = ++_id
    const toast = {
      id,
      status: opts.status || 'info',
      title: opts.title || '',
      body: opts.body || '',
      actionLabel: opts.actionLabel || '',
      durationMs: opts.durationMs ?? 4500,
    }
    _toasts.value = [..._toasts.value, toast]
    if (toast.durationMs > 0) {
      setTimeout(() => dismiss(id), toast.durationMs)
    }
    return id
  }
  function dismiss(id) {
    _toasts.value = _toasts.value.filter((t) => t.id !== id)
  }
  return { toasts: _toasts, push, dismiss }
}

export default defineComponent({
  name: 'KinToastStack',
  components: { KinToast, Transition, TransitionGroup, Teleport },
  props: {
    /** Override the shared store (advanced). When omitted, uses the shared store. */
    toasts: { type: Array, default: null },
    /** Max simultaneously visible toasts. */
    maxVisible: { type: Number, default: 3 },
  },
  emits: ['action', 'dismiss'],
  setup(props, { emit }) {
    const { dismiss: storeDismiss } = useToasts()
    function onClose(id) {
      if (props.toasts) emit('dismiss', id)
      else storeDismiss(id)
    }
    return { onClose, _toasts }
  },
  render() {
    const list = (this.toasts ?? this._toasts).slice(0, this.maxVisible)
    return h(Teleport, { to: 'body' }, [
      h(
        'div',
        { class: 'kin-toast-stack', 'aria-live': 'polite', 'aria-atomic': 'false' },
        [
          h(
            TransitionGroup,
            { name: 'kin-toast', tag: 'div', class: 'kin-toast-stack__group' },
            () =>
              list.map((t) =>
                h(
                  'div',
                  { key: t.id, class: 'kin-toast-stack__item' },
                  [
                    h(KinToast, {
                      status: t.status,
                      title: t.title,
                      body: t.body,
                      actionLabel: t.actionLabel,
                      onAction: () => this.$emit('action', t.id),
                      onClose: () => this.onClose(t.id),
                    }),
                  ],
                ),
              ),
          ),
        ],
      ),
    ])
  },
})
</script>

<style scoped>
.kin-toast-stack {
  position: fixed;
  z-index: 90;
  pointer-events: none;
  /* Mobile-first: top-center, full width within insets */
  top: max(12px, env(safe-area-inset-top));
  left: max(12px, env(safe-area-inset-left));
  right: max(12px, env(safe-area-inset-right));
  display: flex;
  justify-content: center;
}

@media (min-width: 768px) {
  .kin-toast-stack {
    /* Desktop: top-right corner */
    top: 16px;
    right: 16px;
    left: auto;
    justify-content: flex-end;
  }
}

.kin-toast-stack__group {
  display: flex;
  flex-direction: column;
  gap: 8px;
  width: 100%;
  max-width: 380px;
  pointer-events: none;
}

.kin-toast-stack__item {
  pointer-events: auto;
  width: 100%;
  display: flex;
  justify-content: center;
}

@media (min-width: 768px) {
  .kin-toast-stack__item {
    justify-content: flex-end;
  }
}

/* Slide-in from above, fade-out */
.kin-toast-enter-active,
.kin-toast-leave-active {
  transition: opacity 200ms ease, transform 200ms cubic-bezier(0.16, 1, 0.3, 1);
}
.kin-toast-enter-from {
  opacity: 0;
  transform: translateY(-12px);
}
.kin-toast-leave-to {
  opacity: 0;
  transform: translateY(-8px);
}
.kin-toast-move {
  transition: transform 200ms cubic-bezier(0.16, 1, 0.3, 1);
}

@media (prefers-reduced-motion: reduce) {
  .kin-toast-enter-active,
  .kin-toast-leave-active,
  .kin-toast-move {
    transition: opacity 120ms ease;
    transform: none !important;
  }
}
</style>
