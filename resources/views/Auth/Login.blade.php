

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | RM Wood Works</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,600;1,600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #8B4513;
            --primary-light: #A0522D;
            --accent: #D2691E;
            --surface: rgba(255, 255, 255, 0.65);
        }
        
        body {
            font-family: 'Outfit', sans-serif;
        }

        .font-heading {
            font-family: 'Playfair Display', serif;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 25px 50px -12px rgba(139, 69, 19, 0.15);
        }

        .grain-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 400 400' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)' opacity='0.03'/%3E%3C/svg%3E");
            pointer-events: none;
            z-index: 50;
        }

        /* Custom Input Styling */
        .custom-input {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .custom-input:focus {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(139, 69, 19, 0.1), 0 0 0 4px rgba(210, 105, 30, 0.1);
        }

        .input-group:focus-within label {
            color: var(--primary);
        }

        .input-group:focus-within svg {
            color: var(--accent);
            transform: scale(1.1);
        }
    </style>
    @if(filter_var(env('TURNSTILE_ENABLED', false), FILTER_VALIDATE_BOOLEAN))
        <x-turnstile.scripts />
    @endif
</head>
<body class="min-h-screen bg-[#FDFBF7] relative overflow-hidden flex items-center justify-center">

    <!-- Background Decoration -->
    <div class="absolute inset-0 z-0">
        <!-- Abstract Shapes -->
        <div class="absolute top-[-10%] right-[-5%] w-[600px] h-[600px] bg-gradient-to-br from-[#FFE4B5]/40 to-[#FFDAB9]/40 rounded-full blur-[100px]" style="animation-duration: 10s;"></div>
        <div class="absolute bottom-[-10%] left-[-5%] w-[500px] h-[500px] bg-gradient-to-tr from-[#DEB887]/30 to-[#F4A460]/20 rounded-full blur-[80px]"></div>
        
        <!-- Subtle Grid -->
        <div class="absolute inset-0"    style="background-image: linear-gradient(rgba(139, 69, 19, 0.03) 1px, transparent 1px), linear-gradient(90deg, rgba(139, 69, 19, 0.03) 1px, transparent 1px); background-size: 50px 50px;"></div>
    </div>

    <!-- Grain Overlay for texture -->
    <div class="grain-overlay"></div>

    <!-- Main Container -->
    <div class="relative z-10 w-full max-w-[1200px] px-6 py-12 flex flex-col lg:flex-row items-center justify-center lg:justify-between gap-10 lg:gap-16">
        
        <!-- Left Side: Brand Story (Visible on LG and up) -->
        <div class="hidden lg:flex flex-col flex-1 items-start space-y-10">
            <!-- Brand Badge -->
            <div class="inline-flex items-center gap-3 px-4 py-2 rounded-full bg-white/60 backdrop-blur-sm border border-white/40 shadow-sm">
                <span class="w-2 h-2 rounded-full bg-[#D2691E]"></span>
                <span class="text-sm font-semibold text-[#8B4513] tracking-wide uppercase">Premium Craftsmanship</span>
            </div>

            <div class="space-y-6">
                <h1 class="font-heading text-6xl font-bold text-[#4A3728] leading-tight drop-shadow-sm">
                    Mastery in <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#8B4513] to-[#D2691E]">Every Grain</span>
                </h1>
                <p class="text-lg text-[#6D5D54] leading-relaxed max-w-lg">
                    Streamline your woodworking operations with our state-of-the-art inventory management system. Designed for artisans, built for efficiency.
                </p>
            </div>

            <!-- Features Grid -->
            <div class="grid grid-cols-2 gap-6 w-full max-w-lg">
                <div class="group p-5 bg-white/40 rounded-2xl border border-white/50 backdrop-blur-sm hover:bg-white/60 transition-all duration-300">
                    <div class="w-10 h-10 rounded-xl bg gradient-to-br from-[#8B4513] to-[#D2691E] bg-[#8B4513]/10 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5 text-[#8B4513]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    </div>
                    <h3 class="font-bold text-[#4A3728]">Inventory Tracking</h3>
                    <p class="text-sm text-[#8B735B] mt-1">Real-time material logs</p>
                </div>
                <div class="group p-5 bg-white/40 rounded-2xl border border-white/50 backdrop-blur-sm hover:bg-white/60 transition-all duration-300">
                    <div class="w-10 h-10 rounded-xl bg-[#8B4513]/10 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5 text-[#8B4513]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    </div>
                    <h3 class="font-bold text-[#4A3728]">Analytics</h3>
                    <p class="text-sm text-[#8B735B] mt-1">Insightful reporting</p>
                </div>
            </div>
        </div>

        <!-- Right Side: Login Card -->
        <div class="w-full lg:w-[450px]">
            <div class="glass-card rounded-[2rem] p-8 md:p-10 relative overflow-hidden">
                <!-- Glossy Shine Effect -->
                <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 bg-gradient-to-br from-white/40 to-transparent rounded-full blur-2xl pointer-events-none"></div>

                <!-- Form Header -->
                <div class="text-center mb-10 relative">
                    <div class="inline-flex justify-center mb-4 relative group">
                        <div class="absolute inset-0 bg-[#D2691E]/20 rounded-2xl blur-lg group-hover:blur-xl transition-all duration-300"></div>
                        <img src="{{ asset('images/Logo.png') }}" alt="RM Wood Works" class="relative w-24 h-auto drop-shadow-md transform group-hover:scale-105 transition-transform duration-300">
                    </div>
                    <h2 class="font-heading text-3xl font-bold text-[#4A3728] mb-1">Welcome Back</h2>
                    <p class="text-[#8B735B]">Sign in to access your dashboard</p>
                </div>

                <!-- Error Alert -->
                @if($errors->any())
                    <div class="mb-6 bg-red-50/80 backdrop-blur-sm border border-red-200 rounded-xl p-4 flex gap-3 text-red-800">
                        <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <ul class="text-sm list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Form -->
                <form method="POST" action="{{ route('login.attempt') }}" class="space-y-6">
                    @csrf
                    
                    <!-- Email Field -->
                    <div class="input-group">
                        <label for="email" class="block text-sm font-semibold text-[#6D5D54] mb-2 pl-1">Email Address</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-[#A0522D]/60 transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                                </svg>
                            </div>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                                class="custom-input w-full bg-white/60 border border-[#DEB887]/50 rounded-xl px-4 py-3.5 pl-11 text-[#4A3728] placeholder-[#8B735B]/50 font-medium focus:outline-none focus:bg-white"
                                placeholder="role@rmwoodworks.com">
                        </div>
                    </div>

                    <!-- Password Field -->
                    <div class="input-group">
                        <div class="flex justify-between items-center mb-2 pl-1">
                            <label for="password" class="block text-sm font-semibold text-[#6D5D54]">Password</label>
                           
                        </div>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-[#A0522D]/60 transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                            </div>
                            <input type="password" id="password" name="password" required
                                class="custom-input w-full bg-white/60 border border-[#DEB887]/50 rounded-xl px-4 py-3.5 pl-11 text-[#4A3728] placeholder-[#8B735B]/50 font-medium focus:outline-none focus:bg-white"
                                placeholder="••••••••">
                        </div>
                    </div>

            @if(filter_var(env('TURNSTILE_ENABLED', false), FILTER_VALIDATE_BOOLEAN))
            <div class="mt-4 flex flex-col items-center justify-center">
            <div class="min-h-[65px] flex items-center justify-center">
                <x-turnstile
                    data-action="login"
                    data-theme="light"
                />
            </div>
            @error('cf-turnstile-response')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
            @endif            
              
                                            
                    <!-- Submit Button -->
                    <button type="submit" 
                        class="w-full bg-gradient-to-r from-[#8B4513] via-[#A0522D] to-[#8B4513] bg-[length:200%_auto] hover:bg-right transition-all duration-500 text-white font-bold py-4 rounded-xl shadow-lg hover:shadow-[#8B4513]/30 active:scale-[0.98] flex items-center justify-center gap-2 group">
                        <span>Sign In</span>
                        <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </button>
                </form>

                <!-- Footer -->
                <div class="mt-8 pt-6 border-t border-[#8B4513]/10 text-center">
                    <p class="text-xs text-[#8B735B]">
                        &copy; {{ date('Y') }} RM Wood Works Inventory System.
                    </p>
                </div>
            </div>
        </div>
    </div>

</body>
</html>