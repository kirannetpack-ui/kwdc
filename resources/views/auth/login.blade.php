<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>KTM-WDC | Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --ink: #101724;
            --muted: #62728a;
            --line: #dbe4ef;
            --gold: #f5a524;
            --gold-dark: #c77700;
            --green: #0f766e;
            --blue: #2563eb;
            --paper: #ffffff;
        }

        * { box-sizing: border-box; }

        body {
            min-height: 100vh;
            margin: 0;
            color: var(--ink);
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background:
                linear-gradient(120deg, rgba(16, 23, 36, .94), rgba(16, 23, 36, .76)),
                url("{{ asset('images/landing-logistics.svg') }}") right bottom / min(760px, 66vw) auto no-repeat,
                #101724;
        }

        a { color: inherit; }

        .auth-shell {
            width: min(1180px, calc(100% - 32px));
            min-height: 100vh;
            margin: 0 auto;
            display: grid;
            grid-template-columns: minmax(300px, 1fr) minmax(330px, 430px);
            gap: 56px;
            align-items: center;
            padding: 42px 0;
        }

        .brand-side { color: white; }

        .brand {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 38px;
            text-decoration: none;
            font-weight: 900;
            font-size: 22px;
        }

        .brand-mark {
            width: 48px;
            height: 48px;
            display: grid;
            place-items: center;
            border-radius: 8px;
            color: #111827;
            background: var(--gold);
            box-shadow: 0 18px 38px rgba(245, 165, 36, .24);
        }

        .brand-side h1 {
            max-width: 690px;
            margin: 0 0 20px;
            font-size: clamp(42px, 6.4vw, 76px);
            line-height: .96;
            letter-spacing: 0;
        }

        .brand-side p {
            max-width: 590px;
            margin: 0;
            color: #c9d3e3;
            font-size: 18px;
            line-height: 1.7;
        }

        .proof-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 1px;
            max-width: 650px;
            margin-top: 34px;
            overflow: hidden;
            border: 1px solid rgba(219, 228, 239, .18);
            border-radius: 8px;
            background: rgba(219, 228, 239, .18);
        }

        .proof {
            min-height: 110px;
            padding: 18px;
            background: rgba(255, 255, 255, .08);
            backdrop-filter: blur(12px);
        }

        .proof strong { display: block; color: white; font-size: 24px; line-height: 1; }
        .proof span { display: block; margin-top: 8px; color: #c9d3e3; font-size: 13px; font-weight: 700; line-height: 1.4; }

        .auth-card {
            width: 100%;
            padding: 34px;
            border: 1px solid rgba(219, 228, 239, .92);
            border-radius: 8px;
            background: rgba(255, 255, 255, .97);
            box-shadow: 0 28px 80px rgba(0, 0, 0, .34);
            backdrop-filter: blur(18px);
        }

        .card-head { margin-bottom: 28px; }
        .card-head span { color: var(--green); font-size: 13px; font-weight: 900; text-transform: uppercase; letter-spacing: .08em; }
        .card-head h2 { margin: 8px 0 8px; font-size: 31px; line-height: 1.08; letter-spacing: 0; }
        .card-head p { margin: 0; color: var(--muted); line-height: 1.55; }

        .form-group { margin-bottom: 18px; }
        .form-group label {
            display: block;
            margin-bottom: 7px;
            color: #25364d;
            font-size: 14px;
            font-weight: 800;
        }

        .input-wrap { position: relative; }
        .input-wrap i {
            position: absolute;
            left: 13px;
            top: 50%;
            color: #7c8ba1;
            transform: translateY(-50%);
        }

        .input-wrap input {
            width: 100%;
            min-height: 46px;
            padding: 12px 14px 12px 40px;
            border: 1px solid #cfd9e6;
            border-radius: 8px;
            color: var(--ink);
            font: inherit;
            background: #ffffff;
            transition: border-color .16s ease, box-shadow .16s ease;
        }

        .input-wrap input:focus {
            outline: none;
            border-color: var(--gold);
            box-shadow: 0 0 0 4px rgba(245, 165, 36, .16);
        }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 14px;
            margin: 2px 0 22px;
            color: var(--muted);
            font-size: 14px;
            font-weight: 700;
        }

        .form-options label { display: inline-flex; align-items: center; gap: 8px; cursor: pointer; }
        input[type="checkbox"] { width: 16px; height: 16px; accent-color: var(--gold); }
        .form-options a, .switch-link a { color: var(--green); font-weight: 900; text-decoration: none; }

        .btn-auth {
            width: 100%;
            min-height: 48px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            border: 0;
            border-radius: 8px;
            color: #111827;
            background: var(--gold);
            font: inherit;
            font-weight: 900;
            cursor: pointer;
            box-shadow: 0 16px 30px rgba(245, 165, 36, .24);
            transition: transform .16s ease, background-color .16s ease;
        }

        .btn-auth:hover { background: var(--gold-dark); color: white; transform: translateY(-1px); }

        .switch-link {
            margin-top: 22px;
            padding-top: 20px;
            border-top: 1px solid var(--line);
            color: var(--muted);
            text-align: center;
            font-size: 14px;
            font-weight: 700;
        }

        .error-box {
            margin-bottom: 20px;
            padding: 13px 15px;
            border: 1px solid #fecaca;
            border-radius: 8px;
            color: #991b1b;
            background: #fef2f2;
            font-size: 14px;
        }

        .error-box ul { margin: 8px 0 0; padding-left: 20px; }

        @media (max-width: 900px) {
            .auth-shell {
                grid-template-columns: 1fr;
                align-items: start;
                gap: 30px;
                padding: 28px 0;
            }

            .brand-side h1 { font-size: 42px; }
            .brand-side p { font-size: 16px; }
            .auth-card { padding: 25px; }
        }

        @media (max-width: 560px) {
            .proof-grid { grid-template-columns: 1fr; }
            .form-options { flex-direction: column; align-items: flex-start; }
        }
    </style>
