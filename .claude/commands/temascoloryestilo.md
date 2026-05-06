# Guía de Temas, Color, Estilo y UX — Cronos Medical

> **INSTRUCCIÓN CRÍTICA PARA CLAUDE:** Al invocar este comando, SIEMPRE activar primero el skill `frontend-design` con `Skill({ skill: "frontend-design:frontend-design" })` antes de generar cualquier componente UI. Este skill proporciona las capacidades de diseño de producción, criterio estético avanzado y principios de UI modernos que deben combinarse con las especificaciones Cronos definidas en este documento.

Esta guía define el sistema de diseño visual y los patrones UX del proyecto Cronos.
**Consultar antes de crear o modificar cualquier componente UI.**
Todos los colores, componentes y patrones deben seguir este documento.

---

## Protocolo de Uso (leer siempre primero)

Antes de crear o modificar cualquier componente UI en Cronos, seguir este flujo obligatorio:

### Paso 1 — Activar frontend-design skill
```
Skill({ skill: "frontend-design:frontend-design" })
```
El skill aporta: diseño de producción, tipografía avanzada, motion, composición espacial, paletas cohesivas, responsive thinking y criterio anti "AI slop".

### Paso 2 — Analizar lo existente antes de crear
Antes de generar código nuevo, explorar el estado actual:
- Leer el componente o vista existente si ya existe
- Identificar inconsistencias con esta guía
- Proponer mejoras si las hay, antes de solo "cumplir el pedido"

### Paso 3 — Aplicar diseño con criterio Cronos
Combinar los principios del frontend-design skill con las especificaciones de esta guía:
- **Estética Cronos:** refined/medical — limpia, profesional, confiable, no fría
- **No genérico:** evitar patrones predecibles de "dashboard médico promedio"
- **Mobile-first obligatorio:** cada componente diseñado para mobile primero, luego expandido a desktop

---

## Diseño Responsive — Reglas Obligatorias

### Breakpoints Tailwind en uso
| Prefijo | Viewport | Contexto |
|---|---|---|
| (sin prefijo) | `< 640px` | Mobile — **diseñar aquí primero** |
| `sm:` | `≥ 640px` | Mobile grande / tablet pequeña |
| `md:` | `≥ 768px` | Tablet |
| `lg:` | `≥ 1024px` | Desktop con sidebar expandido |
| `xl:` | `≥ 1280px` | Desktop amplio |

### Layout con sidebar
- **Mobile (`< lg`):** sidebar oculto por defecto, hamburger en header
- **Desktop (`lg:`):** sidebar fijo a la izquierda, contenido con `ml-20` (colapsado) o `ml-64` (expandido)
- Nunca usar `fixed` widths en el contenido principal — siempre `flex-1 min-w-0`

### Grillas responsive en formularios
```html
<!-- Formulario estándar: 1 columna mobile, 2 columnas md, 3 columnas xl -->
<div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">
    <x-form_inputs.input ... />
</div>

<!-- Par de campos relacionados: stack en mobile, lado a lado en sm -->
<div class="flex flex-col gap-4 sm:flex-row">
    <div class="flex-1"><x-form_inputs.input ... /></div>
    <div class="flex-1"><x-form_inputs.input ... /></div>
</div>
```

### Tablas en mobile
```html
<!-- Tabla responsive: scroll horizontal en mobile, tabla completa en md -->
<div class="overflow-x-auto -mx-4 sm:mx-0">
    <table class="min-w-full ...">
        <!-- columnas secundarias: ocultar en mobile -->
        <th class="hidden md:table-cell ...">Email</th>
        <td class="hidden md:table-cell ...">{{ $user->email }}</td>
    </table>
</div>

<!-- Alternativa mobile: card list en lugar de tabla -->
<div class="block md:hidden space-y-3">
    @foreach ($items as $item)
    <div class="rounded-xl border border-slate-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
        <div class="flex items-center justify-between">
            <p class="font-semibold text-slate-800 dark:text-gray-100">{{ $item->nombre }}</p>
            <!-- actions -->
        </div>
        <p class="mt-1 text-sm text-slate-500 dark:text-gray-400">{{ $item->descripcion }}</p>
    </div>
    @endforeach
</div>
```

### Modales responsive
```html
<!-- Mobile: bottom sheet / full-width. Desktop: centered modal -->
<div class="fixed inset-0 z-50 flex items-end justify-center sm:items-center bg-black/40 backdrop-blur-sm">
    <div class="w-full rounded-t-2xl sm:max-w-lg sm:rounded-2xl bg-white dark:bg-gray-900 ...">
        <!-- drag handle en mobile -->
        <div class="mx-auto mt-3 h-1 w-10 rounded-full bg-slate-300 dark:bg-gray-700 sm:hidden"></div>
        <!-- contenido -->
    </div>
</div>
```

### Touch targets obligatorios
- Mínimo `44×44px` en cualquier elemento accionable en mobile
- Botones en mobile: `w-full` o `flex-1` en pares — nunca botones pequeños side by side
- Inputs: `py-3` mínimo en mobile (en lugar de `py-2.5`) para facilitar tap
- Espaciado entre elementos tocables: mínimo `gap-3`

---

## Análisis y Mejora de Componentes Existentes

Cuando se modifica un componente existente, evaluar y aplicar si aplica:

### Checklist de calidad visual (frontend-design)
- [ ] **Tipografía:** ¿Usa `font-headline` para títulos y `font-body`/`font-label` para texto? ¿Hay jerarquía clara?
- [ ] **Contraste:** ¿Cumple AA (4.5:1 normal, 3:1 grande) en ambos modos?
- [ ] **Motion:** ¿Hay `transition-all duration-200` en elementos interactivos? ¿Faltan microinteracciones?
- [ ] **Espaciado:** ¿Respeta el sistema (`px-6 py-5` en cards, `space-y-5` entre campos)?
- [ ] **Responsive:** ¿Funciona en 375px (iPhone SE)? ¿Y en 1440px?
- [ ] **Empty state:** ¿Existe para listas/tablas vacías?
- [ ] **Loading state:** ¿Skeleton o spinner apropiado según duración?
- [ ] **Dark mode:** ¿Cada clase light tiene su par `dark:`?
- [ ] **Accesibilidad:** ¿`aria-label` en iconos, `focus:ring` presente, orden de tab lógico?

### Mejoras visuales a priorizar (si el componente las necesita)
1. **Depth y atmósfera:** reemplazar fondos planos por sutil gradiente o textura (`bg-gradient-to-br from-slate-50 to-indigo-50/30`)
2. **Hover states ricos:** no solo color — agregar `shadow` o `translate` sutil
3. **Composición asimétrica:** romper grillas perfectas con elementos destacados
4. **Animaciones de entrada:** `animate-fade-in` o `@starting-style` para elementos que aparecen
5. **Bordes con personalidad:** `border-indigo-200/60` en lugar de `border-slate-200` genérico

