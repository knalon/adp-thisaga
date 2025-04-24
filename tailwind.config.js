import defaultTheme from 'tailwindcss/defaultTheme'

/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    './storage/framework/views/*.php',
    './resources/views/**/*.blade.php',
    './resources/js/**/*.tsx',
  ],

  theme: {
    extend: {
      fontFamily: {
        sans: ['Figtree', ...defaultTheme.fontFamily.sans],
      },
    },
  },

  plugins: [require('daisyui')],

  daisyui: {
    themes: [
      {
        abccars: {
          primary: "#00494D",
          secondary: "#FFC857",
          accent: "#F16A70",
          neutral: "#6E7C7C",
          "base-100": "#F9F6F1",
          "text-neutral-content": "#1C1C1C",
        },
      },
    ],
  },
}
