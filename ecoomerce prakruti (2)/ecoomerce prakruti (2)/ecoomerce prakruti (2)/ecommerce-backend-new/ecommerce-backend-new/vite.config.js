import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    server: {
        host: 'localhost',
        proxy: {
            '^/(?!@vite|resources|node_modules|__vite_ping)': {
                target: 'http://127.0.0.1:8000',
                changeOrigin: false,
            },
        },
    },
});
