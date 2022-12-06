module.exports = {
  root: true,
  env: {
    node: true
  },
  'extends': [
    'plugin:vue/essential',
    'eslint:recommended'
  ],
  overrides: [
    {
      files: ['src/components/**/*.vue'],
      rules: {
        'vue/multi-word-component-names': 0,
      },
    },
  ],
  parserOptions: {
    parser: '@babel/eslint-parser'
  },
  rules: {
    'no-console': process.env.NODE_ENV === 'production' ? 'warn' : 'off',
    'vue/no-unused-vars': "off",
    'no-unused-vars': 'warn',
    'no-debugger': process.env.NODE_ENV === 'production' ? 'warn' : 'off'
  }
}
