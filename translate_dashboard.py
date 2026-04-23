import re
import json

file_path = '/opt/vacaliza-lab/erp-clean/resources/js/Pages/Dashboard.vue'
with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

# Map of exact strings to keys
translations = {
    "Dashboard": "dashboard",
    "Switch Farm Owner": "switch_farm_owner",
    "My Dashboard": "my_dashboard",
    "Live": "live",
    "Switch User Dashboard": "switch_user_dashboard",
    "View dashboard as another user in your farm.": "view_dashboard_as_another_user",
    "Select user...": "select_user",
    "Open": "open",
    "Reset": "reset",
    "Quick Overview": "quick_overview",
    "Total Animals": "total_animals",
    "All registered": "all_registered",
    "Active Animals": "active_animals",
    "Currently active": "currently_active",
    "Total Staff": "total_staff",
    "Team members": "team_members",
    "Total Farms": "total_farms",
    "Locations": "locations",
    "Low Stock Items": "low_stock_items",
    "Needs restock": "needs_restock",
    "Feedings Today": "feedings_today",
    "Completed today": "completed_today",
    "Vaccinations Due": "vaccinations_due",
    "Within 7 days": "within_7_days",
    "Active Health Issues": "active_health_issues",
    "Needs attention": "needs_attention",
    "Reproduction Overview": "reproduction_overview",
    "Total Pregnancies": "total_pregnancies",
    "All records": "all_records",
    "Ongoing Pregnancies": "ongoing_pregnancies",
    "Aborted Pregnancies": "aborted_pregnancies",
    "Unsuccessful": "unsuccessful",
    "Completed Pregnancies": "completed_pregnancies",
    "Calved successfully": "calved_successfully",
    "Total Checkups": "total_checkups",
    "All checkups": "all_checkups",
    "Normal Checkups": "normal_checkups",
    "Healthy results": "healthy_results",
    "Risk Checkups": "risk_checkups",
    "Potential issues": "potential_issues",
    "Critical Checkups": "critical_checkups",
    "Critical attention needed": "critical_attention_needed"
}

en_dict = {}
es_dict = {
    "dashboard": "Panel de Control",
    "switch_farm_owner": "Cambiar Propietario",
    "my_dashboard": "Mi Panel",
    "live": "En Vivo",
    "switch_user_dashboard": "Cambiar Panel de Usuario",
    "view_dashboard_as_another_user": "Ver panel como otro usuario de su granja.",
    "select_user": "Seleccionar usuario...",
    "open": "Abrir",
    "reset": "Reiniciar",
    "quick_overview": "Resumen Rápido",
    "total_animals": "Total de Animales",
    "all_registered": "Todos los registrados",
    "active_animals": "Animales Activos",
    "currently_active": "Actualmente activos",
    "total_staff": "Personal Total",
    "team_members": "Miembros del equipo",
    "total_farms": "Total de Granjas",
    "locations": "Ubicaciones",
    "low_stock_items": "Stock Bajo",
    "needs_restock": "Necesita reposición",
    "feedings_today": "Alimentación Hoy",
    "completed_today": "Completado hoy",
    "vaccinations_due": "Vacunas Pendientes",
    "within_7_days": "En los próximos 7 días",
    "active_health_issues": "Problemas de Salud Activos",
    "needs_attention": "Necesita atención",
    "reproduction_overview": "Resumen de Reproducción",
    "total_pregnancies": "Total de Embarazos",
    "all_records": "Todos los registros",
    "ongoing_pregnancies": "Embarazos en Curso",
    "aborted_pregnancies": "Embarazos Abortados",
    "unsuccessful": "Sin éxito",
    "completed_pregnancies": "Embarazos Completados",
    "calved_successfully": "Parto exitoso",
    "total_checkups": "Total de Chequeos",
    "all_checkups": "Todos los chequeos",
    "normal_checkups": "Chequeos Normales",
    "healthy_results": "Resultados saludables",
    "risk_checkups": "Chequeos de Riesgo",
    "potential_issues": "Problemas potenciales",
    "critical_checkups": "Chequeos Críticos",
    "critical_attention_needed": "Atención crítica necesaria"
}

for text, key in translations.items():
    # Replace in template with {{ $t('key') }}
    # Need to be careful with exact matches.
    # Simple replace for now
    content = content.replace(f">{text}<", f">{{{{ $t('{key}') }}}}<")
    content = content.replace(f">\n                                {text}\n                            <", f">\n                                {{{{ $t('{key}') }}}}\n                            <")
    content = content.replace(f">\n                                {text}<", f">\n                                {{{{ $t('{key}') }}}}<")
    content = content.replace(f">{text}\n", f">{{{{ $t('{key}') }}}}\n")
    content = content.replace(f'"{text}"', f'"{text}"') # Options, attributes etc might need different handling
    content = content.replace(f'<option :value="null">{text}</option>', f'<option :value="null">{{{{ $t(\'{key}\') }}}}</option>')
    
    en_dict[key] = text

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)

# Update json files
def update_json(lang_path, new_data):
    with open(lang_path, 'r', encoding='utf-8') as f:
        data = json.load(f)
    data.update(new_data)
    with open(lang_path, 'w', encoding='utf-8') as f:
        json.dump(data, f, indent=4, ensure_ascii=False)

update_json('/opt/vacaliza-lab/erp-clean/resources/lang/en.json', en_dict)
update_json('/opt/vacaliza-lab/erp-clean/resources/lang/es.json', es_dict)

print("Translation strings replaced and JSON updated.")
