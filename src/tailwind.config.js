import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: {
                    50: 'var(--color-primary-50)',
                    100: 'var(--color-primary-100)',
                    500: 'var(--color-primary-500)',
                    600: 'var(--color-primary-600)',
                    700: 'var(--color-primary-700)',
                    900: 'var(--color-primary-900)',
                },
                secondary: {
                    50: 'var(--color-secondary-50)',
                    100: 'var(--color-secondary-100)',
                    500: 'var(--color-secondary-500)',
                    600: 'var(--color-secondary-600)',
                    700: 'var(--color-secondary-700)',
                    900: 'var(--color-secondary-900)',
                },
                accent: {
                    50: 'var(--color-accent-50)',
                    100: 'var(--color-accent-100)',
                    500: 'var(--color-accent-500)',
                    600: 'var(--color-accent-600)',
                    700: 'var(--color-accent-700)',
                    900: 'var(--color-accent-900)',
                },
                success: 'var(--color-success)',
                warning: 'var(--color-warning)',
                error: 'var(--color-error)',
                info: 'var(--color-info)',
            },
        },
    },

    plugins: [forms],
};
