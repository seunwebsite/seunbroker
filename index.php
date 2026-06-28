<?php
include 'config/config.php';
// Ensure config is loaded
if (!isset($link)) {
    // Basic fallback if db isn't connected
    $link = mysqli_connect("localhost", "root", "", "trading_db"); 
}
?>

<!DOCTYPE html>
<html lang="en" class="dark scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $sitename; ?> | Global Forex, Indices & Commodities Trading</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .glass-card { background: rgba(17, 24, 39, 0.7); backdrop-filter: blur(12px); border: 1px solid rgba(30, 41, 59, 0.5); }
        .gradient-text { background: linear-gradient(to right, #6366F1, #EC4899); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .market-ticker { overflow: hidden; white-space: nowrap; }
        .market-card{
    background:rgba(11,18,32,.85);
    backdrop-filter:blur(20px);
    border:1px solid rgba(255,255,255,.06);
    border-radius:24px;
    padding:24px;
    transition:.4s;
    overflow:hidden;
    position:relative;
}

.market-card:hover{
    transform:translateY(-8px);
    border-color:rgba(99,102,241,.5);
    box-shadow:0 20px 50px rgba(99,102,241,.15);
}

.chart-green{
    height:60px;
    border-radius:12px;
    background:
    linear-gradient(
        180deg,
        rgba(34,197,94,.25),
        rgba(34,197,94,0)
    );
    position:relative;
}

.chart-green::before{
    content:"";
    position:absolute;
    inset:0;
    background:url("data:image/svg+xml,%3Csvg viewBox='0 0 200 50' xmlns='http://www.w3.org/2000/svg'%3E%3Cpolyline fill='none' stroke='%2322c55e' stroke-width='3' points='0,45 20,40 40,38 60,28 80,30 100,20 120,22 140,14 160,10 180,12 200,5'/%3E%3C/svg%3E") center/cover;
}

.chart-red{
    height:60px;
    border-radius:12px;
    background:
    linear-gradient(
        180deg,
        rgba(239,68,68,.25),
        rgba(239,68,68,0)
    );
    position:relative;
}

.chart-red::before{
    content:"";
    position:absolute;
    inset:0;
    background:url("data:image/svg+xml,%3Csvg viewBox='0 0 200 50' xmlns='http://www.w3.org/2000/svg'%3E%3Cpolyline fill='none' stroke='%23ef4444' stroke-width='3' points='0,5 20,8 40,10 60,14 80,18 100,20 120,25 140,30 160,35 180,40 200,45'/%3E%3C/svg%3E") center/cover;
}
    </style>
</head>
<body class="bg-[#02040a] text-gray-200 antialiased">

    <!-- =========================
PREMIUM MARKET BAR
========================= -->

<div class="bg-[#010308] border-b border-white/5 overflow-hidden">


<div class="max-w-7xl mx-auto px-4">

    <div class="flex items-center justify-between h-10">

        <!-- Left -->
        <div class="hidden lg:flex items-center gap-6 text-xs">

            <span class="flex items-center gap-2 text-green-400 font-semibold">
                <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                Markets Open
            </span>

            <span class="text-gray-500">
                Global Markets • Forex • Crypto • Stocks • Commodities
            </span>

        </div>

        <!-- Center -->
        <div class="flex-1 overflow-hidden mx-4">

            <div id="market-ticker"
                 class="whitespace-nowrap text-xs font-medium text-gray-400">

                Loading live market data...

            </div>

        </div>

        <!-- Right -->
        <div class="hidden xl:flex items-center gap-5 text-xs">

            <span class="text-gray-500">
                Support: 24/7
            </span>

            <span class="text-gray-500">
                180+ Countries
            </span>

        </div>

    </div>

</div>

</div>

<!-- =========================
PREMIUM NAVIGATION
========================= -->

<nav class="sticky top-0 z-50 bg-[#02040a]/85 backdrop-blur-2xl border-b border-white/5">


<div class="max-w-7xl mx-auto px-5">

    <div class="h-24 flex items-center justify-between">

        <!-- Logo -->
        <a href="index.php" class="flex items-center gap-4">

            <div class="relative">

                <div class="absolute inset-0 bg-indigo-600 blur-xl opacity-50"></div>

                <div class="relative w-12 h-12 rounded-2xl bg-gradient-to-r from-indigo-600 to-cyan-500 flex items-center justify-center font-black text-white shadow-lg">

                    <?php echo strtoupper(substr($sitename,0,1)); ?>

                </div>

            </div>

            <div>

                <h2 class="text-white text-2xl font-black tracking-tight">
                    <?php echo $sitename; ?>
                </h2>

                <p class="text-[10px] uppercase tracking-[3px] text-gray-500">
                    Global Trading Platform
                </p>

            </div>

        </a>

        <!-- Desktop Menu -->
        <div class="hidden lg:flex items-center gap-10">

            <a href="#markets"
               class="text-sm font-medium text-gray-300 hover:text-white transition">
                Markets
            </a>

            <a href="#products"
               class="text-sm font-medium text-gray-300 hover:text-white transition">
                Products
            </a>

            <a href="#platforms"
               class="text-sm font-medium text-gray-300 hover:text-white transition">
                Platforms
            </a>

            <a href="#accounts"
               class="text-sm font-medium text-gray-300 hover:text-white transition">
                Accounts
            </a>

            <a href="#testimonials"
               class="text-sm font-medium text-gray-300 hover:text-white transition">
                Reviews
            </a>

            <a href="#faq"
               class="text-sm font-medium text-gray-300 hover:text-white transition">
                FAQ
            </a>

        </div>

        <!-- Right Actions -->
        <div class="flex items-center gap-4">

            <!-- Market Status -->
            <div class="hidden xl:flex items-center gap-3 px-4 py-2 rounded-full border border-green-500/20 bg-green-500/10">

                <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>

                <span class="text-green-400 text-xs font-semibold">
                    Live Markets
                </span>

            </div>

            <!-- Login -->
            <a href="auth/login.php"
               class="hidden md:flex items-center justify-center px-5 py-3 text-sm font-semibold text-gray-300 hover:text-white transition">

                Sign In

            </a>

            <!-- Register -->
            <a href="auth/register.php"
               class="group relative overflow-hidden px-7 py-3 rounded-xl font-bold text-sm text-white bg-gradient-to-r from-indigo-600 to-cyan-500 shadow-lg shadow-indigo-600/20 hover:scale-105 transition-all duration-300">

                <span class="relative z-10">
                    Open Account
                </span>

            </a>

            <!-- Mobile Menu -->
            <button id="mobileMenuBtn"
                    class="lg:hidden text-white text-xl">

                <i class="fa-solid fa-bars"></i>

            </button>

        </div>

    </div>

</div>

<!-- MOBILE MENU -->
<div id="mobileMenu"
     class="hidden lg:hidden border-t border-white/5 bg-[#050913]">

    <div class="px-6 py-6 flex flex-col gap-5">

        <a href="#markets" class="text-gray-300">Markets</a>

        <a href="#products" class="text-gray-300">Products</a>

        <a href="#platforms" class="text-gray-300">Platforms</a>

        <a href="#accounts" class="text-gray-300">Accounts</a>

        <a href="#testimonials" class="text-gray-300">Reviews</a>

        <a href="#faq" class="text-gray-300">FAQ</a>

        <div class="border-t border-white/5 pt-5 flex flex-col gap-3">

            <a href="auth/login.php"
               class="w-full text-center py-3 rounded-xl border border-white/10 text-white">

                Sign In

            </a>

            <a href="auth/register.php"
               class="w-full text-center py-3 rounded-xl bg-indigo-600 text-white font-semibold">

                Open Account

            </a>

        </div>

    </div>

</div>

</nav>


    <!-- PREMIUM HERO -->
<section class="relative overflow-hidden py-24 lg:py-32">

    <!-- Background Effects -->
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute top-20 left-20 w-72 h-72 bg-indigo-600/20 blur-[120px] rounded-full"></div>
        <div class="absolute bottom-20 right-20 w-96 h-96 bg-blue-500/10 blur-[150px] rounded-full"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 relative z-10">

        <div class="grid lg:grid-cols-2 gap-20 items-center">

            <!-- LEFT SIDE -->
            <div>

                <!-- Trust Badge -->
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-indigo-500/20 bg-indigo-500/10 mb-8">

                    <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>

                    <span class="text-sm font-semibold text-indigo-300">
                        Trusted by Traders in 180+ Countries
                    </span>

                </div>

                <!-- Headline -->
                <h1 class="text-5xl md:text-7xl font-black leading-tight text-white mb-8">

                    Trade Global Markets

                    <span class="block bg-gradient-to-r from-indigo-400 to-cyan-400 bg-clip-text text-transparent">
                        With Institutional Precision
                    </span>

                </h1>

                <!-- Description -->
                <p class="text-xl text-gray-400 leading-relaxed max-w-xl mb-10">

                    Access Forex, Crypto, Stocks, Indices, Metals and Commodities
                    through one powerful trading ecosystem backed by deep liquidity,
                    advanced execution technology and institutional-grade security.

                </p>

                <!-- CTA -->
                <div class="flex flex-wrap gap-4 mb-12">

                    <a href="auth/register.php"
                       class="px-8 py-4 bg-indigo-600 hover:bg-indigo-700 rounded-xl font-bold text-white transition-all hover:scale-105 shadow-xl shadow-indigo-500/30">

                        Open Live Account

                    </a>

                    <a href="#markets"
                       class="px-8 py-4 border border-gray-700 hover:border-indigo-500 rounded-xl font-bold text-white transition-all">

                        Explore Markets

                    </a>

                </div>

                <!-- Stats -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">

                    <div>
                        <h3 class="text-3xl font-black text-white">$15B+</h3>
                        <p class="text-gray-500 text-sm">Monthly Volume</p>
                    </div>

                    <div>
                        <h3 class="text-3xl font-black text-white">2M+</h3>
                        <p class="text-gray-500 text-sm">Active Traders</p>
                    </div>

                    <div>
                        <h3 class="text-3xl font-black text-white">180+</h3>
                        <p class="text-gray-500 text-sm">Countries</p>
                    </div>

                    <div>
                        <h3 class="text-3xl font-black text-white">99.99%</h3>
                        <p class="text-gray-500 text-sm">Uptime</p>
                    </div>

                </div>

            </div>

            <!-- RIGHT SIDE -->
            <div class="relative">

                <!-- Floating BTC Card -->
                <div class="absolute -top-6 -left-8 z-20 bg-[#0b1220]/90 backdrop-blur-xl border border-white/10 rounded-2xl p-4 shadow-2xl">

                    <div class="flex items-center gap-3">

                        <div class="w-12 h-12 rounded-full bg-orange-500/20 flex items-center justify-center">
                            <i class="fab fa-bitcoin text-orange-400 text-xl"></i>
                        </div>

                        <div>
                            <p class="text-gray-400 text-xs">BTC/USD</p>
                            <p class="font-bold text-white">$108,421</p>
                            <p class="text-green-400 text-xs">+3.84%</p>
                        </div>

                    </div>

                </div>

                <!-- Profit Card -->
                <div class="absolute -bottom-6 right-0 z-20 bg-[#0b1220]/90 backdrop-blur-xl border border-white/10 rounded-2xl p-4 shadow-2xl">

                    <p class="text-gray-400 text-xs mb-1">
                        Today's Profit
                    </p>

                    <p class="text-3xl font-black text-green-400">
                        +$12,480
                    </p>

                </div>

                <!-- Main Dashboard -->
                <div class="bg-[#0b1220]/90 backdrop-blur-xl border border-white/10 rounded-3xl overflow-hidden shadow-[0_20px_100px_rgba(99,102,241,0.25)]">

                    <!-- Header -->
                    <div class="border-b border-white/10 px-6 py-4 flex justify-between items-center">

                        <div class="flex gap-2">
                            <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                            <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                            <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                        </div>

                        <span class="text-gray-400 text-sm">
                            Live Trading Dashboard
                        </span>

                    </div>

                    <!-- Portfolio -->
                    <!-- <div class="p-6 border-b border-white/10">

                        <p class="text-gray-400 text-sm mb-2">
                            Portfolio Balance
                        </p>

                        <h2 class="text-5xl font-black text-white">
                            $158,425
                        </h2>

                        <div class="mt-2 text-green-400 font-semibold">
                            +12.43% This Month
                        </div>

                    </div> -->

                    <!-- Chart -->
<div class="p-6">
    <!-- Increased height here from h-64 to h-[400px] -->
    <div class="h-[400px] w-full rounded-2xl overflow-hidden border border-white/10 shadow-2xl">
        
        <!-- TradingView Widget BEGIN -->
        <div class="tradingview-widget-container" style="height:100%; width:100%;">
            <div id="tradingview_chart" style="height:100%; width:100%;"></div>
            <script type="text/javascript" src="https://s3.tradingview.com/tv.js"></script>
            <script type="text/javascript">
            new TradingView.widget({
                "autosize": true,
                "symbol": "BINANCE:BTCUSDT",
                "interval": "D",
                "timezone": "Etc/UTC",
                "theme": "dark",
                "style": "1",
                "locale": "en",
                "toolbar_bg": "#0b1220",
                "enable_publishing": false,
                "withdateranges": true,
                "allow_symbol_change": true,
                "container_id": "tradingview_chart"
            });
            </script>
        </div>
        <!-- TradingView Widget END -->

    </div>
</div>

                    <!-- Bottom Assets -->
                    <div class="grid grid-cols-3 border-t border-white/10">

                        <div class="p-4 border-r border-white/10">
                            <p class="text-xs text-gray-500">BTC</p>
                            <p class="text-white font-bold">$108K</p>
                        </div>

                        <div class="p-4 border-r border-white/10">
                            <p class="text-xs text-gray-500">ETH</p>
                            <p class="text-white font-bold">$5.2K</p>
                        </div>

                        <div class="p-4">
                            <p class="text-xs text-gray-500">XAUUSD</p>
                            <p class="text-white font-bold">$2,455</p>
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

<!-- LIVE MARKET OVERVIEW -->
<section id="markets" class="py-24 bg-[#040812] relative overflow-hidden">

    <!-- Background Glow -->
    <div class="absolute inset-0">
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[800px] bg-indigo-600/5 blur-[180px] rounded-full"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 relative z-10">

        <!-- Section Heading -->
        <div class="flex flex-col md:flex-row md:items-end md:justify-between mb-14">

            <div>
                <span class="text-indigo-400 font-semibold uppercase tracking-widest text-sm">
                    Live Markets
                </span>

                <h2 class="text-4xl md:text-5xl font-black text-white mt-3">
                    Global Market Overview
                </h2>

                <p class="text-gray-400 mt-4 max-w-2xl">
                    Monitor real-time performance across cryptocurrencies,
                    forex pairs, commodities and major stock indices from a
                    single trading ecosystem.
                </p>
            </div>

            <a href="auth/register.php"
               class="mt-6 md:mt-0 inline-flex items-center gap-2 text-indigo-400 hover:text-white transition">

                View All Markets
                <i class="fa-solid fa-arrow-right"></i>

            </a>

        </div>

        <!-- Markets Grid -->
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">

            <!-- BTC -->
            <div class="market-card group">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h4 class="font-bold text-white">BTC/USD</h4>
                        <p class="text-xs text-gray-500">Bitcoin</p>
                    </div>

                    <div class="w-12 h-12 rounded-xl bg-orange-500/10 flex items-center justify-center">
                        <i class="fab fa-bitcoin text-orange-400 text-xl"></i>
                    </div>
                </div>

                <h3 class="text-3xl font-black text-white">$108,421</h3>

                <div class="text-green-400 font-semibold mt-1">
                    +3.84%
                </div>

                <div class="chart-green mt-5"></div>

                <div class="flex justify-between mt-6 text-xs">
                    <span class="text-gray-500">Volume</span>
                    <span class="text-white">$4.2B</span>
                </div>
            </div>

            <!-- ETH -->
            <div class="market-card group">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h4 class="font-bold text-white">ETH/USD</h4>
                        <p class="text-xs text-gray-500">Ethereum</p>
                    </div>

                    <div class="w-12 h-12 rounded-xl bg-blue-500/10 flex items-center justify-center">
                        <i class="fab fa-ethereum text-blue-400 text-xl"></i>
                    </div>
                </div>

                <h3 class="text-3xl font-black text-white">$5,232</h3>

                <div class="text-green-400 font-semibold mt-1">
                    +2.12%
                </div>

                <div class="chart-green mt-5"></div>

                <div class="flex justify-between mt-6 text-xs">
                    <span class="text-gray-500">Volume</span>
                    <span class="text-white">$2.1B</span>
                </div>
            </div>

            <!-- GOLD -->
            <div class="market-card group">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h4 class="font-bold text-white">XAU/USD</h4>
                        <p class="text-xs text-gray-500">Gold</p>
                    </div>

                    <div class="w-12 h-12 rounded-xl bg-yellow-500/10 flex items-center justify-center">
                        <i class="fa-solid fa-coins text-yellow-400"></i>
                    </div>
                </div>

                <h3 class="text-3xl font-black text-white">$2,455</h3>

                <div class="text-green-400 font-semibold mt-1">
                    +0.82%
                </div>

                <div class="chart-green mt-5"></div>

                <div class="flex justify-between mt-6 text-xs">
                    <span class="text-gray-500">Volume</span>
                    <span class="text-white">$950M</span>
                </div>
            </div>

            <!-- NASDAQ -->
            <div class="market-card group">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h4 class="font-bold text-white">NASDAQ</h4>
                        <p class="text-xs text-gray-500">US Tech Index</p>
                    </div>

                    <div class="w-12 h-12 rounded-xl bg-indigo-500/10 flex items-center justify-center">
                        <i class="fa-solid fa-chart-line text-indigo-400"></i>
                    </div>
                </div>

                <h3 class="text-3xl font-black text-white">19,450</h3>

                <div class="text-red-400 font-semibold mt-1">
                    -0.41%
                </div>

                <div class="chart-red mt-5"></div>

                <div class="flex justify-between mt-6 text-xs">
                    <span class="text-gray-500">Volume</span>
                    <span class="text-white">$6.8B</span>
                </div>
            </div>

            <!-- SOL -->
            <div class="market-card">
                <h4 class="font-bold text-white">SOL/USD</h4>
                <p class="text-gray-500 text-xs mb-4">Solana</p>

                <h3 class="text-2xl font-black text-white">$215.44</h3>

                <div class="text-green-400 font-semibold">
                    +4.12%
                </div>
            </div>

            <!-- XRP -->
            <div class="market-card">
                <h4 class="font-bold text-white">XRP/USD</h4>
                <p class="text-gray-500 text-xs mb-4">Ripple</p>

                <h3 class="text-2xl font-black text-white">$1.84</h3>

                <div class="text-green-400 font-semibold">
                    +1.74%
                </div>
            </div>

            <!-- EURUSD -->
            <div class="market-card">
                <h4 class="font-bold text-white">EUR/USD</h4>
                <p class="text-gray-500 text-xs mb-4">Forex Pair</p>

                <h3 class="text-2xl font-black text-white">1.0872</h3>

                <div class="text-red-400 font-semibold">
                    -0.09%
                </div>
            </div>

            <!-- SP500 -->
            <div class="market-card">
                <h4 class="font-bold text-white">S&P 500</h4>
                <p class="text-gray-500 text-xs mb-4">US Index</p>

                <h3 class="text-2xl font-black text-white">6,420</h3>

                <div class="text-green-400 font-semibold">
                    +0.62%
                </div>
            </div>

        </div>

    </div>

</section>

<section id="why-us" class="py-28 bg-[#02040a] relative overflow-hidden">

    <!-- Background Glow -->
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[900px] h-[900px] bg-indigo-600/5 blur-[180px] rounded-full"></div>

    <div class="max-w-7xl mx-auto px-4 relative z-10">

        <!-- Header -->
        <div class="text-center max-w-3xl mx-auto mb-20">

            <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-indigo-500/20 bg-indigo-500/10 text-indigo-300 text-sm font-semibold">

                <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>

                Trusted Worldwide

            </span>

            <h2 class="mt-8 text-4xl md:text-6xl font-black text-white leading-tight">
                Why
                <span class="bg-gradient-to-r from-indigo-400 to-cyan-400 bg-clip-text text-transparent">
                    2 Million+
                </span>
                Traders Choose Us
            </h2>

            <p class="mt-6 text-gray-400 text-lg">
                Built for retail investors, professional traders and institutions
                seeking deep liquidity, advanced technology and institutional-grade security.
            </p>

        </div>

        <?php

        $features = [

            [
                'icon' => 'fa-shield-halved',
                'title' => 'Institutional Security',
                'desc' => 'Advanced encryption, multi-layer authentication, cold storage protection and enterprise-grade infrastructure.'
            ],

            [
                'icon' => 'fa-bolt',
                'title' => 'Ultra-Fast Execution',
                'desc' => 'Execute trades in milliseconds with low latency routing and optimized liquidity aggregation.'
            ],

            [
                'icon' => 'fa-water',
                'title' => 'Deep Liquidity',
                'desc' => 'Access liquidity pools and competitive spreads across forex, crypto, commodities and indices.'
            ],

            [
                'icon' => 'fa-robot',
                'title' => 'AI Risk Monitoring',
                'desc' => 'Intelligent systems continuously monitor account activity and market conditions for enhanced protection.'
            ],

            [
                'icon' => 'fa-building-columns',
                'title' => 'Segregated Client Funds',
                'desc' => 'Client capital is maintained separately from operational funds for additional financial protection.'
            ],

            [
                'icon' => 'fa-headset',
                'title' => '24/7 Expert Support',
                'desc' => 'Dedicated multilingual support teams available around the clock whenever you need assistance.'
            ]

        ];

        ?>

        <!-- Feature Cards -->
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">

            <?php foreach($features as $feature): ?>

            <div class="group relative overflow-hidden rounded-3xl border border-white/5 bg-[#0b1220]/80 backdrop-blur-xl p-8 hover:-translate-y-2 hover:border-indigo-500/50 hover:shadow-[0_25px_60px_rgba(99,102,241,0.18)] transition-all duration-500">

                <!-- Glow -->
                <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/0 via-transparent to-cyan-500/0 group-hover:from-indigo-500/5 group-hover:to-cyan-500/5 transition-all duration-500"></div>

                <!-- Icon -->
                <div class="relative z-10 w-16 h-16 rounded-2xl bg-indigo-500/10 flex items-center justify-center mb-6 group-hover:bg-indigo-500 transition-all duration-500">

                    <i class="fa-solid <?php echo $feature['icon']; ?> text-2xl text-indigo-400 group-hover:text-white transition-all"></i>

                </div>

                <!-- Content -->
                <div class="relative z-10">

                    <h3 class="text-2xl font-bold text-white mb-4">
                        <?php echo $feature['title']; ?>
                    </h3>

                    <p class="text-gray-400 leading-relaxed">
                        <?php echo $feature['desc']; ?>
                    </p>

                </div>

            </div>

            <?php endforeach; ?>

        </div>

        <!-- Statistics Row -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-8 mt-24">

            <div class="text-center p-8 rounded-3xl border border-white/5 bg-[#0b1220]/60">

                <h3 class="text-5xl font-black text-white mb-2">
                    $15B+
                </h3>

                <p class="text-gray-500">
                    Monthly Trading Volume
                </p>

            </div>

            <div class="text-center p-8 rounded-3xl border border-white/5 bg-[#0b1220]/60">

                <h3 class="text-5xl font-black text-white mb-2">
                    2M+
                </h3>

                <p class="text-gray-500">
                    Active Traders
                </p>

            </div>

            <div class="text-center p-8 rounded-3xl border border-white/5 bg-[#0b1220]/60">

                <h3 class="text-5xl font-black text-white mb-2">
                    180+
                </h3>

                <p class="text-gray-500">
                    Countries Served
                </p>

            </div>

            <div class="text-center p-8 rounded-3xl border border-white/5 bg-[#0b1220]/60">

                <h3 class="text-5xl font-black text-white mb-2">
                    99.99%
                </h3>

                <p class="text-gray-500">
                    Platform Uptime
                </p>

            </div>

        </div>

    </div>

</section>

<section id="products" class="py-28 bg-[#050913] relative overflow-hidden">

    <!-- Background Glow -->
    <div class="absolute top-0 right-0 w-[700px] h-[700px] bg-cyan-500/5 blur-[180px] rounded-full"></div>
    <div class="absolute bottom-0 left-0 w-[600px] h-[600px] bg-indigo-500/5 blur-[180px] rounded-full"></div>

    <div class="max-w-7xl mx-auto px-4 relative z-10">

        <!-- Heading -->
        <div class="text-center max-w-4xl mx-auto mb-20">

            <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-cyan-500/20 bg-cyan-500/10 text-cyan-300 text-sm font-semibold">
                Multi-Asset Trading
            </span>

            <h2 class="mt-8 text-4xl md:text-6xl font-black text-white">
                One Platform.
                <span class="bg-gradient-to-r from-cyan-400 to-indigo-400 bg-clip-text text-transparent">
                    Endless Opportunities.
                </span>
            </h2>

            <p class="mt-6 text-gray-400 text-lg">
                Access thousands of global financial instruments from a single
                professional trading account with deep liquidity and institutional execution.
            </p>

        </div>

        <?php

        $products = [

            [
                'title' => 'Forex',
                'icon' => 'fa-dollar-sign',
                'count' => '70+ Pairs',
                'desc' => 'Trade major, minor and exotic currency pairs with tight spreads.',
                'color' => 'green'
            ],

            [
                'title' => 'Cryptocurrencies',
                'icon' => 'fa-bitcoin-sign',
                'count' => '300+ Assets',
                'desc' => 'Access leading digital assets including Bitcoin, Ethereum and Solana.',
                'color' => 'orange'
            ],

            [
                'title' => 'Stocks',
                'icon' => 'fa-chart-column',
                'count' => '2,000+ Shares',
                'desc' => 'Invest in global companies listed across major exchanges.',
                'color' => 'blue'
            ],

            [
                'title' => 'Indices',
                'icon' => 'fa-chart-line',
                'count' => '30+ Markets',
                'desc' => 'Trade major indices including NASDAQ, S&P 500 and FTSE 100.',
                'color' => 'purple'
            ],

            [
                'title' => 'Commodities',
                'icon' => 'fa-boxes-stacked',
                'count' => '50+ Instruments',
                'desc' => 'Diversify with agricultural, industrial and soft commodities.',
                'color' => 'yellow'
            ],

            [
                'title' => 'Precious Metals',
                'icon' => 'fa-coins',
                'count' => 'Gold, Silver, Platinum',
                'desc' => 'Protect your portfolio with traditional safe-haven assets.',
                'color' => 'amber'
            ],

            [
                'title' => 'Energy Markets',
                'icon' => 'fa-oil-well',
                'count' => '10+ Assets',
                'desc' => 'Trade crude oil, natural gas and energy derivatives.',
                'color' => 'red'
            ],

            [
                'title' => 'ETFs',
                'icon' => 'fa-layer-group',
                'count' => '500+ Funds',
                'desc' => 'Gain diversified exposure across sectors and industries.',
                'color' => 'cyan'
            ]

        ];

        ?>

        <!-- Products Grid -->
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">

            <?php foreach($products as $product): ?>

            <div class="group relative overflow-hidden rounded-3xl border border-white/5 bg-[#0b1220]/80 backdrop-blur-xl p-8 hover:-translate-y-3 hover:border-indigo-500/50 hover:shadow-[0_20px_50px_rgba(99,102,241,0.15)] transition-all duration-500">

                <div class="absolute inset-0 bg-gradient-to-br from-white/[0.02] to-transparent opacity-0 group-hover:opacity-100 transition-all"></div>

                <div class="relative z-10">

                    <div class="w-16 h-16 rounded-2xl bg-indigo-500/10 flex items-center justify-center mb-6 group-hover:bg-indigo-500 transition-all duration-500">

                        <i class="fa-solid <?php echo $product['icon']; ?> text-2xl text-indigo-400 group-hover:text-white"></i>

                    </div>

                    <h3 class="text-2xl font-bold text-white mb-2">
                        <?php echo $product['title']; ?>
                    </h3>

                    <div class="inline-flex items-center px-3 py-1 rounded-full bg-white/5 text-indigo-300 text-sm font-semibold mb-4">
                        <?php echo $product['count']; ?>
                    </div>

                    <p class="text-gray-400 leading-relaxed">
                        <?php echo $product['desc']; ?>
                    </p>

                    <div class="mt-6 flex items-center text-indigo-400 font-medium">
                        Explore Market
                        <i class="fa-solid fa-arrow-right ml-2 group-hover:translate-x-2 transition-transform"></i>
                    </div>

                </div>

            </div>

            <?php endforeach; ?>

        </div>

        <!-- Platform Metrics -->
        <div class="mt-24 rounded-[40px] border border-white/5 bg-[#0b1220]/70 backdrop-blur-xl p-10 md:p-16">

            <div class="grid md:grid-cols-4 gap-10 text-center">

                <div>
                    <h3 class="text-5xl font-black text-white">
                        3,000+
                    </h3>
                    <p class="mt-2 text-gray-400">
                        Tradable Instruments
                    </p>
                </div>

                <div>
                    <h3 class="text-5xl font-black text-white">
                        0.0
                    </h3>
                    <p class="mt-2 text-gray-400">
                        Spread From
                    </p>
                </div>

                <div>
                    <h3 class="text-5xl font-black text-white">
                        1:500
                    </h3>
                    <p class="mt-2 text-gray-400">
                        Maximum Leverage
                    </p>
                </div>

                <div>
                    <h3 class="text-5xl font-black text-white">
                        24/7
                    </h3>
                    <p class="mt-2 text-gray-400">
                        Market Access
                    </p>
                </div>

            </div>

        </div>

    </div>

</section>

<section id="platforms" class="py-28 bg-[#02040a] relative overflow-hidden">

    <!-- Background Effects -->
    <div class="absolute top-0 left-0 w-[700px] h-[700px] bg-indigo-600/5 blur-[180px] rounded-full"></div>
    <div class="absolute bottom-0 right-0 w-[600px] h-[600px] bg-cyan-500/5 blur-[180px] rounded-full"></div>

    <div class="max-w-7xl mx-auto px-4 relative z-10">

        <!-- Section Header -->
        <div class="text-center max-w-4xl mx-auto mb-20">

            <span class="inline-flex items-center px-4 py-2 rounded-full border border-indigo-500/20 bg-indigo-500/10 text-indigo-300 text-sm font-semibold">
                Professional Trading Technology
            </span>

            <h2 class="mt-8 text-4xl md:text-6xl font-black text-white leading-tight">
                Award-Winning
                <span class="bg-gradient-to-r from-indigo-400 to-cyan-400 bg-clip-text text-transparent">
                    Trading Platforms
                </span>
            </h2>

            <p class="mt-6 text-gray-400 text-lg">
                Trade confidently from desktop, web, tablet and mobile.
                Access institutional-grade tools, advanced charting,
                lightning-fast execution and AI-powered market intelligence.
            </p>

        </div>

        <!-- Main Layout -->
        <div class="grid lg:grid-cols-2 gap-20 items-center">

            <!-- Left Side -->
            <div>

                <?php

                $platformFeatures = [

                    [
                        'icon' => 'fa-chart-line',
                        'title' => 'Advanced Charting',
                        'desc' => 'Professional technical analysis tools with multiple chart types and indicators.'
                    ],

                    [
                        'icon' => 'fa-bolt',
                        'title' => 'Ultra-Fast Execution',
                        'desc' => 'Execute orders in milliseconds with institutional-grade infrastructure.'
                    ],

                    [
                        'icon' => 'fa-robot',
                        'title' => 'AI Market Intelligence',
                        'desc' => 'Receive smart insights, trend analysis and automated market monitoring.'
                    ],

                    [
                        'icon' => 'fa-mobile-screen',
                        'title' => 'Trade Anywhere',
                        'desc' => 'Full-featured mobile applications for iOS and Android devices.'
                    ]

                ];

                ?>

                <div class="space-y-6">

                    <?php foreach($platformFeatures as $feature): ?>

                    <div class="flex gap-5 p-6 rounded-3xl border border-white/5 bg-[#0b1220]/70 backdrop-blur-xl hover:border-indigo-500/30 transition-all">

                        <div class="w-14 h-14 rounded-2xl bg-indigo-500/10 flex items-center justify-center shrink-0">
                            <i class="fa-solid <?php echo $feature['icon']; ?> text-indigo-400 text-xl"></i>
                        </div>

                        <div>
                            <h3 class="text-xl font-bold text-white mb-2">
                                <?php echo $feature['title']; ?>
                            </h3>

                            <p class="text-gray-400">
                                <?php echo $feature['desc']; ?>
                            </p>
                        </div>

                    </div>

                    <?php endforeach; ?>

                </div>

            </div>

            <!-- Right Side Trading Mockup -->
            <div class="relative">

                <!-- Glow -->
                <div class="absolute inset-0 bg-indigo-600/20 blur-[120px]"></div>

                <!-- Dashboard -->
                <div class="relative bg-[#0b1220] border border-white/10 rounded-[32px] overflow-hidden shadow-[0_20px_80px_rgba(0,0,0,0.6)]">

                    <!-- Top Bar -->
                    <div class="h-14 border-b border-white/5 flex items-center px-6 gap-2">

                        <div class="w-3 h-3 rounded-full bg-red-500"></div>
                        <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                        <div class="w-3 h-3 rounded-full bg-green-500"></div>

                    </div>

                    <!-- Trading Image -->
                    <div class="p-6">

                        <img
                            src="assets/images/trade.png"
                            alt="Trading Dashboard"
                            class="w-full rounded-2xl object-cover"
                        >

                    </div>

                </div>

                <!-- Floating Card 1 -->
                <div class="absolute -left-10 top-10 bg-[#0f172a] border border-white/10 rounded-2xl p-5 backdrop-blur-xl hidden lg:block">

                    <div class="text-xs text-gray-500 mb-2">
                        Portfolio Growth
                    </div>

                    <div class="text-2xl font-black text-green-400">
                        +28.6%
                    </div>

                </div>

                <!-- Floating Card 2 -->
                <div class="absolute -right-10 bottom-16 bg-[#0f172a] border border-white/10 rounded-2xl p-5 backdrop-blur-xl hidden lg:block">

                    <div class="text-xs text-gray-500 mb-2">
                        Execution Speed
                    </div>

                    <div class="text-2xl font-black text-indigo-400">
                        0.02s
                    </div>

                </div>

            </div>

        </div>

        <!-- Platform Cards -->
        <div class="grid md:grid-cols-2 lg:grid-cols-5 gap-6 mt-24">

            <?php

            $platforms = [

                [
                    'name' => 'WebTrader',
                    'icon' => 'fa-globe',
                    'desc' => 'Trade directly from your browser.'
                ],

                [
                    'name' => 'MetaTrader 4',
                    'icon' => 'fa-chart-line',
                    'desc' => 'Industry-standard forex trading.'
                ],

                [
                    'name' => 'MetaTrader 5',
                    'icon' => 'fa-microchip',
                    'desc' => 'Advanced multi-asset platform.'
                ],

                [
                    'name' => 'Mobile App',
                    'icon' => 'fa-mobile-screen',
                    'desc' => 'Trade on the move.'
                ],

                [
                    'name' => 'AI Terminal',
                    'icon' => 'fa-robot',
                    'desc' => 'Smart insights and automation.'
                ]

            ];

            foreach($platforms as $platform):

            ?>

            <div class="group bg-[#0b1220]/70 border border-white/5 rounded-3xl p-6 hover:border-indigo-500/40 hover:-translate-y-2 transition-all">

                <div class="w-14 h-14 rounded-2xl bg-indigo-500/10 flex items-center justify-center mb-5">

                    <i class="fa-solid <?php echo $platform['icon']; ?> text-indigo-400 text-xl"></i>

                </div>

                <h3 class="text-white font-bold text-lg mb-2">
                    <?php echo $platform['name']; ?>
                </h3>

                <p class="text-gray-400 text-sm">
                    <?php echo $platform['desc']; ?>
                </p>

            </div>

            <?php endforeach; ?>

        </div>

        <!-- Metrics -->
        <div class="grid md:grid-cols-4 gap-8 mt-24">

            <div class="text-center">
                <h3 class="text-5xl font-black text-white">99.99%</h3>
                <p class="text-gray-500 mt-2">Platform Uptime</p>
            </div>

            <div class="text-center">
                <h3 class="text-5xl font-black text-white">50+</h3>
                <p class="text-gray-500 mt-2">Technical Indicators</p>
            </div>

            <div class="text-center">
                <h3 class="text-5xl font-black text-white">1M+</h3>
                <p class="text-gray-500 mt-2">Monthly Trades</p>
            </div>

            <div class="text-center">
                <h3 class="text-5xl font-black text-white">24/7</h3>
                <p class="text-gray-500 mt-2">Market Monitoring</p>
            </div>

        </div>

    </div>

</section>

<section id="testimonials" class="py-28 bg-[#050913] relative overflow-hidden">

    <!-- Background Glow -->
    <div class="absolute top-0 left-0 w-[700px] h-[700px] bg-indigo-500/5 blur-[180px] rounded-full"></div>
    <div class="absolute bottom-0 right-0 w-[700px] h-[700px] bg-cyan-500/5 blur-[180px] rounded-full"></div>

    <div class="max-w-7xl mx-auto px-4 relative z-10">

        <!-- Header -->
        <div class="text-center max-w-4xl mx-auto mb-20">

            <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-green-500/10 border border-green-500/20 text-green-300 text-sm font-semibold">

                <i class="fa-solid fa-circle-check"></i>
                Trusted Worldwide

            </span>

            <h2 class="mt-8 text-4xl md:text-6xl font-black text-white">
                Trusted By
                <span class="bg-gradient-to-r from-indigo-400 to-cyan-400 bg-clip-text text-transparent">
                    Traders Worldwide
                </span>
            </h2>

            <p class="mt-6 text-gray-400 text-lg">
                Join millions of investors and traders using our technology
                to access global financial markets with confidence.
            </p>

        </div>

        <!-- Trustpilot Style Ratings -->
        <div class="grid md:grid-cols-4 gap-6 mb-20">

            <div class="bg-[#0b1220]/80 border border-white/5 rounded-3xl p-8 text-center">

                <div class="flex justify-center gap-1 text-green-400 text-xl mb-4">
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                </div>

                <h3 class="text-4xl font-black text-white">4.9/5</h3>
                <p class="text-gray-500 mt-2">Client Satisfaction</p>

            </div>

            <div class="bg-[#0b1220]/80 border border-white/5 rounded-3xl p-8 text-center">

                <h3 class="text-4xl font-black text-white">2M+</h3>
                <p class="text-gray-500 mt-2">Registered Traders</p>

            </div>

            <div class="bg-[#0b1220]/80 border border-white/5 rounded-3xl p-8 text-center">

                <h3 class="text-4xl font-black text-white">180+</h3>
                <p class="text-gray-500 mt-2">Countries Served</p>

            </div>

            <div class="bg-[#0b1220]/80 border border-white/5 rounded-3xl p-8 text-center">

                <h3 class="text-4xl font-black text-white">$15B+</h3>
                <p class="text-gray-500 mt-2">Monthly Trading Volume</p>

            </div>

        </div>

        <?php

        $testimonials = [

            [
                'name' => 'Michael Anderson',
                'country' => 'United Kingdom',
                'profit' => '+$84,250',
                'review' => 'The execution speed is incredible. I moved from another broker and immediately noticed tighter spreads and faster fills.',
                'avatar' => 'M'
            ],

            [
                'name' => 'Sarah Williams',
                'country' => 'Canada',
                'profit' => '+$42,900',
                'review' => 'The platform is intuitive, professional and highly reliable. Customer support has been exceptional.',
                'avatar' => 'S'
            ],

            [
                'name' => 'Daniel Foster',
                'country' => 'Australia',
                'profit' => '+$127,480',
                'review' => 'The AI trading tools and market insights have completely changed how I approach trading.',
                'avatar' => 'D'
            ]

        ];

        ?>

        <!-- Testimonials -->
        <div class="grid lg:grid-cols-3 gap-8 mb-24">

            <?php foreach($testimonials as $testimonial): ?>

            <div class="bg-[#0b1220]/80 border border-white/5 rounded-3xl p-8 hover:border-indigo-500/40 transition-all">

                <div class="flex items-center justify-between mb-6">

                    <div class="flex items-center gap-4">

                        <div class="w-14 h-14 rounded-full bg-indigo-500 flex items-center justify-center text-white font-bold text-lg">
                            <?php echo $testimonial['avatar']; ?>
                        </div>

                        <div>
                            <h4 class="font-bold text-white">
                                <?php echo $testimonial['name']; ?>
                            </h4>

                            <p class="text-gray-500 text-sm">
                                <?php echo $testimonial['country']; ?>
                            </p>
                        </div>

                    </div>

                    <div class="text-green-400 font-bold">
                        <?php echo $testimonial['profit']; ?>
                    </div>

                </div>

                <div class="flex gap-1 text-yellow-400 mb-5">
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                </div>

                <p class="text-gray-400 leading-relaxed">
                    "<?php echo $testimonial['review']; ?>"
                </p>

            </div>

            <?php endforeach; ?>

        </div>

        <!-- Success Stories -->
        <div class="grid lg:grid-cols-2 gap-10 mb-24">

            <!-- Portfolio Growth Card -->
            <div class="bg-[#0b1220]/80 border border-white/5 rounded-3xl p-8">

                <h3 class="text-2xl font-bold text-white mb-6">
                    Portfolio Growth Snapshot
                </h3>

                <img
                    src="assets/images/profit-chart.png"
                    alt="Portfolio Growth"
                    class="rounded-2xl w-full mb-6"
                >

                <div class="flex justify-between">

                    <div>
                        <p class="text-gray-500 text-sm">Initial Capital</p>
                        <p class="text-white font-bold">$25,000</p>
                    </div>

                    <div>
                        <p class="text-gray-500 text-sm">Current Value</p>
                        <p class="text-green-400 font-bold">$108,250</p>
                    </div>

                </div>

            </div>

            <!-- Trader Success Story -->
            <div class="bg-[#0b1220]/80 border border-white/5 rounded-3xl p-8">

                <h3 class="text-2xl font-bold text-white mb-6">
                    Featured Success Story
                </h3>

                <div class="space-y-6">

                    <p class="text-gray-400 leading-relaxed">
                        "After switching to <?php echo $sitename; ?>, I gained access
                        to advanced market tools, tighter spreads and professional
                        execution. Over the last 18 months, my trading performance
                        improved significantly."
                    </p>

                    <div class="border-t border-white/5 pt-6">

                        <h4 class="font-bold text-white">
                            James Richardson
                        </h4>

                        <p class="text-gray-500">
                            Professional Forex Trader
                        </p>

                    </div>

                </div>

            </div>

        </div>

        <!-- Awards -->
        <div class="mb-24">

            <h3 class="text-3xl font-bold text-white text-center mb-12">
                Industry Recognition
            </h3>

            <div class="grid md:grid-cols-4 gap-6">

                <?php

                $awards = [
                    'Best Trading Platform 2025',
                    'Global Broker Excellence',
                    'Most Trusted Broker',
                    'Innovation in Fintech'
                ];

                foreach($awards as $award):

                ?>

                <div class="bg-[#0b1220]/80 border border-white/5 rounded-3xl p-8 text-center">

                    <i class="fa-solid fa-trophy text-yellow-400 text-4xl mb-5"></i>

                    <h4 class="text-white font-bold">
                        <?php echo $award; ?>
                    </h4>

                </div>

                <?php endforeach; ?>

            </div>

        </div>

        <!-- Partner Logos -->
        <div>

            <h3 class="text-3xl font-bold text-white text-center mb-12">
                Technology & Liquidity Partners
            </h3>

            <div class="grid grid-cols-2 md:grid-cols-6 gap-8 items-center opacity-70">

                <div class="text-center text-xl font-bold text-gray-500">Bloomberg</div>
                <div class="text-center text-xl font-bold text-gray-500">Reuters</div>
                <div class="text-center text-xl font-bold text-gray-500">MetaQuotes</div>
                <div class="text-center text-xl font-bold text-gray-500">TradingView</div>
                <div class="text-center text-xl font-bold text-gray-500">Binance</div>
                <div class="text-center text-xl font-bold text-gray-500">CoinMarketCap</div>

            </div>

        </div>

    </div>

</section>

<section id="faq" class="py-28 bg-[#02040a] relative overflow-hidden">

    <!-- Background Glow -->
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[900px] h-[900px] bg-indigo-600/5 blur-[180px] rounded-full"></div>

    <div class="max-w-7xl mx-auto px-4 relative z-10">

        <!-- Section Header -->
        <div class="text-center max-w-3xl mx-auto mb-20">

            <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-indigo-500/20 bg-indigo-500/10 text-indigo-300 text-sm font-semibold">
                Need Answers?
            </span>

            <h2 class="mt-8 text-4xl md:text-6xl font-black text-white">
                Frequently Asked
                <span class="bg-gradient-to-r from-indigo-400 to-cyan-400 bg-clip-text text-transparent">
                    Questions
                </span>
            </h2>

            <p class="mt-6 text-gray-400 text-lg">
                Everything you need to know before opening your trading account.
            </p>

        </div>

        <!-- FAQ -->
        <div class="max-w-4xl mx-auto space-y-5">

            <?php

            $faqs = [

                [
                    'q' => 'How do I open an account?',
                    'a' => 'Click Register, complete your profile, verify your identity and start funding your account.'
                ],

                [
                    'q' => 'How long do withdrawals take?',
                    'a' => 'Most withdrawal requests are processed within 24 hours depending on the selected payment method.'
                ],

                [
                    'q' => 'What markets can I trade?',
                    'a' => 'You can trade Forex, Cryptocurrencies, Stocks, Indices, Commodities, Metals, ETFs and more.'
                ],

                [
                    'q' => 'Is my money secure?',
                    'a' => 'Client funds are protected using advanced security protocols, encrypted infrastructure and segregated fund management.'
                ],

                [
                    'q' => 'Do you offer mobile trading?',
                    'a' => 'Yes. Access your account from desktop, tablet or mobile devices with our advanced trading platforms.'
                ]

            ];

            foreach($faqs as $index => $faq):

            ?>

            <div class="bg-[#0b1220]/80 border border-white/5 rounded-3xl overflow-hidden">

                <button
                    onclick="toggleFaq(<?php echo $index; ?>)"
                    class="w-full flex items-center justify-between p-6 text-left">

                    <span class="font-bold text-white text-lg">
                        <?php echo $faq['q']; ?>
                    </span>

                    <i id="faqIcon<?php echo $index; ?>"
                       class="fa-solid fa-plus text-indigo-400 transition-all"></i>

                </button>

                <div
                    id="faqContent<?php echo $index; ?>"
                    class="hidden px-6 pb-6 text-gray-400 leading-relaxed">

                    <?php echo $faq['a']; ?>

                </div>

            </div>

            <?php endforeach; ?>

        </div>

        <!-- Funding Methods -->
        <div class="mt-32">

            <div class="text-center mb-14">

                <h2 class="text-4xl font-black text-white mb-4">
                    Fast & Secure Funding Methods
                </h2>

                <p class="text-gray-400">
                    Deposit and withdraw using trusted global payment solutions.
                </p>

            </div>

            <?php

            $payments = [

                ['fa-bitcoin', 'Bitcoin'],
                ['fa-ethereum', 'Ethereum'],
                ['fa-cc-visa', 'Visa'],
                ['fa-cc-mastercard', 'Mastercard'],
                ['fa-building-columns', 'Bank Transfer'],
                ['fa-wallet', 'Digital Wallets']

            ];

            ?>

            <div class="grid md:grid-cols-3 lg:grid-cols-6 gap-6">

                <?php foreach($payments as $payment): ?>

                <div class="bg-[#0b1220]/80 border border-white/5 rounded-3xl p-8 text-center hover:border-indigo-500/30 transition-all">

                    <i class="fa-solid <?php echo $payment[0]; ?> text-4xl text-indigo-400 mb-5"></i>

                    <h4 class="text-white font-semibold">
                        <?php echo $payment[1]; ?>
                    </h4>

                </div>

                <?php endforeach; ?>

            </div>

        </div>

        <!-- Final CTA -->
        <div class="mt-32">

            <div class="relative overflow-hidden rounded-[40px] border border-white/10 bg-gradient-to-br from-indigo-600/20 via-[#0b1220] to-cyan-600/10 p-12 md:p-20 text-center">

                <div class="absolute inset-0 bg-[radial-gradient(circle_at_center,rgba(99,102,241,0.15),transparent_70%)]"></div>

                <div class="relative z-10">

                    <span class="inline-flex items-center px-4 py-2 rounded-full bg-green-500/10 border border-green-500/20 text-green-300 text-sm font-semibold">

                        Start Today

                    </span>

                    <h2 class="mt-8 text-4xl md:text-6xl font-black text-white leading-tight">

                        Trade Global Markets
                        <br>

                        With Confidence

                    </h2>

                    <p class="mt-6 text-lg text-gray-300 max-w-3xl mx-auto">

                        Join thousands of traders accessing professional tools,
                        institutional-grade security and deep global liquidity
                        from one powerful platform.

                    </p>

                    <div class="flex flex-col sm:flex-row justify-center gap-5 mt-10">

                        <a href="auth/register.php"
                           class="px-10 py-5 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-lg transition-all hover:scale-105">

                            Open Live Account

                        </a>

                        <a href="auth/login.php"
                           class="px-10 py-5 rounded-2xl border border-white/10 bg-white/5 hover:bg-white/10 text-white font-bold text-lg transition-all">

                            Sign In

                        </a>

                    </div>

                    <div class="grid md:grid-cols-4 gap-8 mt-16">

                        <div>
                            <h3 class="text-3xl font-black text-white">
                                2M+
                            </h3>

                            <p class="text-gray-400">
                                Traders
                            </p>
                        </div>

                        <div>
                            <h3 class="text-3xl font-black text-white">
                                180+
                            </h3>

                            <p class="text-gray-400">
                                Countries
                            </p>
                        </div>

                        <div>
                            <h3 class="text-3xl font-black text-white">
                                $15B+
                            </h3>

                            <p class="text-gray-400">
                                Monthly Volume
                            </p>
                        </div>

                        <div>
                            <h3 class="text-3xl font-black text-white">
                                99.99%
                            </h3>

                            <p class="text-gray-400">
                                Uptime
                            </p>
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

<footer class="bg-dark-bg pt-16 pb-8 border-t border-dark-border">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-4 gap-8 mb-12">
            <div>
                <h3 class="text-xl font-bold text-white mb-4"><?php echo $sitename; ?></h3>
                <p class="text-sm text-dark-muted">The future of decentralized finance, investment, and payments.</p>
            </div>
            <div>
                <h4 class="font-bold text-white mb-4">Quick Links</h4>
                <ul class="space-y-2 text-sm text-dark-muted">
                    <li><a href="auth/register.php" class="hover:text-brand-primary">AI trading</a></li>
                    <li><a href="auth/register.php" class="hover:text-brand-primary">KYC</a></li>
                    <li><a href="auth/register.php" class="hover:text-brand-primary">Trading Plans</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold text-white mb-4">Legal</h4>
                <ul class="space-y-2 text-sm text-dark-muted">
                    <li><a href="main/privacy.php" class="hover:text-brand-primary">Privacy Policy</a></li>
                    <li><a href="main/terms.php" class="hover:text-brand-primary">Terms of Service</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold text-white mb-4">Contact</h4>
                <ul class="space-y-2 text-sm text-dark-muted">
                    <li><?php echo $site_email; ?></li>
                    <!-- <li><?php echo $site_phone; ?></li> -->
                    <p class="text-sm text-dark-muted">189 Tiyu West Road, Tianhe District, Guangzhou, Guangdong Province, China (Postal Code: 510620).</p>
                </ul>
            </div>
        </div>

        <!-- Disclaimer Section -->


        <div class="text-center text-xs text-dark-muted pt-8 mt-8 border-t border-dark-border">
            &copy; <?php echo date("Y"); ?> <?php echo $sitename; ?>. All rights reserved.
        </div>
    </div>
</footer>

    <!-- Floating Notification Container -->
<div id="toast-container" class="fixed bottom-6 left-6 z-[9999] flex flex-col gap-3 pointer-events-none">
    <!-- Notifications will be injected here -->
</div>

<style>
    /* Custom animation for the toast */
    @keyframes slideIn { from { opacity: 0; transform: translateX(-20px); } to { opacity: 1; transform: translateX(0); } }
    .toast-anim { animation: slideIn 0.5s ease-out; }
</style>

    <!-- Robust Scripts -->
    <script>
        // Live Data Ticker Simulation
        async function updateTicker() {
            try {
                const res = await fetch('https://api.coingecko.com/api/v3/coins/markets?vs_currency=usd&per_page=10');
                const data = await res.json();
                const ticker = document.getElementById('market-ticker');
                ticker.innerHTML = data.map(coin => `<span>${coin.symbol.toUpperCase()}: $${coin.current_price.toLocaleString()}</span>`).join(' | ');
            } catch (e) { ticker.innerHTML = "MARKET DATA UNAVAILABLE"; }
        }
        updateTicker();
        setInterval(updateTicker, 30000);
    </script>

    <script>
function toggleFaq(id) {

    const content = document.getElementById('faqContent' + id);
    const icon = document.getElementById('faqIcon' + id);

    content.classList.toggle('hidden');

    if(content.classList.contains('hidden')) {
        icon.classList.remove('fa-minus');
        icon.classList.add('fa-plus');
    } else {
        icon.classList.remove('fa-plus');
        icon.classList.add('fa-minus');
    }

}
</script>
<script>
    const btn = document.getElementById('mobileMenuBtn');
    const menu = document.getElementById('mobileMenu');

    btn.addEventListener('click', () => {
        menu.classList.toggle('hidden');
    });
</script>
<script>
function showNotification() {
    // Expanded global list of names
    const names = [
        "Kwame", "Amina", "Chidi", "Fatima", "Oluwaseun", "Tendai", "Jabari", "Zola", "Bakari", "Eshe", // Africa
        "Hiroshi", "Mei", "Arjun", "Wei", "Priya", "Sanjay", "Ji-hoon", "Kenji", "Anika", "Lei", // Asia
        "Lars", "Elena", "Mateo", "Sven", "Sofia", "Luca", "Dmitry", "Ingrid", "Hans", "Freja", // Europe
        "John", "Sarah", "James", "Michael", "Emily", "Jessica", "Robert", "Alice", "William", "Olivia", // N. America
        "Thiago", "Camila", "Santiago", "Valentina", "Gabriel", "Isabella", "Mateus", "Lucia", "Diego", "Elena" // S. America
    ];
    
    const amounts = [
        "$1,200", "$25,000", "$450", "$3,200", "$12,400", "$890", 
        "$5,000", "$150", "$52,000", "$7,800", "$1,150", "$9,300",
        "$2,400", "$18,500", "$3,600", "$600"
    ];
    
    const randomName = names[Math.floor(Math.random() * names.length)];
    const randomAmount = amounts[Math.floor(Math.random() * amounts.length)];
    
    const container = document.getElementById('toast-container');
    
    const toast = document.createElement('div');
    toast.className = "bg-[#0b1220] border border-indigo-500/30 p-4 rounded-2xl shadow-2xl flex items-center gap-4 toast-anim pointer-events-auto w-72 mb-4";
    toast.innerHTML = `
        <div class="w-10 h-10 rounded-full bg-indigo-500/20 flex items-center justify-center">
            <i class="fa-solid fa-check text-green-400"></i>
        </div>
        <div>
            <p class="text-white text-xs font-bold">${randomName} just withdrew</p>
            <p class="text-green-400 font-black text-sm">${randomAmount}</p>
        </div>
    `;
    
    container.appendChild(toast);
    
    // Remove after 5 seconds
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transition = '0.5s';
        setTimeout(() => toast.remove(), 500);
    }, 5000);
}

