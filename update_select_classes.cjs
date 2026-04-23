const fs = require('fs');
const path = require('path');

const VUE_DIR = path.join(__dirname, 'resources', 'js');

// Helper to crawl directories
function walkDir(dir, callback) {
    fs.readdirSync(dir).forEach(f => {
        let dirPath = path.join(dir, f);
        let isDirectory = fs.statSync(dirPath).isDirectory();
        isDirectory ? walkDir(dirPath, callback) : callback(path.join(dir, f));
    });
}

function updateSelectClasses() {
    let modifiedFiles = 0;

    walkDir(VUE_DIR, (filePath) => {
        if (!filePath.endsWith('.vue')) return;

        let content = fs.readFileSync(filePath, 'utf-8');
        
        // Find all <select ... class="...">
        // We will use a regex to inject specific utility classes if they are missing
        const selectClassRegex = /<select\s+[^>]*class=(["'])(.*?)\1[^>]*>/gi;
        
        let newContent = content;
        let modified = false;

        newContent = newContent.replace(selectClassRegex, (match, quote, classList) => {
            let classes = classList.split(/\s+/);
            
            const requiredClasses = ['cursor-pointer', 'hover:bg-gray-50', 'transition-colors', 'duration-200'];
            let changed = false;

            requiredClasses.forEach(cls => {
                if (!classes.includes(cls)) {
                    // remove generic transition if we add transition-colors
                    if (cls === 'transition-colors') {
                        classes = classes.filter(c => c !== 'transition');
                    }
                    classes.push(cls);
                    changed = true;
                }
            });

            if (changed) {
                modified = true;
                // Reconstruct the match
                return match.replace(`class=${quote}${classList}${quote}`, `class=${quote}${classes.join(' ')}${quote}`);
            }
            return match;
        });

        if (modified) {
            fs.writeFileSync(filePath, newContent, 'utf-8');
            modifiedFiles++;
        }
    });

    console.log(`Updated select classes in ${modifiedFiles} files.`);
}

updateSelectClasses();
