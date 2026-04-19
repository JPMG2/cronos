/**
 * Motor de validación frontend — Cronos Medical
 *
 * Uso en Alpine x-data:
 *
 *   errors: {},
 *   submit() {
 *       this.errors = validate(
 *           { country_id: this.$wire.country_id, name: this.$wire.name },
 *           { country_id: ['required'], name: ['required', ['minLength', 3]] }
 *       );
 *       if (Object.keys(this.errors).length === 0) this.$wire.save();
 *   }
 *
 * Reglas disponibles:
 *   'required'           — no vacío / no null
 *   'email'              — formato correo válido
 *   'numeric'            — solo números
 *   ['minLength', n]     — mínimo n caracteres
 *   ['maxLength', n]     — máximo n caracteres
 *   ['min', n]           — valor numérico mínimo
 *   ['max', n]           — valor numérico máximo
 */

const messages = {
    required:  'Este campo es requerido.',
    email:     'Ingresa un correo electrónico válido.',
    numeric:   'Solo se permiten valores numéricos.',
    minLength: (n) => `Mínimo ${n} caracteres.`,
    maxLength: (n) => `Máximo ${n} caracteres.`,
    min:       (n) => `El valor mínimo es ${n}.`,
    max:       (n) => `El valor máximo es ${n}.`,
};

const validators = {
    required(value) {
        if (value === null || value === undefined) return false;
        return String(value).trim() !== '';
    },
    email(value) {
        if (!value) return true; // Solo valida si hay valor; combinar con required si es obligatorio
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(String(value));
    },
    numeric(value) {
        if (!value && value !== 0) return true;
        return /^\d+(\.\d+)?$/.test(String(value));
    },
    minLength(min) {
        return (value) => String(value ?? '').trim().length >= min;
    },
    maxLength(max) {
        return (value) => String(value ?? '').trim().length <= max;
    },
    min(min) {
        return (value) => Number(value) >= min;
    },
    max(max) {
        return (value) => Number(value) <= max;
    },
};

/**
 * @param {Record<string, any>} data    — objeto con los valores del formulario
 * @param {Record<string, any[]>} schema — reglas por campo
 * @returns {Record<string, string>}    — errores por campo (vacío = sin errores)
 */
export function validate(data, schema) {
    const errors = {};

    for (const [field, rules] of Object.entries(schema)) {
        for (const rule of rules) {
            const [ruleName, ...args] = Array.isArray(rule) ? rule : [rule];

            const validatorFn = validators[ruleName];
            if (!validatorFn) continue;

            const testFn = args.length > 0 ? validatorFn(...args) : validatorFn;
            const value  = data[field];

            if (!testFn(value)) {
                const msg = messages[ruleName];
                errors[field] = typeof msg === 'function' ? msg(...args) : msg;
                break; // Primer error por campo
            }
        }
    }

    return errors;
}
