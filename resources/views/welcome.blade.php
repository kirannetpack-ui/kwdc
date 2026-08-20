<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KTM-WDC | Warehouse & Distribution Connect</title>
    <meta name="description" content="KTM-WDC connects warehouse storage, dispatch, pickup requests, invoices, fleet operations, and field teams in one logistics platform.">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --ink: #172033;
            --muted: #64748b;
            --line: #dbe3ee;
            --amber: #f59e0b;
            --amber-dark: #b45309;
            --teal: #0f766e;
            --blue: #1d4ed8;
            --paper: #ffffff;
            --soft: #f6f8fb;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            color: var(--ink);
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: var(--paper);
        }
        a { color: inherit; text-decoration: none; }
        .site-header {
            position: sticky;
            top: 0;
            z-index: 10;
            border-bottom: 1px solid rgba(219, 227, 238, .9);
            background: rgba(255, 255, 255, .94);
            backdrop-filter: blur(14px);
        }
        .shell { width: min(1160px, calc(100% - 40px)); margin: 0 auto; }
        .nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-height: 74px;
            gap: 18px;
        }
        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 800;
        }
        .brand-mark {
            width: 44px;
            height: 44px;
            display: grid;
            place-items: center;
            border-radius: 8px;
            color: white;
            background: linear-gradient(135deg, var(--amber), var(--teal));
            box-shadow: 0 10px 24px rgba(15, 118, 110, .22);
        }
        .brand small {
            display: block;
            color: var(--muted);
            font-size: 12px;
            font-weight: 700;
            margin-top: 1px;
        }
        .nav-links {
            display: flex;
            align-items: center;
            gap: 22px;
            color: #40506a;
            font-size: 14px;
            font-weight: 700;
        }
        .nav-actions { display: flex; align-items: center; gap: 10px; }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            min-height: 42px;
            padding: 0 16px;
            border-radius: 8px;
            border: 1px solid var(--line);
            font-size: 14px;
            font-weight: 800;
            white-space: nowrap;
        }
        .btn-primary {
            color: #111827;
            border-color: var(--amber);
            background: var(--amber);
            box-shadow: 0 12px 28px rgba(245, 158, 11, .25);
        }
        .btn-quiet { background: white; }
        .hero {
            min-height: calc(100vh - 74px);
            display: grid;
            align-items: center;
            padding: 56px 0 34px;
            background:
                linear-gradient(90deg, rgba(255,255,255,.98) 0%, rgba(255,255,255,.9) 46%, rgba(246,248,251,.72) 100%),
                url("{{ asset('images/landing-logistics.svg') }}") right center / min(58vw, 760px) auto no-repeat;
            border-bottom: 1px solid var(--line);
        }
        .hero-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(320px, 480px);
            gap: 44px;
            align-items: center;
        }
        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            padding: 8px 11px;
            border: 1px solid #d9eadf;
            border-radius: 8px;
            color: #166534;
            background: #f0fdf4;
            font-size: 13px;
            font-weight: 800;
        }
        h1 {
            max-width: 760px;
            margin: 22px 0 18px;
            font-size: clamp(42px, 7vw, 76px);
            line-height: .96;
            letter-spacing: 0;
        }
        .lead {
            max-width: 660px;
            color: #46566f;
            font-size: 20px;
            line-height: 1.65;
        }
        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 30px;
        }
        .stat-strip {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 1px;
            max-width: 680px;
            margin-top: 34px;
            overflow: hidden;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: var(--line);
        }
        .stat { min-height: 94px; padding: 18px; background: white; }
        .stat strong { display: block; color: #0f172a; font-size: 26px; line-height: 1; }
        .stat span {
            display: block;
            margin-top: 8px;
            color: var(--muted);
            font-size: 13px;
            font-weight: 700;
            line-height: 1.35;
        }
        .ops-panel {
            border: 1px solid rgba(203, 213, 225, .92);
            border-radius: 8px;
            background: rgba(255,255,255,.9);
            box-shadow: 0 24px 70px rgba(15, 23, 42, .14);
            overflow: hidden;
        }
        .panel-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            padding: 16px 18px;
            border-bottom: 1px solid var(--line);
            background: #f8fafc;
        }
        .panel-head strong { font-size: 15px; }
        .live-pill {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 6px 9px;
            border-radius: 999px;
            color: #065f46;
            background: #d1fae5;
            font-size: 12px;
            font-weight: 900;
        }
        .panel-body { padding: 18px; }
        .route-row {
            display: grid;
            grid-template-columns: 38px 1fr auto;
            gap: 12px;
            align-items: center;
            padding: 14px 0;
            border-bottom: 1px solid #edf2f7;
        }
        .route-row:last-child { border-bottom: 0; }
        .icon-tile {
            width: 38px;
            height: 38px;
            display: grid;
            place-items: center;
            border-radius: 8px;
            color: white;
            background: var(--teal);
        }
        .route-row:nth-child(2) .icon-tile { background: var(--blue); }
        .route-row:nth-child(3) .icon-tile { background: var(--amber-dark); }
        .route-row strong { display: block; font-size: 14px; }
        .route-row span {
            display: block;
            margin-top: 4px;
            color: var(--muted);
            font-size: 13px;
        }
        .status {
            color: #0f766e;
            font-size: 12px;
            font-weight: 900;
            text-align: right;
        }
        .section { padding: 74px 0; border-bottom: 1px solid var(--line); }
        .section.soft { background: var(--soft); }
        .section-head {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 24px;
            margin-bottom: 30px;
        }
        .section h2 {
            margin: 0;
            font-size: clamp(30px, 4vw, 46px);
            line-height: 1.05;
            letter-spacing: 0;
        }
        .section-head p {
            max-width: 500px;
            margin: 0;
            color: var(--muted);
            line-height: 1.65;
        }
        .service-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
        }
        .service {
            min-height: 220px;
            padding: 22px;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: white;
        }
        .service i {
            width: 42px;
            height: 42px;
            display: grid;
            place-items: center;
            margin-bottom: 18px;
            border-radius: 8px;
            color: white;
            background: #1f2937;
        }
        .service h3 { margin: 0 0 10px; font-size: 18px; }
        .service p { margin: 0; color: var(--muted); line-height: 1.55; font-size: 14px; }
        .process { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 18px; }
        .step { padding: 24px; border-left: 4px solid var(--amber); background: white; }
        .step span { color: var(--teal); font-weight: 900; font-size: 13px; }
        .step h3 { margin: 10px 0 8px; font-size: 20px; }
        .step p { margin: 0; color: var(--muted); line-height: 1.6; }
        .cta-band { padding: 42px 0; color: white; background: #111827; }
        .cta-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 22px;
        }
        .cta-inner h2 { margin: 0; font-size: clamp(28px, 4vw, 42px); line-height: 1.05; }
        .cta-inner p { margin: 10px 0 0; color: #cbd5e1; line-height: 1.55; }
        footer { padding: 28px 0; color: #64748b; background: white; font-size: 14px; }
        .footer-inner { display: flex; justify-content: space-between; gap: 16px; }
        @media (max-width: 960px) {
            .nav-links { display: none; }
            .hero {
                min-height: auto;
                background:
                    linear-gradient(180deg, rgba(255,255,255,.98), rgba(246,248,251,.86)),
                    url("{{ asset('images/landing-logistics.svg') }}") center bottom / 680px auto no-repeat;
                padding-bottom: 260px;
            }
            .hero-grid { grid-template-columns: 1fr; }
            .ops-panel { max-width: 560px; }
            .service-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .process { grid-template-columns: 1fr; }
            .section-head, .cta-inner, .footer-inner { align-items: flex-start; flex-direction: column; }
        }
        @media (max-width: 640px) {
            .shell { width: min(100% - 28px, 1160px); }
            .nav { min-height: 66px; }
            .brand small, .btn-quiet { display: none; }
            .hero { padding-top: 34px; padding-bottom: 220px; }
            h1 { font-size: 42px; }
            .lead { font-size: 17px; }
            .stat-strip, .service-grid { grid-template-columns: 1fr; }
            .route-row { grid-template-columns: 38px 1fr; }
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
            <div class="shell hero-grid">
                <div>
                    <span class="eyebrow"><i class="fas fa-location-dot"></i> Kathmandu logistics, connected</span>
                    <h1>Storage, dispatch, pickup, and delivery in one control center.</h1>
                    <p class="lead">KTM-WDC gives businesses, drivers, equipment owners, warehouse teams, and clients a shared place to manage requests, inventory, invoices, documents, and field movement.</p>
                    <div class="hero-actions">
                        <a class="btn btn-primary" href="{{ route('register') }}"><i class="fas fa-user-plus"></i> Create account</a>
                        <a class="btn btn-quiet" href="{{ route('login') }}"><i class="fas fa-right-to-bracket"></i> Login to portal</a>
                    </div>
                    <div class="stat-strip" aria-label="Platform highlights">
                        <div class="stat"><strong>24/7</strong><span>Request intake and operational visibility</span></div>
                        <div class="stat"><strong>1</strong><span>Platform for warehouse, fleet, and billing</span></div>
                        <div class="stat"><strong>Secure</strong><span>Private documents, sessions, and invoices</span></div>
                    </div>
                </div>

                <aside class="ops-panel" id="operations" aria-label="Operations snapshot">
                    <div class="panel-head">
                        <strong>Operations Snapshot</strong>
                        <span class="live-pill"><i class="fas fa-circle"></i> Phase 1 live</span>
                    </div>
                    <div class="panel-body">
                        <div class="route-row">
                            <span class="icon-tile"><i class="fas fa-boxes-stacked"></i></span>
                            <div><strong>Warehouse Storage</strong><span>Stock, boxes, batch documents, and request status.</span></div>
                            <span class="status">Tracked</span>
                        </div>
                        <div class="route-row">
                            <span class="icon-tile"><i class="fas fa-truck-fast"></i></span>
                            <div><strong>Dispatch & Pickup</strong><span>Pickup requests, stops, drivers, and movement updates.</span></div>
                            <span class="status">Coordinated</span>
                        </div>
                        <div class="route-row">
                            <span class="icon-tile"><i class="fas fa-file-invoice-dollar"></i></span>
                            <div><strong>Billing & Payments</strong><span>Invoices, receipts, payment sessions, and verification flows.</span></div>
                            <span class="status">Prepared</span>
                        </div>
                    </div>
                </aside>
            </div>
        </section>

        <section class="section" id="services">
            <div class="shell">
                <div class="section-head">
                    <h2>Built for daily logistics work.</h2>
                    <p>Every part of the platform is focused on repeated operational tasks: receiving requests, assigning work, protecting documents, and keeping teams aligned.</p>
                </div>
                <div class="service-grid">
                    <article class="service">
                        <i class="fas fa-warehouse"></i>
                        <h3>Warehouse requests</h3>
                        <p>Clients can request storage, teams can manage warehouse records, and private documents stay controlled.</p>
                    </article>
                    <article class="service">
                        <i class="fas fa-route"></i>
                        <h3>Dispatch tracking</h3>
                        <p>Pickup, delivery, stops, driver assignment, and route activity are organized in one operational flow.</p>
                    </article>
                    <article class="service">
                        <i class="fas fa-hard-hat"></i>
                        <h3>Equipment services</h3>
                        <p>Equipment requests, owner workflows, job records, vehicle documents, and partner operations are connected.</p>
                    </article>
                    <article class="service">
                        <i class="fas fa-shield-halved"></i>
                        <h3>Secure operations</h3>
                        <p>Role-based access, hardened sessions, invoice verification, and document privacy are part of the foundation.</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="section soft" id="process">
            <div class="shell">
                <div class="section-head">
                    <h2>From request to resolution.</h2>
                    <p>KTM-WDC is designed to help operations move cleanly from a customer request into accountable work, billing, and reporting.</p>
                </div>
                <div class="process">
                    <article class="step">
                        <span>01 / Receive</span>
                        <h3>Clients submit requests</h3>
                        <p>Storage, pickup, equipment, and service requests enter the same managed system instead of scattered messages.</p>
                    </article>
                    <article class="step">
                        <span>02 / Coordinate</span>
                        <h3>Teams assign and track</h3>
                        <p>Warehouse, dispatch, driver, agency, and admin roles get the tools they need to keep work visible.</p>
                    </article>
                    <article class="step">
                        <span>03 / Close</span>
                        <h3>Invoices and records stay linked</h3>
                        <p>Documents, invoices, payment records, reminders, and reports remain tied to the operational work.</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="cta-band">
            <div class="shell cta-inner">
                <div>
                    <h2>Ready to enter the portal?</h2>
                    <p>Use your account to access requests, dispatch, warehouse records, invoices, and assigned work.</p>
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
