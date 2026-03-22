<template>
  <!-- Rick Roll Modal (Konami Code) -->
  <Teleport to="body">
    <Transition name="ee-fade">
      <div
        v-if="showRickRoll"
        class="fixed inset-0 z-[200] flex items-center justify-center bg-black/70 backdrop-blur-sm"
        @click.self="showRickRoll = false"
      >
        <div class="relative bg-white dark:bg-prussian-800 rounded-2xl shadow-2xl overflow-hidden max-w-lg w-[90vw] animate-ee-bounce-in">
          <div class="flex items-center justify-between px-4 py-3 bg-wisteria-500 text-white">
            <span class="text-sm font-bold tracking-wide">You found a secret!</span>
            <button
              @click="showRickRoll = false"
              class="w-7 h-7 rounded-full bg-white/20 hover:bg-white/30 flex items-center justify-center transition-colors"
            >
              <span class="text-white text-lg leading-none">&times;</span>
            </button>
          </div>
          <div class="aspect-video">
            <iframe
              v-if="showRickRoll"
              width="100%"
              height="100%"
              src="https://www.youtube.com/embed/dQw4w9WgXcQ?autoplay=1&start=0"
              title="You've been Q32'd!"
              frameborder="0"
              allow="autoplay; encrypted-media"
              allowfullscreen
            ></iframe>
          </div>
          <div class="px-4 py-3 text-center">
            <p class="text-xs text-prussian-400 dark:text-lavender-400">
              You just got Q32'd! Press Escape or click outside to close.
            </p>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>

  <!-- Seven Ate Nine Joke Modal -->
  <Teleport to="body">
    <Transition name="ee-fade">
      <div
        v-if="showSevenAteNine"
        class="fixed inset-0 z-[200] flex items-center justify-center bg-black/60 backdrop-blur-sm"
        @click.self="showSevenAteNine = false"
      >
        <div class="relative bg-white dark:bg-prussian-800 rounded-2xl shadow-2xl overflow-hidden max-w-sm w-[85vw] animate-ee-bounce-in">
          <div class="p-6 text-center">
            <!-- Animated numbers -->
            <div class="flex items-center justify-center gap-2 mb-4">
              <span class="text-6xl animate-ee-scared">6</span>
              <span class="text-6xl animate-ee-menace">7</span>
              <span class="text-4xl animate-ee-fade-chomp">8</span>
              <span class="text-6xl animate-ee-eaten">9</span>
            </div>
            <p class="text-lg font-bold text-prussian-700 dark:text-lavender-100 mb-2">
              Why was 6 afraid of 7?
            </p>
            <p class="text-2xl font-black text-wisteria-500 dark:text-wisteria-400 animate-ee-reveal">
              Because 7 ate 9!
            </p>
            <button
              @click="showSevenAteNine = false"
              class="mt-5 px-5 py-2 rounded-xl bg-wisteria-500 hover:bg-wisteria-600 text-white text-sm font-medium transition-colors"
            >
              Ha ha, okay
            </button>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>

  <!-- Confetti overlay -->
  <Teleport to="body">
    <Transition name="ee-fade">
      <div v-if="showConfetti" class="fixed inset-0 z-[199] pointer-events-none overflow-hidden">
        <div
          v-for="i in 50"
          :key="i"
          class="ee-confetti-piece"
          :style="confettiStyle(i)"
        ></div>
      </div>
    </Transition>
  </Teleport>

  <!-- Party mode overlay (logo clicks) -->
  <Teleport to="body">
    <Transition name="ee-fade">
      <div
        v-if="partyMode"
        class="fixed top-4 left-1/2 -translate-x-1/2 z-[201] px-6 py-3 rounded-full bg-gradient-to-r from-pink-500 via-yellow-400 to-cyan-400 text-white font-bold text-sm shadow-lg animate-ee-party-banner pointer-events-none"
      >
        PARTY MODE ACTIVATED!
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

const showRickRoll = ref(false)
const showSevenAteNine = ref(false)
const showConfetti = ref(false)
const partyMode = ref(false)

// --- Konami Code: Up Up Down Down Left Right Left Right B A ---
const konamiSequence = [
  'ArrowUp', 'ArrowUp', 'ArrowDown', 'ArrowDown',
  'ArrowLeft', 'ArrowRight', 'ArrowLeft', 'ArrowRight',
  'b', 'a',
]
let konamiIndex = 0

// --- Seven Ate Nine: typing "789" ---
const sevenSequence = ['7', '8', '9']
let sevenIndex = 0

// Track typing to ignore when user is in an input
const isTypingInInput = (e) => {
  const tag = e.target.tagName
  return tag === 'INPUT' || tag === 'TEXTAREA' || tag === 'SELECT' || e.target.isContentEditable
}

const handleKeydown = (e) => {
  // Konami code works everywhere (doesn't produce text)
  if (e.key === konamiSequence[konamiIndex]) {
    konamiIndex++
    if (konamiIndex === konamiSequence.length) {
      triggerKonami()
      konamiIndex = 0
    }
  } else if (e.key === konamiSequence[0]) {
    konamiIndex = 1
  } else if (!['ArrowUp', 'ArrowDown', 'ArrowLeft', 'ArrowRight'].includes(e.key)) {
    konamiIndex = 0
  }

  // 789 only triggers when NOT typing in an input
  if (!isTypingInInput(e)) {
    if (e.key === sevenSequence[sevenIndex]) {
      sevenIndex++
      if (sevenIndex === sevenSequence.length) {
        triggerSevenAteNine()
        sevenIndex = 0
      }
    } else if (e.key === sevenSequence[0]) {
      sevenIndex = 1
    } else {
      sevenIndex = 0
    }
  }

  // Escape to close modals
  if (e.key === 'Escape') {
    showRickRoll.value = false
    showSevenAteNine.value = false
  }
}

