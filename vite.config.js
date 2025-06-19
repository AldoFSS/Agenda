import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', 
                'resources/js/app.js',
                'resources/js/funciones/funciones_citas.js',
                'resources/js/funciones/funciones_clientes.js',
                'resources/js/funciones/funciones_categoria.js',
                'resources/js/funciones/funciones_subcategorias.js',
                'resources/js/funciones/funciones_productos.js',
                'resources/js/funciones/funciones_usuario.js',
                'resources/js/funciones/funciones_ventas.js',
                'resources/js/funciones/funciones_pagina.js',
                'resources/js/funciones/grafico.js',
                'resources/js/funciones/funciones_marca.js',
                'resources/js/funciones/funciones_zona.js',
                'resources/js/funciones/funciones_municipio.js',
                'resources/js/funciones/funciones_estados.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
