<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KTM-WDC | Secure Access</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --ink: #101724;
            --line: #dbe4ef;
            --gold: #f5a524;
        }

        * { box-sizing: border-box; }

        body {
            min-height: 100vh;
            margin: 0;
            color: var(--ink);
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background:
                linear-gradient(120deg, rgba(16, 23, 36, .92), rgba(16, 23, 36, .72)),
                url("{{ asset('images/landing-logistics.svg') }}") right bottom / min(720px, 68vw) auto no-repeat,
                #101724;
        }

        a { color: inherit; }

        .guest-wrap {
            width: min(1120px, calc(100% - 32px));
            min-height: 100vh;
            margin: 0 auto;
            display: grid;
            grid-template-columns: minmax(280px, 1fr) minmax(320px, 460px);
            gap: 46px;
            align-items: center;
            padding: 44px 0;
        }

        .brand-panel { color: white; }

        .brand {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 34px;
            text-decoration: none;
            font-weight: 900;
            font-size: 22px;
        }

        .brand-icon {
            width: 46px;
            height: 46px;
            display: grid;
            place-items: center;
            border-radius: 8px;
            color: #101724;
            background: var(--gold);
            box-shadow: 0 18px 36px rgba(245, 165, 36, .22);
        }

        .brand-panel h1 {
            max-width: 640px;
            margin: 0 0 18px;
            font-size: clamp(38px, 6vw, 68px);
            line-height: .98;
            letter-spacing: 0;
        }

        .brand-panel p {
            max-width: 560px;
            margin: 0;
            color: #c9d3e3;
            font-size: 18px;
            line-height: 1.7;
        }

        .guest-card {
            width: 100%;
            padding: 34px;
            border: 1px solid rgba(219, 228, 239, .9);
            border-radius: 8px;
            background: rgba(255, 255, 255, .96);
            box-shadow: 0 28px 80px rgba(0, 0, 0, .34);
            backdrop-filter: blur(18px);
        }

        .guest-card input,
        .guest-card select,
        .guest-card textarea {
            width: 100%;
            min-height: 44px;
            border: 1px solid #cfd9e6;
            border-radius: 8px;
            padding: 10px 13px;
        }

        .guest-card input:focus,
        .guest-card select:focus,
        .guest-card textarea:focus {
            outline: none;
            border-color: var(--gold);
            box-shadow: 0 0 0 4px rgba(245, 165, 36, .16);
        }

        .guest-card button,
        .guest-card .inline-flex {
            border-radius: 8px !important;
        }

        .guest-card button[type="submit"],
        .guest-card .bg-gray-800 {
            color: #101724 !important;
            background: var(--gold) !important;
            font-weight: 900 !important;
        }

        @media (max-width: 860px) {
            .guest-wrap {
                grid-template-columns: 1fr;
                gap: 28px;
                align-items: start;
                padding: 28px 0;
            }

            .brand-panel h1 { font-size: 42px; }
            .brand-panel p { font-size: 16px; }
            .guest-card { padding: 24px; }
        }
    </style>
</head>
<body>
    <main class="guest-wrap">
        <section class="brand-panel">
            <a class="brand" href="{{ route('landing') }}">
                <span class="brand-icon"><i class="fas fa-warehouse"></i></span>
                <span>KTM-WDC</span>
            </a>
            <h1>Secure logistics access for every team.</h1>
            <p>Warehouse requests, dispatch, invoices, documents, and partner work stay connected behind a private portal.</p>
        </section>

        <section class="guest-card">
            {{ $slot }}
        </section>
    </main>
</body>
</html>
