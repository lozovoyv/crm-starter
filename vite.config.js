import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vuePlugin from "@vitejs/plugin-vue";

export default defineConfig({
    resolve: {
        alias: {
            '@': '/front',
        },
    },
    plugins: [
        laravel({
            input: ['front/application.ts'],
            refresh: true,
        }),
        vuePlugin({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
});
