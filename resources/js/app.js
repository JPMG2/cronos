import './bootstrap';

import Collapse from '@alpinejs/collapse';

document.addEventListener('alpine:init', () => {
    Alpine.plugin(Collapse);
});
