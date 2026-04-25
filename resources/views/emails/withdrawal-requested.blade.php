<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><style>
body{font-family:Arial,sans-serif;font-size:14px;color:#333;background:#f5f5f5;margin:0;padding:20px;}
.wrap{max-width:560px;margin:0 auto;background:#fff;border-radius:8px;padding:30px;border:1px solid #e0e0e0;}
h2{color:#111;margin-top:0;}
table{width:100%;border-collapse:collapse;margin:16px 0;}
td{padding:8px 12px;border-bottom:1px solid #eee;font-size:13px;}
td:first-child{color:#666;width:140px;}
.badge{display:inline-block;padding:3px 10px;border-radius:4px;font-size:12px;background:#fef3c7;color:#92400e;}
.footer{margin-top:20px;font-size:12px;color:#999;}
a.btn{display:inline-block;margin-top:16px;padding:10px 20px;background:#111;color:#fff;text-decoration:none;border-radius:6px;font-size:13px;}
</style></head>
<body>
<div class="wrap">
    <h2>💸 New Withdrawal Request</h2>
    <p>A user has submitted a withdrawal request and is awaiting processing.</p>
    <table>
        <tr><td>User</td><td><strong>{{ $userName }}</strong> ({{ $userEmail }})</td></tr>
        <tr><td>Amount</td><td><strong>${{ number_format($amount, 2) }}</strong></td></tr>
        <tr><td>Method</td><td><span class="badge">{{ strtoupper($method) }}</span></td></tr>
        <tr><td>Details</td><td>{{ $details }}</td></tr>
        <tr><td>Date</td><td>{{ now()->format('d.m.Y H:i') }} (Kyiv)</td></tr>
    </table>
    <a href="{{ url('/admin/affiliate-withdrawals') }}" class="btn">View in Admin Panel →</a>
    <div class="footer">{{ config('app.name') }} — automated notification</div>
</div>
</body>
</html>
