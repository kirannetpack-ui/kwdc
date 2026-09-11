<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="KTM-WDC connects warehouse space, deliveries and the teams that keep your business moving in Nepal.">
    <title>KTM-WDC | Warehouse & Distribution Connect</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/kwdc-public.css') }}">
</head>
<body>
    <a class="skip-link" href="#main">Skip to content</a>
    <header class="site-header">
        <nav class="public-nav shell" aria-label="Main navigation">
            <a class="wordmark" href="{{ route('landing') }}"><i class="fas fa-warehouse" aria-hidden="true"></i> KTM-WDC</a>
            <div class="nav-sections"><a href="#services">Services</a><a href="#partners">Partners</a></div>
            <div class="nav-actions"><a href="{{ route('login') }}">Log in</a><a class="button button-dark" href="{{ route('register') }}">Get started <i class="fas fa-arrow-right" aria-hidden="true"></i></a></div>
        </nav>
    </header>
    <main id="main">
        <section class="hero">
            <img class="hero-image" src="{{ asset('images/logistics-campus.png') }}" alt="Illustrated warehouse campus with delivery vehicles" fetchpriority="high" width="1536" height="1024">
            <div class="hero-content shell">
                <span class="eyebrow">Warehouse & Distribution Connect</span>
                <h1>KTM-WDC</h1>
                <p class="hero-title">Space to grow.<br>Room to move.</p>
                <p class="hero-copy">Warehousing, deliveries and trusted teams.<br>Your business, connected across Nepal.</p>
                <a class="button button-dark" href="{{ route('register') }}">Find your workspace <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
            </div>
            <span class="illustration-credit">Concept illustration</span>
        </section>
        <section class="services shell" id="services">
            <div class="section-heading"><div><span class="eyebrow">Connected operations</span><h2>Every move, in one place.</h2></div><a class="text-link" href="{{ route('login') }}">Open your portal <i class="fas fa-arrow-right" aria-hidden="true"></i></a></div>
            <div class="service-grid">
                <article><span class="service-icon green"><i class="fas fa-warehouse" aria-hidden="true"></i></span><h3>Warehouse space</h3><p>Find space, request capacity and manage your stock.</p><a href="{{ route('register', ['role' => 'client']) }}">Explore warehousing <i class="fas fa-arrow-right" aria-hidden="true"></i></a></article>
                <article><span class="service-icon gold"><i class="fas fa-truck" aria-hidden="true"></i></span><h3>Pickup & delivery</h3><p>Connect locations, assign drivers and follow each job.</p><a href="{{ route('register', ['role' => 'client']) }}">Plan a delivery <i class="fas fa-arrow-right" aria-hidden="true"></i></a></article>
                <article><span class="service-icon blue"><i class="fas fa-boxes-stacked" aria-hidden="true"></i></span><h3>Equipment & teams</h3><p>Coordinate equipment and security for your operations.</p><a href="{{ route('register') }}">Connect your team <i class="fas fa-arrow-right" aria-hidden="true"></i></a></article>
            </div>
        </section>
        <section class="partners" id="partners">
            <div class="shell partner-layout"><div><span class="eyebrow">Work with KTM-WDC</span><h2>Your expertise.<br>A shared workspace.</h2><p>Choose your role and keep the work moving.</p></div>
                <div class="partner-list">
                    @foreach(['driver' => ['Drivers', 'fa-truck-fast'], 'property_owner' => ['Warehouse owners', 'fa-building'], 'equipment_owner' => ['Equipment owners', 'fa-tractor'], 'security_agency' => ['Security agencies', 'fa-shield-halved']] as $role => $details)
                    <a href="{{ route('register', ['role' => $role]) }}"><i class="fas {{ $details[1] }}" aria-hidden="true"></i><span>{{ $details[0] }}</span><i class="fas fa-arrow-up-right-from-square" aria-hidden="true"></i></a>
                    @endforeach
                </div>
            </div>
        </section>
        <section class="closing shell"><h2>Ready for your next move?</h2><a class="button button-dark" href="{{ route('register') }}">Create an account <i class="fas fa-arrow-right" aria-hidden="true"></i></a></section>
    </main>
    <footer class="shell"><a class="wordmark" href="{{ route('landing') }}">KTM-WDC</a><span>&copy; {{ date('Y') }} Warehouse & Distribution Connect</span><a href="{{ route('login') }}">Partner login</a></footer>
</body>
</html>
