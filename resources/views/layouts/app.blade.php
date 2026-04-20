<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'NEXORA Dashboard')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .glowing-text {
            text-shadow: 0 0 10px rgba(79, 70, 229, 0.8), 0 0 20px rgba(79, 70, 229, 0.4);
        }
        .gradient-rank {
            background: linear-gradient(to right, #f59e0b, #ec4899);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased flex h-screen overflow-hidden">

    <!-- Mobile Sidebar Overlay -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-gray-900 bg-opacity-50 z-20 hidden md:hidden transition-opacity" onclick="toggleMobileMenu()"></div>

    <!-- Sidebar -->
    <aside id="sidebar" class="w-64 bg-gray-900 text-white flex flex-col justify-between fixed md:relative z-30 h-full transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out">
        <div>
            <!-- Logo -->
            <div class="h-20 flex items-center justify-center border-b border-gray-800">
                <span class="text-3xl font-black text-indigo-500 glowing-text tracking-widest uppercase">NEXORA</span>
            </div>

            <!-- Navigation Links -->
            <nav class="mt-8 px-4 space-y-2">
                <a href="/dashboard" class="flex items-center py-3 px-4 rounded-xl transition duration-200 {{ request()->is('dashboard') ? 'bg-indigo-600 text-white shadow-lg' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    Dashboard
                </a>
                <a href="/genealogy" class="flex items-center py-3 px-4 rounded-xl transition duration-200 {{ request()->is('genealogy*') ? 'bg-indigo-600 text-white shadow-lg' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    Genealogy Tree
                </a>
                <a href="#" onclick="alert('Feature placeholder for Upgrade Rank modal/page');" class="flex items-center py-3 px-4 rounded-xl transition duration-200 text-gray-400 hover:bg-gray-800 hover:text-white">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 11l7-7 7 7M5 19l7-7 7 7"></path></svg>
                    Upgrade Rank
                </a>
                <a href="#" onclick="alert('Feature placeholder for Withdrawals modal/page');" class="flex items-center py-3 px-4 rounded-xl transition duration-200 text-gray-400 hover:bg-gray-800 hover:text-white">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Withdrawals
                </a>

                @if(auth()->user() && auth()->user()->is_admin)
                <a href="/admin/dashboard" class="flex items-center py-3 px-4 rounded-xl transition duration-200 {{ request()->is('admin/dashboard') ? 'bg-indigo-600 text-white shadow-lg' : 'text-indigo-400 hover:bg-indigo-900 hover:text-indigo-200' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    Admin Panel
                </a>
                @endif
            </nav>
        </div>

        <!-- Logout Button -->
        <div class="px-4 mb-6">
            <form action="/logout" method="POST">
                @csrf
                <button type="submit" class="flex items-center w-full py-3 px-4 text-red-400 hover:bg-red-500 hover:text-white rounded-xl transition duration-200">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    Logout
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content wrapper -->
    <div class="flex-1 flex flex-col overflow-hidden">

        <!-- Top Navbar -->
        <header class="h-20 bg-white border-b border-gray-200 flex items-center justify-between px-4 md:px-8 shadow-sm z-10">
            <div class="flex items-center">
                <!-- Hamburger Menu Button -->
                <button onclick="toggleMobileMenu()" class="md:hidden mr-4 text-gray-500 hover:text-gray-700 focus:outline-none">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <div class="text-xl md:text-2xl font-bold text-gray-800">
                    @yield('header_title', 'Overview')
                </div>
            </div>

            <div class="flex items-center space-x-6">
                <!-- User Profile / Rank Badge -->
                <div class="flex items-center space-x-3 bg-gray-100 py-2 px-4 rounded-full border border-gray-200 shadow-sm">
                    <div class="h-8 w-8 rounded-full bg-indigo-500 flex items-center justify-center text-white font-bold uppercase">
                        {{ substr(auth()->user()->username ?? 'U', 0, 1) }}
                    </div>
                    <div class="text-sm font-semibold text-gray-700">
                        {{ auth()->user()->username ?? 'User' }}
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-gray-900 text-white shadow-inner">
                        RUB <span class="gradient-rank">{{ auth()->user()->rub_rank ?? 0 }}</span>
                    </span>
                </div>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-8">
            @if(session('status'))
                <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-xl shadow-sm">
                    {{ session('status') }}
                </div>
            @endif
            @if($errors->any())
                <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-xl shadow-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Global Toast Container -->
    <div id="toast-container" class="fixed bottom-5 right-5 z-50 flex flex-col gap-2"></div>

    <script>
        function toggleMobileMenu() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');

            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }

        function showToast(message) {
            const container = document.getElementById('toast-container');

            const toast = document.createElement('div');
            toast.className = 'bg-gray-800 text-white px-6 py-3 rounded-xl shadow-lg transform transition-all duration-300 translate-y-10 opacity-0 flex items-center gap-3';

            toast.innerHTML = `
                <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span class="font-medium">${message}</span>
            `;

            container.appendChild(toast);

            // Animate in
            setTimeout(() => {
                toast.classList.remove('translate-y-10', 'opacity-0');
            }, 10);

            // Remove after 3 seconds
            setTimeout(() => {
                toast.classList.add('opacity-0', 'translate-y-2');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }
    </script>
</body>
</html>
