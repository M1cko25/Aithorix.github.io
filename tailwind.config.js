import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['sans', ...defaultTheme.fontFamily.sans],
                poppins: ['Poppins', 'sans'],
            },
            colors: {
                'button': '#0463CA',
                'button-hover': '#0487E2',
                'blue': '#09B1EC',
                'cyan': '#65C2F5',
                'light-blue': '#B0D6F5',
                'light-gray': '#E0E0E0',
                'light': '#F5F5F5',
                'dark': '#36454A',
                'neutral': '#D9D9D9',
                'error': '#FF3232',
                'success': '#16A249',
                'dark-gray': '#737373',
            }
        },
    },
    plugins: [],
};
