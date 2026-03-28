import {defineConfig} from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    server: {
        host: '0.0.0.0', // Permite que el servidor de Vite escuche fuera del contenedor
        port: 5173,      // El puerto por defecto
        strictPort: true,
        cors: true, // Habilita CORS explícitamente
        hmr: {
            host: 'localhost', // Tu navegador buscará los cambios en tu máquina local
        },
        watch: {
            usePolling: true, // Crucial en Linux/Docker para detectar cuando guardas archivos
        },
    },
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true, // Esto es lo que hace que la página se refresque sola
        }),
    ],
    build: {
        rollupOptions: {
            output: {
                manualChunks(path) {
                    if (path.includes('node_modules')) {
                        return 'vendor';
                    }
                    if (path.includes('resources/js')) {
                        return 'app';
                    }
                    if (path.includes('resources/css')) {
                        return 'styles';
                    }
                }
            }
        }
    }
});
