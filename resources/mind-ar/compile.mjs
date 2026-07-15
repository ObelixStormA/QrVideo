import { OfflineCompiler } from 'mind-ar/src/image-target/offline-compiler.js';
import { writeFile } from 'fs/promises';
import { loadImage } from 'canvas';

async function main() {
    const [, , inputPath, outputPath] = process.argv;

    if (!inputPath || !outputPath) {
        console.error('Usage: node compile.mjs <input-image-path> <output-mind-path>');
        process.exit(1);
    }

    const image = await loadImage(inputPath);
    const compiler = new OfflineCompiler();

    await compiler.compileImageTargets([image], (progress) => {
        console.log(`progress: ${progress}`);
    });

    await writeFile(outputPath, compiler.exportData());
    console.log('OK');
}

main().catch((error) => {
    console.error(error?.stack ?? String(error));
    process.exit(1);
});
