import './bootstrap';

import Collapse from '@alpinejs/collapse';
import { validate } from './validation.js';
import mask from '@alpinejs/mask';

window.validate = validate;

document.addEventListener('alpine:init', () => {
    Alpine.plugin(Collapse);
    Alpine.plugin(mask);

    Alpine.data('branchManager', () => ({
        mode: 'create',
        editingCode: '',
        search: '',
        panelOpen: true,
        errors: {},

        newBranch() {
            this.mode = 'create';
            this.editingCode = '';
            this.$wire.newBranch();
        },

        selectBranch(id, code) {
            this.mode = 'edit';
            this.editingCode = code;
            this.$wire.selectBranch(id);
        },

        submit() {
            this.errors = validate(
                {
                    name: this.$wire.form.name,
                    phone: this.$wire.form.phone,
                    email: this.$wire.form.email,
                    regionId: this.$wire.form.regionId,
                    address: this.$wire.form.address,
                    postalCode: this.$wire.form.postalCode,
                    currentStatusId: this.$wire.form.currentStatusId,
                },
                {
                    name: ['required', ['minLength', 3]],
                    phone: ['required', ['minLength', 10]],
                    email: ['required', ['email']],
                    regionId: ['required'],
                    address: ['required', ['minLength', 6]],
                    postalCode: ['required', ['minLength', 3]],
                    currentStatusId: ['required'],
                }
            );
            if (Object.keys(this.errors).length === 0) this.$wire.saveBranch();
        },
    }));
});
