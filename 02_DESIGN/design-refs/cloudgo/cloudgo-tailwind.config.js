/** @type {import('tailwindcss').Config} */
export default {
  theme: {
    extend: {
    colors: {
        primary: {
            '50': 'hsl(213, 79%, 97%)',
            '100': 'hsl(213, 79%, 94%)',
            '200': 'hsl(213, 79%, 86%)',
            '300': 'hsl(213, 79%, 76%)',
            '400': 'hsl(213, 79%, 64%)',
            '500': 'hsl(213, 79%, 50%)',
            '600': 'hsl(213, 79%, 40%)',
            '700': 'hsl(213, 79%, 32%)',
            '800': 'hsl(213, 79%, 24%)',
            '900': 'hsl(213, 79%, 16%)',
            '950': 'hsl(213, 79%, 10%)',
            DEFAULT: '#1b74e4'
        },
        secondary: {
            '50': 'hsl(51, 100%, 97%)',
            '100': 'hsl(51, 100%, 94%)',
            '200': 'hsl(51, 100%, 86%)',
            '300': 'hsl(51, 100%, 76%)',
            '400': 'hsl(51, 100%, 64%)',
            '500': 'hsl(51, 100%, 50%)',
            '600': 'hsl(51, 100%, 40%)',
            '700': 'hsl(51, 100%, 32%)',
            '800': 'hsl(51, 100%, 24%)',
            '900': 'hsl(51, 100%, 16%)',
            '950': 'hsl(51, 100%, 10%)',
            DEFAULT: '#ffd700'
        },
        accent: {
            '50': 'hsl(353, 72%, 97%)',
            '100': 'hsl(353, 72%, 94%)',
            '200': 'hsl(353, 72%, 86%)',
            '300': 'hsl(353, 72%, 76%)',
            '400': 'hsl(353, 72%, 64%)',
            '500': 'hsl(353, 72%, 50%)',
            '600': 'hsl(353, 72%, 40%)',
            '700': 'hsl(353, 72%, 32%)',
            '800': 'hsl(353, 72%, 24%)',
            '900': 'hsl(353, 72%, 16%)',
            '950': 'hsl(353, 72%, 10%)',
            DEFAULT: '#cb2134'
        },
        'neutral-50': '#ffffff',
        'neutral-100': '#d9d9d9',
        'neutral-200': '#6b6e71',
        'neutral-300': '#000000',
        'neutral-400': '#4a4a4a',
        'neutral-500': '#777777',
        'neutral-600': '#222222',
        'neutral-700': '#e3e6eb',
        'neutral-800': '#545b64',
        'neutral-900': '#b9b9b9',
        background: '#ffffff',
        foreground: '#273444'
    },
    fontFamily: {
        sans: [
            'Roboto',
            'sans-serif'
        ],
        heading: [
            'Inter',
            'sans-serif'
        ]
    },
    fontSize: {
        '20': [
            '20px',
            {
                lineHeight: '32px'
            }
        ],
        '21': [
            '21px',
            {
                lineHeight: '35.7px'
            }
        ],
        '22': [
            '22px',
            {
                lineHeight: '35.2px'
            }
        ],
        '23': [
            '23px',
            {
                lineHeight: '26.45px'
            }
        ],
        '24': [
            '24px',
            {
                lineHeight: '28.8px'
            }
        ],
        '26': [
            '26px',
            {
                lineHeight: '36.4px',
                letterSpacing: '0.5px'
            }
        ],
        '28': [
            '28px',
            {
                lineHeight: '44.8px'
            }
        ],
        '30': [
            '30px',
            {
                lineHeight: '30px'
            }
        ],
        '32': [
            '32px',
            {
                lineHeight: '41.6px'
            }
        ],
        '34': [
            '34px',
            {
                lineHeight: 'normal'
            }
        ],
        '35': [
            '35px',
            {
                lineHeight: '35px'
            }
        ],
        '36': [
            '36px',
            {
                lineHeight: '44px'
            }
        ],
        '38': [
            '38px',
            {
                lineHeight: '49.4px'
            }
        ],
        '42': [
            '42px',
            {
                lineHeight: '54.6px'
            }
        ],
        '28.8': [
            '28.8px',
            {
                lineHeight: '46.08px'
            }
        ]
    },
    spacing: {
        '19': '38px',
        '24': '48px',
        '30': '60px',
        '35': '70px',
        '40': '80px',
        '43': '86px',
        '45': '90px',
        '50': '100px',
        '75': '150px',
        '107': '214px',
        '1px': '1px',
        '43px': '43px',
        '65px': '65px',
        '73px': '73px',
        '83px': '83px'
    },
    borderRadius: {
        sm: '3px',
        md: '10px',
        lg: '14px',
        xl: '24px',
        full: '100px'
    },
    boxShadow: {
        xs: 'rgba(102, 98, 98, 0.4) 0px 1px 4px 0px',
        sm: 'rgb(82, 159, 255) 0px 0px 8px 0px',
        md: 'rgba(27, 116, 228, 0.15) 0px 0px 16px 0px',
        lg: 'rgba(0, 0, 0, 0.3) 0px 4px 20px 0px',
        xl: 'rgba(0, 0, 0, 0.2) 0px 10px 30px 0px',
        '2xl': 'rgba(0, 0, 0, 0.35) 0px 24px 70px 0px'
    },
    screens: {
        sm: '641px',
        md: '769px'
    },
    transitionDuration: {
        '80': '0.08s',
        '100': '0.1s',
        '150': '0.15s',
        '180': '0.18s',
        '200': '0.2s',
        '220': '0.22s',
        '300': '0.3s',
        '400': '0.4s',
        '500': '0.5s',
        '5000': '5s'
    },
    transitionTimingFunction: {
        default: 'ease',
        linear: 'linear'
    },
    container: {
        center: true,
        padding: '15px'
    },
    maxWidth: {
        container: '1320px'
    }
},
  },
};
