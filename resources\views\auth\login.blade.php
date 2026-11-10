<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Hashmi Grains POS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Outfit:wght@500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Plus Jakarta Sans', sans-serif; }
        body {
            background: radial-gradient(circle at 50% 30%, #064e3b 0%, #022c19 50%, #01150c 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            color: #1e293b;
            position: relative;
            overflow-x: hidden;
        }

        /* Ambient Glowing Orbs in Background */
        .glow-orb-top {
            position: fixed;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.22) 0%, rgba(0,0,0,0) 70%);
            top: -200px;
            right: -100px;
            pointer-events: none;
            z-index: 0;
        }

        .glow-orb-bottom {
            position: fixed;
            width: 550px;
            height: 550px;
            background: radial-gradient(circle, rgba(245, 158, 11, 0.15) 0%, rgba(0,0,0,0) 70%);
            bottom: -200px;
            left: -100px;
            pointer-events: none;
            z-index: 0;
        }

        /* Background Watermark Logo */
        .bg-watermark-container {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 820px;
            max-width: 92vw;
            opacity: 0.09;
            pointer-events: none;
            z-index: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            user-select: none;
            filter: drop-shadow(0 0 50px rgba(16, 185, 129, 0.5));
        }

        .bg-watermark-img {
            width: 100%;
            height: auto;
            object-fit: contain;
        }

        /* Login Card */
        .login-card {
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 35px 70px -15px rgba(0, 0, 0, 0.65), 0 0 0 1px rgba(255, 255, 255, 0.2);
            max-width: 450px;
            width: 100%;
            overflow: hidden;
            position: relative;
            z-index: 1;
            animation: fadeIn 0.4s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(12px) scale(0.98); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        .login-header {
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            padding: 2rem 1.75rem 1.35rem;
            border-bottom: 2px solid #e2e8f0;
            text-align: center;
            position: relative;
        }

        .logo-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 0.75rem;
        }

        .login-logo-img {
            height: 96px;
            width: auto;
            max-width: 100%;
            object-fit: contain;
            border-radius: 12px;
            transition: transform 0.2s ease;
        }
        .login-logo-img:hover {
            transform: scale(1.02);
        }

        .brand-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
            padding: 4px 14px;
            border-radius: 9999px;
            font-size: 0.74rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .store-subtext {
            font-size: 0.73rem;
            color: #64748b;
            margin-top: 6px;
            font-weight: 500;
        }

        .login-body {
            padding: 2rem 1.75rem;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-label {
            display: block;
            font-size: 0.85rem;
            font-weight: 700;
            color: #334155;
            margin-bottom: 0.45rem;
        }

        .input-group {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-group i {
            position: absolute;
            left: 1.1rem;
            color: #94a3b8;
            font-size: 0.95rem;
        }

        .form-control {
            width: 100%;
            padding: 0.8rem 1rem 0.8rem 2.9rem;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            font-size: 0.95rem;
            font-weight: 500;
            color: #0f172a;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            background: #fafafa;
        }

        .form-control:focus {
            outline: none;
            background: #ffffff;
            border-color: #10b981;
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.15);
        }

        .btn-submit {
            width: 100%;
            padding: 0.9rem;
            background: linear-gradient(135deg, #0f5132 0%, #15803d 50%, #16a34a 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1.02rem;
            font-weight: 800;
            cursor: pointer;
            box-shadow: 0 6px 18px rgba(22, 163, 74, 0.35);
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(22, 163, 74, 0.45);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
            padding: 0.75rem 1rem;
            border-radius: 10px;
            font-size: 0.85rem;
            margin-bottom: 1.2rem;
            font-weight: 600;
        }
    </style>
</head>
<body>

    <!-- Ambient Glowing Orbs -->
    <div class="glow-orb-top"></div>
    <div class="glow-orb-bottom"></div>

    <!-- Background Watermark Logo -->
    <div class="bg-watermark-container">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="40 10 520 170" class="bg-watermark-img">
          <path d="M 60,85 Q 300,10 540,85" fill="none" stroke="#10b981" stroke-width="6" stroke-linecap="round" />
          <path d="M 120,83 Q 300,25 480,83" fill="none" stroke="#f59e0b" stroke-width="3" stroke-linecap="round" />
          <g transform="translate(300, 48)">
            <path d="M 0,15 C -25,-10 -55,-15 -55,-40 C -55,-48 -35,-52 0,-15 Z" fill="#10b981" />
            <path d="M 0,15 C 25,-10 55,-15 55,-40 C 55,-48 35,-52 0,-15 Z" fill="#10b981" />
          </g>
          <text x="300" y="132" font-family="'Outfit', 'Plus Jakarta Sans', sans-serif" font-size="52" font-weight="900" letter-spacing="2.5" fill="#10b981" text-anchor="middle">HASHMI GRAINS</text>
          <text x="300" y="169" font-family="'Outfit', 'Plus Jakarta Sans', sans-serif" font-size="20" font-weight="800" letter-spacing="5.5" fill="#10b981" text-anchor="middle">PURE BY NATURE</text>
        </svg>
    </div>

    <!-- Login Card -->
    <div class="login-card">
        <div class="login-header">
            <div class="logo-wrapper">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="40 10 520 170" style="height: 84px; width: 100%; max-width: 280px; object-fit: contain;">
                  <defs>
                    <linearGradient id="loginLeafGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                      <stop offset="0%" stop-color="#4ade80" />
                      <stop offset="60%" stop-color="#22c55e" />
                      <stop offset="100%" stop-color="#16a34a" />
                    </linearGradient>
                    <linearGradient id="loginGoldGrad" x1="0%" y1="0%" x2="100%" y2="0%">
                      <stop offset="0%" stop-color="#f59e0b" stop-opacity="0.2" />
                      <stop offset="30%" stop-color="#fbbf24" />
                      <stop offset="50%" stop-color="#fef08a" />
                      <stop offset="70%" stop-color="#fbbf24" />
                      <stop offset="100%" stop-color="#f59e0b" stop-opacity="0.2" />
                    </linearGradient>
                    <filter id="loginGlow" x="-20%" y="-20%" width="140%" height="140%">
                      <feGaussianBlur stdDeviation="3" result="blur" />
                      <feComposite in="SourceGraphic" in2="blur" operator="over" />
                    </filter>
                  </defs>
                  <path d="M 60,85 Q 300,10 540,85" fill="none" stroke="url(#loginGoldGrad)" stroke-width="4.5" stroke-linecap="round" />
                  <path d="M 120,83 Q 300,25 480,83" fill="none" stroke="url(#loginGoldGrad)" stroke-width="2" stroke-linecap="round" opacity="0.85" />
                  <g transform="translate(300, 48)">
                    <path d="M 0,15 C -25,-10 -55,-15 -55,-40 C -55,-48 -35,-52 0,-15 Z" fill="url(#loginLeafGrad)" />
                    <path d="M 0,15 C -25,-10 -55,-15 -55,-40 C -30,-40 -10,-25 0,15 Z" fill="#15803d" opacity="0.5" />
                    <path d="M 0,15 Q -25,-25 -45,-38" fill="none" stroke="#dcfce7" stroke-width="2" stroke-linecap="round" />
                    <path d="M 0,15 C 25,-10 55,-15 55,-40 C 55,-48 35,-52 0,-15 Z" fill="url(#loginLeafGrad)" />
                    <path d="M 0,15 C 25,-10 55,-15 55,-40 C 30,-40 10,-25 0,15 Z" fill="#166534" opacity="0.5" />
                    <path d="M 0,15 Q 25,-25 45,-38" fill="none" stroke="#dcfce7" stroke-width="2" stroke-linecap="round" />
                    <circle cx="0" cy="-25" r="4.5" fill="#86efac" filter="url(#loginGlow)" opacity="0.95" />
                  </g>
                  <text x="300" y="132" font-family="'Outfit', 'Plus Jakarta Sans', Arial Black, sans-serif" font-size="52" font-weight="900" letter-spacing="2.5" fill="#0f5132" text-anchor="middle">HASHMI GRAINS</text>
                  <text x="548" y="105" font-family="Arial, sans-serif" font-size="14" font-weight="bold" fill="#d97706">®</text>
                  <line x1="85" y1="162" x2="190" y2="162" stroke="#16a34a" stroke-width="2.5" stroke-linecap="round" />
                  <text x="300" y="169" font-family="'Outfit', 'Plus Jakarta Sans', sans-serif" font-size="20" font-weight="800" letter-spacing="5.5" fill="#15803d" text-anchor="middle">PURE BY NATURE</text>
                  <line x1="410" y1="162" x2="515" y2="162" stroke="#16a34a" stroke-width="2.5" stroke-linecap="round" />
                </svg>
            </div>
            <div class="brand-badge">
                <i class="fa-solid fa-seedling"></i>
                <span>Point of Sale & Inventory System</span>
            </div>
            <div class="store-subtext">Shop #47, Overseas B Commercial, Bahria Town Lahore</div>
        </div>

        <div class="login-body">
            @if($errors->any())
                <div class="alert-error">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label">Email / Username</label>
                    <div class="input-group">
                        <i class="fa-solid fa-envelope"></i>
                        <input type="text" name="email" class="form-control" placeholder="Enter email or username" value="{{ old('email', 'admin@123') }}" required autofocus>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Password</label>
                    <div class="input-group">
                        <i class="fa-solid fa-lock"></i>
                        <input type="password" name="password" class="form-control" placeholder="••••••••" value="admin123" required>
                    </div>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fa-solid fa-arrow-right-to-bracket"></i>
                    <span>Sign In to POS</span>
                </button>
            </form>
        </div>
    </div>
</body>
</html>
