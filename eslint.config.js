import skipFormatting from '@vue/eslint-config-prettier/skip-formatting'
import pluginVue from 'eslint-plugin-vue'
import globals from 'globals'
import js from '@eslint/js'

export default [
    {
        name: 'app/files-to-lint',
        files: ['**/*.{js,mjs,jsx,vue}'],
    },

    {
        name: 'app/files-to-ignore',
        ignores: [
            '**/dist/**',
            '**/dist-ssr/**',
            '**/coverage/**',
            '**/vendor/**',
            '**/build/**',
        ],
    },

    js.configs.recommended,
    ...pluginVue.configs['flat/recommended'],
    skipFormatting,

    {
        rules: {
            'vue/multi-word-component-names': 'off',
            'no-console': 'error',
            'no-debugger': 'error',
            'no-undef': 'error',
            'no-unused-vars': 'error',
        },
        languageOptions: {
            sourceType: 'module',
            globals: {
                ...globals.browser
            }
        }
    }
]
