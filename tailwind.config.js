import defaultTheme from 'tailwindcss/defaultTheme'
import forms from '@tailwindcss/forms'
import typography from '@tailwindcss/typography'
import daisyui from 'daisyui'

/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    './storage/framework/views/*.php',
    './resources/views/**/*.blade.php',
    './resources/js/**/*.jsx',
    './resources/js/**/*.tsx',
  ],

  theme: {
    extend: {
      fontFamily: {
        sans: ['Figtree', ...defaultTheme.fontFamily.sans],
      },
      colors: {
        primary: '#00494D',    // Midnight Teal
        secondary: '#FFC857',  // Amber Gold
        accent: '#F16A70',     // Blush Coral
        neutral: '#6E7C7C',    // Slate Gray
        'base-100': '#F9F6F1', // Soft Ivory
        'text-neutral': '#1C1C1C', // Jet Black
      }
    },
  },

  plugins: [forms, typography, daisyui],

  daisyui: {
    themes: [
      {
        light: {
          primary: '#00494D',      // Midnight Teal
          secondary: '#FFC857',    // Amber Gold
          accent: '#F16A70',       // Blush Coral
          neutral: '#6E7C7C',      // Slate Gray
          'base-100': '#F9F6F1',   // Soft Ivory
          'neutral-content': '#1C1C1C', // Jet Black
        },
      },
    ],
  },
}
