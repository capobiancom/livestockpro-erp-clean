const fs = require('fs');

function walk(dir) {
    let results = [];
    const list = fs.readdirSync(dir);
    list.forEach(function(file) {
        file = dir + '/' + file;
        const stat = fs.statSync(file);
        if (stat && stat.isDirectory()) { 
            results = results.concat(walk(file));
        } else { 
            if (file.endsWith('.vue')) {
                results.push(file);
            }
        }
    });
    return results;
}

const files = walk('resources/js');
let fixedCount = 0;

files.forEach(file => {
    let content = fs.readFileSync(file, 'utf8');
    const regex = /:class="(\{[^}]+\})\s+cursor-pointer hover:bg-gray-50 transition-colors duration-200"/g;
    
    if (regex.test(content)) {
        content = content.replace(regex, ':class="[$1, \'cursor-pointer hover:bg-gray-50 transition-colors duration-200\']"');
        fs.writeFileSync(file, content, 'utf8');
        console.log('Fixed', file);
        fixedCount++;
    }
});

console.log(`Fixed ${fixedCount} files.`);
