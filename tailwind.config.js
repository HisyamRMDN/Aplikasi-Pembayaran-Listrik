/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.vue',
    ],
    theme: {
    extend: {
        backgroundImage: {
        'grid-slate-100': "linear-gradient(to right, rgba(241, 245, 249, 0.3) 1px, transparent 1px), linear-gradient(to bottom, rgba(241, 245, 249, 0.3) 1px, transparent 1px)",
        },
    },
    },
    safelist: [
    'bg-white/20', 'bg-white/60', 'bg-gray-50/50',
    'border-white/20', 'text-blue-500/20',
    'bg-grid-slate-100', 'hover:bg-white',
    'text-blue-300', 'text-blue-200',
    'bg-gradient-to-r', 'from-blue-600', 'to-indigo-600',
    'hover:from-blue-700', 'hover:to-indigo-700',
    ],
    plugins: [
    require('@tailwindcss/forms'),
    ],
}
