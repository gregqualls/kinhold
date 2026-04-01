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
              class="w-7 h-7 rounded-full bg-white/20 hover:bg-white/30 flex items-center justify-center transition-colors"
              @click="showRickRoll = false"
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
              title="You've been Kinholded!"
              frameborder="0"
              allow="autoplay; encrypted-media"
              allowfullscreen
            ></iframe>
          </div>
          <div class="px-4 py-3 text-center">
            <p class="text-xs text-prussian-400 dark:text-lavender-400">
              You just got Kinholded! Press Escape or click outside to close.
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
              class="mt-5 px-5 py-2 rounded-xl bg-wisteria-500 hover:bg-wisteria-600 text-white text-sm font-medium transition-colors"
              @click="showSevenAteNine = false"
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
          v-for="i in 15"
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

  <!-- Mirror mode toast -->
  <Teleport to="body">
    <Transition name="ee-fade">
      <div
        v-if="mirrorMode"
        class="fixed top-4 left-1/2 -translate-x-1/2 z-[201] px-6 py-3 rounded-full bg-cyan-500 text-white font-bold text-sm shadow-lg animate-ee-party-banner pointer-events-none"
        style="transform: translateX(-50%) scaleX(-1);"
      >
        !edoM rorriM
      </div>
    </Transition>
  </Teleport>

  <!-- Matrix rain overlay -->
  <Teleport to="body">
    <Transition name="ee-fade">
      <div v-if="matrixMode" class="fixed inset-0 z-[199] pointer-events-none overflow-hidden bg-black/40">
        <canvas ref="matrixCanvas" class="w-full h-full"></canvas>
      </div>
    </Transition>
  </Teleport>

  <!-- Disco mode overlay -->
  <Teleport to="body">
    <Transition name="ee-fade">
      <div
        v-if="discoMode"
        class="fixed inset-0 z-[199] pointer-events-none overflow-hidden"
      >
        <!-- Color wash overlay -->
        <div class="absolute inset-0 ee-disco-lights opacity-40"></div>

        <!-- Spotlights -->
        <div class="absolute inset-0">
          <div class="ee-spotlight ee-spotlight-1"></div>
          <div class="ee-spotlight ee-spotlight-2"></div>
          <div class="ee-spotlight ee-spotlight-3"></div>
        </div>

        <!-- Disco ball + banner -->
        <div class="fixed top-4 left-1/2 -translate-x-1/2 z-[201] flex flex-col items-center">
          <SparklesIcon class="w-14 h-14 text-yellow-300 animate-ee-disco-ball" />
          <div class="mt-2 px-6 py-3 rounded-full bg-gradient-to-r from-purple-500 via-pink-500 to-yellow-400 text-white font-bold text-sm shadow-lg animate-ee-party-banner">
            DISCO MODE!
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>

  <!-- Badge earned celebration overlay -->
  <Teleport to="body">
    <Transition name="ee-fade">
      <div
        v-if="badgeEarned"
        class="fixed inset-0 z-[210] flex items-center justify-center bg-black/60 backdrop-blur-md pointer-events-none"
      >
        <!-- Confetti behind the badge -->
        <div class="fixed inset-0 z-[209] pointer-events-none overflow-hidden">
          <div
            v-for="i in 15"
            :key="'badge-confetti-' + i"
            class="ee-confetti-piece"
            :style="confettiStyle(i)"
          ></div>
        </div>

        <div class="animate-ee-bounce-in text-center z-[211]">
          <!-- Hexagonal badge icon with glow -->
          <div class="relative mx-auto mb-4 w-28 h-28">
            <div
              class="ee-hex-badge"
              :style="{ '--badge-color': badgeEarned.color }"
            >
              <div class="ee-hex-inner flex items-center justify-center text-white">
                <component :is="getBadgeIcon(badgeEarned.icon)" class="w-12 h-12" />
              </div>
            </div>
            <div
              class="absolute inset-0 ee-hex-glow"
              :style="{ '--badge-color': badgeEarned.color }"
            ></div>
          </div>

          <p class="text-golden-400 text-sm font-bold tracking-widest uppercase mb-1 animate-ee-reveal">
            Badge Earned!
          </p>
          <p class="text-white text-2xl font-black mb-2">
            {{ badgeEarned.name }}
          </p>
          <p class="text-white/70 text-sm max-w-xs mx-auto">
            {{ badgeEarned.description }}
          </p>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref, onMounted, onUnmounted, watch, nextTick } from 'vue'
import {
  SparklesIcon,
  TrophyIcon,
  KeyIcon,
  HashtagIcon,
  SunIcon,
  EyeIcon,
  BoltIcon,
  MusicalNoteIcon,
  MapIcon,
} from '@heroicons/vue/24/solid'
import api from '@/services/api'

const showRickRoll = ref(false)
const showSevenAteNine = ref(false)
const showConfetti = ref(false)
const partyMode = ref(false)
const mirrorMode = ref(false)
const matrixMode = ref(false)
const discoMode = ref(false)
const badgeEarned = ref(null)
const matrixCanvas = ref(null)