---

---

## Stack y Configuración

- **Framework CSS:** Tailwind CSS v3 con `@tailwindcss/forms` plugin
- **Dark mode:** `darkMode: 'class'` — activado vía clase `dark` en `<html>`
- **Fuentes:** Plus Jakarta Sans (`font-headline`) + Manrope (`font-body`, `font-label`)
- **Componentes dinámicos:** Livewire 4 + Alpine.js 3
- **Iconos:** exclusivamente Heroicons vía `<x-menu.heroicon name="..." />`
- **Config:** `tailwind.config.js` en raíz del `src/`

> **⚠️ Nota `@tailwindcss/forms`:** este plugin inyecta un `background-image` SVG como flecha en los `<select>`. Para eliminarlo, usar siempre `appearance-none bg-none` + `style="background-image:none"` o la regla global ya definida en `app.css`.

---

## Tipografía

| Familia | Config | Uso |
|---|---|---|
| Plus Jakarta Sans | `font-headline` | Títulos, h1-h3, CTAs, nombres de marca |
| Manrope | `font-body` / `font-label` | Cuerpo de texto, labels, descripciones, datos |

```html
<h2 class="font-headline font-extrabold text-2xl tracking-tight">Título de sección</h2>
<p class="font-body text-sm text-slate-600 dark:text-gray-400">Texto de apoyo</p>
<label class="font-label text-sm font-semibold">Label de campo</label>
```

---

## Utilidades CSS Globales (`app.css`)

Disponibles en toda la app sin necesidad de clases Tailwind largas:

| Clase | Uso |
|---|---|
| `.signature-gradient` | Gradiente de marca — botones CTA y paneles hero. Light: `indigo-600→indigo-800`, Dark: `sky-500→indigo-600` |
| `.glass-panel` | Panel glassmorphism — navs flotantes, headers sticky. 80% opacidad + `backdrop-blur(24px)` |
| `.shadow-ambient` | Sombra suave teñida de primario: `0px 12px 32px rgba(79,70,229,0.08)` |
| `.card-surface` | Atajo: `bg-white rounded-2xl shadow-ambient dark:bg-gray-900` |

```html
<!-- CTA con gradiente de marca -->
<button class="signature-gradient text-white rounded-xl px-6 py-3 font-bold">Guardar</button>

<!-- Panel flotante con glass -->
<div class="glass-panel rounded-2xl p-6">...</div>
```

---

## Paleta Base del Sistema

### Light Mode
| Elemento | Clase Tailwind | Uso |
|---|---|---|
| Nav background (sidebar + header) | `bg-indigo-50` | Fondo de navegación |
| Nav border | `border-indigo-100` | Bordes internos del nav |
| Content background | `bg-white` | Área de contenido principal |
| Page background | `bg-slate-50` | Body/main — fondo ligeramente gris |
| **Color de marca primario** | `indigo-600` | Botones, activos, acciones |
| **Color de marca suave** | `indigo-100 / indigo-200` | Hovers, fondos activos |
| Texto principal | `text-slate-800` | Títulos, texto importante |
| Texto secundario | `text-slate-600` | Labels, texto normal |
| Texto terciario | `text-slate-400` | Placeholders, hints |
| Acento éxito | `emerald-600` | Online, guardado, confirmación |
| Acento peligro | `rose-500 / rose-600` | Errores, eliminar |
| Acento advertencia | `amber-500` | Alertas, precaución |

### Dark Mode
| Elemento | Clase Tailwind | Uso |
|---|---|---|
| Nav background (sidebar + header) | `bg-gray-900` | Fondo de navegación |
| Nav border | `border-gray-800` | Bordes internos del nav |
| Content background | `bg-gray-950` | Área de contenido principal |
| **Color de marca primario** | `sky-400 / sky-500` | Botones activos, acciones |
| **Color de marca suave** | `sky-500/20` | Hovers, fondos activos |
| Texto principal | `text-gray-100` | Títulos, texto importante |
| Texto secundario | `text-gray-300` | Labels, texto normal |
| Texto terciario | `text-gray-500` | Placeholders, hints |
| Acento éxito | `emerald-400` | Online, guardado |
| Acento peligro | `rose-400` | Errores, eliminar |
| Acento advertencia | `amber-400` | Alertas, precaución |

---

## Componentes de Formulario — `x-form_inputs.*`

> **Regla:** usar **siempre** estos componentes Blade para inputs, selects y textareas.
> Nunca escribir `<input>`, `<select>` o `<textarea>` HTML crudo en vistas, salvo casos con escape hatch justificado.

### API común (props compartidos por todos)

| Prop | Tipo | Uso |
|---|---|---|
| `label` | `string\|null` | Etiqueta visible. Nunca omitir. |
| `description` | `string\|null` | Texto de ayuda debajo del label |
| `name` | `string` | Nombre del campo — liga con `@error` automáticamente |
| `size` | `'sm'\|'md'\|'lg'` | Tamaño. Default: `md` |
| `required` | `bool` | Muestra `*` en rojo junto al label |
| `disabled` | `bool` | Aplica opacidad + `cursor-not-allowed` |
| `readonly` | `bool` | Solo lectura, mismo estilo que disabled |

Los estados de error se muestran automáticamente con `@error($name)` — no hace falta pasarlos manualmente.

---

### `x-form_inputs.input` — Input sin icono

Usar cuando el campo no necesita icono decorativo. La opción más limpia para la mayoría de formularios.

```blade
{{-- Básico --}}
<x-form_inputs.input label="Nombre completo" name="nombre" placeholder="Juan García" required />

{{-- Con descripción --}}
<x-form_inputs.input
    label="Código postal"
    name="codigo_postal"
    description="5 dígitos numéricos"
    size="sm"
    required
/>

{{-- Contraseña — toggle eye/eye-slash incluido automáticamente --}}
<x-form_inputs.input
    label="Nueva contraseña"
    name="password"
    type="password"
    autocomplete="new-password"
    required
/>

{{-- Con wire:model de Livewire --}}
<x-form_inputs.input label="Email" name="email" type="email" wire:model.live="email" required />
```

**Props adicionales:** `type` (default `text`), `placeholder`, `value`, `autofocus`, `autocomplete`

---

### `x-form_inputs.text_input` — Input con icono

Usar cuando el campo se beneficia de un icono leading para dar contexto visual (email, teléfono, búsqueda, etc).

```blade
{{-- Con icono leading --}}
<x-form_inputs.text_input
    label="Correo electrónico"
    name="email"
    type="email"
    icon="envelope"
    placeholder="correo@ejemplo.com"
    autocomplete="email"
    required
/>

{{-- Contraseña con icono — toggle automático --}}
<x-form_inputs.text_input
    label="Contraseña"
    name="password"
    type="password"
    icon="lock-closed"
    placeholder="••••••••"
    autocomplete="current-password"
    required
/>

{{-- Con icono trailing (solo decorativo, no password) --}}
<x-form_inputs.text_input
    label="Búsqueda"
    name="search"
    icon="magnifying-glass"
    iconTrailing="adjustments-horizontal"
    placeholder="Buscar paciente…"
/>
```

