import fs from 'fs';
import path from 'path';

function toKebabCaseStr(str) {
    return str.replace(/([a-z0-9])([A-Z])/g, '$1-$2').toLowerCase();
}

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
    
    // Replace relative paths like ../Components/... and ./Components/...
    // Pattern: any string starting with . that contains /Components/ or /Pages/
    const regex = /(['"])(\.?\.\/[^'"]*?(?:Components|Pages)\/[^'"]+\.vue)(['"])/g;
    let newContent = content.replace(regex, (match, quote1, importPath, quote2) => {
        // e.g. importPath is "../Components/Landing/HeroSection.vue"
        const parts = importPath.split('/');
        const newParts = parts.map((p, idx) => {
            if (idx === parts.length - 1) {
                return toKebabCaseStr(p);
            }
            if (p === 'Components' || p === 'Pages' || p === 'Landing' || p === 'UI' || p === 'Form' || p === 'Alumni' || p === 'Auth' || p === 'Biro3' || p === 'Prodi' || p === 'Superadmin' || p === 'Kuesioner') {
                return p.toLowerCase();
            }
            return p;
        });
        return quote1 + newParts.join('/') + quote2;
    });

    // Also replace things like ./AlumniCard.vue if it is PascalCase.
    // Wait, `./AlumniCard.vue` inside `landing` folder.
    const regexLocal = /(['"])(\.\/[^'"]+\.vue)(['"])/g;
    newContent = newContent.replace(regexLocal, (match, quote1, importPath, quote2) => {
        const parts = importPath.split('/');
        const newParts = parts.map((p, idx) => {
            if (idx === parts.length - 1) {
                return toKebabCaseStr(p);
            }
            return p.toLowerCase();
        });
        return quote1 + newParts.join('/') + quote2;
    });

    if (content !== newContent) {
        fs.writeFileSync(filepath, newContent);
        console.log(`Updated relative imports in ${filepath}`);
    }
});

console.log('Update relative imports completed.');
