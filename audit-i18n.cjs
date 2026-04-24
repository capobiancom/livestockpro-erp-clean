const fs = require('fs');
const path = require('path');

function findHardcodedStrings(dir) {
    let files = [];
    const entries = fs.readdirSync(dir, { withFileTypes: true });
    
    for (const entry of entries) {
        const fullPath = path.join(dir, entry.name);
        if (entry.isDirectory()) {
            files = files.concat(findHardcodedStrings(fullPath));
        } else if (entry.isFile() && fullPath.endsWith('.vue')) {
            const content = fs.readFileSync(fullPath, 'utf-8');
            const templateMatch = content.match(/<template[^>]*>([\s\S]*?)<\/template>/i);
            
            if (templateMatch) {
                let template = templateMatch[1];
                // Strip comments
                template = template.replace(/<!--[\s\S]*?-->/g, '');
                // Strip {{ }}
                template = template.replace(/{{[\s\S]*?}}/g, '');
                // Strip script and style if any
                template = template.replace(/<script[^>]*>[\s\S]*?<\/script>/gi, '');
                template = template.replace(/<style[^>]*>[\s\S]*?<\/style>/gi, '');
                
                // Find anything between > and < that contains letters
                const matches = template.match(/>([^<]+)</g);
                if (matches) {
                    const texts = matches
                        .map(m => m.slice(1, -1).trim())
                        .filter(t => /[a-zA-ZÁÉÍÓÚáéíóúÑñ]/.test(t) && t.length > 1) // At least 2 chars
                        .filter(t => !/^[A-Z_]+$/.test(t)); // Ignore things like just 'ES' or 'EN'
                        
                    if (texts.length > 0) {
                        files.push({ file: fullPath, texts: [...new Set(texts)] });
                    }
                }
            }
        }
    }
    return files;
}

const results = findHardcodedStrings('./resources/js');
let totalUnmapped = 0;
let output = "# Auditoría de Textos Hardcodeados\n\n";

results.forEach(res => {
    output += `### ${res.file}\n`;
    res.texts.forEach(t => {
        output += `- "${t}"\n`;
    });
    output += `\n`;
    totalUnmapped += res.texts.length;
});

output += `**Total de textos sin traducir encontrados:** ${totalUnmapped}\n`;
fs.writeFileSync('audit_results.md', output);
console.log("Audit complete. Found: " + totalUnmapped);