**Props adicionales:** `icon` (heroicon leading), `iconTrailing` (heroicon trailing), `type`, `placeholder`, `value`, `autofocus`, `autocomplete`

> El toggle de contraseña usa `x-data="{ showPwd: false }"` interno — no requiere Alpine externo.

---

### `x-form_inputs.select` — Select con chevron custom

Reemplaza el select nativo. Elimina la flecha del browser con `appearance-none` + `bg-none` + `background-image:none` (necesario por `@tailwindcss/forms`).

```blade
{{-- Básico --}}
<x-form_inputs.select label="Estado" name="estado" required>
    <option value="">Seleccionar…</option>
    <option value="activo">Activo</option>
    <option value="inactivo">Inactivo</option>
</x-form_inputs.select>

{{-- Con icono leading --}}
<x-form_inputs.select label="Tipo de entidad" name="tipo_entidad" icon="building-library">
    <option value="">Seleccionar tipo…</option>
    <option value="clinica">Clínica Privada</option>
    <option value="hospital">Hospital</option>
</x-form_inputs.select>

{{-- Con wire:model --}}
<x-form_inputs.select label="Rol" name="rol" wire:model="rol" required>
    <option value="">Seleccionar rol…</option>
    <option value="admin">Administrador</option>
    <option value="medico">Médico</option>
</x-form_inputs.select>
```

**Props adicionales:** `icon` (heroicon leading opcional). El chevron `chevron-up-down` se agrega automáticamente.

> **Importante:** nunca usar `<select>` HTML crudo — `@tailwindcss/forms` le agrega su propia flecha que se superpone con el chevron del componente.

---

### `x-form_inputs.textarea` — Área de texto

```blade
{{-- Básico --}}
<x-form_inputs.textarea
    label="Observaciones"
    name="observaciones"
    placeholder="Notas adicionales…"
    :rows="4"
/>

{{-- Con descripción y opcional --}}
<x-form_inputs.textarea
    label="Descripción"
    name="descripcion"
    description="(opcional)"
    placeholder="Breve descripción…"
    :rows="3"
/>

{{-- Con wire:model --}}
<x-form_inputs.textarea
    label="Diagnóstico"
    name="diagnostico"
    wire:model.live="diagnostico"
    required
/>
```

**Props adicionales:** `placeholder`, `value`, `rows` (default `4`). Siempre `resize-none` por defecto.

---

### `x-form-inputs.autocomplete` — Selector con búsqueda

Búsqueda client-side sobre un array de opciones. Soporta teclado, clear, empty state y loading.

```blade
{{-- Básico --}}
<x-form-inputs.autocomplete
    label="País"
    name="pais_id"
    placeholder="Seleccionar país…"
    :options="$this->country->map(fn($p) => ['value' => $p->id, 'label' => $p->name])"
    wire:model.live="country_id"
    required />

{{-- Con spinner de carga (loading-target = propiedad que dispara el request) --}}
<x-form-inputs.autocomplete
    label="Provincia"
    name="provincia_id"
    class="data-loading:opacity-50"
    :options="$this->province->map(fn($p) => ['value' => $p->id, 'label' => $p->name])"
    wire:model.live="province_id"
    :loading="true"
    loading-target="country_id"
    required />
```

| Prop | Default | Descripción |
|---|---|---|
| `options` | `[]` | Array `[{value, label}]` o `['key' => 'label']` |
| `value` | `null` | Valor pre-seleccionado |
| `icon` | `null` | Heroicon leading |
| `loading` | `false` | Activa el spinner en el trailing |
| `loadingTarget` | `null` | Propiedad Livewire que dispara el spinner (evita activar todos los spinners simultáneamente) |
| `class` | — | Se mergea en el div raíz (ej: `data-loading:opacity-50`) |

**Cascada de selects dependientes:** usar `wire:key="slug-{{ $parentId }}"` en un `<div>` wrapper — fuerza Alpine a reinicializar con las opciones nuevas cuando cambia el padre.

```blade
<div wire:key="province-{{ $this->country_id }}">
    <x-form-inputs.autocomplete ... />
</div>
```

> **⚠️ `loading-target` es obligatorio cuando hay múltiples autocompletes con `loading=true`** — sin target, todos los spinners se activan ante cualquier request de Livewire.

**Archivo:** `resources/views/components/form-inputs/autocomplete.blade.php`

---

### Escape hatch — cuando necesitás control total

```blade
{{-- Solo cuando los componentes no cubren el caso específico --}}
<div>
    <label class="block text-sm font-semibold text-slate-700 dark:text-gray-300">
        Campo especial <span class="text-rose-500">*</span>
    </label>
    <p class="mt-1 mb-1.5 text-xs text-slate-400 dark:text-gray-500">Texto de ayuda</p>
    <input
        class="block w-full rounded-xl border border-indigo-200/80 bg-white px-4 py-2.5 text-sm
               placeholder-slate-400 shadow-sm transition-all duration-200 focus:outline-none
               focus:ring-2 focus:border-indigo-400 focus:ring-indigo-400/25
               dark:bg-gray-800 dark:text-gray-100 dark:border-gray-700
               dark:focus:border-sky-500 dark:focus:ring-sky-400/25"
        type="text" name="campo" />
    @error('campo')
        <p class="mt-1 text-xs font-medium text-rose-500 dark:text-rose-400">{{ $message }}</p>
    @enderror
</div>
```

---

## Vocabulario de Props Blade

Usar **los mismos nombres en todos los componentes** para que la API sea predecible:

| Prop | Uso |
|---|---|
| `label` | Etiqueta visible del campo/botón |
| `description` | Texto secundario debajo del label |
| `icon` | Icono leading (nombre Heroicon) |
| `iconTrailing` | Icono trailing del campo/botón |
| `variant` | Estilo visual: `primary`, `secondary`, `ghost`, `danger`, `success` |
| `size` | Tamaño: `sm`, `md`, `lg` |
| `required` | Campo obligatorio |
| `disabled` | Deshabilitado |
| `readonly` | Solo lectura |
| `loading` | Estado de carga (muestra spinner) |

---

## Componentes HTML

### CONTENEDOR PRINCIPAL DE PÁGINA (main-div)

Componente raíz de cada vista Livewire. Clase CSS: `.main-div-container`.

