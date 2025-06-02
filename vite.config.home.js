import { defineConfig } from "vite";
import { resolve } from "path";

export default defineConfig({
    publicDir: false,
    build: {
        outDir: "resources/assets",
        emptyOutDir: false,
        cssCodeSplit: false,
        rollupOptions: {
            input: resolve(__dirname, "resources/frontend/home/ts/index.ts"),
            output: {
                entryFileNames: "js/home.min.js",
                assetFileNames: "css/home.min.css",
                manualChunks: undefined,
                inlineDynamicImports: true,
            },
        },
    },
    resolve: {
        alias: {
            "@": resolve(__dirname, "resources/frontend/home/ts"),
        },
    },
});
