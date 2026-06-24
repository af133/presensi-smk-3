import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';
import os from 'os';
const getLocalIp = () => {
    const interfaces = os.networkInterfaces();
    for (const name of Object.keys(interfaces)) {
        for (const iface of interfaces[name]) {
            if (iface.family === 'IPv4' && !iface.internal) {
                return iface.address;
            }
        }
    }
    return 'localhost';
};
export default defineConfig(({ mode }) => {
    const hostIp = getLocalIp();
    
    return {
        plugins: [
            laravel({
                input: ['resources/css/app.css', 'resources/js/app.js'],
                refresh: true,
                fonts: [
                    bunny('Instrument Sans', {
                        weights: [400, 500, 600],
                    }),
                ],
                publicDirectory: 'public',
            }),
            tailwindcss(),
        ],
        server: mode === 'development' ? {
            host: '0.0.0.0',
            port: 5173,
            cors: true,
            headers: {
                'Access-Control-Allow-Origin': '*',
            },
            hmr: {
                host: hostIp,
                protocol: 'ws',
            },
            watch: {
                ignored: ['**/storage/framework/views/**'],
            },
        } : {},
        build: {
            outDir: 'public/build',
            assetsDir: 'assets',
            emptyOutDir: true,
        }
    };
});