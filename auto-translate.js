import fs from 'fs';
import path from 'path';
import { globSync } from 'glob';
import { translate } from '@vitalets/google-translate-api';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const langDir = path.join(__dirname, 'resources/lang');
const languages = ['es', 'en', 'pt']; // Spanish is base, target: English, Portuguese
const fileMapping = { 'es': 'es.json', 'en': 'en.json', 'pt': 'pt_BR.json' };

const extractRegexVue = /\$t\(\s*['"]([^'"]+)['"]\s*\)/g;
const extractRegexVueComponent = /t\(\s*['"]([^'"]+)['"]\s*\)/g;
const extractRegexPhp = /__\(\s*['"]([^'"]+)['"]\s*\)/g;

function extractStrings() {
    const strings = new Set();

    // 1. Scan Vue/JS files
    const vueFiles = globSync('resources/js/**/*.{vue,js}');
    vueFiles.forEach(file => {
        const content = fs.readFileSync(file, 'utf8');
        let match;
        while ((match = extractRegexVue.exec(content)) !== null) {
            strings.add(match[1]);
        }
        while ((match = extractRegexVueComponent.exec(content)) !== null) {
            strings.add(match[1]);
        }
    });

    // 2. Scan PHP files
    const phpFiles = globSync('{app,resources/views}/**/*.php');
    phpFiles.forEach(file => {
        const content = fs.readFileSync(file, 'utf8');
        let match;
        while ((match = extractRegexPhp.exec(content)) !== null) {
            // Laravel defaults to some auth messages like auth.failed, let's ignore dot notations if they refer to files
            // But if it's normal string, keep it. 
            if(!match[1].includes('.')) {
                strings.add(match[1]);
            }
        }
    });

    return Array.from(strings);
}

async function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

async function main() {
    console.log('🔍 Extracting texts from .vue, .js, and .php files...');
    const extractedStrings = extractStrings();
    console.log(`✅ Found ${extractedStrings.length} unique text strings.`);

    // Read existing translation files
    const translations = {};
    for (const lang of languages) {
        const filePath = path.join(langDir, fileMapping[lang]);
        if (fs.existsSync(filePath)) {
            translations[lang] = JSON.parse(fs.readFileSync(filePath, 'utf8'));
        } else {
            translations[lang] = {};
        }
    }

    // Process translations
    for (const text of extractedStrings) {
        // Base text is assumed to be Spanish
        if (!translations['es'][text]) {
            translations['es'][text] = text;
        }

        // Translate to other languages if missing
        for (const targetLang of ['en', 'pt']) {
            if (!translations[targetLang][text]) {
                try {
                    console.log(`🌐 Translating to ${targetLang.toUpperCase()}: "${text}"`);
                    const res = await translate(text, { from: 'es', to: targetLang });
                    translations[targetLang][text] = res.text;
                    // Be nice to the free API
                    await sleep(500);
                } catch (error) {
                    console.error(`❌ Error translating "${text}" to ${targetLang}:`, error.message);
                    translations[targetLang][text] = text; // fallback to original
                }
            }
        }
    }

    // Save translation files
    console.log('\n💾 Saving translation files...');
    for (const lang of languages) {
        const filePath = path.join(langDir, fileMapping[lang]);
        if (!fs.existsSync(langDir)){
            fs.mkdirSync(langDir, { recursive: true });
        }
        
        // Sort keys alphabetically for better git diffs
        const sortedTranslations = Object.keys(translations[lang])
            .sort()
            .reduce((acc, key) => {
                acc[key] = translations[lang][key];
                return acc;
            }, {});

        fs.writeFileSync(filePath, JSON.stringify(sortedTranslations, null, 4));
        console.log(`✅ Updated ${fileMapping[lang]}`);
    }

    console.log('\n🎉 Auto-translation completed successfully!');
}

main().catch(console.error);