```blade
<x-form-style.main-div>

    {{-- Header de sección --}}
    <div class="border-b border-slate-100 px-6 py-4 dark:border-gray-800">
        <h2 class="text-base font-bold text-slate-800 dark:text-gray-100">Título</h2>
        <p class="mt-0.5 text-sm text-slate-500 dark:text-gray-500">Descripción</p>
    </div>

    {{-- Body --}}
    <div class="px-6 py-5">
        {{-- inputs, tablas, etc. --}}
    </div>

    {{-- Footer con botones (opcional) --}}
    <div class="flex items-center justify-end gap-3 border-t border-slate-100 bg-slate-50/50 px-6 py-4 dark:border-gray-800 dark:bg-gray-900/40">
        {{-- botones --}}
    </div>

</x-form-style.main-div>
```

**Estilos:** Light: `bg-white border-indigo-200 shadow-md shadow-slate-200/80` · Dark: `bg-gray-900 border-indigo-900 shadow-lg shadow-black/30`
**Archivo:** `resources/views/components/form-style/main-div.blade.php`

> **⚠️ NUNCA usar `overflow-hidden` en `main-div`** si hay `<x-form-inputs.autocomplete>` dentro — clipa el dropdown.

> **⚠️ `main-div-container` DEBE tener `relative`** — ya está aplicado en el componente. Sin `relative`, los orbes decorativos (`absolute`) se posicionan respecto al viewport en lugar del card, generando scroll fantasma en `<main>`.

> **⚠️ Los orbes decorativos internos deben usar posiciones NO negativas** — usar `right-0 top-0` y `bottom-0 left-0`, nunca `-right-N` ni `-left-N`. Con `relative` activo, valores negativos empujan los orbes fuera del card causando scroll horizontal.

---

### SECTION HEADER CON ÍCONO (patrón validado)

Para separar secciones dentro del body de un `main-div`, usar este patrón de título con badge de ícono. El color del badge comunica la categoría semántica de la sección.

```blade
{{-- Ícono primario (indigo) — localización, datos generales --}}
<div class="flex items-center gap-3">
    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-100 text-indigo-600 dark:bg-indigo-500/15 dark:text-indigo-400">
        <x-menu.heroicon name="map-pin" class="h-4 w-4" />
    </div>
    <h3 class="font-headline text-base font-bold text-slate-800 dark:text-gray-100">Localización</h3>
</div>

{{-- Ícono éxito (emerald) — finanzas, confirmación --}}
<div class="flex items-center gap-3">
    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-100 text-emerald-600 dark:bg-emerald-500/15 dark:text-emerald-400">
        <x-menu.heroicon name="currency-dollar" class="h-4 w-4" />
    </div>
    <h3 class="font-headline text-base font-bold text-slate-800 dark:text-gray-100">Moneda y Finanzas</h3>
</div>
```

**Reglas del patrón:**
- Badge: siempre `h-8 w-8 rounded-lg` — nunca `rounded-full` (reservado para dots/avatares)
- Ícono dentro: siempre `h-4 w-4`
- Color del badge = semántica de la sección, no el primario por defecto
- Usar como separador visual cuando un formulario tiene 2+ grupos lógicos en layout de 2 columnas

**Layout 2 columnas con section headers:**
```blade
<div class="grid grid-cols-1 gap-6 lg:grid-cols-2 lg:gap-x-8">
    <div class="space-y-4">
        {{-- section header izquierda --}}
        {{-- campos --}}
    </div>
    <div class="space-y-4">
        {{-- section header derecha --}}
        {{-- campos --}}
    </div>
</div>
```

---

### FOOTER DE FORMULARIO (footer-button)

Componente reutilizable para el pie de cualquier formulario. Siempre usar en lugar de escribir los botones manualmente.

```blade
{{-- Básico --}}
<x-form-style.footer-button />

{{-- Con labels y acción cancelar --}}
<x-form-style.footer-button
    save-label="Crear Región"
    cancel-label="Descartar"
    cancel-action="resetForm" />
```

| Prop | Default | Descripción |
|---|---|---|
| `saveLabel` | `"Guardar"` | Texto del botón guardar |
| `cancelLabel` | `"Cancelar"` | Texto del botón cancelar |
| `saveIcon` | `"document-check"` | Heroicon del botón guardar |
| `cancelAction` | `null` | Método Livewire a llamar al cancelar |

**Comportamiento:** Mobile — botones full-width apilados. Desktop (`sm+`) — lado a lado, alineados a la derecha. Loading automático con `wire:loading`.
**Archivo:** `resources/views/components/form-style/footer-button.blade.php`

---

### BOTONES

Las clases de botones viven en `resources/css/app.css` bajo `@layer components`.
Siempre combinar: `btn-base` + variant + tamaño.

```html
<!-- Primario — acción principal -->
<button class="btn-base btn-primary btn-md">Guardar</button>

<!-- Primario con gradiente de marca (CTAs hero, login) -->
<button class="btn-base btn-lg signature-gradient text-white shadow-[0px_8px_24px_rgba(79,70,229,0.20)] hover:-translate-y-0.5">
    Iniciar sesión
</button>

<!-- Secundario — alternativa neutral -->
<button class="btn-base btn-secondary btn-md">Volver</button>

<!-- Cancelar — danger suave. SIEMPRE para cancelar formularios -->
<button class="btn-base btn-cancel btn-md">Cancelar</button>

<!-- Peligro — eliminar / acción irreversible. Solo en modales de confirmación -->
<button class="btn-base btn-danger btn-md">Eliminar</button>

<!-- Ghost — acción discreta / link-style -->
<button class="btn-base btn-ghost btn-md">Ver detalle</button>

<!-- Éxito — aprobar / confirmar estado positivo -->
<button class="btn-base btn-success btn-md">Aprobar</button>

<!-- Par cancelar + guardar en formulario compacto (sm) -->
<button type="button" class="btn-base btn-cancel btn-sm flex-1 sm:flex-none">
    <x-menu.heroicon name="x-mark" class="h-3.5 w-3.5" />
    Cancelar
</button>
<button type="submit" class="btn-base btn-primary btn-sm flex-1 sm:flex-none">
    <x-menu.heroicon name="document-check" class="h-3.5 w-3.5" />
    Guardar Empresa
</button>

<!-- Ícono solo — siempre aria-label -->
<button class="rounded-lg p-2 text-slate-500 transition-colors hover:bg-indigo-100 hover:text-slate-700 dark:text-gray-500 dark:hover:bg-gray-800 dark:hover:text-gray-300"
        aria-label="Editar">
    <x-menu.heroicon name="pencil" class="h-5 w-5" />
</button>

<!-- Loading state (wire:loading) -->
<button wire:loading.attr="disabled" class="btn-base btn-primary btn-md opacity-60 disabled:cursor-not-allowed">
    <x-menu.heroicon wire:loading.remove name="check" class="h-4 w-4" />
    <svg wire:loading class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
    </svg>
    <span wire:loading.remove>Guardar</span>
    <span wire:loading>Guardando…</span>
</button>
```