// Show a notification every 8 seconds
setInterval(showNotification, 8000);

// Initial trigger
setTimeout(showNotification, 3000);
</script>
<script>
    async function updateTicker() {
        const ticker = document.getElementById('market-ticker'); // Ensure this is declared here
        try {
            const res = await fetch('https://api.coingecko.com/api/v3/coins/markets?vs_currency=usd&per_page=10');
            const data = await res.json();
            ticker.innerHTML = data.map(coin => `<span>${coin.symbol.toUpperCase()}: $${coin.current_price.toLocaleString()}</span>`).join(' | ');
        } catch (e) { 
            console.error(e);
            ticker.innerHTML = "MARKET DATA UNAVAILABLE"; 
        }
    }
    updateTicker();
    setInterval(updateTicker, 30000);
</script>
<script>
    document.addEventListener("DOMContentLoaded", function() {

        // Check if the user has already seen the notification
        if (!localStorage.getItem('hasSeenWarning')) {

            Swal.fire({
                title: 'Notice',

                text: 'All trades and investment activities are protected through advanced security measures, including end-to-end encryption and industry-standard security protocols',

                icon: 'warning',

                confirmButtonText: 'I Understand',

                confirmButtonColor: '#4f46e5',

                background: '#0b1220',

                color: '#ffffff',

                backdrop: true,

                allowOutsideClick: false

            }).then(() => {

                // Set flag in localStorage so it doesn't show again
                localStorage.setItem('hasSeenWarning', 'true');

            });

        }

    });
</script>
</body>
</html>