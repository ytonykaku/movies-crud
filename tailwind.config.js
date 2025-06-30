/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './app/Views/**/*.twig',
    './public/js/**/*.js',
    './node_modules/flowbite/**/*.js'
  ],
  theme: {
    extend: {},
  },
  plugins: [
    require('flowbite/plugin')
  ],
}