**Variantes de botones:**

| Variant | Cuándo usar |
|---|---|
| `primary` | Acción principal del formulario/vista — una sola por área |
| `primary gradient` | CTAs hero, login, onboarding — máximo 1 por pantalla |
| `secondary` | Alternativa neutral ("Volver", "Ver más") |
| `cancelar` (danger suave) | Cancelar formulario / descartar cambios — **siempre rose suave, nunca rojo intenso** |
| `danger` | Eliminar / acción irreversible — rojo sólido, solo en modales de confirmación |
| `ghost` | Acciones discretas, links-style dentro de tablas o cards |
| `success` | Aprobar / confirmar estado positivo |

**Tamaños:**

| Size | Padding | Texto | Ícono | Gap | Cuándo usar |
|---|---|---|---|---|---|
| `sm` | `px-3 py-1.5` | `text-xs` | `h-3.5 w-3.5` | `gap-1.5` | Formularios compactos, footers ajustados |
| `md` | `px-4 py-2.5` | `text-sm` | `h-4 w-4` | `gap-2` | Default — uso general |
| `lg` | `px-6 py-3` | `text-base` | `h-5 w-5` | `gap-2` | CTAs prominentes, hero sections |

**Reglas de botones:**
- Siempre `rounded-xl` y `font-semibold`
- `active:scale-[0.98]` en todos los botones para feedback táctil
- Botones solo-icono: siempre `aria-label` con descripción de la acción
- **Deshabilitar + cambiar texto** mientras envía — nunca dejar el botón activo durante submit
- Texto con verbos específicos: "Crear empresa", no "Enviar"
- En mobile: botones en par (`cancelar` + `guardar`) deben ser `flex-1` para ocupar el ancho completo y `sm:flex-none` en desktop
- **Cancelar** siempre usa el variant danger suave (rose suave) — nunca el secundario neutro ni el peligro sólido

---

### TABLAS

```html
<!-- Contenedor -->
<div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">

    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-slate-200 bg-indigo-50/80 dark:border-gray-800 dark:bg-gray-800/60">
                <!-- Header con sort -->
                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-gray-500">
                    <button class="inline-flex items-center gap-1 hover:text-slate-700 dark:hover:text-gray-300">
                        Columna
                        <x-menu.heroicon name="chevron-up-down" class="h-3.5 w-3.5" />
                    </button>
                </th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 dark:divide-gray-800">
            <!-- Skeleton row (estado loading) -->
            <tr wire:loading>
                <td class="px-4 py-3"><div class="h-4 w-3/4 animate-pulse rounded bg-slate-200 dark:bg-gray-700"></div></td>
            </tr>

            <!-- Fila normal -->
            <tr class="transition-colors duration-150 hover:bg-indigo-50/60 dark:hover:bg-gray-800/50">
                <td class="px-4 py-3 font-medium text-slate-800 dark:text-gray-200">Valor principal</td>
                <td class="px-4 py-3 text-slate-500 dark:text-gray-500">Valor secundario</td>
                <!-- Row actions — siempre en dropdown al final -->
                <td class="px-4 py-3 text-right">
                    <!-- dropdown ⋯ -->
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Footer con paginación -->
    <div class="border-t border-slate-100 bg-slate-50/50 px-4 py-3 dark:border-gray-800 dark:bg-gray-900/50">
        {{ $items->links() }}
    </div>
</div>
```

**Reglas de tablas:**
- Paginación server-side siempre que haya >100 filas potenciales
- Sort clickeando el header con indicador visual (`chevron-up-down`)
- Row actions en dropdown `⋯` al final de la fila — no botones sueltos
- Loading: skeleton rows, **no** spinner que reemplaza toda la tabla
- Filtros arriba de la tabla, no en los headers
- Empty state si no hay resultados (ver sección Empty States)

---

### CARDS

```html
<!-- Card estándar -->
<div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
    <div class="border-b border-slate-100 px-6 py-4 dark:border-gray-800">
        <h3 class="text-base font-bold text-slate-800 dark:text-gray-100">Título</h3>
        <p class="mt-0.5 text-sm text-slate-500 dark:text-gray-500">Descripción</p>
    </div>
    <div class="px-6 py-5"><!-- contenido --></div>
    <div class="border-t border-slate-100 bg-slate-50/50 px-6 py-4 dark:border-gray-800 dark:bg-gray-900/50">
        <!-- acciones -->
    </div>
</div>

<!-- Card KPI / estadística -->
<div class="rounded-2xl border border-indigo-100 bg-white shadow-sm dark:border-indigo-900/40 dark:bg-gray-900">
    <div class="flex items-center gap-4 px-6 py-5">
        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-100 text-indigo-600 dark:bg-indigo-500/15 dark:text-indigo-400">
            <x-menu.heroicon name="users" class="h-6 w-6" />
        </div>
        <div>
            <p class="text-2xl font-bold text-slate-800 dark:text-gray-100">1,234</p>
            <p class="text-sm text-slate-500 dark:text-gray-500">Pacientes activos</p>
        </div>
    </div>
</div>
```

---

### MODALES

```html
<!-- Backdrop -->
<div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm dark:bg-black/60">

    <!-- Modal container -->
    <div class="w-full max-w-lg rounded-2xl bg-white shadow-2xl shadow-slate-300/30 ring-1 ring-slate-200/80 dark:bg-gray-900 dark:shadow-black/40 dark:ring-gray-800">

        <!-- Header -->
        <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4 dark:border-gray-800">
            <h2 class="text-base font-bold text-slate-800 dark:text-gray-100">Título del Modal</h2>
            <button class="rounded-lg p-1.5 text-slate-400 transition-colors hover:bg-slate-100 hover:text-slate-600 dark:text-gray-600 dark:hover:bg-gray-800 dark:hover:text-gray-400"
                    aria-label="Cerrar modal">
                <x-menu.heroicon name="x-mark" class="h-5 w-5" />
            </button>
        </div>

        <!-- Body -->
        <div class="px-6 py-5"><!-- contenido --></div>

        <!-- Footer -->
        <div class="flex items-center justify-end gap-3 border-t border-slate-100 bg-slate-50/60 px-6 py-4 dark:border-gray-800 dark:bg-gray-900/40">
            <!-- botón secundario + botón primario -->
        </div>

    </div>
</div>
```

**Reglas de modales:**
- Cierre por: click fuera, `Esc`, botón X visible
- Focus automático al primer input o botón primario al abrir
- Devolver focus al trigger al cerrar
- Máximo `max-w-lg` para simples, `max-w-4xl` para complejos — nunca full-width en desktop
- **Nunca** modales anidados

---

### MODAL DE CONFIRMACIÓN DESTRUCTIVA

