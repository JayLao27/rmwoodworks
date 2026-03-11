<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,600;1,600&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            #pageLoader .loader-container {
                text-align: center;
                position: relative;
            }

            #pageLoader .logo-wrapper {
                position: relative;
                width: 260px;
                height: 260px;
                margin: 0 auto 28px;
            }

            #pageLoader .logo {
                width: 100%;
                height: 100%;
                object-fit: contain;
                filter: brightness(0) invert(1);
                animation: logoFloat 3s ease-in-out infinite;
            }

            @keyframes logoFloat {
                0%, 100% { transform: translateY(0px); }
                50% { transform: translateY(-12px); }
            }

            #pageLoader .circle-glow {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                width: 240px;
                height: 240px;
                border-radius: 50%;
                border: 3px solid rgba(255, 255, 255, 0.15);
                animation: pulse 2s ease-in-out infinite;
            }

            @keyframes pulse {
                0%, 100% {
                    transform: translate(-50%, -50%) scale(1);
                    opacity: 0.3;
                }
                50% {
                    transform: translate(-50%, -50%) scale(1.08);
                    opacity: 0.6;
                }
            }

            #pageLoader .rotating-gear {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                width: 300px;
                height: 300px;
                border: 2px dashed rgba(255, 255, 255, 0.2);
                border-radius: 50%;
                animation: rotate 8s linear infinite;
            }

            @keyframes rotate {
                from { transform: translate(-50%, -50%) rotate(0deg); }
                to { transform: translate(-50%, -50%) rotate(360deg); }
            }

            #pageLoader .rotating-gear::before {
                content: '';
                position: absolute;
                top: -4px;
                left: 50%;
                width: 8px;
                height: 8px;
                background: #ffffff;
                border-radius: 50%;
                transform: translateX(-50%);
            }

            #pageLoader .loading-text {
                color: #ffffff;
                font-size: 20px;
                font-weight: 300;
                letter-spacing: 4px;
                text-transform: uppercase;
                margin-bottom: 16px;
                animation: textPulse 2s ease-in-out infinite;
            }

            @keyframes textPulse {
                0%, 100% { opacity: 0.5; }
                50% { opacity: 1; }
            }

            #pageLoader .progress-bar {
                width: 260px;
                height: 3px;
                background: rgba(255, 255, 255, 0.1);
                border-radius: 10px;
                margin: 0 auto;
                overflow: hidden;
                position: relative;
            }

            #pageLoader .progress-fill {
                height: 100%;
                background: linear-gradient(90deg,
                    rgba(255, 255, 255, 0.3) 0%,
                    #ffffff 50%,
                    rgba(255, 255, 255, 0.3) 100%);
                border-radius: 10px;
                animation: progressSlide 2s ease-in-out infinite;
                width: 50%;
            }

            @keyframes progressSlide {
                0% { transform: translateX(-100%); }
                100% { transform: translateX(200%); }
            }

            #pageLoader .dots {
                margin-top: 12px;
                display: flex;
                justify-content: center;
                gap: 8px;
            }

            #pageLoader .dot {
                width: 8px;
                height: 8px;
                background: #ffffff;
                border-radius: 50%;
                animation: dotBounce 1.4s ease-in-out infinite;
            }

            #pageLoader .dot:nth-child(1) { animation-delay: 0s; }
            #pageLoader .dot:nth-child(2) { animation-delay: 0.2s; }
            #pageLoader .dot:nth-child(3) { animation-delay: 0.4s; }

            @keyframes dotBounce {
                0%, 60%, 100% {
                    transform: translateY(0);
                    opacity: 0.4;
                }
                30% {
                    transform: translateY(-12px);
                    opacity: 1;
                }
            }

            #pageLoader .particles {
                position: absolute;
                width: 100%;
                height: 100%;
                top: 0;
                left: 0;
                pointer-events: none;
            }

            #pageLoader .particle {
                position: absolute;
                width: 2px;
                height: 2px;
                background: #ffffff;
                border-radius: 50%;
                opacity: 0;
                animation: particleFloat 4s ease-in-out infinite;
            }

            @keyframes particleFloat {
                0% {
                    transform: translateY(0) translateX(0);
                    opacity: 0;
                }
                20% { opacity: 0.6; }
                100% {
                    transform: translateY(-180px) translateX(var(--drift));
                    opacity: 0;
                }
            }

            #pageLoader .particle:nth-child(1) { left: 20%; animation-delay: 0s; --drift: 20px; }
            #pageLoader .particle:nth-child(2) { left: 40%; animation-delay: 0.8s; --drift: -15px; }
            #pageLoader .particle:nth-child(3) { left: 60%; animation-delay: 1.6s; --drift: 25px; }
            #pageLoader .particle:nth-child(4) { left: 80%; animation-delay: 2.4s; --drift: -20px; }
            #pageLoader .particle:nth-child(5) { left: 30%; animation-delay: 3.2s; --drift: 15px; }
        </style>
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <!-- Loading Indicator -->
        <div id="pageLoader" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-[9999] hidden items-center justify-center">
            <div class="loader-container">
                <div class="logo-wrapper">
                    <div class="rotating-gear"></div>
                    <div class="circle-glow"></div>
                    <img src="{{ asset('images/Logo.png') }}" alt="RM Wood Works" class="logo">
                    <div class="particles">
                        <div class="particle"></div>
                        <div class="particle"></div>
                        <div class="particle"></div>
                        <div class="particle"></div>
                        <div class="particle"></div>
                    </div>
                </div>
                <div class="loading-text">Loading</div>
                <div class="progress-bar">
                    <div class="progress-fill"></div>
                </div>
                <div class="dots">
                    <div class="dot"></div>
                    <div class="dot"></div>
                    <div class="dot"></div>
                </div>
            </div>
        </div>

        <!-- Mobile Header -->
        <div class="md:hidden flex items-center justify-between bg-[#1e293b] text-white p-4 border-b border-slate-700" x-data>
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/logo-white.png') }}" alt="Logo" class="w-8 h-8 object-contain">
                <span class="font-bold text-lg">RM WOOD</span>
            </div>
            <button @click="$dispatch('toggle-sidebar')" class="p-2 rounded-lg hover:bg-slate-700 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </div>

        <div class="flex h-screen bg-gray-100 overflow-hidden">
            @include('layouts.sidebar')

            <!-- Main Content -->
            <div class="flex-1 flex flex-col overflow-hidden relative">
                @yield('main-content')
            </div>
        </div>

        <script>
            // Show loading indicator on navigation
            document.addEventListener('DOMContentLoaded', function() {
                const loader = document.getElementById('pageLoader');
                const links = document.querySelectorAll('a:not([target="_blank"])');
                
                links.forEach(link => {
                    link.addEventListener('click', function(e) {
                        // Only show loader for navigation links (not javascript:void, #, or current page)
                        const href = this.getAttribute('href');
                        if (href && href !== '#' && !href.startsWith('javascript:') && href !== window.location.pathname) {
                            loader.classList.remove('hidden');
                            loader.classList.add('flex');
                        }
                    });
                });

                // Hide loader when page is fully loaded (backup)
                window.addEventListener('pageshow', function() {
                    loader.classList.add('hidden');
                    loader.classList.remove('flex');
                });
                
                // Header Scroll Animation
                const mainContent = document.querySelector('.overflow-y-auto');
                const header = document.querySelector('header') || document.querySelector('.bg-amber-50.border-b'); // Adjust selector as needed based on actual header structure
                
                if (mainContent && header) {
                    let lastScrollY = 0;
                    
                    // Add transition for smooth margin change
                    header.style.transition = 'margin-top 0.3s ease-in-out';
                    
                    mainContent.addEventListener('scroll', () => {
                        const currentScrollY = mainContent.scrollTop;
                        const headerHeight = header.offsetHeight;
                        
                        if (currentScrollY > 50) { // Trigger earlier
                            if (currentScrollY > lastScrollY) {
                                // Scrolling DOWN
                                header.style.marginTop = `-${headerHeight}px`;
                            } else {
                                // Scrolling UP
                                header.style.marginTop = '0px';
                            }
                        } else {
                            // At the top
                            header.style.marginTop = '0px';
                        }
                        
                        lastScrollY = currentScrollY;
                    });
                }
                
                // Check for session flash messages
                @if(session('success'))
                    showSuccessNotification("{{ session('success') }}");
                @endif
                
                @if(session('error'))
                    showErrorNotification("{{ session('error') }}");
                @endif
            });

            // Global Utility Functions
            function toNumber(value) {
                const n = parseFloat(value);
                return Number.isFinite(n) ? n : 0;
            }

            function formatCurrency(num) {
                return '₱' + toNumber(num).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }

            // Global Notification Functions
            function showSuccessNotification(message) {
                const notif = document.createElement('div');
                notif.className = 'fixed top-5 right-5 z-[99999] animate-fadeIn';
                notif.innerHTML = `
                    <div class="flex items-center gap-3 bg-green-100 border-2 border-green-400 text-green-800 rounded-lg px-6 py-4 shadow-lg">
                        <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="font-medium text-sm">${message}</span>
                        <button onclick="this.parentElement.parentElement.remove()" class="text-green-600 hover:text-green-800 ml-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                `;
                document.body.appendChild(notif);
                setTimeout(() => {
                    if(notif.parentElement) notif.remove();
                }, 4000);
            }

            function showErrorNotification(message) {
                const notif = document.createElement('div');
                notif.className = 'fixed top-5 right-5 z-[99999] animate-fadeIn';
                notif.innerHTML = `
                    <div class="flex items-center gap-3 bg-red-100 border-2 border-red-400 text-red-800 rounded-lg px-6 py-4 shadow-lg">
                        <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <span class="font-medium text-sm">${message}</span>
                        <button onclick="this.parentElement.parentElement.remove()" class="text-red-600 hover:text-red-800 ml-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                `;
                document.body.appendChild(notif);
                setTimeout(() => {
                    if(notif.parentElement) notif.remove();
                }, 5000);
            }
        </script>
        <style>
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(-10px); }
                to { opacity: 1; transform: translateY(0); }
            }
            .animate-fadeIn {
                animation: fadeIn 0.3s ease-out forwards;
            }
        </style>
        @stack('modals')
    </body>
</html>
