import js from '@eslint/js';
import pluginVue from 'eslint-plugin-vue';
import globals from 'globals';

export default [
    js.configs.recommended,
    ...pluginVue.configs['flat/recommended'],
    {
        files: ['resources/js/**/*.{js,vue}'],
        languageOptions: {
            globals: {
                ...globals.browser,
            },
        },
        rules: {
            'no-debugger': 'error',
            'no-console': 'warn',
            'no-unused-vars': ['warn', { argsIgnorePattern: '^_' }],
            'vue/multi-word-component-names': 'off',
            'vue/require-default-prop': 'off',
            'vue/no-v-html': 'off',
            'vue/max-attributes-per-line': 'off',
            'vue/singleline-html-element-content-newline': 'off',
            'vue/html-self-closing': ['warn', {
                html: { void: 'always', normal: 'never', component: 'always' },
            }],
        },
    },
    {
        ignores: ['vendor/**', 'node_modules/**', 'public/**', 'storage/**', 'bootstrap/**'],
    },
];
