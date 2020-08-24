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
                },
                success: {
                    '100': '#def7ec',
                    '200': '#bcf0da',
                    '400': '#31c48d',
                    '500': '#0e9f6e',
                },
                warning: {
                    '100': '#fdf6b2',
                    '200': '#fce96a',
                    '400': '#e3a008',
                    '500': '#c27803',
                },
                danger: {
                    '200': '#fbd5d5',
                    '400': '#f98080',
                    '500': '#f05252',
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
