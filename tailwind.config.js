/** @type {import('tailwindcss').Config} */
export default {
  darkMode: 'class',
  content: [
    './resources/js/**/*.{js,vue}',
    './resources/views/**/*.php',
  ],
  theme: {
    extend: {
      colors: {
        // ── Legacy palette (DO NOT MODIFY — app uses these everywhere) ──────
        // Prussian Blue — dark navy for sidebar, headings, primary actions
        // Values come from CSS custom properties so themes can override them
        prussian: {
          50:  'rgb(var(--prussian-50) / <alpha-value>)',
          100: 'rgb(var(--prussian-100) / <alpha-value>)',
          200: 'rgb(var(--prussian-200) / <alpha-value>)',
          300: 'rgb(var(--prussian-300) / <alpha-value>)',
          400: 'rgb(var(--prussian-400) / <alpha-value>)',
          500: 'rgb(var(--prussian-500) / <alpha-value>)',
          600: 'rgb(var(--prussian-600) / <alpha-value>)',
          700: 'rgb(var(--prussian-700) / <alpha-value>)',
          800: 'rgb(var(--prussian-800) / <alpha-value>)',
          900: 'rgb(var(--prussian-900) / <alpha-value>)',
          950: 'rgb(var(--prussian-950) / <alpha-value>)',
        },
        // Wisteria — purple accent for interactive elements, buttons, highlights
        wisteria: {
          50:  'rgb(var(--wisteria-50) / <alpha-value>)',
          100: 'rgb(var(--wisteria-100) / <alpha-value>)',
          200: 'rgb(var(--wisteria-200) / <alpha-value>)',
          300: 'rgb(var(--wisteria-300) / <alpha-value>)',
          400: 'rgb(var(--wisteria-400) / <alpha-value>)',
          500: 'rgb(var(--wisteria-500) / <alpha-value>)',
          600: 'rgb(var(--wisteria-600) / <alpha-value>)',
          700: 'rgb(var(--wisteria-700) / <alpha-value>)',
          800: 'rgb(var(--wisteria-800) / <alpha-value>)',
          900: 'rgb(var(--wisteria-900) / <alpha-value>)',
          950: 'rgb(var(--wisteria-950) / <alpha-value>)',
        },
        // Lavender — light backgrounds, cards, subtle fills
        lavender: {
          50:  'rgb(var(--lavender-50) / <alpha-value>)',
          100: 'rgb(var(--lavender-100) / <alpha-value>)',
          200: 'rgb(var(--lavender-200) / <alpha-value>)',
          300: 'rgb(var(--lavender-300) / <alpha-value>)',
          400: 'rgb(var(--lavender-400) / <alpha-value>)',
          500: 'rgb(var(--lavender-500) / <alpha-value>)',
          600: 'rgb(var(--lavender-600) / <alpha-value>)',
          700: 'rgb(var(--lavender-700) / <alpha-value>)',
          800: 'rgb(var(--lavender-800) / <alpha-value>)',
          900: 'rgb(var(--lavender-900) / <alpha-value>)',
          950: 'rgb(var(--lavender-950) / <alpha-value>)',
        },
        // Golden Sand — warm accent for highlights, success, secondary actions
        sand: {
          50:  'rgb(var(--sand-50) / <alpha-value>)',
          100: 'rgb(var(--sand-100) / <alpha-value>)',
          200: 'rgb(var(--sand-200) / <alpha-value>)',
          300: 'rgb(var(--sand-300) / <alpha-value>)',
          400: 'rgb(var(--sand-400) / <alpha-value>)',
          500: 'rgb(var(--sand-500) / <alpha-value>)',
          600: 'rgb(var(--sand-600) / <alpha-value>)',
          700: 'rgb(var(--sand-700) / <alpha-value>)',
          800: 'rgb(var(--sand-800) / <alpha-value>)',
          900: 'rgb(var(--sand-900) / <alpha-value>)',
          950: 'rgb(var(--sand-950) / <alpha-value>)',
        },
        // Ink — text, near-black scale. Numeric keys 50–950 are legacy.
        // Redesign semantic aliases (primary/secondary/tertiary/inverse) added below.
        ink: {
          50:  'rgb(var(--ink-50) / <alpha-value>)',
          100: 'rgb(var(--ink-100) / <alpha-value>)',
          200: 'rgb(var(--ink-200) / <alpha-value>)',
          300: 'rgb(var(--ink-300) / <alpha-value>)',
          400: 'rgb(var(--ink-400) / <alpha-value>)',
          500: 'rgb(var(--ink-500) / <alpha-value>)',
          600: 'rgb(var(--ink-600) / <alpha-value>)',
          700: 'rgb(var(--ink-700) / <alpha-value>)',
          800: 'rgb(var(--ink-800) / <alpha-value>)',
          900: 'rgb(var(--ink-900) / <alpha-value>)',
          950: 'rgb(var(--ink-950) / <alpha-value>)',
          // Redesign semantic aliases — vars defined in tokens.css
          primary:   'rgb(var(--ink-primary) / <alpha-value>)',
          secondary: 'rgb(var(--ink-secondary) / <alpha-value>)',
          tertiary:  'rgb(var(--ink-tertiary) / <alpha-value>)',
          inverse:   'rgb(var(--ink-inverse) / <alpha-value>)',
        },
        // Kinhold brand palette (hardcoded, not themed)
        kin: {
          ivory: '#FAF8F5',
          cream: '#F5F2EE',
          white: '#FFFFFF',
          border: '#E8E4DF',
          'border-dark': '#2E2E32',
          black: '#1C1C1E',
          'off-white': '#F0EDE9',
          'bg-dark': '#121214',
          'surface-dark': '#1C1C20',
          'surface-dark-alt': '#252528',
          gold: '#C4975A',
          'gold-hover': '#D4A96A',
          'gold-active': '#B38A50',
          success: '#5B8C6A',
          warning: '#C48B3F',
          error: '#C45B5B',
          info: '#5B7B9C',
          'gray-50': '#F5F3F0',
          'gray-100': '#EDEBE7',
          'gray-200': '#D9D5CF',
          'gray-300': '#B8B3AB',
          'gray-400': '#8A857D',
          'gray-500': '#6B6966',
          'gray-600': '#4A4640',
          'gray-700': '#333028',
          'gray-800': '#252528',
          'gray-900': '#1C1C20',
        },

        // ── Redesign token layer (Tier 0.1) ─────────────────────────────────
        // Parallel to the legacy palette; new redesign components consume these.
        // CSS custom properties defined in resources/css/tokens.css.
        // Light and dark values are authored independently in that file.
        surface: {
          app:     'rgb(var(--surface-app) / <alpha-value>)',
          raised:  'rgb(var(--surface-raised) / <alpha-value>)',
          sunken:  'rgb(var(--surface-sunken) / <alpha-value>)',
          overlay: 'rgb(var(--surface-overlay) / <alpha-value>)',
        },
        border: {
          subtle: 'rgb(var(--border-subtle) / <alpha-value>)',
          strong: 'rgb(var(--border-strong) / <alpha-value>)',
        },
        accent: {
          'lavender-soft': 'rgb(var(--accent-lavender-soft) / <alpha-value>)',
          'lavender-bold': 'rgb(var(--accent-lavender-bold) / <alpha-value>)',
          'peach-soft':    'rgb(var(--accent-peach-soft) / <alpha-value>)',
          'peach-bold':    'rgb(var(--accent-peach-bold) / <alpha-value>)',
          'mint-soft':     'rgb(var(--accent-mint-soft) / <alpha-value>)',
          'mint-bold':     'rgb(var(--accent-mint-bold) / <alpha-value>)',
          'sun-soft':      'rgb(var(--accent-sun-soft) / <alpha-value>)',
          'sun-bold':      'rgb(var(--accent-sun-bold) / <alpha-value>)',
        },
        status: {
          success: 'rgb(var(--status-success) / <alpha-value>)',
          pending: 'rgb(var(--status-pending) / <alpha-value>)',
          paused:  'rgb(var(--status-paused) / <alpha-value>)',
          failed:  'rgb(var(--status-failed) / <alpha-value>)',
          info:    'rgb(var(--status-info) / <alpha-value>)',
          warning: 'rgb(var(--status-warning) / <alpha-value>)',
        },
        brand: {
          gold: 'rgb(var(--brand-gold) / <alpha-value>)',
        },

        // Distinct calendar event colors (not themed)
        cal: {
          blue: '#2f4a7a',
          purple: '#7d57a8',
          teal: '#0d9488',
          rose: '#be185d',
          amber: '#a5a84e',
          emerald: '#047857',
          sky: '#0369a1',
          fuchsia: '#a21caf',
          orange: '#c2410c',
          lime: '#4d7c0f',
        },
      },
      fontFamily: {
        sans: ['Inter', 'system-ui', '-apple-system', 'sans-serif'],
        heading: ['"Plus Jakarta Sans"', 'Inter', 'system-ui', 'sans-serif'],
        mono: ['"JetBrains Mono"', 'ui-monospace', 'monospace'],
      },
      // ── Redesign type scale (Tier 0.2) ──────────────────────────────────────
      // Each entry maps to a CSS custom property from resources/css/tokens.css.
      // Produces utilities: text-hero, text-display, text-h1 … text-mono.
      // Line-height and letter-spacing values mirror the --lh-* / --ls-* vars.
      // NOTE: DO NOT modify or remove existing default Tailwind sizes (text-xs,
      // text-sm, text-base, etc.) — the app uses them extensively.
      fontSize: {
        'hero':    ['var(--text-hero)',     { lineHeight: '0.95',  letterSpacing: '-0.03em'  }],
        'display': ['var(--text-display)',  { lineHeight: '1.05',  letterSpacing: '-0.02em'  }],
        'h1':      ['var(--text-h1)',       { lineHeight: '1.15',  letterSpacing: '-0.015em' }],
        'h2':      ['var(--text-h2)',       { lineHeight: '1.2',   letterSpacing: '-0.01em'  }],
        'h3':      ['var(--text-h3)',       { lineHeight: '1.3',   letterSpacing: '-0.005em' }],
        'h4':      ['var(--text-h4)',       { lineHeight: '1.35',  letterSpacing: '0'        }],
        'body':    ['var(--text-body)',     { lineHeight: '1.55',  letterSpacing: '0'        }],
        'body-sm': ['var(--text-body-sm)',  { lineHeight: '1.5',   letterSpacing: '0'        }],
        'caption': ['var(--text-caption)',  { lineHeight: '1.4',   letterSpacing: '0.02em'   }],
        'mono':    ['var(--text-mono)',     { lineHeight: '1.5',   letterSpacing: '0'        }],
      },
      boxShadow: {
        // ── Legacy shadows (DO NOT REMOVE — app uses these) ──────────────────
        card: '0 1px 3px 0 rgba(28, 28, 30, 0.06), 0 1px 2px -1px rgba(28, 28, 30, 0.04)',
        'card-lg': '0 4px 6px -1px rgba(28, 28, 30, 0.08), 0 2px 4px -2px rgba(28, 28, 30, 0.05)',
        'card-xl': '0 10px 15px -3px rgba(28, 28, 30, 0.1), 0 4px 6px -4px rgba(28, 28, 30, 0.05)',
        'glow': '0 0 20px rgba(196, 151, 90, 0.25)',
        // ── Redesign shadow tokens (Tier 0.3) ────────────────────────────────
        // All values reference CSS custom properties defined in tokens.css so
        // light + dark modes are handled automatically by the :root / html.dark blocks.
        resting:  'var(--shadow-resting)',
        hover:    'var(--shadow-hover)',
        elevated: 'var(--shadow-elevated)',
        modal:    'var(--shadow-modal)',
        glass:    'var(--shadow-glass)',
      },
      borderRadius: {
        // ── Legacy radius (updated to point at the new var) ──────────────────
        // Previously hardcoded to '12px'; now var(--radius-card) = 20px.
        // Any component using rounded-card automatically gets the design token.
        card:  'var(--radius-card)',
        // ── Redesign radius tokens (Tier 0.3) ────────────────────────────────
        pill:  'var(--radius-pill)',
        sheet: 'var(--radius-sheet)',
        field: 'var(--radius-field)',
        tile:  'var(--radius-tile)',
      },
      // ── Redesign motion tokens (Tier 0.3) ───────────────────────────────────
      // Duration values reference CSS vars so the reduced-motion @media override
      // in tokens.css (which sets all to 0ms) applies automatically.
      transitionDuration: {
        instant:    'var(--duration-instant)',
        quick:      'var(--duration-quick)',
        deliberate: 'var(--duration-deliberate)',
        sheet:      'var(--duration-sheet)',
      },
      transitionTimingFunction: {
        'out-soft':    'var(--ease-out-soft)',
        'in-out-soft': 'var(--ease-in-out-soft)',
        spring:        'var(--ease-spring)',
      },
      // ── Redesign gradient tokens (Tier 0.4) ─────────────────────────────────
      // Each utility references a single CSS var that swaps between `:root` and
      // `html.dark` automatically — no dark: variant needed in templates.
      backgroundImage: {
        'iridescent-subtle': 'var(--gradient-iridescent-subtle)',
        'iridescent-warm':   'var(--gradient-iridescent-warm)',
        'ambient':           'var(--gradient-ambient)',
        'accent-lavender':   'var(--gradient-accent-lavender)',
        'accent-peach':      'var(--gradient-accent-peach)',
        'accent-mint':       'var(--gradient-accent-mint)',
        'accent-sun':        'var(--gradient-accent-sun)',
      },
      animation: {
        'pulse-subtle': 'pulse-subtle 3s ease-in-out infinite',
        'bounce-gentle': 'bounce-gentle 2s ease-in-out infinite',
      },
      keyframes: {
        'pulse-subtle': {
          '0%, 100%': { opacity: '1' },
          '50%': { opacity: '0.85' },
        },
        'bounce-gentle': {
          '0%, 100%': { transform: 'translateY(0)' },
          '50%': { transform: 'translateY(-2px)' },
        },
      },
      spacing: {
        'safe-bottom': 'max(1rem, env(safe-area-inset-bottom))',
      },
      minHeight: {
        'nav': '5rem',
      },
    },
  },
  plugins: [],
}
