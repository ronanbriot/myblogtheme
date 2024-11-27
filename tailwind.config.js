/** @type {import('tailwindcss').Config} config */

import flyonui from 'flyonui';
import defaultTheme from 'tailwindcss/defaultTheme.js'

const config = {
  content: ['./app/**/*.php', './resources/**/*.{php,vue,js}'],
  theme: {
    screens: {
      'sm': '601px',
      // => @media (min-width: 576px) { ... }

      'md': '782px',
      // => @media (min-width: 960px) { ... }

      'lg': '992px',
      // => @media (min-width: 1440px) { ... }
      'xl': '1200px',
      'Ul': '1440px',
    },
    extend: {
      colors: {
        'base-content': 'rgba(75, 85, 99, 1)',
      }, // Extend Tailwind's default colors
      fontFamily: {
        'sans': ['"Schoolbell-Regular"', ...defaultTheme.fontFamily.sans],
      },
    },
    container: {
      center: true,
      padding: {
        DEFAULT: '1rem',
        sm: '2rem',
        md: '3rem',
        lg: '4rem',
        xl: '5rem',
        '2xl': '6rem',
      },
    },
  },
  plugins: [
    flyonui
  ],
};

export default config;
