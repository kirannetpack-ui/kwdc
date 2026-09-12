<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="KTM-WDC connects commercial warehouse space, freight deliveries, heavy equipment, and trusted teams across Nepal.">
    <title>KTM-WDC | Warehouse & Distribution Connect</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/kwdc-public.css') }}">
    <style>

        .footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 40px;
            padding: 60px 0 30px 0;
        }
        @media (max-width: 768px) {
            .footer-grid { grid-template-columns: 1fr; gap: 30px; }
        }
        .footer-col h4 {
            font-size: 13px;
            font-weight: 700;
            color: #1e293b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 16px;
        }
        .footer-col ul {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .footer-col ul a {
            color: #64748b;
            text-decoration: none;
            font-size: 13.5px;
            transition: color 0.15s;
        }
        .footer-col ul a:hover {
            color: #ea580c;
        }
        .footer-bottom {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 24px 0 40px 0;
            border-top: 1px solid #e2e8f0;
            font-size: 12px;
            color: #94a3b8;
            flex-wrap: wrap;
            gap: 16px;
        }
    </style>
</head>
<body>
    <a class="skip-link" href="#main">Skip to content</a>
    <header class="site-header">
        <nav class="public-nav shell" aria-label="Main navigation">
            <a class="wordmark" href="{{ route('landing') }}"><i class="fas fa-warehouse text-orange-500" aria-hidden="true"></i> KTM-WDC</a>
            <div class="nav-sections">
                <a href="#services">Services</a>
                <a href="#partners">Network Roles</a>
                <a href="{{ route('compliance') }}">Compliance</a>
                <a href="{{ route('privacy-policy') }}">Legal</a>
            </div>
            <div class="nav-actions">
                <a href="{{ route('login') }}">Log in</a>
                <a class="button button-dark" href="{{ route('register') }}">Get started <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
            </div>
        </nav>
    </header>

    <main id="main">
        <section class="hero">
            <img class="hero-image" src="{{ asset('images/logistics-campus.png') }}" alt="Illustrated warehouse campus with delivery vehicles" fetchpriority="high" width="1536" height="1024">
            <div class="hero-content shell">
                <span class="eyebrow">Warehouse &amp; Distribution Connect</span>
                <h1>KTM-WDC</h1>
                <p class="hero-title">Space to grow.<br>Room to move.</p>
                <p class="hero-copy">Commercial warehousing, live freight dispatch and trusted fleet operations across Nepal.</p>
                <div style="display: flex; gap: 12px; flex-wrap: wrap; margin-top: 24px;">
                    <a class="button button-dark" href="{{ route('register') }}">Find your workspace <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
                    <a class="button" style="background:#ffffff; color:#0f172a; border:1px solid #cbd5e1;" href="{{ route('login') }}">Partner Portal <i class="fas fa-sign-in-alt" aria-hidden="true"></i></a>
                </div>
            </div>
            <span class="illustration-credit">Commercial Logistics Network</span>
        </section>


        <section class="services shell" id="services">
            <div class="section-heading">
                <div>
                    <span class="eyebrow">Integrated Operations</span>
                    <h2>Every move, in one place.</h2>
                </div>
                <a class="text-link" href="{{ route('login') }}">Open your portal <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
            </div>
            <div class="service-grid">
                <article>
                    <span class="service-icon green"><i class="fas fa-warehouse" aria-hidden="true"></i></span>
                    <h3>Commercial Storage</h3>
                    <p>Verified dry &amp; cold storage with loading docks, CCTV, and instant lease booking.</p>
                    <a href="{{ route('register', ['role' => 'client']) }}">Explore warehousing <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
                </article>
                <article>
                    <span class="service-icon gold"><i class="fas fa-truck" aria-hidden="true"></i></span>
                    <h3>Live Freight Dispatch</h3>
                    <p>Multi-stop route planning, assigned vetted drivers, and real-time GPS tracking.</p>
                    <a href="{{ route('register', ['role' => 'client']) }}">Plan a delivery <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
                </article>
                <article>
                    <span class="service-icon blue"><i class="fas fa-boxes-stacked" aria-hidden="true"></i></span>
                    <h3>Equipment &amp; Security</h3>
                    <p>Industrial forklifts, certified operators, and on-duty security guard posts.</p>
                    <a href="{{ route('register') }}">Connect your team <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
                </article>
            </div>
        </section>

        <section class="partners" id="partners">
            <div class="shell partner-layout">
                <div>
                    <span class="eyebrow">Commercial Network</span>
                    <h2>Your expertise.<br>A shared workspace.</h2>
                    <p>Register as an enterprise partner or operational driver.</p>
                </div>
                <div class="partner-list">
                    @foreach(['driver' => ['Drivers & Fleet Owners', 'fa-truck-fast'], 'property_owner' => ['Warehouse Facility Owners', 'fa-building'], 'equipment_owner' => ['Machinery & Equipment Owners', 'fa-tractor'], 'security_agency' => ['Licensed Security Agencies', 'fa-shield-halved']] as $role => $details)
                    <a href="{{ route('register', ['role' => $role]) }}">
                        <i class="fas {{ $details[1] }}" aria-hidden="true"></i>
                        <span>{{ $details[0] }}</span>
                        <i class="fas fa-arrow-up-right-from-square" aria-hidden="true"></i>
                    </a>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="closing shell">
            <h2>Ready for your next move?</h2>
            <a class="button button-dark" href="{{ route('register') }}">Create an account <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
        </section>
    </main>

    <footer class="shell">
        <div class="footer-grid">
            <div class="footer-col">
                <a class="wordmark" href="{{ route('landing') }}"><i class="fas fa-warehouse text-orange-500"></i> KTM-WDC</a>
                <p style="color: #64748b; font-size: 13px; line-height: 1.6; margin-top: 12px;">
                    Nepal's premier integrated logistics platform connecting warehousing capacity, highway freight dispatch, certified equipment, and facility security.
                </p>
                <p style="color: #64748b; font-size: 12px; margin-top: 8px;">
                    <strong>Central Office:</strong> Tinkune &amp; Baluwatar Freight Corridors, Kathmandu, Nepal<br>
                    <strong>Helpline:</strong> +977-1-5912400 &bull; support@kwdc.test
                </p>
            </div>
            <div class="footer-col">
                <h4>Solutions</h4>
                <ul>
                    <li><a href="{{ route('register', ['role' => 'client']) }}">Warehouse Booking</a></li>
                    <li><a href="{{ route('register', ['role' => 'client']) }}">Live Freight Dispatch</a></li>
                    <li><a href="{{ route('register', ['role' => 'equipment_owner']) }}">Machinery Rental</a></li>
                    <li><a href="{{ route('register', ['role' => 'security_agency']) }}">Security Posts</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Network Partners</h4>
                <ul>
                    <li><a href="{{ route('register', ['role' => 'driver']) }}">Driver Registration</a></li>
                    <li><a href="{{ route('register', ['role' => 'property_owner']) }}">List Your Warehouse</a></li>
                    <li><a href="{{ route('login') }}">Partner Portal Login</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Legal &amp; Compliance</h4>
                <ul>
                    <li><a href="{{ route('privacy-policy') }}">Privacy Policy</a></li>
                    <li><a href="{{ route('terms-of-service') }}">Terms of Service</a></li>
                    <li><a href="{{ route('compliance') }}">Carrier &amp; Safety Compliance</a></li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <span>&copy; {{ date('Y') }} KTM-WDC (Warehouse &amp; Distribution Connect). All rights reserved.</span>
            <div style="display: flex; gap: 16px;">
                <a href="{{ route('privacy-policy') }}" style="color: inherit; text-decoration: none;">Privacy</a>
                <a href="{{ route('terms-of-service') }}" style="color: inherit; text-decoration: none;">Terms</a>
                <a href="{{ route('compliance') }}" style="color: inherit; text-decoration: none;">Compliance</a>
            </div>
        </div>
    </footer>
</body>
</html>
