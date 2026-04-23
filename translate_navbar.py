import re
import json
import os

navbar_path = "resources/js/Layouts/Components/Navbar.vue"
with open(navbar_path, "r", encoding="utf-8") as f:
    content = f.read()

content = content.replace("Hello,", "{{ $t('hello') }},")
content = content.replace("Admin Dashboard", "{{ $t('admin_dashboard') }}")
content = content.replace(">Login</Link", ">{{ $t('login') }}</Link")
content = content.replace(">Register</Link", ">{{ $t('register') }}</Link")

with open(navbar_path, "w", encoding="utf-8") as f:
    f.write(content)

replacements = {
    "hello": "Hello",
    "admin_dashboard": "Admin Dashboard",
    "login": "Login",
    "register": "Register"
}

spanish = {
    "hello": "Hola",
    "admin_dashboard": "Tablero de Admin",
    "login": "Iniciar Sesión",
    "register": "Registrarse"
}

for lang, data_dict in [("en.json", replacements), ("es.json", spanish)]:
    path = f"resources/lang/{lang}"
    if os.path.exists(path):
        with open(path, "r", encoding="utf-8") as f:
            data = json.load(f)
    else:
        data = {}
    
    for k, v in data_dict.items():
        data[k] = v
        
    with open(path, "w", encoding="utf-8") as f:
        json.dump(data, f, ensure_ascii=False, indent=4)

print("Navbar translated.")
