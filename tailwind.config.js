const colors = require('tailwindcss/colors');

// Premium dark purple/navy theme palette
const gray = {
    50: 'hsl(240, 20%, 97%)',
    100: 'hsl(240, 15%, 91%)',
    200: 'hsl(240, 12%, 82%)',
    300: 'hsl(240, 10%, 65%)',
    400: 'hsl(240, 8%, 53%)',
    500: 'hsl(250, 12%, 40%)',
    600: 'hsl(255, 18%, 28%)',
    700: 'hsl(258, 25%, 18%)',
    800: 'hsl(260, 30%, 12%)',
    900: 'hsl(262, 35%, 8%)',
};

// Purple accent palette
const purple = {
    50: 'hsl(270, 100%, 98%)',
    100: 'hsl(270, 95%, 93%)',
    200: 'hsl(270, 85%, 85%)',
    300: 'hsl(270, 80%, 75%)',
    400: 'hsl(270, 75%, 60%)',
    500: 'hsl(275, 70%, 50%)',
    600: 'hsl(278, 65%, 45%)',
    700: 'hsl(280, 60%, 38%)',
    800: 'hsl(282, 55%, 30%)',
    900: 'hsl(285, 50%, 22%)',
};

module.exports = {
    content: [
        './resources/scripts/**/*.{js,ts,tsx}',
    ],
    theme: {
        extend: {
            fontFamily: {
                header: ['"IBM Plex Sans"', '"Roboto"', 'system-ui', 'sans-serif'],
            },
            colors: {
                black: '#0a0812',
                primary: purple,
                gray: gray,
                neutral: gray,
                cyan: colors.cyan,
                purple: purple,
            },
            fontSize: {
                '2xs': '0.625rem',
            },
            transitionDuration: {
                250: '250ms',
            },
            borderColor: theme => ({
                default: theme('colors.neutral.400', 'currentColor'),
            }),
        },
    },
    plugins: [
        require('@tailwindcss/line-clamp'),
        require('@tailwindcss/forms')({
            strategy: 'class',
        }),
    ]
};

