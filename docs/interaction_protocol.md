# Protocolo de Documentación y Resúmenes Automáticos

Este documento contiene las reglas obligatorias para la documentación de sesiones de programación colaborativa en este proyecto.

## REGLAS GENERALES

1. **Automatización**: Al finalizar cada tarea importante o bloque de trabajo, se generará automáticamente un resumen estructurado sin necesidad de petición previa.
2. **Concisión**: El resumen debe ser claro, corto y accionable.

## FORMATO DEL RESUMEN

- **Session Title**: Título corto (4-8 palabras), iniciado con verbo en acción (ej: "Implementing Vue i18n Integration").
- **User Objective**: Objetivo principal del usuario.
- **Work Accomplished**: Archivos modificados y cambios realizados.
- **Technical Decisions**: Decisiones, librerías y enfoques elegidos.
- **Integration Context**: Sistemas involucrados (Laravel, React, IoT, etc.), APIs y flujo de datos.
- **Environment State**: Entorno, Base de Datos, Servicios activos y Rama.
- **Lessons Learned**: Problemas, soluciones y buenas prácticas.
- **Next Steps**: Tareas pendientes e inmediatas.

## ALMACENAMIENTO

1. **Historial**: `docs/interaction_log.md`
2. **Conocimiento**: `docs/knowledge/`
3. **Knowledge Items**: Proponer guardar como KI cualquier hallazgo reutilizable.

---

## CONTEXTO DEL PROYECTO

- **Backend**: Laravel SaaS
- **Frontend**: Vue (Inertia) y React (Vacaliza)
- **IoT**: Node-RED, LoRa, MQTT
- **Bases de Datos**: SQL + MongoDB
