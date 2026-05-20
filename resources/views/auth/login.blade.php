<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login — PWD Registry Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'DM Sans', sans-serif; }
        .font-serif { font-family: 'DM Serif Display', serif; }

        body {
            background-color: #1a2a4a;
            min-height: 100vh;
        }

        /*
         * Z-INDEX STACK (low → high):
         *   0  .bg-scene   — the photo
         *   1  .bg-overlay — dark tint + blur
         *   2  .bg-dots    — dot texture
         *   3  .page-content — logos, card, form (all clickable)
         */

        .bg-scene {
            position: fixed;
            inset: 0;
            z-index: 0;
            background-image: url("{{ asset('images/municipal-hall.png') }}");
            background-size: cover;
            background-position: center center;
            background-repeat: no-repeat;
        }

        .bg-overlay {
            position: fixed;
            inset: 0;
            z-index: 1;
            background: rgba(8, 22, 52, 0.60);
            backdrop-filter: blur(2px);
            -webkit-backdrop-filter: blur(2px);
            pointer-events: none;
        }

        .bg-dots {
            position: fixed;
            inset: 0;
            z-index: 2;
            background-image: radial-gradient(rgba(255,255,255,0.06) 1px, transparent 1px);
            background-size: 24px 24px;
            pointer-events: none;
        }

        .page-content {
            position: relative;
            z-index: 3;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 1rem;
            gap: 1.25rem;
        }

        /* Left panel gradient */
        .panel-gradient {
            background: linear-gradient(160deg, #0c2d6b 0%, #1a4fa0 50%, #1565c0 100%);
        }

        /* Animated entry */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .fade-up   { animation: fadeUp 0.6s ease forwards; }
        .fade-up-1 { animation-delay: 0.1s; opacity: 0; }
        .fade-up-2 { animation-delay: 0.2s; opacity: 0; }
        .fade-up-3 { animation-delay: 0.3s; opacity: 0; }
        .fade-up-4 { animation-delay: 0.4s; opacity: 0; }
        .fade-up-5 { animation-delay: 0.5s; opacity: 0; }

        /* Custom focus ring */
        .input-field:focus {
            outline: none;
            border-color: #1a4fa0;
            box-shadow: 0 0 0 3px rgba(26, 79, 160, 0.12);
        }

        /* Button */
        .btn-primary {
            background: linear-gradient(135deg, #1a4fa0 0%, #0c2d6b 100%);
            transition: all 0.2s ease;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #1565c0 0%, #1a4fa0 100%);
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(26, 79, 160, 0.35);
        }
        .btn-primary:active { transform: translateY(0); }

        .card-shadow {
            box-shadow: 0 24px 70px rgba(0,0,0,0.35), 0 4px 20px rgba(0,0,0,0.18);
        }

        .deco-circle {
            position: absolute;
            border-radius: 50%;
            background: rgba(255,255,255,0.05);
        }

        /* Thin divider between logos */
        .logo-divider {
            width: 1px;
            height: 36px;
            background: rgba(255, 255, 255, 0.25);
            border-radius: 999px;
            flex-shrink: 0;
        }
    </style>
</head>
<body>

    <div class="bg-scene"></div>
    <div class="bg-overlay"></div>
    <div class="bg-dots"></div>

    <div class="page-content">

        {{-- ===================== MAIN CARD ===================== --}}
        <div class="w-full max-w-4xl card-shadow rounded-2xl overflow-hidden flex flex-col md:flex-row fade-up" style="animation-delay: 0s; min-height: 560px;">

            {{-- ===================== LEFT PANEL ===================== --}}
            <div class="panel-gradient relative flex flex-col items-center justify-center px-10 py-12 md:w-5/12 overflow-hidden">

                <div class="deco-circle w-64 h-64 -top-16 -left-16"></div>
                <div class="deco-circle w-48 h-48 -bottom-10 -right-10"></div>
                <div class="deco-circle w-24 h-24 top-1/2 right-4" style="background: rgba(255,255,255,0.03);"></div>

                {{-- 3 Logos side by side --}}
                <div class="relative z-10 flex items-center gap-3 mb-6 fade-up fade-up-1">

                    {{-- Logo 1: Republic of the Philippines seal --}}
                    <img src="{{ asset('images/ph-logo.png') }}"
                         alt="Republic of the Philippines"
                         class="w-14 h-14 object-contain drop-shadow-lg">

                    <div class="logo-divider"></div>

                    {{-- Logo 2: PWDAO logo --}}
                    <img src="{{ asset('images/pwdao-logo.png') }}"
                         alt="PWDAO Logo"
                         class="w-14 h-14 object-contain drop-shadow-lg">

                    <div class="logo-divider"></div>

                    {{-- Logo 3: LGU / Capoocan logo --}}
                    <img src="{{ asset('images/capoocan-logo.png') }}"
                         alt="LGU Logo"
                         class="w-14 h-14 object-contain drop-shadow-lg">

                </div>

                <div class="relative z-10 text-center fade-up fade-up-2">
                    <p class="text-yellow-300 text-xs font-semibold tracking-widest uppercase mb-1">LOCAL GOVERNMENT UNIT OF CAPOOCAN</p>
                    <h1 class="text-white font-serif text-2xl leading-tight mb-1">PDAO</h1>
                    <p class="text-blue-200 text-xs leading-snug">Persons with Disability<br>Affairs Office</p>
                </div>

                <div class="relative z-10 w-12 h-px bg-blue-400 my-6 fade-up fade-up-3"></div>

                <div class="relative z-10 text-center fade-up fade-up-3">
                    <h2 class="text-white font-serif text-lg leading-snug">PWD Registry<br>Management System</h2>
                    <p class="text-blue-300 text-xs mt-2">Registry for Persons<br>with Disabilities v4.0</p>
                </div>

                <div class="relative z-10 mt-auto pt-8 fade-up fade-up-4">
                    <span class="inline-block bg-white bg-opacity-10 text-blue-200 text-xs px-3 py-1 rounded-full border border-blue-400 border-opacity-30">
                        Authorized Personnel Only
                    </span>
                </div>
            </div>

            {{-- ===================== RIGHT PANEL ===================== --}}
            <div class="bg-white flex flex-col justify-center px-8 md:px-12 py-12 md:w-7/12">

                <div class="mb-8 fade-up fade-up-2">
                    <h2 class="font-serif text-3xl text-gray-800 mb-1">Welcome back</h2>
                    <p class="text-gray-400 text-sm">Sign in to your account to continue</p>
                </div>

                @if (session('error'))
                    <div class="mb-5 flex items-start gap-3 bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-lg fade-up fade-up-2">
                        <svg class="w-4 h-4 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                        </svg>
                        {{ session('error') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    {{-- Email --}}
                    <div class="fade-up fade-up-3">
                        <label for="email" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                            Username
                        </label>
                        <input
                            type="string"
                            id="username"
                            name="username"
                            value="{{ old('usernae') }}"
                            required
                            autofocus
                            autocomplete="username"
                            placeholder="John Doe"
                            class="input-field w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-gray-800 text-sm transition-all duration-200 @error('email') border-red-400 bg-red-50 @enderror"
                        />
                        @error('email')
                            <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div class="fade-up fade-up-4">
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="password" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Password
                            </label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-xs text-blue-600 hover:text-blue-800 transition-colors">
                                    Forgot password?
                                </a>
                            @endif
                        </div>
                        <div class="relative">
                            <input
                                type="password"
                                id="password"
                                name="password"
                                required
                                autocomplete="current-password"
                                placeholder="••••••••"
                                class="input-field w-full px-4 py-3 pr-11 rounded-xl border border-gray-200 bg-gray-50 text-gray-800 text-sm transition-all duration-200 @error('password') border-red-400 bg-red-50 @enderror"
                            />
                            <button type="button" onclick="togglePassword()" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                                <svg id="eye-icon" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg id="eye-off-icon" class="w-5 h-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Remember me --}}
                    <div class="flex items-center gap-2 fade-up fade-up-4">
                        <input
                            type="checkbox"
                            id="remember"
                            name="remember"
                            class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer"
                        />
                        <label for="remember" class="text-sm text-gray-500 cursor-pointer select-none">
                            Keep me signed in
                        </label>
                    </div>

                    {{-- Submit --}}
                    <div class="pt-1 fade-up fade-up-5">
                        <button type="submit" class="btn-primary w-full text-white font-semibold py-3 px-6 rounded-xl text-sm tracking-wide">
                            Sign In
                        </button>
                    </div>
                </form>

                <p class="mt-8 text-center text-xs text-gray-300 fade-up fade-up-5">
                    &copy; {{ date('Y') }} Department of Social Welfare and Development.<br>All rights reserved.
                </p>
            </div>
        </div>

    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');
            const eyeOffIcon = document.getElementById('eye-off-icon');
            if (input.type === 'password') {
                input.type = 'text';
                eyeIcon.classList.add('hidden');
                eyeOffIcon.classList.remove('hidden');
            } else {
                input.type = 'password';
                eyeIcon.classList.remove('hidden');
                eyeOffIcon.classList.add('hidden');
            }
        }
    </script>
</body>
</html>