import { defineConfig } from "vite";
import { resolve } from "path";

export default defineConfig({
    publicDir: false,
    build: {
        outDir: "resources/assets",
        emptyOutDir: false,
        cssCodeSplit: false,
        rollupOptions: {
            input: resolve(__dirname, "resources/frontend/admin/js/index.js"),
            output: {
                entryFileNames: "js/admin.min.js",
                assetFileNames: "css/admin.min.css",
                manualChunks: undefined,
                inlineDynamicImports: true,
            },
        },
    },
    resolve: {
        alias: {
            "@": resolve(__dirname, "resources/frontend/admin/js"),
        },
    },
});
