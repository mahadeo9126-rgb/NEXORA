<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - NEXORA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .glowing-text {
            text-shadow: 0 0 10px rgba(79, 70, 229, 0.8), 0 0 20px rgba(79, 70, 229, 0.4);
        }
    </style>
</head>
<body class="bg-gray-50 flex h-screen items-center justify-center font-sans">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl overflow-hidden p-8">

        <div class="text-center mb-8">
            <h1 class="text-4xl font-black text-indigo-600 glowing-text tracking-widest uppercase mb-2">NEXORA</h1>
            <p class="text-gray-500 font-medium">Welcome back! Please login to your account.</p>
        </div>

        @if($errors->any())
            <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-4 rounded text-sm text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="/login" class="space-y-6">
            @csrf

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Password</label>
                <input type="password" name="password" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200">
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input type="checkbox" name="remember" id="remember" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label for="remember" class="ml-2 block text-sm text-gray-700">Remember me</label>
                </div>
            </div>

            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-xl shadow-lg transition duration-200">
                Sign In
            </button>
        </form>

        <p class="mt-8 text-center text-sm text-gray-600">
            Don't have an account?
            <a href="/register" class="font-bold text-indigo-600 hover:text-indigo-500 transition duration-200">Register here</a>
        </p>

    </div>
</body>
</html>
