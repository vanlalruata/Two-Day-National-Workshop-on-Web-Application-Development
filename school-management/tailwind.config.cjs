/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./**/*.php",
    "./assets/src/**/*.css"
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          50: '#eef2ff',
          100: '#e3e9ff',
          500: '#2563eb', // vivid blue
          700: '#1e40af'
        }
      }
    }
  },
  plugins: []
};