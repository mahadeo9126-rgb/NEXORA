<!DOCTYPE html>
<html>
<head>
    <title>User Dashboard - Nexora</title>
</head>
<body>
    <h1>Welcome, {{ $user->username }} (Rank: RUB {{ $user->rub_rank }})</h1>

    <div style="border: 1px solid #ccc; padding: 10px; margin-bottom: 20px;">
        <h3>Your Referral Link</h3>
        <input type="text" readonly value="{{ $referralLink }}" style="width: 100%;">
    </div>

    <div style="border: 1px solid #ccc; padding: 10px; margin-bottom: 20px;">
        <h3>Financials</h3>
        <p>Withdrawable Balance: ${{ number_format($user->withdrawable_balance, 2) }}</p>
        <p>Shopping Credit: ${{ number_format($user->shopping_credit, 2) }}</p>
        <p>Income Predictor: ${{ number_format($incomePredictor, 2) }} (Potential based on current matrix)</p>
    </div>

    <div style="border: 1px solid #ccc; padding: 10px; margin-bottom: 20px;">
        <h3>Foundation Fund</h3>
        <p>Live Counter: ${{ number_format($foundationFund, 2) }}</p>
    </div>

    <div style="border: 1px solid #ccc; padding: 10px;">
        <h3>Quick Actions</h3>
        <a href="/genealogy">View Genealogy Tree</a><br>

        @if($user->rub_rank < 7)
        <form action="/upgrade" method="POST" style="margin-top: 10px;">
            @csrf
            <input type="hidden" name="level" value="{{ $user->rub_rank + 1 }}">
            <input type="text" name="tx_hash" placeholder="Deposit Tx Hash" required>
            <button type="submit">Upgrade to RUB {{ $user->rub_rank + 1 }}</button>
        </form>
        @endif

        <form action="/subscribe" method="POST" style="margin-top: 10px;">
            @csrf
            <input type="text" name="tx_hash" placeholder="Subscription Tx Hash ($39)" required>
            <button type="submit">Renew Subscription</button>
        </form>

        <hr>

        <h4>Wallet & Withdrawals</h4>
        <form action="/wallet/update" method="POST" style="margin-top: 10px;">
            @csrf
            <input type="text" name="wallet_address" value="{{ $user->wallet_address }}" required>
            <button type="submit">Update Wallet</button>
            <small>(Locks withdrawals for 24h)</small>
        </form>

        <form action="/withdraw/request" method="POST" style="margin-top: 10px;">
            @csrf
            <button type="submit">Request Withdrawal OTP</button>
        </form>

        @if($user->otp_code)
        <form action="/withdraw/verify" method="POST" style="margin-top: 10px;">
            @csrf
            <input type="number" name="amount" placeholder="Amount to withdraw" required>
            <input type="text" name="otp" placeholder="Enter 6-digit OTP" required>
            <button type="submit">Confirm Withdrawal</button>
        </form>
        @endif

        <hr>

        <form action="/logout" method="POST" style="margin-top: 10px;">
            @csrf
            <button type="submit">Logout</button>
        </form>
    </div>

    @if(session('status'))
        <div style="color: green; margin-top: 15px;">{{ session('status') }}</div>
    @endif
    @if($errors->any())
        <div style="color: red; margin-top: 15px;">{{ $errors->first() }}</div>
    @endif
</body>
</html>
