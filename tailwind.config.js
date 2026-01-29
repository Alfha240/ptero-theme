const colors = require('tailwindcss/colors');

// Clean dark theme palette
const gray = {
    50: 'hsl(220, 20%, 97%)',
    100: 'hsl(220, 15%, 91%)',
    200: 'hsl(220, 12%, 82%)',
    300: 'hsl(220, 10%, 65%)',
    400: 'hsl(220, 8%, 50%)',
    500: 'hsl(220, 10%, 40%)',
    600: 'hsl(220, 15%, 30%)',
    700: 'hsl(220, 20%, 22%)',
    800: 'hsl(220, 25%, 16%)',
    900: 'hsl(220, 30%, 10%)',
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
                black: '#0d1117',
                primary: colors.indigo,
                gray: gray,
                neutral: gray,
                cyan: colors.cyan,
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


