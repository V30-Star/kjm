<!-- resources/views/auth/login.blade.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sign in</title>
    <!-- Tailwind quick use (CDN). For production, prefer Vite build. -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        emerald: {
                            600: '#059669'
                        }
                    }
                }
            }
        }
    </script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="min-h-dvh bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 text-slate-100">
    <div class="flex min-h-dvh items-center justify-center p-4">
        <div class="w-full max-w-md">
            <!-- Card -->
            <div class="rounded-2xl bg-white/10 backdrop-blur border border-white/10 shadow-2xl">
                <div class="p-8">
                    <!-- Logo / Title -->
                    <div class="mb-6 text-center">
                        <div class="mx-auto mb-3 h-12 w-12 rounded-xl bg-emerald-600/90 grid place-content-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-white" viewBox="0 0 24 24"
                                fill="currentColor">
                                <path d="M12 2a10 10 0 1 0 10 10A10.011 10.011 0 0 0 12 2Zm1 15h-2v-2h2Zm0-4h-2V7h2Z" />
                            </svg>
                        </div>
                        <h1 class="text-xl font-semibold">Sign in to Kembar Jaya Motor</h1>
                        <p class="text-slate-300 text-sm">Welcome back. Please enter your details.</p>
                    </div>

                    <!-- Errors -->
                    @if ($errors->any())
                        <div class="mb-4 rounded-lg border border-rose-500/30 bg-rose-500/10 p-3 text-sm text-rose-200">
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Form -->
                    <form method="POST" action="{{ route('login.post') }}" x-data="{ show: false }" class="space-y-4">
                        @csrf

                        <div>
                            <label class="mb-1 block text-sm text-slate-200">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}" required autofocus
                                class="w-full rounded-xl bg-white/10 border border-white/10 focus:border-emerald-500 focus:ring-emerald-500 px-4 py-2.5 placeholder:text-slate-400 outline-none"
                                placeholder="you@example.com" />
                        </div>

                        <div>
                            <label class="mb-1 block text-sm text-slate-200">Password</label>
                            <div class="relative">
                                <input :type="show ? 'text' : 'password'" name="password" required
                                    class="w-full rounded-xl bg-white/10 border border-white/10 focus:border-emerald-500 focus:ring-emerald-500 px-4 py-2.5 placeholder:text-slate-400 outline-none pr-12"
                                    placeholder="••••••••" />
                                <button type="button" @click="show=!show"
                                    class="absolute inset-y-0 right-3 grid place-content-center text-slate-300 hover:text-white">
                                    <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                        viewBox="0 0 24 24" fill="currentColor">
                                        <path
                                            d="M12 5c-7 0-10 7-10 7s3 7 10 7 10-7 10-7-3-7-10-7Zm0 12a5 5 0 1 1 5-5 5.006 5.006 0 0 1-5 5Z" />
                                    </svg>
                                    <svg x-show="show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                        viewBox="0 0 24 24" fill="currentColor">
                                        <path
                                            d="M3.53 2.47 2.47 3.53 6.09 7.15A11.8 11.8 0 0 0 2 12s3 7 10 7a10.5 10.5 0 0 0 5.79-1.73l3.68 3.68 1.06-1.06ZM12 17c-5.52 0-8.49-4.49-9.45-6 .46-.73 1.23-1.82 2.33-2.86l3 3A5 5 0 0 0 12 17Zm9.45-5c-.32.51-1 1.57-2 2.56l-3.15-3.15a5 5 0 0 0-4.71-4.71l-2.2-2.2A10.58 10.58 0 0 1 12 5c7 0 10 7 10 7Z" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                                <input type="checkbox" name="remember"
                                    class="h-4 w-4 rounded border-white/10 bg-white/5 text-emerald-600 focus:ring-emerald-500" />
                                <span class="text-sm text-slate-300">Remember me</span>
                            </label>
                        </div>

                        <button type="submit"
                            class="w-full rounded-xl bg-emerald-600 hover:bg-emerald-700 px-4 py-2.5 font-medium text-white transition">
                            Sign in
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
