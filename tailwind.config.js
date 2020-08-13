module.exports = {
    purge: {
        content: [
            './inc/*.php',
            './resources/**/*.js'
        ],
        whitelist: ['mode-dark']
    },
    theme: {
        extend: {
            colors: {
                primary: {
                    '50': '#eff4ff',
                    '100': '#e8eefb',
                    '400': '#5d87e6',
                    '500': '#1853db',
                    '600': '#164bc5',
                    '700': '#0e3283'
                }
            }
        }
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
