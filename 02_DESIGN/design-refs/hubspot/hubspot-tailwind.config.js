/** @type {import('tailwindcss').Config} */
export default {
  theme: {
    extend: {
    colors: {
        primary: {
            '50': 'hsl(60, 100%, 97%)',
            '100': 'hsl(60, 100%, 94%)',
            '200': 'hsl(60, 100%, 86%)',
            '300': 'hsl(60, 100%, 76%)',
            '400': 'hsl(60, 100%, 64%)',
            '500': 'hsl(60, 100%, 50%)',
            '600': 'hsl(60, 100%, 40%)',
            '700': 'hsl(60, 100%, 32%)',
            '800': 'hsl(60, 100%, 24%)',
            '900': 'hsl(60, 100%, 16%)',
            '950': 'hsl(60, 100%, 10%)',
            DEFAULT: '#ffff00'
        },
        secondary: {
            '50': 'hsl(348, 100%, 97%)',
            '100': 'hsl(348, 100%, 94%)',
            '200': 'hsl(348, 100%, 86%)',
            '300': 'hsl(348, 100%, 76%)',
            '400': 'hsl(348, 100%, 64%)',
            '500': 'hsl(348, 100%, 50%)',
            '600': 'hsl(348, 100%, 40%)',
            '700': 'hsl(348, 100%, 32%)',
            '800': 'hsl(348, 100%, 24%)',
            '900': 'hsl(348, 100%, 16%)',
            '950': 'hsl(348, 100%, 10%)',
            DEFAULT: '#d9002b'
        },
        accent: {
            '50': 'hsl(181, 100%, 97%)',
            '100': 'hsl(181, 100%, 94%)',
            '200': 'hsl(181, 100%, 86%)',
            '300': 'hsl(181, 100%, 76%)',
            '400': 'hsl(181, 100%, 64%)',
            '500': 'hsl(181, 100%, 50%)',
            '600': 'hsl(181, 100%, 40%)',
            '700': 'hsl(181, 100%, 32%)',
            '800': 'hsl(181, 100%, 24%)',
            '900': 'hsl(181, 100%, 16%)',
            '950': 'hsl(181, 100%, 10%)',
            DEFAULT: '#006162'
        },
        'neutral-50': '#333333',
        'neutral-100': '#000000',
        'neutral-200': '#8a8a8a',
        'neutral-300': '#ffffff',
        'neutral-400': '#fcece9',
        'neutral-500': '#f5f5f5',
        background: '#ffffff',
        foreground: '#000000'
    },
    fontFamily: {
        sans: [
            'Lexend Deca',
            'sans-serif'
        ],
        heading: [
            'HubSpot Sans',
            'sans-serif'
        ]
    },
    fontSize: {
        '14': [
            '14px',
            {
                lineHeight: '24px'
            }
        ],
        '16': [
            '16px',
            {
                lineHeight: 'normal'
            }
        ],
        '22': [
            '22px',
            {
                lineHeight: '27px'
            }
        ]
    },
    spacing: {
        '0': '2px',
        '1': '32px'
    },
    borderRadius: {
        md: '6px',
        lg: '16px'
    },
    boxShadow: {
        none: 'rgb(255, 255, 255) 0px 0px 0px 2px inset'
    },
    screens: {
        '545px': '545px',
        sm: '576px',
        md: '830px',
        lg: '992px',
        '1200px': '1200px'
    },
    transitionDuration: {
        '150': '0.15s'
    },
    transitionTimingFunction: {
        default: 'ease'
    },
    container: {
        center: true,
        padding: '0px'
    },
    maxWidth: {
        container: '100%'
    }
},
  },
};