```html
<div class="...modal-backdrop...">
    <div class="w-full max-w-md rounded-2xl ...">
        <div class="px-6 py-5">
            <div class="flex items-center gap-3 mb-4">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-rose-100 dark:bg-rose-500/15">
                    <x-menu.heroicon name="exclamation-triangle" class="h-5 w-5 text-rose-600 dark:text-rose-400" />
                </div>
                <h2 class="text-base font-bold text-slate-800 dark:text-gray-100">¿Eliminar paciente?</h2>
            </div>
            <p class="text-sm text-slate-600 dark:text-gray-400">
                Se eliminarán todos los turnos y estudios asociados. <strong>Esta acción no se puede deshacer.</strong>
            </p>
        </div>
        <div class="flex items-center justify-end gap-3 border-t border-slate-100 bg-slate-50/60 px-6 py-4 dark:border-gray-800 dark:bg-gray-900/40">
            <button class="...botón-secundario...">Cancelar</button>
            <button class="...botón-peligro...">Sí, eliminar paciente</button>
        </div>
    </div>
</div>
```

**Reglas:**
- **Nunca** `confirm()` o `alert()` nativos del browser
- Botón por defecto = cancelar (seguro)
- Botón destructivo: rojo, texto específico ("Sí, eliminar paciente"), nunca genérico ("OK")
- Explicar el impacto y si es irreversible

---

### ALERTAS / CALLOUTS

Usar `x-feedback.alerts` — componente Blade estático para callouts inline dentro de vistas.

```blade
{{-- Tipos: success | error | warning | info  —  Tamaños: sm | md | lg --}}
<x-feedback.alerts type="warning" message="Verifique los datos antes de continuar." />
<x-feedback.alerts type="success" message="Configuración guardada correctamente." size="sm" />
<x-feedback.alerts type="error"   message="No se pudo procesar la solicitud." />
<x-feedback.alerts type="info"    message="Esta acción afecta a todas las sucursales." size="lg" />
```

**Archivo:** `resources/views/components/feedback/alerts.blade.php`

> **No usar** HTML crudo de alerta — el componente aplica automáticamente los colores del enum `InformacionColors`.

---

### SISTEMA DE NOTIFICACIONES — Tres capas

El sistema tiene tres componentes distintos según el origen y la naturaleza del mensaje.

#### 1. `x-feedback.flash` — Flash de sesión (redirects del servidor)

Para mensajes que vienen de un redirect de PHP: middleware, controladores, acciones que redirigen.

```php
// En middleware, controlador o action:
return redirect()->route('empresa.datos')
    ->with('warning', 'Debe configurar la empresa antes de continuar.');

return redirect()->route('dashboard')
    ->with('success', 'Empresa creada correctamente.');
```

- Se coloca **una sola vez** en `layouts/app.blade.php` — ya está incluido.
- Lee `session('success')`, `session('error')`, `session('warning')`, `session('info')` automáticamente.
- Auto-dismiss en 6 segundos con barra de progreso. Dismiss manual con ×.
- Diseño: acento lateral de 3px + `font-headline font-bold` en el label + `font-body text-sm` en el mensaje.
- **Archivo:** `resources/views/components/feedback/flash.blade.php`

#### 2. `x-feedback.toast` — Toast en tiempo real (Livewire)

Para notificaciones disparadas desde componentes Livewire sin redirect.

```php
// Dentro de cualquier componente Livewire (via HasNotifications trait o directo):
$this->dispatch('notify', type: 'success', message: 'Sucursal guardada correctamente.');
$this->dispatch('notify', type: 'error',   message: 'No se pudo guardar. Intentá de nuevo.');
$this->dispatch('notify', type: 'warning', message: 'Algunos campos fueron corregidos.');
$this->dispatch('notify', type: 'info',    message: 'Los cambios se aplicarán al siguiente turno.');
```

- Se coloca **una sola vez** en `layouts/app.blade.php` — ya está incluido.
- Posición: `top-right` fixed. Apilable hasta 4 toasts simultáneos. Auto-dismiss 4.5s.
- **Archivo:** `resources/views/components/feedback/toast.blade.php`

#### Cuándo usar cada uno

| Origen | Componente | Ejemplo |
|---|---|---|
| Redirect de middleware / controlador | `x-feedback.flash` (automático) | Empresa no configurada, acceso bloqueado |
| Acción Livewire sin redirect | `x-feedback.toast` (dispatch) | Guardado exitoso, error de validación server |
| Callout estático en vista | `x-feedback.alerts` (prop) | Aviso permanente dentro de un formulario |

---

### InformacionColors — Enum de estilos semánticos

`App\Enums\Styles\InformacionColors` centraliza los estilos de todos los tipos de feedback.
Casos: `Warning`, `Info`, `Success`, `Error`.

| Método | Retorna | Uso |
|---|---|---|
| `label()` | `string` | Texto del tipo: "Advertencia", "Información"… |
| `icon()` | `string` | Nombre heroicon: `exclamation-triangle`… |
| `badgeClasses()` | `string` | Clases bg + text para badges pequeños |
| `alertWrapperClass()` | `string` | Border + bg para contenedor de alerta |
| `alertAccentClass()` | `string` | Color del acento lateral y barra de progreso |
| `alertLabelClass()` | `string` | Color del texto del label tipado |

> Nunca hardcodear colores de feedback — siempre usar los métodos del enum.

---

### BADGES / ETIQUETAS

```html
<!-- Neutral -->
<span class="inline-flex items-center rounded-lg bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-600 dark:bg-gray-800 dark:text-gray-400">Pendiente</span>

<!-- Primario -->
<span class="inline-flex items-center rounded-lg bg-indigo-100 px-2.5 py-0.5 text-xs font-semibold text-indigo-700 dark:bg-indigo-500/15 dark:text-indigo-300">Activo</span>

<!-- Éxito con dot -->
<span class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-100 px-2.5 py-0.5 text-xs font-semibold text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400">
    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 dark:bg-emerald-400"></span>
    Completado
</span>

<!-- Advertencia -->
<span class="inline-flex items-center rounded-lg bg-amber-100 px-2.5 py-0.5 text-xs font-semibold text-amber-700 dark:bg-amber-500/10 dark:text-amber-400">En revisión</span>

<!-- Peligro -->
<span class="inline-flex items-center rounded-lg bg-rose-100 px-2.5 py-0.5 text-xs font-semibold text-rose-700 dark:bg-rose-500/10 dark:text-rose-400">Cancelado</span>
```

---

## Patrones UX

### Formularios
- ✅ Label siempre visible — nunca solo placeholder
- ✅ Description debajo del label para contexto opcional
- ✅ Error debajo del campo, en rojo automático via `@error`
- ✅ Campos requeridos marcados con `*` — no solo con color
- ✅ Botón submit deshabilitado + texto cambiado mientras envía ("Guardar" → "Guardando…")
- ✅ `wire:dirty` para indicar cambios no guardados (borde `amber` sutil)
- ✅ `autocomplete` attributes correctos (`email`, `current-password`, `new-password`)
- ✅ `inputmode` correcto en mobile (`numeric`, `email`, `tel`)
- ❌ Nunca resetear un form por error de validación
- ❌ Nunca perder datos del usuario en navegación accidental

