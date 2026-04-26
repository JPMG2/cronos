import './bootstrap';

import Collapse from '@alpinejs/collapse';
import { validate } from './validation.js';
import mask from '@alpinejs/mask';

Alpine.plugin(mask);

// Disponible en cualquier Alpine x-data como: validate(data, rules)
window.validate = validate;

document.addEventListener('alpine:init', () => {
    Alpine.plugin(Collapse);
});
