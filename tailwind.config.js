/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Plus Jakarta Sans', 'sans-serif'],
                outfit: ['Outfit', 'sans-serif'],
            },
            colors: {
                primary: '#3B82F6',
                'primary-light': '#EFF6FF',
                secondary: '#64748B',
                background: '#F8FAFC',
                card: '#FFFFFF',
                customBlue: '#145eb3',
                lightBlue: '#4b96e6',
            },
            boxShadow: {
                'floating': '0 10px 40px -10px rgba(0,0,0,0.08)',
                'bottom-nav': '0 -4px 20px rgba(0,0,0,0.05)',
                'soft': '0 2px 10px rgba(0,0,0,0.02)',
            },
            animation: {
                'fade-in': 'fadeIn 0.3s ease-out',
                'slide-up': 'slideUp 0.4s cubic-bezier(0.16, 1, 0.3, 1)',
            },
            keyframes: {
                fadeIn: {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                slideUp: {
                    '0%': { transform: 'translateY(20px)', opacity: '0' },
                    '100%': { transform: 'translateY(0)', opacity: '1' },
                }
            }
        }
    },
    plugins: [],
}
