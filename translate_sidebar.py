import re
import json
import os

sidebar_path = "resources/js/Layouts/Components/Sidebar.vue"
with open(sidebar_path, "r", encoding="utf-8") as f:
    content = f.read()

# Pattern to find English text inside tags like >Text</Link> or <span>Text</span>
# We'll target specific ones to be safe, or just manually list them for Sidebar.vue since it's finite.
replacements = {
    "Farm Productivity": "farm_productivity",
    "Animals": "animals",
    "Breeds": "breeds",
    "Herds": "herds",
    "Reproduction": "reproduction",
    "Artificial Inseminations": "artificial_inseminations",
    "Pregnancy Records": "pregnancy_records",
    "Pregnancy Checkups": "pregnancy_checkups",
    "Calving Records": "calving_records",
    "Newborn Calves": "newborn_calves",
    "Health": "health",
    "Health Issues": "health_issues",
    "Health Events": "health_events",
    "Diseases": "diseases",
    "Disease Treatments": "disease_treatments",
    "Treatments": "treatments",
    "Vaccinations": "vaccinations",
    "Event Types": "event_types",
    "Feeding": "feeding",
    "Feeding Records": "feeding_records",
    "Production": "production",
    "Milk Records": "milk_records",
    "Milk Sales": "milk_sales",
    "Accounts": "accounts",
    "Cash & Bank Management": "cash_bank_management",
    "Chart of Accounts": "chart_of_accounts",
    "Journal Entries": "journal_entries",
    "Journal Voucher Report": "journal_voucher_report",
    "Balance Sheet": "balance_sheet",
    "Profit & Loss": "profit_loss",
    "Cash Flow": "cash_flow",
    "Trial Balance": "trial_balance",
    "Fixed Assets Register": "fixed_assets_register",
    "Finance": "finance",
    "Sales": "sales",
    "Purchases": "purchases",
    "Customers": "customers",
    "Manage Customers": "manage_customers",
    "Customer Payments": "customer_payments",
    "Inventory": "inventory",
    "Inventory Items": "inventory_items",
    "Medicine Items": "medicine_items",
    "Categories": "categories",
    "Suppliers": "suppliers",
    "Dashboard": "dashboard"
}

spanish_translations = {
    "farm_productivity": "Productividad",
    "animals": "Animales",
    "breeds": "Razas",
    "herds": "Rebaños",
    "reproduction": "Reproducción",
    "artificial_inseminations": "Inseminaciones Artificiales",
    "pregnancy_records": "Registros de Preñez",
    "pregnancy_checkups": "Controles de Preñez",
    "calving_records": "Registros de Partos",
    "newborn_calves": "Crías Recién Nacidas",
    "health": "Salud",
    "health_issues": "Problemas de Salud",
    "health_events": "Eventos de Salud",
    "diseases": "Enfermedades",
    "disease_treatments": "Tratamientos de Enfermedades",
    "treatments": "Tratamientos",
    "vaccinations": "Vacunaciones",
    "event_types": "Tipos de Eventos",
    "feeding": "Alimentación",
    "feeding_records": "Registros de Alimentación",
    "production": "Producción",
    "milk_records": "Registros de Leche",
    "milk_sales": "Ventas de Leche",
    "accounts": "Cuentas",
    "cash_bank_management": "Gestión de Caja y Bancos",
    "chart_of_accounts": "Plan de Cuentas",
    "journal_entries": "Asientos de Diario",
    "journal_voucher_report": "Reporte de Comprobantes",
    "balance_sheet": "Balance General",
    "profit_loss": "Estado de Resultados",
    "cash_flow": "Flujo de Caja",
    "trial_balance": "Balance de Comprobación",
    "fixed_assets_register": "Registro de Activos Fijos",
    "finance": "Finanzas",
    "sales": "Ventas",
    "purchases": "Compras",
    "customers": "Clientes",
    "manage_customers": "Administrar Clientes",
    "customer_payments": "Pagos de Clientes",
    "inventory": "Inventario",
    "inventory_items": "Artículos de Inventario",
    "medicine_items": "Medicamentos",
    "categories": "Categorías",
    "suppliers": "Proveedores",
    "dashboard": "Tablero"
}

for eng, key in replacements.items():
    # Replace >Text</
    content = re.sub(r'>\s*' + re.escape(eng) + r'\s*<\/', f">{{{{ $t('{key}') }}}}</", content)

with open(sidebar_path, "w", encoding="utf-8") as f:
    f.write(content)

# Update es.json
es_json_path = "resources/lang/es.json"
if os.path.exists(es_json_path):
    with open(es_json_path, "r", encoding="utf-8") as f:
        es_data = json.load(f)
else:
    es_data = {}

for key, es_val in spanish_translations.items():
    es_data[key] = es_val

with open(es_json_path, "w", encoding="utf-8") as f:
    json.dump(es_data, f, ensure_ascii=False, indent=4)

# Update en.json
en_json_path = "resources/lang/en.json"
if os.path.exists(en_json_path):
    with open(en_json_path, "r", encoding="utf-8") as f:
        en_data = json.load(f)
else:
    en_data = {}

for eng, key in replacements.items():
    en_data[key] = eng

with open(en_json_path, "w", encoding="utf-8") as f:
    json.dump(en_data, f, ensure_ascii=False, indent=4)

print("Translation strings extracted and JSON files updated.")
