import { defineConfig } from 'vite';
import { resolve } from 'path';
import fs from 'fs';
import liveReload from 'vite-plugin-live-reload';

export default defineConfig({
  plugins: [
    liveReload(__dirname + '/**/*.php'),
    // Custom plugin to automatically copy compiled assets to the root dir for WordPress
    {
      name: 'copy-to-root',
      closeBundle() {
        // Copy compiled style.css to root
        const cssPath = fs.existsSync('dist/style.css') ? 'dist/style.css' : 'dist/assets/style.css';
        if (fs.existsSync(cssPath)) {
          fs.copyFileSync(cssPath, resolve(__dirname, 'style.css'));
        }

        // Copy compiled script.js to root
        const jsPath = 'dist/js/script.js';
        if (fs.existsSync(jsPath)) {
          fs.copyFileSync(jsPath, resolve(__dirname, 'script.js'));
        }
      },
    },
  ],
  publicDir: false,
  build: {
    outDir: resolve(__dirname, 'dist'),
    emptyOutDir: true,
    minify: 'esbuild',
    manifest: true,
    rollupOptions: {
      input: {
        script: resolve(__dirname, 'src/js/script.js'),
        style: resolve(__dirname, 'src/scss/style.scss'),
      },
      output: {
        entryFileNames: 'js/[name].js',
        assetFileNames: '[name][extname]',
      },
    },
  },
  server: {
    cors: true,
    strictPort: true,
    port: 5173,
  },
});