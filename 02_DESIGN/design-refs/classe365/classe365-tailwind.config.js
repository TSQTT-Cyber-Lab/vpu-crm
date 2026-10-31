/** @type {import('tailwindcss').Config} */
export default {
  theme: {
    extend: {
    colors: {
        primary: {
            '50': 'hsl(220, 75%, 97%)',
            '100': 'hsl(220, 75%, 94%)',
            '200': 'hsl(220, 75%, 86%)',
            '300': 'hsl(220, 75%, 76%)',
            '400': 'hsl(220, 75%, 64%)',
            '500': 'hsl(220, 75%, 50%)',
            '600': 'hsl(220, 75%, 40%)',
            '700': 'hsl(220, 75%, 32%)',
            '800': 'hsl(220, 75%, 24%)',
            '900': 'hsl(220, 75%, 16%)',
            '950': 'hsl(220, 75%, 10%)',
            DEFAULT: '#10306f'
        },
        secondary: {
            '50': 'hsl(235, 71%, 97%)',
            '100': 'hsl(235, 71%, 94%)',
            '200': 'hsl(235, 71%, 86%)',
            '300': 'hsl(235, 71%, 76%)',
            '400': 'hsl(235, 71%, 64%)',
            '500': 'hsl(235, 71%, 50%)',
            '600': 'hsl(235, 71%, 40%)',
            '700': 'hsl(235, 71%, 32%)',
            '800': 'hsl(235, 71%, 24%)',
            '900': 'hsl(235, 71%, 16%)',
            '950': 'hsl(235, 71%, 10%)',
            DEFAULT: '#6772e5'
        },
        accent: {
            '50': 'hsl(7, 55%, 97%)',
            '100': 'hsl(7, 55%, 94%)',
            '200': 'hsl(7, 55%, 86%)',
            '300': 'hsl(7, 55%, 76%)',
            '400': 'hsl(7, 55%, 64%)',
            '500': 'hsl(7, 55%, 50%)',
            '600': 'hsl(7, 55%, 40%)',
            '700': 'hsl(7, 55%, 32%)',
            '800': 'hsl(7, 55%, 24%)',
            '900': 'hsl(7, 55%, 16%)',
            '950': 'hsl(7, 55%, 10%)',
            DEFAULT: '#d27164'
        },
        'neutral-50': '#000000',
        'neutral-100': '#ffffff',
        'neutral-200': '#555555',
        'neutral-300': '#101010',
        'neutral-400': '#7a95b0',
        'neutral-500': '#f2f9ff',
        'neutral-600': '#8b97a7',
        'neutral-700': '#808080',
        'neutral-800': '#6f767e',
        'neutral-900': '#ffe5f4',
        background: '#ffffff',
        foreground: '#6f767e'
    },
    fontFamily: {
        sans: [
            'Inter',
            'sans-serif'
        ],
        heading: [
            'Poppins',
            'sans-serif'
        ],
        font2: [
            'Inter-Regular_43',
            'sans-serif'
        ],
        font3: [
            'Inter-Medium_3y',
            'sans-serif'
        ],
        font4: [
            'Inter-SemiBold_48',
            'sans-serif'
        ],
        font5: [
            'Inter-Bold_3x',
            'sans-serif'
        ],
        font6: [
            'Inter-ExtraBold_3z',
            'sans-serif'
        ],
        font7: [
            'Inter-Bold_5m',
            'sans-serif'
        ]
    },
    fontSize: {
        '24': [
            '24px',
            {
                lineHeight: 'normal'
            }
        ],
        '26': [
            '26px',
            {
                lineHeight: 'normal'
            }
        ],
        '28': [
            '28px',
            {
                lineHeight: '33.6px'
            }
        ],
        '29': [
            '29px',
            {
                lineHeight: '43.5px'
            }
        ],
        '30': [
            '30px',
            {
                lineHeight: 'normal'
            }
        ],
        '32': [
            '32px',
            {
                lineHeight: 'normal'
            }
        ],
        '34': [
            '34px',
            {
                lineHeight: '30px'
            }
        ],
        '36': [
            '36px',
            {
                lineHeight: '36px'
            }
        ],
        '40': [
            '40px',
            {
                lineHeight: '47px'
            }
        ],
        '42': [
            '42px',
            {
                lineHeight: '51px'
            }
        ],
        '44': [
            '44px',
            {
                lineHeight: 'normal',
                letterSpacing: '-1px'
            }
        ],
        '45': [
            '45px',
            {
                lineHeight: '67.5px'
            }
        ],
        '48': [
            '48px',
            {
                lineHeight: '59px'
            }
        ],
        '50': [
            '50px',
            {
                lineHeight: '60px',
                letterSpacing: '0.5px'
            }
        ],
        '56': [
            '56px',
            {
                lineHeight: 'normal'
            }
        ]
    },
    spacing: {
        '0': '1px',
        '1': '3px',
        '2': '8px',
        '3': '14px',
        '4': '18px',
        '5': '23px',
        '6': '27px',
        '7': '35px',
        '8': '40px',
        '9': '44px',
        '10': '48px',
        '11': '53px',
        '12': '59px',
        '13': '64px',
        '14': '70px',
        '15': '80px',
        '16': '86px',
        '17': '100px',
        '18': '107px',
        '19': '109px',
        '20': '150px',
        '21': '256px',
        '22': '375px',
        '23': '448px',
        '24': '455px'
    },
    borderRadius: {
        sm: '4px',
        md: '8px',
        lg: '12px',
        xl: '24px',
        full: '9999px'
    },
    boxShadow: {
        xs: 'rgba(108, 122, 224, 0.133) 0px 2px 8px 0px',
        sm: 'rgba(26, 26, 26, 0.133) 0px 2px 16px 0px',
        md: 'rgba(84, 84, 84, 0.25) 0px 4px 20px 0px',
        lg: 'rgba(194, 194, 194, 0.01) 0px 65px 26px 0px, rgba(194, 194, 194, 0.05) 0px 36px 22px 0px, rgba(194, 194, 194, 0.09) 0px 16px 16px 0px, rgba(194, 194, 194, 0.1) 0px 4px 9px 0px'
    },
    screens: {
        sm: '481px',
        md: '769px'
    },
    transitionDuration: {
        '200': '0.2s',
        '300': '0.3s',
        '600': '0.6s',
        '1000': '1s'
    },
    transitionTimingFunction: {
        default: 'ease',
        custom: 'cubic-bezier(0.25, 0.46, 0.45, 0.94)'
    },
    container: {
        center: true,
        padding: '0px'
    },
    maxWidth: {
        container: '1376px'
    }
},
  },
};
