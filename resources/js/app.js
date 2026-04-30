import './bootstrap';

import Collapse from '@alpinejs/collapse';
import { validate } from './validation.js';
import mask from '@alpinejs/mask';

window.validate = validate;

document.addEventListener('alpine:init', () => {
    Alpine.plugin(Collapse);
    Alpine.plugin(mask);
});