</head>
<body>
    <main class="auth-shell">
        <section class="brand-side">
            <a class="brand" href="{{ route('landing') }}">
                <span class="brand-mark"><i class="fas fa-warehouse"></i></span>
                <span>KTM-WDC</span>
            </a>
            <h1>Run warehouse work from one secure portal.</h1>
            <p>Access requests, dispatch orders, pickup jobs, documents, stock, invoices, reminders, and partner workflows without jumping between tools.</p>
            <div class="proof-grid" aria-label="Platform highlights">
                <div class="proof"><strong>Live</strong><span>Phase one portal published and ready to review.</span></div>
                <div class="proof"><strong>Roles</strong><span>Client, admin, driver, property, equipment, and security access.</span></div>
                <div class="proof"><strong>Secure</strong><span>Private documents, sessions, and billing flows.</span></div>
            </div>
        </section>

        <section class="auth-card">
            <div class="card-head">
                <span>Portal access</span>
                <h2>Welcome back.</h2>
                <p>Sign in to continue managing KTM-WDC operations.</p>
            </div>

            @if($errors->any())
            <div class="error-box">
                <strong>Please fix:</strong>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <div class="input-wrap">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" placeholder="you@company.com" required autofocus autocomplete="username">
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrap">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" id="password" placeholder="Enter your password" required autocomplete="current-password">
                    </div>
                </div>

                <div class="form-options">
                    <label>
                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        Remember me
                    </label>
                    <a href="{{ route('password.request') }}">Forgot password?</a>
                </div>

                <button type="submit" class="btn-auth">
                    <i class="fas fa-right-to-bracket"></i> Sign In
                </button>
            </form>

            <div class="switch-link">
                New to KTM-WDC? <a href="{{ route('register') }}">Create an account</a>
            </div>
        </section>
    </main>
</body>
</html>
