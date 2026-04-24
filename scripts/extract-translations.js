import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const jsDir = path.resolve(__dirname, '../resources/js');
const langDir = path.resolve(__dirname, '../resources/lang');

// Regular expression to match $t('...') or $t("...") or $t(`...`)
// It matches literal strings. It will not match dynamic expressions like $t(`hello ${name}`).
const regex = /\$t\(\s*(['"`])(.*?)\1\s*\)/g;

function walk(dir) {
    let results = [];
    const list = fs.readdirSync(dir);
    list.forEach(file => {
        const fullPath = path.resolve(dir, file);
        const stat = fs.statSync(fullPath);
        if (stat && stat.isDirectory()) {
            results = results.concat(walk(fullPath));
        } else if (file.endsWith('.vue') || file.endsWith('.js')) {
            results.push(fullPath);
        }
    });
    return results;
}

const files = walk(jsDir);
const strings = new Set();

files.forEach(file => {
    const content = fs.readFileSync(file, 'utf8');
    let match;
    while ((match = regex.exec(content)) !== null) {
        // match[2] is the string inside the quotes
        const str = match[2];
        if (str.trim().length > 0) {
            strings.add(str);
        }
    }
});

const languages = ['en', 'es', 'pt'];

languages.forEach(lang => {
    const langFile = path.resolve(langDir, `${lang}.json`);
    let currentDict = {};
    if (fs.existsSync(langFile)) {
        try {
            currentDict = JSON.parse(fs.readFileSync(langFile, 'utf8'));
        } catch (e) {
            console.error(`Error reading ${langFile}:`, e);
        }
    }
    
    let added = 0;
    strings.forEach(str => {
        if (currentDict[str] === undefined) {
            currentDict[str] = str; // Default missing translations to the same string
            added++;
        }
    });
    
    // Sort keys alphabetically
    const sortedDict = {};
    Object.keys(currentDict).sort().forEach(key => {
        sortedDict[key] = currentDict[key];
    });

    if (!fs.existsSync(langDir)){
        fs.mkdirSync(langDir, { recursive: true });
    }

    fs.writeFileSync(langFile, JSON.stringify(sortedDict, null, 4));
    console.log(`[${lang}] Updated! Added ${added} new keys. Total keys: ${Object.keys(sortedDict).length}`);
});

console.log('Extraction complete! Check resources/lang/*.json');
