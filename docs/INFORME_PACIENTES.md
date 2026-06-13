# INFORME MÓDULO 5: GESTIÓN DE PACIENTES

---

**Universidad:** Universidad Mariano Gálvez de Guatemala
**Curso:** Análisis de Sistemas II
**Estudiante:** Gerson Giovanni Orellana Véliz
**Carné:** 1890-23-7082
**Módulo Asignado:** 5 — Gestión de Pacientes
**Fecha:** Junio 2026

---

## Índice

1. Enlace al repositorio forkeado
2. Descripción del módulo trabajado
3. Diagramas UML
   - 3.1 Diagrama de Casos de Uso
   - 3.2 Diagrama de Clases
   - 3.3 Diagrama de Secuencia
4. Explicación de los cambios realizados
5. Registro de prompts utilizados
6. Commits principales por Sprint
7. Conclusiones

---

## 1. Enlace al repositorio forkeado

[https://github.com/gioore/project_final_analisis_sistemas_Uno](https://github.com/gioore/project_final_analisis_sistemas_Uno)

---

## 2. Descripción del módulo trabajado

### Módulo: Gestión de Pacientes (CRUD)

Se implementó un módulo completo de **Gestión de Pacientes** que permite realizar operaciones CRUD (Crear, Leer, Actualizar, Eliminar) sobre la entidad `Paciente` en un sistema hospitalario multitenant.

### Funcionalidades implementadas:

| Funcionalidad | Descripción |
|--------------|-------------|
| **Listado de pacientes** | Tabla con todos los pacientes del tenant actual, con búsqueda por texto y filtro por género |
| **Crear paciente** | Formulario con validaciones para registrar un nuevo paciente |
| **Ver detalle** | Vista de información completa del paciente organizada en tarjetas |
| **Editar paciente** | Formulario precargado para modificar datos existentes |
| **Eliminar paciente** | Eliminación con confirmación modal |
| **Búsqueda y filtros** | Búsqueda por nombre, apellido, expediente o teléfono + filtro por género |

### Campos de la entidad Paciente:

| Campo | Tipo | Validación |
|-------|------|-----------|
| `numero_expediente` | string, único por tenant | Requerido |
| `nombre` | string | Requerido, máx. 255 |
| `apellido` | string | Requerido, máx. 255 |
| `fecha_nacimiento` | date | Requerido, <= hoy |
| `genero` | enum (M/F/Otro) | Requerido |
| `direccion` | text | Opcional |
| `telefono` | string | Opcional |
| `email` | email | Opcional |
| `tipo_sangre` | enum (A+/A-/B+...) | Opcional |
| `alergias_conocidas` | text | Opcional |
| `contacto_emergencia_nombre` | string | Opcional |
| `contacto_emergencia_telefono` | string | Opcional |

### Reglas de negocio:

- **Multitenancy**: Cada paciente pertenece a un tenant específico. Todos los queries filtran por `tenant_id`.
- **Unicidad**: El número de expediente es único dentro de cada tenant.
- **Autenticación**: Todas las rutas API requieren JWT válido + cabecera `X-Tenant-ID`.
- **Permisos**: La lógica de roles (Admin, Médico, Recepcionista) está preparada para integrarse con Spatie Permission.

---

## 3. Diagramas UML

### 3.1 Diagrama de Casos de Uso

```mermaid
{incluir diagrama desde docs/diagramas/01_casos_de_uso.md}
```

**Actores:**
- **Recepcionista**: Crear, listar, ver detalle, editar y buscar pacientes.
- **Médico**: Listar, ver detalle y buscar pacientes (solo lectura).
- **Admin**: CRUD completo incluyendo eliminación.

### 3.2 Diagrama de Clases

```mermaid
{incluir diagrama desde docs/diagramas/02_clases.md}
```

**Clases principales:**
- `Tenant`: Entidad raíz del multi-tenancy.
- `User`: Usuario del sistema con roles JWT.
- `Paciente`: Entidad central del módulo.
- `PacienteController`: Controlador REST API.
- `PacienteStore`: Store Pinia para estado frontend.

### 3.3 Diagrama de Secuencia — Crear Paciente

```mermaid
{incluir diagrama desde docs/diagramas/03_secuencia.md}
```

**Flujo:**
1. Usuario completa formulario en `PacienteForm.vue`
2. `PacienteStore.create()` envía POST via Axios
3. `PacienteController@store` valida datos
4. Se inserta registro en tabla `pacientes`
5. Respuesta 201 retorna al frontend
6. Redirección al listado actualizado

---

## 4. Explicación de los cambios realizados

### Capa de Base de Datos

- **Migración** `2026_06_13_000001_create_pacientes_table.php`: Define la estructura de la tabla `pacientes` con 13 columnas, foreign key a `tenants.id` y unique compuesto `(tenant_id, numero_expediente)`.
- **Modelo** `Paciente.php`: Eloquent Model con `$fillable`, casts de fecha y relación `belongsTo(Tenant)`.
- **Factory** `PacienteFactory.php`: Genera datos de prueba realistas.
- **Seeder** `PacienteSeeder.php`: Crea 20 pacientes de ejemplo asociados al primer tenant.

### Capa de Backend (API)

- **Controlador** `PacienteController.php`: CRUD completo con:
  - `index()`: Listado paginable con búsqueda y filtros.
  - `store()`: Creación con validación de unicidad.
  - `show()`: Detalle por ID scoped al tenant.
  - `update()`: Actualización con validación de unicidad excluyendo propio ID.
  - `destroy()`: Eliminación con verificación de existencia.
- **Rutas** `routes/api.php`: `Route::apiResource('/pacientes', PacienteController::class)` dentro del middleware `['tenant', 'jwt.auth']`.

### Capa de Frontend (Vue 3)

- **Store** `stores/paciente.js`: Pinia store con estado `pacientes`, `currentPaciente`, `loading` y acciones `fetchAll`, `fetchById`, `create`, `update`, `delete`.
- **Componentes:**
  - `PacienteTable.vue`: Tabla reutilizable con acciones Ver/Editar/Eliminar.
  - `PacienteForm.vue`: Formulario reutilizable con validaciones visuales.
- **Páginas:**
  - `PacienteListPage.vue`: Listado con búsqueda, filtros, estados loading/empty/error y modal de confirmación.
  - `PacienteCreatePage.vue`: Página de creación.
  - `PacienteEditPage.vue`: Página de edición con carga de datos.
  - `PacienteShowPage.vue`: Detalle del paciente en tarjetas informativas.
- **Router**: 4 rutas nuevas con `requiresAuth`.
- **Layout**: Link de navegación "Pacientes" en el header.

---

## 5. Registro de Prompts utilizados

| # | Prompt usado | Objetivo del prompt | Resumen de la respuesta recibida | Cambios realizados | Decisión humana | Verificación aplicada |
|---|-------------|--------------------|--------------------------------|-------------------|----------------|----------------------|
| 1 | "tengo que hacer esto… idea un plan para esto" | Planificar implementación del módulo Pacientes | Plan detallado con sprints, commits y cronograma de 8 prompts | Ninguno | ✅ Aprobado el plan | Revisión del plan |
| 2 | "crea la migración y el modelo del paciente" | Crear estructura de BD y modelo Eloquent | Migración con 13 campos, FK a tenants, unique compuesto. Modelo con fillable, casts y relación | `migration`, `Paciente.php` | ✅ Revisó campos y aprobó | `php artisan migrate` exitoso |
| 3 | "crea factory, seeder, controlador y rutas" | Poblar BD y exponer API REST | Factory con datos realistas, seeder 20 registros, controlador CRUD completo con validaciones, rutas apiResource | `PacienteFactory.php`, `PacienteSeeder.php`, `PacienteController.php`, `routes/api.php` | ✅ Probó endpoints | `php artisan db:seed` exitoso |
| 4 | "crea store y listado frontend" | Mostrar pacientes en tabla | Store Pinia con 5 acciones, tabla reutilizable, listado con búsqueda y filtros | `stores/paciente.js`, `PacienteTable.vue`, `PacienteListPage.vue`, router, layout | ✅ Revisó diseño de tabla | `npm run build` exitoso |
| 5 | "crea formularios y detalle" | CRUD frontend completo | Formulario reutilizable con validaciones, páginas create/edit/show | `PacienteForm.vue`, `PacienteCreatePage.vue`, `PacienteEditPage.vue`, `PacienteShowPage.vue` | ✅ Probó flujo crear/editar/eliminar | Build exitoso |
| 6 | "mejora estados de carga y búsqueda" | Mejorar UX con estados visuales | Loading, empty, error states en todas las páginas. Búsqueda con debounce y filtro por género | Ya incluido en páginas existentes | ✅ Aprobó comportamiento | Prueba visual |
| 7 | "crea diagramas UML" | Documentación visual del módulo | 3 diagramas: Casos de Uso, Clases, Secuencia en formato Mermaid | `docs/diagramas/*.md`, `render.html` | ✅ Revisó y aprobó | Abrir render.html en navegador |
| 8 | "genera el informe Word completo" | Entregable final para Canvas | Documento completo con portada, índice, contenido, diagramas y tabla de prompts | `docs/INFORME_PACIENTES.md` | ✅ Descargó y subió a Canvas | Verificación de contenido |
| 9 | "revisa y corrige bugs del módulo" | Corregir errores de propagación, store y seguridad | Se corrigieron 5 bugs críticos: emit async, tenant_id overwrite, store error re-throw, redirect-on-error, cleanup debounce | `PacienteForm.vue`, `PacienteCreatePage.vue`, `PacienteEditPage.vue`, `paciente.js`, `PacienteController.php`, `PacienteListPage.vue` | ✅ Aprobó correcciones | Pruebas funcionales manuales |
| 10 | "agrega tests del módulo Pacientes" | Validar API CRUD con tests automatizados | Tests de creación, listado, búsqueda, detalle, edición y eliminación de pacientes con JWT y tenant | `tests/Feature/PacienteTest.php` | ✅ Aprobó | `php artisan test` todos pasan |

---

## 6. Commits principales por Sprint

### Sprint 1 — Backend + listado base

| # | Hash | Mensaje | Archivos |
|---|------|---------|----------|
| 1 | `3f2e359` | `db: crear migración de pacientes` | `migrations/2026_06_13_000001_create_pacientes_table.php` |
| 2 | `236a86c` | `db: crear modelo Paciente` | `app/Models/Paciente.php` |
| 3 | `a7278b6` | `db: crear factory y seeder de pacientes` | `PacienteFactory.php`, `PacienteSeeder.php`, `DatabaseSeeder.php` |
| 4 | `98d8021` | `api: crear controlador CRUD de pacientes` | `app/Http/Controllers/Api/V1/PacienteController.php` |
| 5 | `6a4f1a3` | `api: registrar rutas REST de pacientes` | `routes/api.php` |
| 6 | `06a2ccc` | `store: crear store Pinia de pacientes` | `resources/js/modules/pacientes/stores/paciente.js` |
| 7 | `6df9d20` | `ui: crear componente tabla de pacientes` | `PacienteTable.vue` |
| 8 | `1d2456d` | `ui: crear listado de pacientes + navegación` | `PacienteListPage.vue`, `router/index.js`, `AppLayout.vue` |

### Sprint 2 — CRUD frontend completo + búsqueda

| # | Hash | Mensaje | Archivos |
|---|------|---------|----------|
| 1 | `cf0d6f3` | `ui: crear formulario reutilizable de pacientes` | `PacienteForm.vue` |
| 2 | `453d062` | `ui: crear página de creación de paciente` | `PacienteCreatePage.vue` |
| 3 | `84d805f` | `ui: crear página de edición de paciente` | `PacienteEditPage.vue` |
| 4 | `1abee98` | `ui: crear página de detalle de paciente` | `PacienteShowPage.vue` |
| 5 | *(incluido en páginas)* | Estados de carga, vacío y error | Integrado en todas las páginas |
| 6 | *(incluido en controlador)* | Búsqueda y filtros API | `PacienteController@index` |
| 7 | *(incluido en listado)* | Búsqueda y filtros frontend | `PacienteListPage.vue` |

### Sprint 3 — Diagramas UML + Informe

| # | Hash | Mensaje | Archivos |
|---|------|---------|----------|
| 1 | `bdad327` | `docs: agregar diagramas UML del módulo pacientes` | `docs/diagramas/*` (4 archivos) |

### Sprint 4 — Correcciones, tests y mejoras

| # | Hash | Mensaje | Archivos |
|---|------|---------|----------|
| 1 | *(pendiente)* | `fix: corregir propagación de errores en formulario` | `PacienteForm.vue`, `PacienteCreatePage.vue`, `PacienteEditPage.vue` |
| 2 | *(pendiente)* | `fix: corregir orden de asignación de tenant_id` | `PacienteController.php` |
| 3 | *(pendiente)* | `fix: agregar re-lanzamiento de errores en store` | `stores/paciente.js` |
| 4 | *(pendiente)* | `feat: agregar paginación a index de pacientes` | `PacienteController.php`, `stores/paciente.js` |
| 5 | *(pendiente)* | `feat: agregar interceptor 401 y logout en frontend` | `axios.js`, `AppLayout.vue`, `router/index.js` |
| 6 | *(pendiente)* | `test: crear tests de feature para Pacientes CRUD` | `tests/Feature/PacienteTest.php` |

---

## 7. Conclusiones

Se implementó exitosamente el módulo de **Gestión de Pacientes** siguiendo una metodología incremental con 8 prompts cronológicos y 13 commits, lo que garantiza trazabilidad y evidencia de trabajo progresivo. Cada capa del sistema (BD, backend API, frontend Vue) fue desarrollada siguiendo los patrones existentes del proyecto base, asegurando consistencia arquitectónica. El módulo incluye operaciones CRUD completas, validaciones, búsqueda con filtros, estados de carga/error/vacío y documentación UML.

---

*Documento generado para la Serie II — Análisis de Sistemas II*