// Track locally found eggs to avoid unnecessary API calls
const getLocalEggs = () => {
  try {
    return JSON.parse(localStorage.getItem('kinhold_easter_eggs') || '[]')
  } catch {
    return []
  }
}
const saveLocalEgg = (key) => {
  const eggs = getLocalEggs()
  if (!eggs.includes(key)) {
    eggs.push(key)
    localStorage.setItem('kinhold_easter_eggs', JSON.stringify(eggs))
  }
}

// Badge icon map — returns Heroicon component
const getBadgeIcon = (icon) => {
  const map = {
    'key': KeyIcon,
    'hashtag': HashtagIcon,
    'sun': SunIcon,
    'eye': EyeIcon,
    'lightning': BoltIcon,
    'music-note': MusicalNoteIcon,
    'compass': MapIcon,
  }
  return map[icon] || TrophyIcon
}

// --- Badge reporting ---
const badgeQueue = []
let showingBadge = false

const reportEasterEgg = async (eggKey) => {
  saveLocalEgg(eggKey)
  try {
    const response = await api.post('/badges/easter-egg', { egg_key: eggKey })
    if (response.data.badges?.length > 0) {
      for (const badge of response.data.badges) {
        badgeQueue.push(badge)
      }
      showNextBadge()
    }
  } catch {
    // Silently fail — easter eggs shouldn't break anything
  }
}

const showNextBadge = () => {
  if (showingBadge || badgeQueue.length === 0) return
  showingBadge = true
  const badge = badgeQueue.shift()
  showBadgeEarned(badge)
}

const showBadgeEarned = (badge) => {
  badgeEarned.value = badge
  showConfetti.value = true
  setTimeout(() => {
    badgeEarned.value = null
    showConfetti.value = false
    showingBadge = false
    // Show next badge in queue if any
    setTimeout(() => showNextBadge(), 300)
  }, 3000)
}

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

// --- Mirror Mode: typing "mirror" ---
const mirrorSequence = ['m', 'i', 'r', 'r', 'o', 'r']
let mirrorIndex = 0

// --- Matrix Mode: typing "matrix" ---
const matrixSequence = ['m', 'a', 't', 'r', 'i', 'x']
let matrixIndex = 0