### Estados de carga
| Duración | Qué mostrar |
|---|---|
| < 100ms | Nada |
| 100–300ms | Spinner pequeño o cambio en el botón (`wire:loading`) |
| 300ms – 1s | Skeleton loader en el área afectada |
| > 1s | Skeleton + mensaje ("Buscando…") |
| > 5s | Barra de progreso real si es posible |

```html
<!-- Skeleton de fila de tabla -->
<tr wire:loading.delay>
    <td class="px-4 py-3"><div class="h-4 w-2/3 animate-pulse rounded bg-slate-200 dark:bg-gray-700"></div></td>
    <td class="px-4 py-3"><div class="h-4 w-1/2 animate-pulse rounded bg-slate-200 dark:bg-gray-700"></div></td>
</tr>
```

### Empty States

```html
<div class="flex flex-col items-center justify-center px-6 py-16 text-center">
    <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-indigo-100 text-indigo-400 dark:bg-indigo-500/15 dark:text-indigo-400 mb-4">
        <x-menu.heroicon name="users" class="h-8 w-8" />
    </div>
    <h3 class="text-base font-bold text-slate-800 dark:text-gray-100 mb-1">Aún no hay pacientes</h3>
    <p class="text-sm text-slate-500 dark:text-gray-400 max-w-xs mb-5">
        Registrá el primer paciente para comenzar a gestionar turnos y estudios.
    </p>
    <button class="...botón-primario...">Crear primer paciente</button>
</div>
```

Estructura obligatoria: **icono + título claro + subtítulo + CTA principal**.

### Feedback de acciones

| Tipo | Componente | Cuándo usarlo |
|---|---|---|
| **Inline** | `@error` de Laravel | Errores de validación de formulario |
| **Flash** | `x-feedback.flash` | Redirect de middleware o controlador — se lee automático de sesión |
| **Toast** | `x-feedback.toast` | Acciones Livewire exitosas / errores no críticos. Auto-dismiss 4.5s |
| **Callout** | `x-feedback.alerts` | Aviso estático permanente dentro de una vista |
| **Modal** | `x-advice-window` | Confirmaciones destructivas o flujos que bloquean la UI |

> Ver sección **SISTEMA DE NOTIFICACIONES** para uso detallado de cada componente.

### Búsqueda
- Debounce 300ms antes de disparar request (`wire:model.live.debounce.300ms`)
- Spinner pequeño en el input mientras busca
- Empty state propio: "No encontramos resultados para '{{ $search }}'"

### Microinteracciones
- `active:scale-[0.98]` en botones para feedback táctil
- `wire:dirty` → borde `amber-400` sutil en campos modificados sin guardar
- Transiciones siempre presentes (`transition-all duration-200`)
- Hover feedback en todo lo clickeable

---

## Accesibilidad

- ✅ Navegación por teclado completa — Tab recorre todo lo accionable en orden lógico
- ✅ `focus:ring-2` siempre presente — nunca eliminar sin reemplazar
- ✅ `aria-label` en todo botón que solo contiene un icono
- ✅ `aria-live="polite"` en regiones que cambian dinámicamente
- ✅ Contraste mínimo AA: 4.5:1 texto normal, 3:1 texto grande
- ✅ No depender solo del color para transmitir información
- ✅ Target táctil mínimo 44×44px en mobile

---

## Reglas Generales de Consistencia

### Border Radius
| Elemento | Clase |
|---|---|
| Inputs, botones, badges, alertas | `rounded-xl` |
| Cards, modales, contenedores grandes | `rounded-2xl` |
| Botones de ícono pequeños | `rounded-lg` |
| Dots / indicadores de estado | `rounded-full` |

### Sombras
| Elemento | Clase |
|---|---|
| Contenedor de página (`main-div`) | `shadow-md shadow-slate-200/80` · `dark:shadow-lg dark:shadow-black/30` |
| Cards y contenedores internos | `shadow-sm` |
| Modales y dropdowns | `shadow-xl shadow-slate-200/60` |
| Botones primarios/peligro/éxito | `shadow-sm shadow-{color}-200` (solo light) |
| CTAs con `.signature-gradient` | `shadow-[0px_8px_24px_rgba(79,70,229,0.20)]` |

### Espaciado en formularios
- Gap entre campos: `space-y-5` o `space-y-6`
- Padding interno de cards/secciones: `px-6 py-5`
- Padding de footer (modal/card): `px-6 py-4`

### Iconos

**Protocolo obligatorio antes de usar cualquier ícono:**

1. **Verificar en `resources/views/components/menu/heroicons-registry.php`** — confirmar que el nombre existe en el array.
2. **Si NO existe** — buscar en heroicons.com (outline, stroke-width 1.5) y **agregarlo** en la categoría correspondiente antes de usarlo.
3. **Nunca asumir** que un nombre existe — uno inexistente renderiza `question-mark-circle` silenciosamente.

**Cómo agregar** (en la categoría correcta del registry):
```php
'nombre-del-icono' => '<path stroke-linecap="round" stroke-linejoin="round" d="...SVG path..."/>',
```

**Advertencia — `:class` en Blade components:**
```blade
{{-- ❌ INCORRECTO — Blade evalúa `open` como constante PHP → error 500 --}}
<x-menu.heroicon name="chevron-down" :class="open ? 'rotate-180' : ''" />

{{-- ✅ CORRECTO — el :class en elemento HTML nativo que Alpine procesa --}}
<span :class="open ? 'rotate-180' : ''" class="inline-flex transition-all duration-300">
    <x-menu.heroicon name="chevron-down" class="h-3 w-3" />
</span>
```

**Íconos registrados relevantes:**
`envelope`, `lock-closed`, `eye`, `eye-slash`, `map-pin`, `phone`, `building-office`, `building-library`, `identification`, `chevron-up-down`, `information-circle`, `check-circle`, `x-circle`, `x-mark`, `shield-check`, `calendar`, `beaker`, `users`, `document-check`, `login`, `sun`, `moon`, `bars-3`, `pencil`, `trash`, `plus-circle`, `magnifying-glass`, `currency-dollar`, `globe-alt`, `clock`, `hashtag`

> `light-tem` y `dark-tem` son aliases legacy de `sun` y `moon` — usar los nombres primarios.

**Tamaños estándar:**
- `h-4 w-4` — inline, badges, items de dropdown
- `h-5 w-5` — inputs, botones, nav items
- `h-6 w-6` — header, acciones prominentes

Un solo set de iconos — nunca mezclar con SVGs inline ni otras librerías.

---

## Anti-patrones (NO hacer)

