module.exports = {
    purge: {
        content: [
            './inc/*.php',
            './resources/**/*.js'
        ],
        whitelist: ['mode-dark']
    },
    theme: {
        extend: {},
    },
    variants: {
        backgroundColor: ['dark', 'dark-hover', 'dark-group-hover', 'dark-even', 'dark-odd'],
        borderColor: ['dark', 'dark-disabled', 'dark-focus', 'dark-focus-within'],
        textColor: ['dark', 'dark-hover', 'dark-active', 'dark-placeholder']
    },
    plugins: [
        require('tailwindcss-dark-mode')()
    ],
    important: true,
}
