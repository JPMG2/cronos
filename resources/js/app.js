import './bootstrap';

import Collapse from '@alpinejs/collapse';
import { validate } from './validation.js';

// Disponible en cualquier Alpine x-data como: validate(data, rules)
window.validate = validate;

document.addEventListener('alpine:init', () => {
    Alpine.plugin(Collapse);
});
