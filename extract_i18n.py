import os
import re
import json

def extract_and_replace():
    vue_dir = '/opt/vacaliza-lab/erp-clean/resources/js'
    extracted_strings = set()
    
    # Regex to find text between HTML tags that looks like visible text
    # It must contain at least one letter, and shouldn't contain Vue brackets {{ }}
    # Also ignore script and style tags
    text_pattern = re.compile(r'>\s*([^<>{}\n\t]+(?:[\s\n\t]+[^<>{}\n\t]+)*)\s*<')

    for root, _, files in os.walk(vue_dir):
        for file in files:
            if file.endswith('.vue'):
                filepath = os.path.join(root, file)
                
                with open(filepath, 'r', encoding='utf-8') as f:
                    content = f.read()
                
                # We need to process only the <template> part to avoid breaking script/style
                template_match = re.search(r'<template>(.*?)</template>', content, re.DOTALL)
                if not template_match:
                    continue
                    
                template_content = template_match.group(1)
                new_template_content = template_content
                
                # Find all text nodes
                matches = text_pattern.findall(template_content)
                for match in matches:
                    text = match.strip()
                    # Filter out short strings, numbers, or code-like strings
                    if len(text) > 1 and re.search(r'[A-Za-z]', text) and not text.startswith('@'):
                        # Check if it's already translated or just an expression
                        if '{{' not in text and '$t(' not in text:
                            extracted_strings.add(text)
                            # Create a safe key name if you want, but for Vue i18n, the string itself can be the key
                            # Replace the exact match in the template
                            # Use positive lookbehinds/lookaheads to only replace between tags
                            safe_text = re.escape(match)
                            new_template_content = re.sub(
                                r'(>)\s*' + safe_text + r'\s*(<)', 
                                r'\1 {{ $t("' + text.replace('"', '\\"') + r'") }} \2', 
                                new_template_content,
                                count=1
                            )
                
                if new_template_content != template_content:
                    new_content = content.replace(template_content, new_template_content)
                    with open(filepath, 'w', encoding='utf-8') as f:
                        f.write(new_content)

    # Output the extracted strings to a JSON file
    dict_out = {s: s for s in extracted_strings}
    with open('/opt/vacaliza-lab/erp-clean/resources/lang/extracted_en.json', 'w', encoding='utf-8') as f:
        json.dump(dict_out, f, indent=4, ensure_ascii=False)
        
    print(f"Extraction complete. Found {len(extracted_strings)} new strings.")

if __name__ == "__main__":
    extract_and_replace()
