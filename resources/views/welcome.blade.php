<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KTM-WDC | Warehouse & Distribution Connect</title>
    <meta name="description" content="KTM-WDC connects warehouse storage, dispatch, pickup requests, invoices, fleet operations, and field teams in one logistics platform.">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --ink: #101724;
            --muted: #62728a;
            --line: #dbe4ef;
            --gold: #f5a524;
            --green: #0f766e;
            --blue: #2563eb;
            --paper: #ffffff;
            --soft: #f4f7fb;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            color: var(--ink);
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: var(--paper);
        }

        a { color: inherit; text-decoration: none; }

        .shell {
            width: min(1180px, calc(100% - 36px));
            margin: 0 auto;
        }

        .site-header {
            position: fixed;
            inset: 0 0 auto;
            z-index: 20;
            border-bottom: 1px solid rgba(219, 228, 239, .18);
            background: rgba(16, 23, 36, .78);
            backdrop-filter: blur(18px);
        }

        .nav {
            min-height: 76px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            color: white;
        }

        .brand {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            font-weight: 900;
            font-size: 20px;
        }

        .brand-mark {
            width: 44px;
            height: 44px;
            display: grid;
            place-items: center;
            border-radius: 8px;
            color: #111827;
            background: var(--gold);
            box-shadow: 0 14px 30px rgba(245, 165, 36, .26);
        }

        .brand small {
            display: block;
            color: #c9d3e3;
            font-size: 12px;
            font-weight: 800;
            margin-top: 2px;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 24px;
            color: #d8e0eb;
            font-size: 14px;
            font-weight: 800;
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn {
            min-height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            padding: 0 16px;
            border: 1px solid rgba(219, 228, 239, .22);
            border-radius: 8px;
            font-size: 14px;
            font-weight: 900;
            white-space: nowrap;
        }

        .btn-primary {
            color: #111827;
            border-color: var(--gold);
            background: var(--gold);
            box-shadow: 0 15px 30px rgba(245, 165, 36, .24);
        }

        .btn-quiet {
            color: white;
            background: rgba(255, 255, 255, .08);
        }

        .hero {
            min-height: 92vh;
            display: grid;
            align-items: center;
            padding: 132px 0 76px;
            color: white;
            background:
                linear-gradient(90deg, rgba(16, 23, 36, .96) 0%, rgba(16, 23, 36, .86) 43%, rgba(16, 23, 36, .50) 100%),
                url("{{ asset('images/landing-logistics.svg') }}") right bottom / min(820px, 62vw) auto no-repeat,
                #101724;
        }

        .hero-copy {
            max-width: 780px;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            padding: 8px 11px;
            border: 1px solid rgba(219, 228, 239, .2);
            border-radius: 8px;
            color: #d9f99d;
            background: rgba(15, 118, 110, .16);
            font-size: 13px;
            font-weight: 900;
        }

        h1 {
            margin: 24px 0 18px;
            max-width: 820px;
            font-size: clamp(50px, 8vw, 92px);
            line-height: .92;
            letter-spacing: 0;
        }

        .lead {
            max-width: 690px;
            margin: 0;
            color: #d7e0ec;
            font-size: 20px;
            line-height: 1.7;
        }

        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 32px;
        }

        .hero-strip {
            margin-top: 42px;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 1px;
            max-width: 720px;
            overflow: hidden;
            border: 1px solid rgba(219, 228, 239, .18);
            border-radius: 8px;
            background: rgba(219, 228, 239, .18);
        }

        .hero-stat {
            min-height: 104px;
            padding: 18px;
            background: rgba(255, 255, 255, .08);
            backdrop-filter: blur(12px);
        }

        .hero-stat strong { display: block; color: white; font-size: 26px; line-height: 1; }
        .hero-stat span { display: block; margin-top: 8px; color: #c9d3e3; font-size: 13px; font-weight: 800; line-height: 1.4; }

        .section {
            padding: 78px 0;
            border-bottom: 1px solid var(--line);
        }

        .section.soft { background: var(--soft); }

        .section-head {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 28px;
            margin-bottom: 32px;
        }

        .section h2 {
            max-width: 620px;
            margin: 0;
            font-size: clamp(32px, 4.6vw, 52px);
            line-height: 1;
            letter-spacing: 0;
        }

        .section-head p {
            max-width: 520px;
            margin: 0;
            color: var(--muted);
            line-height: 1.7;
        }

        .service-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
        }

        .service {
            min-height: 236px;
            padding: 24px;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: white;
            box-shadow: 0 14px 34px rgba(15, 23, 42, .07);
        }

        .service i,
        .flow-step i {
            width: 44px;
            height: 44px;
            display: grid;
            place-items: center;
            margin-bottom: 18px;
            border-radius: 8px;
            color: white;
            background: var(--green);
        }

        .service:nth-child(2) i { background: var(--blue); }
        .service:nth-child(3) i { color: #111827; background: var(--gold); }
        .service:nth-child(4) i { background: #7c3aed; }
        .service h3 { margin: 0 0 10px; font-size: 19px; }
        .service p { margin: 0; color: var(--muted); line-height: 1.6; font-size: 14px; }

        .ops-grid {
            display: grid;
            grid-template-columns: 1.15fr .85fr;
            gap: 18px;
            align-items: stretch;
        }

        .ops-board {
            border: 1px solid var(--line);
            border-radius: 8px;
            overflow: hidden;
            background: white;
            box-shadow: 0 14px 34px rgba(15, 23, 42, .07);
        }

        .board-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 17px 20px;
            border-bottom: 1px solid var(--line);
            background: #f8fafc;
            font-weight: 900;
        }

        .live {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 7px 10px;
            border-radius: 999px;
            color: #065f46;
            background: #d1fae5;
            font-size: 12px;
            font-weight: 900;
        }

        .ops-row {
            display: grid;
            grid-template-columns: 44px 1fr auto;
            gap: 14px;
            align-items: center;
            padding: 18px 20px;
            border-bottom: 1px solid #edf2f7;
        }

        .ops-row:last-child { border-bottom: 0; }
        .ops-row i { width: 44px; height: 44px; display: grid; place-items: center; border-radius: 8px; color: white; background: var(--green); }
        .ops-row:nth-child(3) i { background: var(--blue); }
        .ops-row:nth-child(4) i { color: #111827; background: var(--gold); }
        .ops-row strong { display: block; font-size: 15px; }
        .ops-row span { display: block; margin-top: 4px; color: var(--muted); font-size: 13px; line-height: 1.4; }
        .status { color: var(--green); font-size: 12px; font-weight: 900; text-align: right; }

        .flow {
            display: grid;
            gap: 12px;
        }

        .flow-step {
            min-height: 120px;
            padding: 20px;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: white;
        }

        .flow-step small { color: var(--green); font-weight: 900; }
        .flow-step h3 { margin: 8px 0 7px; }
        .flow-step p { margin: 0; color: var(--muted); line-height: 1.55; font-size: 14px; }

        .cta-band {
            padding: 50px 0;
            color: white;
            background: #101724;
        }

        .cta-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
        }

        .cta-inner h2 {
            margin: 0;
            font-size: clamp(30px, 4vw, 46px);
            line-height: 1.05;
        }

        .cta-inner p { margin: 10px 0 0; color: #c9d3e3; line-height: 1.6; }
        footer { padding: 28px 0; color: #63728a; background: white; font-size: 14px; }
        .footer-inner { display: flex; justify-content: space-between; gap: 16px; }

        @media (max-width: 980px) {
            .nav-links { display: none; }
            .hero { background:
                linear-gradient(180deg, rgba(16, 23, 36, .96), rgba(16, 23, 36, .80)),
                url("{{ asset('images/landing-logistics.svg') }}") center bottom / 720px auto no-repeat,
                #101724;
                padding-bottom: 260px;
            }
            .service-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .section-head, .cta-inner, .footer-inner { align-items: flex-start; flex-direction: column; }
            .ops-grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 640px) {
            .shell { width: min(100% - 28px, 1180px); }
            .brand small, .btn-quiet, .nav-actions .btn:first-child { display: none; }
            .nav { min-height: 68px; }
            .hero { min-height: auto; padding-top: 108px; padding-bottom: 220px; }
            h1 { font-size: 48px; }
            .lead { font-size: 17px; }
            .hero-strip, .service-grid { grid-template-columns: 1fr; }
            .ops-row { grid-template-columns: 44px 1fr; }
            .status { grid-column: 2; text-align: left; }
        }
    </style>
</head>
<body>
    <header class="site-header">
        <div class="shell nav">
            <a class="brand" href="{{ route('landing') }}" aria-label="KTM-WDC home">
                <span class="brand-mark"><i class="fas fa-warehouse"></i></span>
                <span>KTM-WDC<small>Warehouse & Distribution Connect</small></span>
            </a>
            <nav class="nav-links" aria-label="Public navigation">
                <a href="#services">Services</a>
                <a href="#operations">Operations</a>
                <a href="#process">Process</a>
            </nav>
            <div class="nav-actions">
                <a class="btn btn-quiet" href="{{ route('login') }}"><i class="fas fa-right-to-bracket"></i> Login</a>
                <a class="btn btn-primary" href="{{ route('register') }}"><i class="fas fa-user-plus"></i> Register</a>
            </div>
        </div>
    </header>

    <main>
        <section class="hero">
            <div class="shell hero-copy">
                <span class="eyebrow"><i class="fas fa-circle"></i> Phase 1 published on Render</span>
                <h1>KTM-WDC</h1>
                <p class="lead">Warehouse & Distribution Connect brings storage requests, dispatch, pickup, inventory, invoices, documents, reminders, and field teams into one polished logistics portal.</p>
                <div class="hero-actions">
                    <a class="btn btn-primary" href="{{ route('register') }}"><i class="fas fa-user-plus"></i> Create account</a>
                    <a class="btn btn-quiet" href="{{ route('login') }}"><i class="fas fa-right-to-bracket"></i> Login to portal</a>
                </div>
                <div class="hero-strip" aria-label="Platform highlights">
                    <div class="hero-stat"><strong>1</strong><span>Connected portal for warehouse, fleet, stock, and billing.</span></div>
                    <div class="hero-stat"><strong>24/7</strong><span>Request intake, reminders, notifications, and visibility.</span></div>
                    <div class="hero-stat"><strong>Secure</strong><span>Private documents, sessions, invoices, and role access.</span></div>
                </div>
            </div>
        </section>

        <section class="section" id="services">
            <div class="shell">
                <div class="section-head">
                    <h2>Built for real logistics workflows.</h2>
                    <p>The public site now presents KTM-WDC like a company platform first, while the portal handles the actual daily work behind login.</p>
                </div>
                <div class="service-grid">
                    <article class="service"><i class="fas fa-warehouse"></i><h3>Warehouse requests</h3><p>Clients request storage, teams manage properties, and warehouse records stay organized.</p></article>
                    <article class="service"><i class="fas fa-truck-fast"></i><h3>Dispatch & pickup</h3><p>Coordinate pickup jobs, delivery orders, stops, drivers, and live movement updates.</p></article>
                    <article class="service"><i class="fas fa-boxes-stacked"></i><h3>Stock & documents</h3><p>Track boxes, goods, insurance files, vehicle records, and warehouse documentation.</p></article>
                    <article class="service"><i class="fas fa-file-invoice-dollar"></i><h3>Invoices & reports</h3><p>Keep payment sessions, receipts, invoice verification, margins, and reports connected.</p></article>
                </div>
            </div>
        </section>

        <section class="section soft" id="operations">
            <div class="shell ops-grid">
                <div class="ops-board">
                    <div class="board-head">
                        <span>Operations Snapshot</span>
                        <span class="live"><i class="fas fa-circle"></i> Live preview</span>
                    </div>
                    <div class="ops-row"><i class="fas fa-clipboard-list"></i><div><strong>Requests enter the platform</strong><span>Clients, property owners, drivers, equipment partners, and agencies use role-aware flows.</span></div><span class="status">Received</span></div>
                    <div class="ops-row"><i class="fas fa-route"></i><div><strong>Teams coordinate work</strong><span>Dispatch, pickup, stock, tracking, invoices, and notifications stay in one place.</span></div><span class="status">Coordinated</span></div>
                    <div class="ops-row"><i class="fas fa-chart-line"></i><div><strong>Management gets visibility</strong><span>Approvals, reports, reminders, earnings, analytics, and partner records are surfaced.</span></div><span class="status">Visible</span></div>
                </div>

                <div class="flow" id="process">
                    <article class="flow-step"><small>01 / Receive</small><h3>Capture the request</h3><p>Customers and partners submit the information operations need to start work.</p></article>
                    <article class="flow-step"><small>02 / Assign</small><h3>Move it through teams</h3><p>Admins, warehouse staff, drivers, security teams, and owners stay aligned by role.</p></article>
                    <article class="flow-step"><small>03 / Close</small><h3>Keep the record</h3><p>Invoices, documents, reminders, and reporting remain tied to the operational activity.</p></article>
                </div>
            </div>
        </section>

        <section class="cta-band">
            <div class="shell cta-inner">
                <div>
                    <h2>Enter the KTM-WDC portal.</h2>
                    <p>Use your account to manage requests, warehouse records, dispatch, invoices, documents, and assigned work.</p>
                </div>
                <div class="hero-actions">
                    <a class="btn btn-primary" href="{{ route('register') }}"><i class="fas fa-user-plus"></i> Register</a>
                    <a class="btn btn-quiet" href="{{ route('login') }}"><i class="fas fa-right-to-bracket"></i> Login</a>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <div class="shell footer-inner">
            <span>&copy; {{ date('Y') }} KTM-WDC. Warehouse & Distribution Connect.</span>
            <span>Phase 1 published on Render.</span>
        </div>
    </footer>
</body>
</html>
