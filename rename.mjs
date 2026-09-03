import fs from 'fs';
import path from 'path';

const targetDir = 'c:/ALL FILES/Tugas Akhir/tracer/tracer-study/resources/js';

function toKebabCase(str) {
    if (!str.endsWith('.vue')) return str.toLowerCase();
    const name = str.replace('.vue', '');
    const kebab = name.replace(/([a-z0-9])([A-Z])/g, '$1-$2').toLowerCase();
    return `${kebab}.vue`;
}

function processDirectory(dir) {
    const items = fs.readdirSync(dir);
    
    // First process all files and recurse into directories
    for (const item of items) {
        const fullPath = path.join(dir, item);
        const stat = fs.statSync(fullPath);
        
        if (stat.isDirectory()) {
            processDirectory(fullPath);
            // Rename directory to lowercase
            const newDirPath = path.join(dir, item.toLowerCase());
            if (fullPath !== newDirPath) {
                // Windows is case-insensitive, so we need a temp rename
                const tempPath = fullPath + '-temp';
                fs.renameSync(fullPath, tempPath);
                fs.renameSync(tempPath, newDirPath);
            }
        } else {
            // Rename file to kebab-case
            if (item.endsWith('.vue')) {
                const newName = toKebabCase(item);
                const newFilePath = path.join(dir, newName);
                if (fullPath !== newFilePath) {
                    const tempPath = fullPath + '-temp';
                    fs.renameSync(fullPath, tempPath);
                    fs.renameSync(tempPath, newFilePath);
                }
            }
        }
    }
}

processDirectory(targetDir);
console.log('Renaming completed.');
