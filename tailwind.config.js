module.exports = {
  content: [
    "./*.html",
    "./login/**/*.{php,html,js}",
    "./src/**/*.{html,js,php}",
    "./*.php",
    "./login_page/*.{html,php,js}",
    "./author/*.{html,php,js}",
    "./admin/*.{html,php,js}",
    "./category/*.{html,php,js}",
  ],
  theme: {
    extend: {
      boxShadow: {
        'yellow-900': '0 2px 2px -1px rgba(202,138,4,0.5), 0 2px 2px -2px rgba(202,138,4,0.5)',
      },
    },
  },
  plugins: [],
};