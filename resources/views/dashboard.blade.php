@extends('layouts.app')
@section('title', 'Dashboard - NEXORA')
@section('header_title', 'Dashboard')

@section('content')
<div class="space-y-8">

    <!-- Welcome Header -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 flex flex-col md:flex-row items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Welcome back, {{ $user->username }}</h1>
            <p class="text-gray-500 mt-2">Here is what's happening with your NEXORA account today.</p>
        </div>
        <div class="mt-4 md:mt-0 flex flex-col items-end">
            <span class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Current Rank</span>
            <span class="text-4xl font-black bg-clip-text text-transparent bg-gradient-to-r from-yellow-400 to-pink-500">
                RUB {{ $user->rub_rank }}
            </span>
        </div>
    </div>

    <!-- 3 Premium Stat Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <!-- Balance Card -->
        <div class="bg-gradient-to-br from-indigo-900 to-indigo-800 rounded-2xl shadow-lg p-6 relative overflow-hidden text-white">
            <div class="relative z-10">
                <h3 class="text-indigo-200 font-medium tracking-wide uppercase text-sm mb-2">Withdrawable Balance</h3>
                <div class="flex items-baseline space-x-2">
                    <span class="text-4xl font-black">${{ number_format($user->withdrawable_balance, 2) }}</span>
                    <span class="text-indigo-300 font-semibold">USDT</span>
                </div>
            </div>
            <div class="absolute -right-6 -bottom-6 opacity-10">
                <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a8 8 0 100 16 8 8 0 000-16zm1 11H9v-2H8v-2h1V7h2v2h1v2h-1v2z"></path></svg>
            </div>
        </div>

        <!-- Shopping Credit Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col justify-center">
            <h3 class="text-gray-500 font-medium tracking-wide uppercase text-sm mb-2">Shopping Credit</h3>
            <div class="flex items-baseline space-x-2">
                <span class="text-3xl font-bold text-gray-800">${{ number_format($user->shopping_credit, 2) }}</span>
                <span class="text-gray-400 font-medium">USDT</span>
            </div>
        </div>

        <!-- Matrix Status Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col justify-center">
            <h3 class="text-gray-500 font-medium tracking-wide uppercase text-sm mb-2">Matrix Size</h3>
            <div class="flex items-baseline space-x-2">
                <span class="text-3xl font-bold text-gray-800">{{ $matrixSize }}</span>
                <span class="text-gray-400 font-medium">Active Partners</span>
            </div>
        </div>

    </div>

    <!-- Referral Link -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-gray-800 font-bold mb-4">Your Referral Link</h3>
        <div class="flex">
            <input type="text" readonly value="{{ $referralLink }}" class="flex-1 bg-gray-50 border border-gray-200 rounded-l-xl px-4 py-3 text-gray-600 focus:outline-none">
            <button onclick="navigator.clipboard.writeText('{{ $referralLink }}'); alert('Copied!')" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-r-xl font-medium transition duration-200">Copy</button>
        </div>
    </div>

    <!-- Quick Actions Row -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Upgrade Form -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-gray-800 font-bold mb-4 text-lg">Level Up (Upgrade Rank)</h3>
            <form action="/upgrade" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="level" value="{{ $user->rub_rank + 1 }}">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Target Upgrade</label>
                    <div class="w-full bg-gray-100 border border-gray-200 rounded-xl px-4 py-3 text-gray-500 font-semibold cursor-not-allowed">
                        RUB {{ $user->rub_rank < 7 ? $user->rub_rank + 1 : 'MAX (7)' }}
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deposit Tx Hash (BEP20)</label>
                    <input type="text" name="tx_hash" placeholder="0x..." required class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
                </div>
                <button type="submit" class="w-full bg-gradient-to-r from-yellow-500 to-orange-500 hover:from-yellow-600 hover:to-orange-600 text-white font-bold py-3 px-4 rounded-xl shadow-md transition duration-200" {{ $user->rub_rank == 7 ? 'disabled' : '' }}>
                    Confirm Upgrade
                </button>
            </form>
        </div>

        <!-- Withdrawal Form -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-gray-800 font-bold mb-4 text-lg">Request Withdrawal</h3>
            @if(!$user->otp_code)
            <form action="/withdraw/request" method="POST" class="h-full flex flex-col justify-center space-y-4">
                @csrf
                <p class="text-sm text-gray-500 mb-4">You will receive an OTP code to your registered email to authorize this withdrawal.</p>
                <button type="submit" class="w-full bg-gray-900 hover:bg-black text-white font-bold py-3 px-4 rounded-xl shadow-md transition duration-200">
                    Request OTP
                </button>
            </form>
            @else
            <form action="/withdraw/verify" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Amount (USDT)</label>
                    <input type="number" name="amount" placeholder="0.00" min="10" required class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">6-Digit OTP</label>
                    <input type="text" name="otp" placeholder="------" maxlength="6" required class="w-full border border-gray-300 rounded-xl px-4 py-3 text-center tracking-widest font-mono focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
                </div>
                <button type="submit" class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-4 rounded-xl shadow-md transition duration-200">
                    Process Withdrawal
                </button>
            </form>
            @endif
        </div>
    </div>

    <!-- Recent Transactions Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100">
            <h3 class="text-gray-800 font-bold text-lg">Recent Transactions</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                        <th class="px-6 py-4 font-medium">Type</th>
                        <th class="px-6 py-4 font-medium">Amount</th>
                        <th class="px-6 py-4 font-medium">Tx Hash</th>
                        <th class="px-6 py-4 font-medium">Date</th>
                        <th class="px-6 py-4 font-medium text-right">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($recentTransactions as $tx)
                    <tr class="hover:bg-gray-50 transition duration-150">
                        <td class="px-6 py-4">
                            <span class="font-medium text-gray-800 capitalize">{{ $tx->type }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-bold {{ $tx->type === 'withdrawal' ? 'text-red-500' : 'text-green-500' }}">
                                {{ $tx->type === 'withdrawal' ? '-' : '+' }}${{ number_format($tx->amount, 2) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-500 font-mono text-sm">
                            {{ substr($tx->tx_hash, 0, 10) }}...
                        </td>
                        <td class="px-6 py-4 text-gray-500 text-sm">
                            {{ $tx->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            @if($tx->status === 'completed')
                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">Completed</span>
                            @elseif($tx->status === 'pending')
                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">Pending</span>
                            @else
                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">Rejected</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">No recent transactions found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