### Diseño
- ❌ Múltiples tonos del mismo color sin sistema
- ❌ Más de 5 estilos de botón distintos en la misma app
- ❌ Iconos de sets diferentes mezclados
- ❌ Márgenes hardcodeados en componentes reutilizables — usar `gap-*` en el contenedor
- ❌ Copiar/pegar clases Tailwind largas en 20 lugares → extraer a componente Blade
- ❌ `<input>`, `<select>` o `<textarea>` HTML crudo cuando existe el componente `x-form_inputs.*`

### Interacción
- ❌ `alert()` / `confirm()` / `prompt()` nativos del browser
- ❌ Tooltips con información crítica (no accesibles en mobile)
- ❌ Modales que no se pueden cerrar con `Esc`
- ❌ Inputs sin label visible
- ❌ Auto-submit de formularios al tipear
- ❌ Scroll horizontal accidental
- ❌ Modales anidados

### Código
- ❌ Componentes con 10+ props → descomponer
- ❌ Lógica de negocio en componentes visuales
- ❌ `<select>` sin el componente `x-form_inputs.select` — `@tailwindcss/forms` agrega su propia flecha que se superpone

### Responsive (anti-patrones)
- ❌ Anchuras fijas en píxeles (`w-[320px]`) — siempre usar utilidades Tailwind relativas
- ❌ `overflow-hidden` en contenedor padre sin revisar que no corte contenido en mobile
- ❌ `text-xs` en mobile para "meter más info" — sacrifica legibilidad, mejor reorganizar el layout
- ❌ Grillas de 3+ columnas sin breakpoint responsive
- ❌ Tablas sin scroll horizontal o alternativa card-list en mobile
- ❌ Botones de acción pequeños y juntos en mobile sin espacio táctil suficiente

### Layout / Scroll (anti-patrones)
- ❌ Elementos `absolute` dentro de un contenedor **sin** `relative` en el padre — se posicionan respecto al viewport y pueden generar scroll fantasma en `<main>`
- ❌ Orbes decorativos con posiciones negativas (`-right-N`, `-left-N`) en contenedores con `relative` — los empujan fuera del card causando scroll horizontal involuntario
- ❌ Doble `overflow-y-auto` apilado — solo `<main>` debe scrollear; los contenedores de formulario crecen naturalmente sin overflow propio

---

## Principios de Diseño Avanzado (frontend-design)

Estos principios del skill `frontend-design` aplican al contexto médico de Cronos:

### Tono estético de Cronos
- **Refined / Medical-Professional:** limpio pero no estéril, confiable sin ser aburrido
- Usar espacio negativo generoso — las interfaces médicas procesan mucha info, el respiro visual reduce el estrés cognitivo
- Asimetría controlada: romper grillas perfectas con un elemento hero o accent destaca sin romper la seriedad
- **Nunca:** gradientes de neón, animaciones excesivas, fondos oscuros saturados como principal

### Tipografía avanzada
- **Jerarquía en 3 niveles máximo por vista:** page title → section heading → body
- Letter-spacing en caps: `tracking-wider` en labels uppercase, `tracking-tight` en headings grandes
- Números en datos médicos: `tabular-nums` para alinear columnas de valores numéricos
- `leading-relaxed` en párrafos de descripción para mejorar lectura en pantallas pequeñas

```html
<!-- Número médico alineado en tabla -->
<td class="font-mono tabular-nums text-right text-slate-800 dark:text-gray-100">1,234</td>

<!-- Heading de página -->
<h1 class="font-headline text-2xl font-extrabold tracking-tight text-slate-900 dark:text-gray-50 lg:text-3xl">
    Gestión de Pacientes
</h1>
```

### Motion y microinteracciones

```html
<!-- Entrada de card con fade-up -->
<div class="animate-in fade-in slide-in-from-bottom-2 duration-300 fill-mode-both"
     style="animation-delay: 100ms">
    <!-- card -->
</div>

<!-- Skeleton con shimmer más suave -->
<div class="relative overflow-hidden rounded-xl bg-slate-100 dark:bg-gray-800">
    <div class="absolute inset-0 -translate-x-full animate-[shimmer_1.5s_infinite]
                bg-gradient-to-r from-transparent via-white/20 to-transparent"></div>
</div>

<!-- Hover lift en cards KPI -->
<div class="... transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
```

### Composición y atmósfera

```html
<!-- Fondo de página con gradiente sutil (más interesante que bg-slate-50 plano) -->
<main class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50/20
             dark:from-gray-950 dark:via-gray-950 dark:to-indigo-950/20">

<!-- Hero section / panel destacado con depth -->
<div class="relative overflow-hidden rounded-2xl bg-white dark:bg-gray-900
            border border-indigo-100 dark:border-indigo-900/40">
    <!-- accent decorativo de fondo -->
    <div class="pointer-events-none absolute -right-8 -top-8 h-32 w-32 rounded-full
                bg-indigo-100/40 blur-2xl dark:bg-indigo-500/10"></div>
    <div class="relative px-6 py-5"><!-- contenido --></div>
</div>

<!-- Separador con gradiente en lugar de border sólido -->
<div class="h-px bg-gradient-to-r from-transparent via-indigo-200 to-transparent
            dark:via-indigo-800"></div>
```

### Paleta extendida para contexto médico

Además de los colores base, usar semánticamente:

| Contexto | Light | Dark | Uso |
|---|---|---|---|
| Laboratorio/análisis | `violet-600` / `violet-100` | `violet-400` / `violet-500/15` | Estudios de lab, resultados |
| Urgencias/guardia | `rose-600` / `rose-100` | `rose-400` / `rose-500/15` | Prioridad alta, emergencias |
| Cirugía/procedimientos | `cyan-600` / `cyan-100` | `cyan-400` / `cyan-500/15` | Quirófano, procedimientos |
| Farmacia/medicamentos | `teal-600` / `teal-100` | `teal-400` / `teal-500/15` | Prescripciones, medicación |
| Administrativo/billing | `amber-600` / `amber-100` | `amber-400` / `amber-500/15` | Facturas, pagos, admin |

> **Regla:** estos colores van SOLO en badges e iconos de categoría — no en fondos grandes ni botones primarios.

---

## Integridad del Sistema de Diseño

Al finalizar cualquier componente nuevo, verificar:

1. **Consistencia cross-component:** ¿el componente se ve "del mismo sistema" que el sidebar, los formularios y las tablas?
2. **Pixel-perfect en ambos modos:** revisar en DevTools con `dark` class en `<html>`
3. **375px (iPhone SE) sin scroll horizontal:** el test mínimo de mobile
4. **1440px desktop:** confirmar que el espacio extra se usa bien, no queda todo apretado al centro
5. **Sin clases huérfanas:** toda clase Tailwind usada debe estar en `tailwind.config.js` safelist o ser una clase estándar

---
