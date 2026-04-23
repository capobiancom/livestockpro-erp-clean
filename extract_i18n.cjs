const fs = require('fs');
const path = require('path');

const VUE_DIR = path.join(__dirname, 'resources', 'js');
const EN_JSON_PATH = path.join(__dirname, 'resources', 'lang', 'en.json');
const ES_JSON_PATH = path.join(__dirname, 'resources', 'lang', 'es.json');

// Helper to crawl directories
function walkDir(dir, callback) {
    fs.readdirSync(dir).forEach(f => {
        let dirPath = path.join(dir, f);
        let isDirectory = fs.statSync(dirPath).isDirectory();
        isDirectory ? walkDir(dirPath, callback) : callback(path.join(dir, f));
    });
}

function extractTranslations() {
    let enTranslations = {};
    let esTranslations = {};

    // Load existing translations
    if (fs.existsSync(EN_JSON_PATH)) {
        enTranslations = JSON.parse(fs.readFileSync(EN_JSON_PATH, 'utf-8'));
    }
    if (fs.existsSync(ES_JSON_PATH)) {
        esTranslations = JSON.parse(fs.readFileSync(ES_JSON_PATH, 'utf-8'));
    }

    let newlyExtracted = 0;

    walkDir(VUE_DIR, (filePath) => {
        if (!filePath.endsWith('.vue')) return;

        let content = fs.readFileSync(filePath, 'utf-8');
        
        // This is a naive regex approach. 
        // It looks for text nodes inside <template> that do NOT contain {{ or }} or < or >
        const templateMatch = content.match(/<template>([\s\S]*?)<\/template>/);
        if (!templateMatch) return;

        let templateContent = templateMatch[1];
        
        // Regex to find text between tags: > Text <
        const textRegex = />([^<>{}\n]+)</g;
        let match;
        
        let newTemplateContent = templateContent;
        let modified = false;

        while ((match = textRegex.exec(templateContent)) !== null) {
            let text = match[1].trim();
            // Ignore very short strings, pure numbers, or common symbols
            if (text.length > 2 && /[a-zA-Z]/.test(text) && !text.includes('$t(') && !text.includes('v-')) {
                // Key format: lowercase, replace spaces with underscores
                let key = text.toLowerCase().replace(/[^a-z0-9]+/g, '_').replace(/^_+|_+$/g, '');
                
                if (key.length > 0) {
                    if (!enTranslations[key]) {
                        enTranslations[key] = text;
                        // Add placeholder for Spanish
                        esTranslations[key] = text + " (TR)"; 
                        newlyExtracted++;
                    }
                    
                    // Optional: we can automatically replace the text with {{ $t('key') }}
                    // But to be completely safe, we just extract it for now, 
                    // or replace exactly the match.
                    const safeText = text.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
                    const replaceRegex = new RegExp(`>\\s*${safeText}\\s*<`);
                    newTemplateContent = newTemplateContent.replace(replaceRegex, `> {{ $t('${key}') }} <`);
                    modified = true;
                }
            }
        }

        if (modified) {
            // Write back to the file
            let newContent = content.replace(templateContent, newTemplateContent);
            fs.writeFileSync(filePath, newContent, 'utf-8');
        }
    });

    // Save JSON files
    fs.writeFileSync(EN_JSON_PATH, JSON.stringify(enTranslations, null, 4), 'utf-8');
    fs.writeFileSync(ES_JSON_PATH, JSON.stringify(esTranslations, null, 4), 'utf-8');

    console.log(`Extraction complete. Found ${newlyExtracted} new strings.`);
}

extractTranslations();