const triggerKonami = () => {
  showConfetti.value = true
  showRickRoll.value = true
  setTimeout(() => {
    showConfetti.value = false
  }, 4000)
}

const triggerSevenAteNine = () => {
  showSevenAteNine.value = true
}

// --- Logo click party mode (exposed for Sidebar) ---
let logoClickCount = 0
let logoClickTimer = null

const handleLogoClick = () => {
  logoClickCount++
  clearTimeout(logoClickTimer)

  if (logoClickCount >= 7) {
    logoClickCount = 0
    triggerPartyMode()
  } else {
    logoClickTimer = setTimeout(() => {
      logoClickCount = 0
    }, 2000)
  }
}

const triggerPartyMode = () => {
  partyMode.value = true
  showConfetti.value = true
  document.documentElement.classList.add('ee-party-mode')

  setTimeout(() => {
    partyMode.value = false
    showConfetti.value = false
    document.documentElement.classList.remove('ee-party-mode')
  }, 5000)
}

// Confetti style generator
const confettiColors = ['#6C63FF', '#FFD700', '#FF6B6B', '#4ECDC4', '#FF69B4', '#00CED1', '#FFA500', '#7B68EE']
const confettiStyle = (i) => {
  const color = confettiColors[i % confettiColors.length]
  const left = Math.random() * 100
  const delay = Math.random() * 2
  const duration = 2 + Math.random() * 2
  const size = 6 + Math.random() * 6
  const rotation = Math.random() * 360
  return {
    '--ee-color': color,
    left: `${left}%`,
    width: `${size}px`,
    height: `${size * 0.6}px`,
    animationDelay: `${delay}s`,
    animationDuration: `${duration}s`,
    transform: `rotate(${rotation}deg)`,
  }
}

onMounted(() => {
  window.addEventListener('keydown', handleKeydown)
})

onUnmounted(() => {
  window.removeEventListener('keydown', handleKeydown)
  clearTimeout(logoClickTimer)
  document.documentElement.classList.remove('ee-party-mode')
})

// Expose logo click handler for parent components
defineExpose({ handleLogoClick })
</script>

<style>
/* Easter egg animations — intentionally NOT in @layer to keep them isolated */

/* Bounce-in for modals */
@keyframes ee-bounce-in {
  0% { transform: scale(0.3) rotate(-5deg); opacity: 0; }
  50% { transform: scale(1.05) rotate(1deg); }
  70% { transform: scale(0.95); }
  100% { transform: scale(1) rotate(0deg); opacity: 1; }
}
.animate-ee-bounce-in {
  animation: ee-bounce-in 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
}

/* Scared 6 */
@keyframes ee-scared {
  0%, 100% { transform: translateX(0); }
  10%, 30%, 50%, 70%, 90% { transform: translateX(-3px); }
  20%, 40%, 60%, 80% { transform: translateX(3px); }
}
.animate-ee-scared {
  animation: ee-scared 0.8s 0.5s ease-in-out infinite;
  color: #3b82f6;
}

/* Menacing 7 */
@keyframes ee-menace {
  0%, 100% { transform: scale(1); }
  50% { transform: scale(1.15); }
}
.animate-ee-menace {
  animation: ee-menace 0.6s ease-in-out infinite;
  color: #ef4444;
}

/* 8 fading out */
@keyframes ee-fade-chomp {
  0%, 40% { opacity: 1; transform: scale(1); }
  100% { opacity: 0.3; transform: scale(0.7); }
}
.animate-ee-fade-chomp {
  animation: ee-fade-chomp 1.5s ease-in forwards;
  color: #a855f7;
}

/* 9 getting eaten */
@keyframes ee-eaten {
  0% { opacity: 1; transform: translateX(0) scale(1); }
  60% { opacity: 1; transform: translateX(0) scale(1); }
  100% { opacity: 0; transform: translateX(-20px) scale(0); }
}
.animate-ee-eaten {
  animation: ee-eaten 2s ease-in forwards;
  color: #f59e0b;
}

/* Reveal punchline */
@keyframes ee-reveal {
  0%, 60% { opacity: 0; transform: translateY(10px); }
  100% { opacity: 1; transform: translateY(0); }
}
.animate-ee-reveal {
  animation: ee-reveal 2.2s ease-out forwards;
}

/* Confetti pieces */
@keyframes ee-confetti-fall {
  0% {
    transform: translateY(-10vh) rotate(0deg);
    opacity: 1;
  }
  100% {
    transform: translateY(110vh) rotate(720deg);
    opacity: 0;
  }
}
.ee-confetti-piece {
  position: absolute;
  top: -10px;
  background: var(--ee-color);
  border-radius: 2px;
  animation: ee-confetti-fall linear forwards;
}

/* Party mode banner */
@keyframes ee-party-banner {
  0%, 100% { transform: translateX(-50%) scale(1); }
  25% { transform: translateX(-50%) scale(1.1) rotate(-2deg); }
  75% { transform: translateX(-50%) scale(1.1) rotate(2deg); }
}
.animate-ee-party-banner {
  animation: ee-party-banner 0.5s ease-in-out infinite;
}

/* Party mode on html element — rainbow hue-rotate */
@keyframes ee-party-hue {
  0% { filter: hue-rotate(0deg); }
  100% { filter: hue-rotate(360deg); }
}
html.ee-party-mode {
  animation: ee-party-hue 2s linear infinite;
}

/* Transition classes */
.ee-fade-enter-active {
  transition: opacity 0.3s ease;
}
.ee-fade-leave-active {
  transition: opacity 0.2s ease;
}
.ee-fade-enter-from,
.ee-fade-leave-to {
  opacity: 0;
}
</style>
