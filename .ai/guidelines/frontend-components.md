## Componentes de Formulario — Regla Absoluta

NUNCA escribir `<input>`, `<select>` ni `<textarea>` HTML crudo en vistas Blade.
SIEMPRE usar los componentes del sistema, sin excepciones:

- `<x-form-inputs.text_input>` — input con o sin icono (prop `icon`). Soporta `alpineError` para errores Alpine y `@error` para errores del servidor automáticamente.
- `<x-form-inputs.input>` — input sin icono.
- `<x-form-inputs.select>` — select con chevron custom.
- `<x-form-inputs.textarea>` — textarea.

Los archivos viven en `resources/views/components/form-inputs/` (con guión).

Esta regla existe porque ignorarla rompe la validación visual (el borde rojo no aparece en errores del servidor) y genera retrabajo que el usuario ya corrigió múltiples veces.
