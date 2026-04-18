<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - Nexora</title>
</head>
<body>
    <h1>Admin Control Panel</h1>

    <div style="border: 1px solid #ccc; padding: 20px; text-align: center;">
        <h3>SYSTEM SYNC</h3>
        <p>Use this button to manually process pending system updates, replacing the need for Cron Jobs.</p>

        <form action="/admin/sync" method="POST">
            @csrf
            <button type="submit" style="padding: 15px 30px; font-size: 16px; background-color: #007bff; color: white; border: none; cursor: pointer;">
                RUN SYSTEM SYNC
            </button>
        </form>
    </div>

    @if(session('status'))
        <div style="color: green; margin-top: 15px; text-align: center;">{{ session('status') }}</div>
    @endif
</body>
</html>
