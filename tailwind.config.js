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
        // Prussian Blue — dark navy for sidebar, headings, primary actions
        prussian: {
          50: '#e8edf5',
          100: '#c5d0e3',
          200: '#8fa1c7',
          300: '#5a72ab',
          400: '#2f4a7a',
          500: '#05204A',  // Base
          600: '#041a3d',
          700: '#031430',
          800: '#020e22',
          900: '#010815',
          950: '#01050e',
        },
        // Wisteria — purple accent for interactive elements, buttons, highlights
        wisteria: {
          50: '#f5f0fa',
          100: '#ebe1f5',
          200: '#d7c3eb',
          300: '#c3a5e1',
          400: '#B497D6',  // Base
          500: '#9b75c5',
          600: '#7d57a8',
          700: '#5e4080',
          800: '#402b57',
          900: '#21162d',
          950: '#150e1d',
        },
        // Lavender — light backgrounds, cards, subtle fills
        lavender: {
          50: '#f4f5f9',
          100: '#edeef5',
          200: '#E1E2EF',  // Base
          300: '#c8c9df',
          400: '#a9abcb',
          500: '#8a8db7',
          600: '#6e71a0',
          700: '#55577d',
          800: '#3c3d59',
          900: '#232436',
          950: '#161720',
        },
        // Golden Sand — warm accent for highlights, success, secondary actions
        sand: {
          50: '#f6f6e8',
          100: '#ecedd1',
          200: '#d9dba3',
          300: '#cdd080',
          400: '#BBBE64',  // Base
          500: '#a5a84e',
          600: '#84863e',
          700: '#63652f',
          800: '#42431f',
          900: '#212210',
          950: '#141408',
        },
        // Black — deepest text and contrast
        ink: {
          50: '#e6e6e7',
          100: '#b3b3b5',
          200: '#808083',
          300: '#4d4d51',
          400: '#26262a',
          500: '#02020A',  // Base
          600: '#020208',
          700: '#010106',
          800: '#010104',
          900: '#000002',
          950: '#000001',
        },
        // Distinct calendar event colors
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
      },
      boxShadow: {
        card: '0 1px 3px 0 rgba(5, 32, 74, 0.06), 0 1px 2px -1px rgba(5, 32, 74, 0.04)',
        'card-lg': '0 4px 6px -1px rgba(5, 32, 74, 0.08), 0 2px 4px -2px rgba(5, 32, 74, 0.05)',
        'card-xl': '0 10px 15px -3px rgba(5, 32, 74, 0.1), 0 4px 6px -4px rgba(5, 32, 74, 0.05)',
        'glow': '0 0 20px rgba(180, 151, 214, 0.3)',
      },
      borderRadius: {
        card: '1rem',
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
