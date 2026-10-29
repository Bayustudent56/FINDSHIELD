/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['"Plus Jakarta Sans"', 'Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
            },
            colors: {
                canvas: '#F8FAFB',
                ink: '#1B2B3A',
                brand: {
                    DEFAULT: '#0D5C46',
                    dark: '#0D4738',
                    mint: '#E6F4EF',
                    soft: '#8ECFC0',
                },
                warn: {
                    bg: '#FEF6E6',
                    text: '#925E09',
                },
                iris: {
                    DEFAULT: '#5B5BB5',
                    soft: '#EEEEFB',
                    mid: '#AEB0E3',
                },
            },
        },
    },
    plugins: [],
};
