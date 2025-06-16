import { defineConfig } from "vite";
import { resolve } from "path";

const IS_PRODUCTION = false; 
const DIR_NAME = `admin`;
const OUT_DIR = `resources/assets`;
const WORK_DIR = `resources/frontend/${DIR_NAME}`;

export default defineConfig({
    publicDir: false,
    esbuild: {
        keepNames: !IS_PRODUCTION
    },
    build: {
        sourcemap: !IS_PRODUCTION, 
        outDir: OUT_DIR,
        emptyOutDir: false,
        cssCodeSplit: false,
        rollupOptions: {
            input: resolve(__dirname, `${WORK_DIR}/js/index.js`),
            output: {
                entryFileNames: `js/${DIR_NAME}.min.js`,
                assetFileNames: `css/${DIR_NAME}.min.css`,
                manualChunks: undefined,
                inlineDynamicImports: true,
            },
        },
    },
    resolve: {
        alias: {
            "@": resolve(__dirname, `${WORK_DIR}/js`),
        },
    },
});
