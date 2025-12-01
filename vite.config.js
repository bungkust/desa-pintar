import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    build: {
        // Ensure consistent chunk naming for production
        rollupOptions: {
            output: {
                // Prevent hash changes for same content
                manualChunks: undefined,
                // Consistent naming
                chunkFileNames: 'assets/js/[name]-[hash].js',
                entryFileNames: 'assets/js/[name]-[hash].js',
                assetFileNames: 'assets/[ext]/[name]-[hash].[ext]',
            },
        },
        // Generate source maps for debugging
        sourcemap: process.env.APP_ENV !== 'production',
    },
    server: {
        host: '127.0.0.1', // Force IPv4 localhost
    },
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/filament-custom.css',
                'resources/js/app.js',
                'resources/js/charts.js',
            ],
            refresh: true,
        }),
    ],
    build: {
        minify: 'esbuild',
        cssMinify: true,
        rollupOptions: {
            output: {
                manualChunks: {
                    'vendor': [], // Future vendor chunks can be added here
                },
                chunkFileNames: 'assets/js/[name]-[hash].js',
                entryFileNames: 'assets/js/[name]-[hash].js',
                assetFileNames: 'assets/[ext]/[name]-[hash].[ext]',
            },
        },
        sourcemap: false,
        cssCodeSplit: true,
        chunkSizeWarningLimit: 1000,
    },
});

