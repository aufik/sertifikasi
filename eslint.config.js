import js from '@eslint/js';
import tseslint from '@typescript-eslint/eslint-plugin';
import tsParser from '@typescript-eslint/parser';
import reactHooks from 'eslint-plugin-react-hooks';
import prettier from 'eslint-config-prettier';

export default [
  js.configs.recommended,
  {
    files: ['resources/js/**/*.{ts,tsx}'],
    languageOptions: { parser: tsParser, parserOptions: { ecmaFeatures: { jsx: true }, sourceType: 'module' } },
    plugins: { '@typescript-eslint': tseslint, 'react-hooks': reactHooks },
    rules: { ...tseslint.configs.recommended.rules, ...reactHooks.configs.recommended.rules }
  },
  prettier
];
