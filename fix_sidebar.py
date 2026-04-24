import os

path = "/opt/vacaliza-lab/erp-clean/resources/js/Layouts/Components/Sidebar.vue"
with open(path, "r") as f:
    content = f.read()

content = content.replace("bg-lime-500", "bg-[var(--primary-color,theme(colors.rose.600))]")
content = content.replace("text-lime-600", "text-[var(--primary-color,theme(colors.rose.600))]")
content = content.replace("text-lime-500", "text-[var(--primary-color,theme(colors.rose.600))]")
content = content.replace("bg-lime-50", "bg-[var(--primary-color,theme(colors.rose.600))]/10")

with open(path, "w") as f:
    f.write(content)

