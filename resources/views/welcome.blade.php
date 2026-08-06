<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pairride - The Ultimate Ride-Hailing Experience</title>
    <!-- Tailwind CSS (via CDN for immediate stunning rendering without build steps) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    
    <script>
        tailwind.config = {
            darkMode: 'media',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            light: '#728c40',
                            DEFAULT: '#556B2F', // Olive Green
                            dark: '#3b4a20',
                        }
                    },
                    animation: {
                        'float': 'float 6s ease-in-out infinite',
                        'fade-in-up': 'fadeInUp 1s ease-out',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-20px)' },
                        },
                        fadeInUp: {
                            '0%': { opacity: '0', transform: 'translateY(20px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        }
                    }
                }
            }
        }
    </script>
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .glass-panel {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        @media (prefers-color-scheme: dark) {
            .glass-panel {
                background: rgba(30, 41, 59, 0.85);
                border: 1px solid rgba(255, 255, 255, 0.1);
            }
        }
    </style>
</head>
<body class="antialiased bg-gray-50 text-gray-900 dark:bg-gray-900 dark:text-gray-100 min-h-screen flex flex-col selection:bg-brand selection:text-white">

    <!-- Navbar -->
    <nav class="fixed w-full z-50 glass-panel shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex items-center space-x-2">
                    <!-- Brand Icon -->
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-brand-light to-brand-dark flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.242-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <span class="text-2xl font-extrabold tracking-tight bg-clip-text text-transparent bg-gradient-to-r from-brand to-brand-light">Pairride</span>
                </div>
                <div class="hidden md:flex items-center space-x-6">
                    <a href="#features" class="font-medium hover:text-brand transition-colors">Features</a>
                    <a href="#download" class="font-medium hover:text-brand transition-colors">Download Apps</a>
                    <a href="/admin" class="px-5 py-2.5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-full font-semibold hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300">Admin Portal</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <main class="flex-grow pt-32 pb-16 flex items-center relative overflow-hidden">
        <!-- Background Elements -->
        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[50%] bg-brand/20 rounded-full blur-[100px] animate-pulse"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[50%] bg-brand-light/20 rounded-full blur-[100px] animate-pulse" style="animation-delay: 2s;"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 w-full flex flex-col lg:flex-row items-center gap-12">
            <!-- Text Content -->
            <div class="lg:w-1/2 text-center lg:text-left animate-fade-in-up">
                <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-brand/10 text-brand font-semibold text-sm mb-6 border border-brand/20">
                    <span class="w-2 h-2 rounded-full bg-brand animate-ping"></span>
                    <span>System Live & Real-Time</span>
                </div>
                
                <h1 class="text-5xl lg:text-7xl font-extrabold tracking-tight mb-6 leading-tight">
                    Ride smarter with <br/>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand to-brand-light">Pairride.</span>
                </h1>
                
                <p class="text-lg lg:text-xl text-gray-600 dark:text-gray-400 mb-8 max-w-2xl mx-auto lg:mx-0">
                    Experience the next generation of ride-hailing. Powered by real-time WebSocket tracking, secure payments via Paystack and Flutterwave, and an intuitive Olive Green aesthetic.
                </p>
                
                <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start space-y-4 sm:space-y-0 sm:space-x-4">
                    <a href="#download" class="w-full sm:w-auto px-8 py-4 bg-brand hover:bg-brand-dark text-white rounded-2xl font-bold text-lg shadow-xl shadow-brand/30 hover:-translate-y-1 transition-all duration-300 flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                        Get the App
                    </a>
                    <a href="/admin" class="w-full sm:w-auto px-8 py-4 bg-white dark:bg-gray-800 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-700 hover:border-brand dark:hover:border-brand rounded-2xl font-bold text-lg hover:-translate-y-1 transition-all duration-300 flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        View Dashboard
                    </a>
                </div>
            </div>

            <!-- UI Mockup Illustration -->
            <div class="lg:w-1/2 relative flex justify-center animate-float">
                <div class="w-[300px] h-[600px] bg-white dark:bg-gray-900 rounded-[3rem] border-[8px] border-gray-900 dark:border-gray-800 shadow-2xl relative overflow-hidden flex flex-col">
                    <!-- Phone Notch -->
                    <div class="absolute top-0 inset-x-0 h-6 bg-gray-900 dark:bg-gray-800 rounded-b-3xl mx-16 z-20"></div>
                    
                    <!-- App UI Simulation -->
                    <div class="flex-grow bg-gray-100 dark:bg-gray-800 relative">
                        <!-- Simulated Map -->
                        <div class="absolute inset-0 bg-cover bg-center opacity-40" style="background-image: url('https://maps.googleapis.com/maps/api/staticmap?center=6.5244,3.3792&zoom=14&size=400x800&maptype=roadmap&key=YOUR_API_KEY')"></div>
                        <div class="absolute inset-0 bg-gradient-to-b from-transparent to-white dark:to-gray-900"></div>
                        
                        <!-- Map Pin -->
                        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 z-10">
                            <div class="w-12 h-12 rounded-full bg-brand/20 flex items-center justify-center animate-ping absolute inset-0"></div>
                            <svg class="w-10 h-10 text-brand relative z-10" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                        </div>
                    </div>
                    
                    <!-- Bottom Sheet -->
                    <div class="h-[250px] bg-white dark:bg-gray-900 rounded-t-3xl shadow-[0_-10px_40px_rgba(0,0,0,0.1)] z-10 p-6 flex flex-col">
                        <div class="w-12 h-1.5 bg-gray-300 dark:bg-gray-700 rounded-full mx-auto mb-6"></div>
                        <h3 class="font-bold text-xl mb-4">Confirm Pickup</h3>
                        <div class="flex items-center mb-3">
                            <div class="w-3 h-3 rounded-full bg-brand mr-3"></div>
                            <div class="flex-grow h-4 bg-gray-200 dark:bg-gray-800 rounded"></div>
                        </div>
                        <div class="flex items-center mb-6">
                            <div class="w-3 h-3 rounded-full bg-red-500 mr-3"></div>
                            <div class="flex-grow h-4 bg-gray-200 dark:bg-gray-800 rounded"></div>
                        </div>
                        <div class="mt-auto w-full py-4 bg-brand rounded-xl flex items-center justify-center">
                            <div class="w-32 h-4 bg-white/50 rounded"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <footer class="glass-panel border-t border-gray-200 dark:border-gray-800 py-8 text-center mt-auto">
        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
            &copy; {{ date('Y') }} Pairride Global. Powered by Laravel 11, Flutter & Filament.
        </p>
    </footer>

</body>
</html>
