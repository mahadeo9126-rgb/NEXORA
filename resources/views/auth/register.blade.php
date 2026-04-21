<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - NEXORA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .glowing-text {
            text-shadow: 0 0 10px rgba(79, 70, 229, 0.8), 0 0 20px rgba(79, 70, 229, 0.4);
        }
    </style>
</head>
<body class="bg-gray-50 flex min-h-screen items-center justify-center font-sans py-12">
    <div class="w-full max-w-2xl bg-white rounded-2xl shadow-xl overflow-hidden p-8">

        <div class="text-center mb-8">
            <h1 class="text-4xl font-black text-indigo-600 glowing-text tracking-widest uppercase mb-2">NEXORA</h1>
            <p class="text-gray-500 font-medium">Join the platform and build your matrix.</p>
        </div>

        @if($errors->any())
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded text-sm text-red-700">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ url('/register') }}" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Username -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Username</label>
                    <input type="text" name="username" value="{{ old('username') }}" required autofocus
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200">
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200">
                </div>
            </div>

            <!-- Password -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Password</label>
                <input type="password" name="password" required minlength="8"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200">
                <p class="text-xs text-gray-500 mt-1">Must be at least 8 characters long.</p>
            </div>

            <hr class="my-6 border-gray-200">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Sponsor ID -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Sponsor ID</label>
                    <input type="text" name="sponsor_id" value="{{ old('sponsor_id', request('ref')) }}" required {{ request('ref') ? 'readonly' : '' }}
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200">
                </div>

                <!-- Placement Preference -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Placement Preference</label>
                    <select name="placement_pref" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200 bg-white">
                        <option value="left" {{ old('placement_pref') == 'left' ? 'selected' : '' }}>Left Branch</option>
                        <option value="right" {{ old('placement_pref') == 'right' ? 'selected' : '' }}>Right Branch</option>
                        <option value="extreme_left" {{ old('placement_pref') == 'extreme_left' ? 'selected' : '' }}>Extreme Left</option>
                        <option value="extreme_right" {{ old('placement_pref') == 'extreme_right' ? 'selected' : '' }}>Extreme Right</option>
                    </select>
                </div>
            </div>

            <hr class="my-6 border-gray-200">

            <!-- Wallet Address -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Wallet Address (BEP20)</label>
                <input type="text" name="wallet_address" value="{{ old('wallet_address') }}" required placeholder="0x..."
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200 font-mono text-sm">
                <p class="text-xs text-red-500 mt-1 font-semibold">Security Warning: Cannot be changed later without admin approval.</p>
            </div>

            <!-- Transaction Hash -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Registration Deposit Tx Hash (60 USDT)</label>
                <input type="text" name="tx_hash" value="{{ old('tx_hash') }}" required placeholder="0x..."
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200 font-mono text-sm">
                <p class="text-xs text-gray-500 mt-1">Please send exactly 60 USDT on the BSC network to the Master Wallet before registering.</p>
            </div>

            <button type="submit" class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold py-4 px-6 rounded-xl shadow-lg transition duration-200 text-lg tracking-wide mt-4">
                Complete Registration
            </button>
        </form>

        <p class="mt-8 text-center text-sm text-gray-600">
            Already have an account?
            <a href="{{ url('/login') }}" class="font-bold text-indigo-600 hover:text-indigo-500 transition duration-200">Log in here</a>
        </p>

    </div>
</body>
</html>
