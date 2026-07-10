<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Welcome!</title>
<style>
  body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f9fafb; margin: 0; padding: 0; }
  .container { max-width: 560px; margin: 40px auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
  .header { background: #4f46e5; padding: 32px; text-align: center; }
  .header h1 { color: #ffffff; margin: 0; font-size: 24px; font-weight: 700; }
  .body { padding: 32px; }
  .body h2 { color: #111827; font-size: 20px; margin-top: 0; }
  .body p { color: #6b7280; line-height: 1.6; }
  .btn { display: inline-block; background: #4f46e5; color: #ffffff !important; text-decoration: none; padding: 12px 28px; border-radius: 8px; font-weight: 600; font-size: 15px; margin: 16px 0; }
  .features { background: #f9fafb; border-radius: 8px; padding: 20px; margin: 20px 0; }
  .feature { padding: 5px 0; color: #374151; font-size: 14px; }
  .footer { padding: 20px 32px; border-top: 1px solid #f3f4f6; text-align: center; color: #9ca3af; font-size: 12px; }
</style>
</head>
<body>
<div class="container">
  <div class="header"><h1>{{ $tenantName }}</h1></div>
  <div class="body">
    <h2>Welcome aboard, {{ $user->name }}! 🎉</h2>
    <p>Your workspace is ready. Start using {{ $tenantName }} to manage your team.</p>
    <a href="http://{{ $tenantDomain }}/dashboard" class="btn">Go to your dashboard →</a>
    <div class="features">
      <div class="feature">✅ Team management</div>
      <div class="feature">✅ Role-based access</div>
      <div class="feature">✅ Activity logs</div>
      <div class="feature">✅ Billing & plans</div>
    </div>
    <p style="color:#374151">— The {{ config('app.name') }} Team</p>
  </div>
  <div class="footer">
    <p>{{ config('app.name') }} · All rights reserved</p>
  </div>
</div>
</body>
</html>