// --- Disco Mode: 10 spacebar presses within 2 seconds ---
let spacebarPresses = []

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

  // Text-based sequences only trigger when NOT typing in an input
  if (!isTypingInInput(e)) {
    // 789
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

    // "mirror"
    if (e.key === mirrorSequence[mirrorIndex]) {
      mirrorIndex++
      if (mirrorIndex === mirrorSequence.length) {
        triggerMirror()
        mirrorIndex = 0
      }
    } else if (e.key === mirrorSequence[0]) {
      mirrorIndex = 1
    } else {
      mirrorIndex = 0
    }

    // "matrix"
    if (e.key === matrixSequence[matrixIndex]) {
      matrixIndex++
      if (matrixIndex === matrixSequence.length) {
        triggerMatrix()
        matrixIndex = 0
      }
    } else if (e.key === matrixSequence[0]) {
      matrixIndex = 1
    } else {
      matrixIndex = 0
    }

    // Spacebar disco detection
    if (e.key === ' ') {
      e.preventDefault()
      const now = Date.now()
      spacebarPresses.push(now)
      // Keep only presses within last 2 seconds
      spacebarPresses = spacebarPresses.filter(t => now - t <= 2000)
      if (spacebarPresses.length >= 10) {
        triggerDisco()
        spacebarPresses = []
      }
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
  reportEasterEgg('konami')
  setTimeout(() => {
    showConfetti.value = false
  }, 4000)
}

const triggerSevenAteNine = () => {
  showSevenAteNine.value = true
  reportEasterEgg('seven_ate_nine')
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
  reportEasterEgg('party_mode')

  setTimeout(() => {
    partyMode.value = false
    showConfetti.value = false
    document.documentElement.classList.remove('ee-party-mode')
  }, 5000)
}

// --- Mirror Mode ---
const triggerMirror = () => {
  mirrorMode.value = true
  document.body.style.transform = 'scaleX(-1)'
  document.body.style.overflow = 'hidden'
  reportEasterEgg('mirror')

  setTimeout(() => {
    mirrorMode.value = false
    document.body.style.transform = ''
    document.body.style.overflow = ''
  }, 5000)
}

// --- Matrix Rain ---
let matrixAnimFrame = null

const triggerMatrix = () => {
  matrixMode.value = true
  reportEasterEgg('matrix')

  nextTick(() => {
    startMatrixRain()
  })

  setTimeout(() => {
    matrixMode.value = false
    if (matrixAnimFrame) {
      cancelAnimationFrame(matrixAnimFrame)
      matrixAnimFrame = null
    }
  }, 5000)
}

const startMatrixRain = () => {
  const canvas = matrixCanvas.value
  if (!canvas) return

  const ctx = canvas.getContext('2d')
  canvas.width = window.innerWidth
  canvas.height = window.innerHeight

  const fontSize = 14
  const columns = Math.floor(canvas.width / fontSize)
  const drops = Array(columns).fill(1)
  const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789@#$%^&*()_+-=[]{}|;:,.<>?~ﾊﾐﾋｰｳｼﾅﾓﾆｻﾜﾂｵﾘｱﾎﾃﾏｹﾒｴｶｷﾑﾕﾗｾﾈｽﾀﾇﾍ'

  const draw = () => {
    if (!matrixMode.value) return

    ctx.fillStyle = 'rgba(0, 0, 0, 0.05)'
    ctx.fillRect(0, 0, canvas.width, canvas.height)

    ctx.fillStyle = '#0F0'
    ctx.font = `${fontSize}px monospace`

    for (let i = 0; i < drops.length; i++) {
      const char = chars[Math.floor(Math.random() * chars.length)]
      ctx.fillStyle = Math.random() > 0.98 ? '#fff' : `hsl(120, 100%, ${30 + Math.random() * 40}%)`
      ctx.fillText(char, i * fontSize, drops[i] * fontSize)

      if (drops[i] * fontSize > canvas.height && Math.random() > 0.975) {
        drops[i] = 0
      }
      drops[i]++
    }

    matrixAnimFrame = requestAnimationFrame(draw)
  }

  draw()
}

// --- Disco Mode ---
const triggerDisco = () => {
  discoMode.value = true
  reportEasterEgg('disco')

  setTimeout(() => {
    discoMode.value = false
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
  document.body.style.transform = ''
  document.body.style.overflow = ''
  if (matrixAnimFrame) {
    cancelAnimationFrame(matrixAnimFrame)
  }
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

/* Disco lights color wash */
@keyframes ee-disco-color-cycle {
  0% { background-color: rgba(255, 0, 0, 0.3); }
  16% { background-color: rgba(255, 136, 0, 0.3); }
  33% { background-color: rgba(255, 255, 0, 0.3); }
  50% { background-color: rgba(0, 255, 0, 0.3); }
  66% { background-color: rgba(0, 136, 255, 0.3); }
  83% { background-color: rgba(136, 0, 255, 0.3); }
  100% { background-color: rgba(255, 0, 255, 0.3); }
}
.ee-disco-lights {
  animation: ee-disco-color-cycle 1.5s linear infinite;
}

/* Disco spotlights */
.ee-spotlight {
  position: absolute;
  width: 200px;
  height: 200px;
  border-radius: 50%;
  filter: blur(60px);
  animation: ee-spotlight-move 2s ease-in-out infinite;
}
.ee-spotlight-1 {
  background: rgba(255, 0, 128, 0.5);
  top: 10%;
  left: 10%;
  animation-delay: 0s;
}
.ee-spotlight-2 {
  background: rgba(0, 200, 255, 0.5);
  top: 40%;
  right: 10%;
  animation-delay: 0.6s;
}
.ee-spotlight-3 {
  background: rgba(255, 230, 0, 0.5);
  bottom: 10%;
  left: 40%;
  animation-delay: 1.2s;
}
@keyframes ee-spotlight-move {
  0%, 100% { transform: translate(0, 0) scale(1); }
  25% { transform: translate(50px, -30px) scale(1.3); }
  50% { transform: translate(-30px, 50px) scale(0.8); }
  75% { transform: translate(40px, 40px) scale(1.2); }
}

/* Disco ball spin */
@keyframes ee-disco-ball {
  0% { transform: translateX(-50%) rotate(0deg) scale(1); }
  25% { transform: translateX(-50%) rotate(90deg) scale(1.1); }
  50% { transform: translateX(-50%) rotate(180deg) scale(1); }
  75% { transform: translateX(-50%) rotate(270deg) scale(1.1); }
  100% { transform: translateX(-50%) rotate(360deg) scale(1); }
}
.animate-ee-disco-ball {
  animation: ee-disco-ball 1s linear infinite;
}

/* Hexagonal badge shape */
.ee-hex-badge {
  width: 112px;
  height: 112px;
  position: relative;
  clip-path: polygon(50% 0%, 100% 25%, 100% 75%, 50% 100%, 0% 75%, 0% 25%);
  background: linear-gradient(135deg, var(--badge-color), color-mix(in srgb, var(--badge-color) 60%, black));
}

.ee-hex-inner {
  width: 100%;
  height: 100%;
}

/* Badge glow effect */
@keyframes ee-badge-glow {
  0%, 100% { opacity: 0.4; transform: scale(1); }
  50% { opacity: 0.8; transform: scale(1.15); }
}
.ee-hex-glow {
  clip-path: polygon(50% 0%, 100% 25%, 100% 75%, 50% 100%, 0% 75%, 0% 25%);
  background: var(--badge-color);
  filter: blur(20px);
  animation: ee-badge-glow 1.5s ease-in-out infinite;
  z-index: -1;
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
