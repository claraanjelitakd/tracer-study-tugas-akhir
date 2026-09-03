import fs from 'fs';
import path from 'path';

function toKebabCaseStr(str) {
    return str.replace(/([a-z0-9])([A-Z])/g, '$1-$2').toLowerCase();
}

// 1. Update app.js
const appJsPath = 'c:/ALL FILES/Tugas Akhir/tracer/tracer-study/resources/js/app.js';
let appJsContent = fs.readFileSync(appJsPath, 'utf8');
appJsContent = appJsContent.replace(/\.\/Pages\//g, './pages/');
fs.writeFileSync(appJsPath, appJsContent);
console.log('Updated app.js');

// 2. Update all .vue and .js files in resources/js
function walkSync(dir, callback) {
    const files = fs.readdirSync(dir);
    for (const file of files) {
        const filepath = path.join(dir, file);
        if (fs.statSync(filepath).isDirectory()) {
            walkSync(filepath, callback);
        } else if (filepath.endsWith('.vue') || filepath.endsWith('.js')) {
            callback(filepath);
        }
    }
}

walkSync('c:/ALL FILES/Tugas Akhir/tracer/tracer-study/resources/js', (filepath) => {
    let content = fs.readFileSync(filepath, 'utf8');
    
    // Replace @/Components/... and @/Pages/...
    // Regex matches @/ followed by anything until .vue
    const regex = /@\/(Components|Pages)\/([^'"]+)\.vue/g;
    let newContent = content.replace(regex, (match, folder, rest) => {
        const newFolder = folder.toLowerCase();
        const parts = rest.split('/');
        const newParts = parts.map((p, idx) => {
            if (idx === parts.length - 1) {
                return toKebabCaseStr(p); // filename
            }
            return p.toLowerCase(); // folder
        });
        return `@/${newFolder}/${newParts.join('/')}.vue`;
    });
    
    if (content !== newContent) {
        fs.writeFileSync(filepath, newContent);
        console.log(`Updated imports in ${filepath}`);
    }
});

// 3. Update routes and controllers
const phpDirs = [
    'c:/ALL FILES/Tugas Akhir/tracer/tracer-study/routes',
    'c:/ALL FILES/Tugas Akhir/tracer/tracer-study/app/Http/Controllers'
];

function processPhpFiles(dir) {
    const files = fs.readdirSync(dir);
    for (const file of files) {
        const filepath = path.join(dir, file);
        if (fs.statSync(filepath).isDirectory()) {
            processPhpFiles(filepath);
        } else if (filepath.endsWith('.php')) {
            let content = fs.readFileSync(filepath, 'utf8');
            const regex = /Inertia::render\(['"]([^'"]+)['"]/g;
            let newContent = content.replace(regex, (match, inertiaPath) => {
                const newPath = inertiaPath.split('/').map(p => {
                    // For the last part (filename), we kebab case it
                    // But actually Inertia render usually just matches the filename without extension.
                    // Previously 'Superadmin/Dashboard' -> file was 'Dashboard.vue', now it is 'dashboard.vue'
                    // So we kebab-case the filename part too.
                    return toKebabCaseStr(p); 
                }).join('/');
                return `Inertia::render('${newPath}'`;
            });
            if (content !== newContent) {
                fs.writeFileSync(filepath, newContent);
                console.log(`Updated Inertia::render in ${filepath}`);
            }
        }
    }
}

phpDirs.forEach(d => processPhpFiles(d));

console.log('Update completed.');
