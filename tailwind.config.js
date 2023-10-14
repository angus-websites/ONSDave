import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    darkMode: '',

    theme: {
        extend: {
            fontFamily: {
                sans: ['Outfit', ...defaultTheme.fontFamily.sans],
            },

            colors: {
                primary: {
                    50: "#f2f5e1",
                    100: "#eef2d8",
                    200: "#e9efce",
                    300: "#dce5b0",
                    400: "#c2d175",
                    DEFAULT: "#a8bd3a",
                    600: "#97aa34",
                    700: "#7e8e2c",
                    800: "#657123",
                    900: "#525d1c",
                },

                secondary: {
                    50: "#d9e2e6",
                    100: "#ccd8dd",
                    200: "#bfced5",
                    300: "#99b1bc",
                    400: "#4d7789",
                    DEFAULT: "#003c57",
                    600: "#00364e",
                    700: "#002d41",
                    800: "#002434",
                    900: "#001d2b",
                },

                accent: {
                    50: "#d9f1f2",
                    100: "#cceded",
                    200: "#bfe8e9",
                    300: "#99dadb",
                    400: "#4dbfc1",
                    DEFAULT: "#00a3a6",
                    600: "#009395",
                    700: "#007a7d",
                    800: "#006264",
                    900: "#005051",
                },

                success: {
                    50: "#dbece3",
                    100: "#cfe6d9",
                    200: "#c3e0d0",
                    300: "#9fcdb4",
                    400: "#57a87b",
                    DEFAULT: "#0f8243",
                    600: "#0e753c",
                    700: "#0b6232",
                    800: "#094e28",
                    900: "#074021",
                },

                warning: {
                    50: "#fef7d9",
                    100: "#fef4cc",
                    200: "#fef2bf",
                    300: "#fde999",
                    400: "#fcd94d",
                    DEFAULT: "#fbc900",
                    600: "#e2b500",
                    700: "#bc9700",
                    800: "#977900",
                    900: "#7b6200",
                },

                error: {
                    50: "#f8d9dd",
                    100: "#f6ccd1",
                    200: "#f3c0c6",
                    300: "#ec9aa4",
                    400: "#de4e5f",
                    DEFAULT: "#d0021b",
                    600: "#bb0218",
                    700: "#9c0214",
                    800: "#7d0110",
                    900: "#66010d",
                },

                surface: {
                    50: "#e2e3ea",
                    100: "#d8dae2",
                    200: "#ced1db",
                    300: "#b1b5c6",
                    400: "#777e9b",
                    DEFAULT: "#3c4770",
                    600: "#364065",
                    700: "#2d3554",
                    800: "#242b43",
                    900: "#1d2337",
                },
            },
        },
    },

    plugins: [forms, typography],
};
