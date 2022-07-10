<script>

export default {
    props: {
        name: {type: String, required: true},
        form: {type: Object, required: true},
        defaultValue: {default: null},
        hideTitle: {type: Boolean, default: false},
    },

    emits: ['change'],

    computed: {
        title() {
            return this.get('titles', this.name);
        },
        value() {
            return this.get('values', null);
        },
        original() {
            return this.get('originals', this.defaultValue);
        },
        valid() {
            return !this.form.is_loaded ? true : Boolean(this.get('valid', true));
        },
        errors() {
            return this.get('errors', []);
        },
        required() {
            const rules = this.get('rules', null);
            if (rules === null || typeof rules !== "object") {
                return false;
            }
            const keys = Object.keys(rules);
            return keys.length === 0 ? false : keys.some(rule => rule === 'required');
        },
    },

    methods: {
        get(scope, defaults) {
            if (!this.form.is_loaded) {
                return null;
            }
            return typeof this.form[scope][this.name] !== "undefined" ? this.form[scope][this.name] : defaults;
        },
        change(value, name) {
            this.form.update(name, value);
            this.$emit('change', value, name);
        },
        focus() {
            if (typeof this.$refs.input.focus === "function") {
                this.$refs.input.focus();
            }
        },
    }
}
</script>
