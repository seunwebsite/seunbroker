<?php
include 'config/config.php';
?>

<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $sitename; ?> - DeFi, Wallet & Investments</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        display: ['Space Grotesk', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            primary: '#6366F1', // Indigo
                            secondary: '#818CF8',
                            accent: '#4F46E5',
                            dark: '#312E81'
                        },
                        dark: { 
                            bg: '#02040a', 
                            panel: '#0B0F19', 
                            card: '#111827',
                            border: '#1E293B',
                            text: '#E2E8F0',
                            muted: '#94A3B8'
                        }
                    },
                    boxShadow: {
                        'neon': '0 0 20px rgba(99, 102, 241, 0.3)',
                        'card': '0 8px 32px 0 rgba(0, 0, 0, 0.4)',
                    }
                }
            }
        }
    </script>

    <style>
        body { background-color: #02040a; color: #E2E8F0; }
        
        /* Glassmorphism Card Effect */
        .glass-card {
            background: rgba(17, 24, 39, 0.7);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(30, 41, 59, 0.5);
        }

        /* Virtual Card Gradient */
        .credit-card-bg {
            background: linear-gradient(135deg, #6366F1 0%, #A855F7 50%, #EC4899 100%);
        }

        /* Animations */
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        .animate-float { animation: float 6s ease-in-out infinite; }

        @keyframes marquee {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }
        .animate-marquee { animation: marquee 30s linear infinite; }
        
        .no-scrollbar::-webkit-scrollbar { display: none; }
        
        /* Mobile Menu */
        #mobile-menu { transition: all 0.3s ease-in-out; max-height: 0; opacity: 0; overflow: hidden; }
        #mobile-menu.open { max-height: 500px; opacity: 1; }
    </style>
</head>
<body class="antialiased overflow-x-hidden selection:bg-brand-primary selection:text-white">

    <div class="bg-dark-panel border-b border-dark-border py-2 overflow-hidden whitespace-nowrap z-50">
        <div id="crypto-ticker" class="inline-block animate-marquee pl-4 text-xs font-mono">
            <span class="text-dark-muted">Loading live market data...</span>
        </div>
    </div>

    <nav class="sticky top-0 z-40 bg-dark-bg/90 backdrop-blur-md border-b border-dark-border">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-brand-primary flex items-center justify-center shadow-neon">
                        <span class="font-bold text-white text-xl">TA</span>
                    </div>
                    <span class="font-sans font-bold text-xl tracking-tight text-white"><?php echo $sitename; ?></span>
                </div>

                <div class="hidden lg:block">
                    <div class="flex items-center space-x-8">
                        <a href="#wallet" class="text-sm font-medium hover:text-brand-primary transition-colors">Wallet</a>
                        <a href="#invest" class="text-sm font-medium hover:text-brand-primary transition-colors">Investments</a>
                        <a href="#cards" class="text-sm font-medium hover:text-brand-primary transition-colors">Cards</a>
                        <a href="#market" class="text-sm font-medium hover:text-brand-primary transition-colors">Market</a>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <a href='auth/login.php'> <button class="hidden md:block bg-transparent hover:text-white text-gray-300 px-5 py-2 text-sm font-medium transition-all">Log In</button></a>
                   <a href='auth/register.php'> <button class="bg-white hover:bg-gray-200 text-black px-6 py-2.5 rounded-full text-sm font-bold shadow-lg transition-all hover:scale-105">Get Started</button></a>
                    <button id="mobile-menu-btn" class="lg:hidden text-gray-300 hover:text-white"><i class="fa-solid fa-bars text-2xl"></i></button>
                </div>
            </div>
        </div>
        <div id="mobile-menu" class="lg:hidden bg-dark-panel border-b border-dark-border">
            <div class="px-4 py-4 space-y-3">
                <a href="#wallet" class="block text-gray-300 hover:text-white">Wallet</a>
                <a href="#invest" class="block text-gray-300 hover:text-white">Investments</a>
                <a href="#cards" class="block text-gray-300 hover:text-white">Cards</a>
                  <a href="auth/login.php" class="block text-gray-300 hover:text-white">Login</a>
                   <a href="auth/register.php" class="block text-gray-300 hover:text-white">Register</a>
                
            </div>
        </div>
    </nav>

    <section class="relative pt-24 pb-20 overflow-hidden">
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full h-full bg-[radial-gradient(circle_at_center,_var(--tw-gradient-stops))] from-brand-primary/20 via-transparent to-transparent opacity-50 pointer-events-none"></div>
        
        <div class="relative z-10 max-w-4xl mx-auto px-4 text-center">
            
            <div class="inline-block px-4 py-1.5 rounded-full bg-[#1a1f35] border border-brand-primary/30 text-brand-secondary text-[10px] md:text-xs font-bold tracking-widest uppercase mb-8">
                Institutional Asset Management
            </div>
            
            <h1 class="text-6xl md:text-8xl font-black tracking-tighter mb-6 leading-[0.9] text-white">
                REDEFINING <br>
                <span class="text-transparent bg-clip-text bg-gradient-to-b from-brand-secondary to-brand-primary">DIGITAL</span> <br>
                WEALTH
            </h1>
            
            <p class="text-base md:text-lg text-gray-400 max-w-xl mx-auto mb-10 font-medium leading-relaxed">
                The all-in-one ecosystem for professional crypto banking, automated trading, and high-yield staking. Built for the elite, accessible to you.
            </p>
            
            <div class="flex justify-center w-full">
                <a href="auth/login.php" class="w-full md:w-auto bg-brand-primary hover:bg-brand-accent text-white px-10 py-4 rounded-xl font-bold text-lg shadow-neon transition-all hover:scale-[1.02] active:scale-95">
                    Explore Dashboard
                </a>
            </div>
        </div>
    </section>

    <section class="pb-24 relative z-20">
        <div class="w-full max-w-[95%] mx-auto px-2 md:px-0">
            <div class="relative group">
                <div class="absolute -inset-1 bg-gradient-to-r from-brand-primary via-purple-600 to-brand-primary opacity-30 blur-2xl group-hover:opacity-50 transition duration-1000 group-hover:duration-200"></div>
                
                <div class="relative rounded-xl bg-dark-panel border border-dark-border shadow-2xl overflow-hidden ring-1 ring-white/10">
                    <div class="h-10 bg-dark-card border-b border-dark-border flex items-center px-4 gap-2">
                        <div class="flex gap-2">
                            <div class="w-3 h-3 rounded-full bg-red-500/80"></div>
                            <div class="w-3 h-3 rounded-full bg-yellow-500/80"></div>
                            <div class="w-3 h-3 rounded-full bg-green-500/80"></div>
                        </div>
                        <div class="mx-auto w-1/2 md:w-2/3 h-6 bg-dark-bg rounded border border-dark-border flex items-center justify-center text-xs text-dark-muted font-mono overflow-hidden whitespace-nowrap px-2">
                            <i class="fa-solid fa-lock mr-2 text-green-500"></i> <?php echo $siteurl; ?>
                        </div>
                    </div>

                    <div class="aspect-[16/9] md:aspect-[21/9] bg-dark-bg relative overflow-hidden">
                        <img src="assets/images/dash.png" 
                            alt="Dashboard Preview" 
                            class="w-full h-full object-cover object-top"
                            loading="lazy">
                        
                        <div class="absolute inset-0 bg-dark-bg/0 hover:bg-dark-bg/10 transition-colors flex items-center justify-center opacity-0 hover:opacity-100 cursor-pointer">
                            <span class="bg-brand-primary text-white px-4 py-2 rounded-lg font-bold shadow-lg transform translate-y-4 group-hover:translate-y-0 transition-transform">
                                Live Preview
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="wallet" class="py-16 relative bg-dark-panel/30 border-t border-dark-border">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-3 gap-8">
                
                <div class="lg:col-span-2">
                    <div class="glass-card rounded-2xl overflow-hidden shadow-card h-full">
                        <div class="p-6 border-b border-dark-border flex justify-between items-center bg-dark-panel/50">
                            <div>
                                <h3 class="text-xl font-bold text-white">My Wallet</h3>
                                <p class="text-xs text-dark-muted">Multi-Chain Support (ERC20, BEP20, TRC20)</p>
                            </div>
                            <div class="bg-dark-bg px-3 py-1 rounded border border-dark-border flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-green-500"></span>
                                <span class="text-xs font-mono">Connected</span>
                            </div>
                        </div>

                        <div class="p-6 grid md:grid-cols-2 gap-8">
                            <div class="flex flex-col justify-center">
                                <p class="text-dark-muted text-sm mb-1">Total Balance</p>
                                <h2 class="text-4xl font-display font-bold text-white mb-6">$42,894.52</h2>
                                
                                <div class="grid grid-cols-3 gap-3">
                                    <button onclick="switchTab('send')" class="flex flex-col items-center gap-2 p-3 rounded-xl bg-dark-bg border border-dark-border hover:border-brand-primary hover:bg-brand-primary/10 transition-all group">
                                        <div class="w-10 h-10 rounded-full bg-brand-primary/20 flex items-center justify-center text-brand-primary group-hover:bg-brand-primary group-hover:text-white">
                                            <i class="fa-solid fa-paper-plane"></i>
                                        </div>
                                        <span class="text-xs font-medium">Send</span>
                                    </button>
                                    <button onclick="switchTab('receive')" class="flex flex-col items-center gap-2 p-3 rounded-xl bg-dark-bg border border-dark-border hover:border-brand-primary hover:bg-brand-primary/10 transition-all group">
                                        <div class="w-10 h-10 rounded-full bg-green-500/20 flex items-center justify-center text-green-400 group-hover:bg-green-500 group-hover:text-white">
                                            <i class="fa-solid fa-qrcode"></i>
                                        </div>
                                        <span class="text-xs font-medium">Receive</span>
                                    </button>
                                    <button onclick="switchTab('swap')" class="flex flex-col items-center gap-2 p-3 rounded-xl bg-dark-bg border border-dark-border hover:border-brand-primary hover:bg-brand-primary/10 transition-all group">
                                        <div class="w-10 h-10 rounded-full bg-purple-500/20 flex items-center justify-center text-purple-400 group-hover:bg-purple-500 group-hover:text-white">
                                            <i class="fa-solid fa-arrow-right-arrow-left"></i>
                                        </div>
                                        <span class="text-xs font-medium">Swap</span>
                                    </button>
                                </div>
                            </div>

                            <div class="bg-dark-bg rounded-xl border border-dark-border p-5 min-h-[300px] relative">
    
                                <!-- SEND TAB -->
                                <div id="tab-send" class="space-y-4">
                                    <div class="flex justify-between items-center mb-2">
                                        <h4 class="font-bold text-white">Send Crypto</h4>
                                        <span class="text-xs text-brand-primary cursor-pointer">Max Amount</span>
                                    </div>
                                    <div>
                                        <label class="text-xs text-dark-muted block mb-1">Asset</label>
                                        <select class="w-full bg-dark-panel border border-dark-border rounded-lg p-2.5 text-sm text-white focus:border-brand-primary outline-none">
                                            <option>Bitcoin (BTC)</option>
                                            <option>Ethereum (ETH)</option>
                                            <option>USDT (TRC20)</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="text-xs text-dark-muted block mb-1">Recipient Address</label>
                                        <div class="relative">
                                            <input type="text" placeholder="Paste address..." class="w-full bg-dark-panel border border-dark-border rounded-lg p-2.5 text-sm text-white focus:border-brand-primary outline-none pl-9">
                                            <i class="fa-solid fa-wallet absolute left-3 top-3 text-dark-muted text-xs"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="text-xs text-dark-muted block mb-1">Amount</label>
                                        <input type="number" placeholder="0.00" class="w-full bg-dark-panel border border-dark-border rounded-lg p-2.5 text-sm text-white focus:border-brand-primary outline-none">
                                    </div>
                                    <!-- FIXED SEND BUTTON -->
                                    <button onclick="window.location.href='auth/login.php';" class="w-full bg-brand-primary hover:bg-brand-accent text-white font-bold py-2.5 rounded-lg mt-2 transition-colors">Confirm Send</button>
                                </div>

                                <!-- RECEIVE TAB -->
                                <div id="tab-receive" class="hidden flex-col items-center justify-center h-full text-center space-y-4">
                                    <h4 class="font-bold text-white mb-2">Receive Bitcoin</h4>
                                    <div class="bg-white p-3 rounded-lg">
                                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=3J98t1WpEZ73CNmQviecrnyiWrnqRhWNLy" alt="QR">
                                    </div>
                                    <div class="w-full">
                                        <p class="text-xs text-dark-muted mb-1">Wallet Address</p>
                                        <div class="flex items-center bg-dark-panel border border-dark-border rounded-lg overflow-hidden">
                                            <input type="text" value="3J98t1WpEZ73CNmQviecrnyiWrnqRhWNLy" readonly class="bg-transparent text-xs text-brand-secondary p-2 w-full outline-none">
                                            <!-- FIXED COPY BUTTON -->
                                            <button onclick="alert('Please log in to use your actual wallet address.'); window.location.href='auth/login.php';" class="px-3 py-2 bg-dark-border hover:bg-brand-primary hover:text-white transition-colors"><i class="fa-regular fa-copy"></i></button>
                                        </div>
                                    </div>
                                </div>

                                <!-- SWAP TAB -->
                                <div id="tab-swap" class="hidden space-y-3">
                                    <h4 class="font-bold text-white mb-2">Instant Swap</h4>
                                    <div class="bg-dark-panel p-3 rounded-lg border border-dark-border">
                                        <div class="flex justify-between text-xs text-dark-muted mb-1"><span>From</span><span>Bal: 1.2 ETH</span></div>
                                        <div class="flex items-center justify-between">
                                            <!-- FIXED SWAP CALCULATOR -->
                                            <input type="number" id="swap-from" placeholder="0.0" min="0" class="bg-transparent text-white font-bold text-lg w-20 outline-none">
                                            <span class="bg-dark-bg px-2 py-1 rounded text-xs font-bold border border-dark-border">ETH</span>
                                        </div>
                                    </div>
                                    <div class="flex justify-center -my-3 relative z-10">
                                        <div class="bg-brand-primary p-1.5 rounded-full text-white text-xs border-4 border-dark-bg">
                                            <i class="fa-solid fa-arrow-down"></i>
                                        </div>
                                    </div>
                                    <div class="bg-dark-panel p-3 rounded-lg border border-dark-border">
                                        <div class="flex justify-between text-xs text-dark-muted mb-1"><span>To</span><span id="swap-usd-estimate">~ $0.00</span></div>
                                        <div class="flex items-center justify-between">
                                            <!-- FIXED SWAP CALCULATOR -->
                                            <input type="number" id="swap-to" placeholder="0.0" min="0" class="bg-transparent text-white font-bold text-lg w-20 outline-none">
                                            <span class="bg-dark-bg px-2 py-1 rounded text-xs font-bold border border-dark-border">USDT</span>
                                        </div>
                                    </div>
                                    <!-- FIXED SWAP BUTTON -->
                                    <button onclick="window.location.href='auth/login.php';" class="w-full bg-brand-primary hover:bg-brand-accent text-white font-bold py-2.5 rounded-lg mt-2 transition-colors">Swap Assets</button>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-1">
                    <div class="glass-card rounded-2xl p-6 h-full border border-dark-border">
                        <h3 class="font-bold text-white mb-4">Recent Activity</h3>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-3 rounded-xl hover:bg-dark-bg/50 transition-colors border border-transparent hover:border-dark-border">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-green-500/10 text-green-400 flex items-center justify-center">
                                        <i class="fa-solid fa-arrow-down"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-white">Received USDT</p>
                                        <p class="text-xs text-dark-muted">Today, 10:23 AM</p>
                                    </div>
                                </div>
                                <span class="text-green-400 font-mono text-sm">+$500.00</span>
                            </div>
                            <div class="flex items-center justify-between p-3 rounded-xl hover:bg-dark-bg/50 transition-colors border border-transparent hover:border-dark-border">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-red-500/10 text-red-400 flex items-center justify-center">
                                        <i class="fa-solid fa-arrow-up"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-white">Sent Bitcoin</p>
                                        <p class="text-xs text-dark-muted">Yesterday</p>
                                    </div>
                                </div>
                                <span class="text-white font-mono text-sm">-0.02 BTC</span>
                            </div>
                            <div class="flex items-center justify-between p-3 rounded-xl hover:bg-dark-bg/50 transition-colors border border-transparent hover:border-dark-border">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-brand-primary/10 text-brand-primary flex items-center justify-center">
                                        <i class="fa-solid fa-repeat"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-white">Swapped ETH</p>
                                        <p class="text-xs text-dark-muted">2 days ago</p>
                                    </div>
                                </div>
                                <span class="text-white font-mono text-sm">1.5 ETH</span>
                            </div>
                        </div>
                        <!-- FIXED HISTORY BUTTON -->
                        <button onclick="window.location.href='auth/login.php';" class="w-full mt-6 py-2 text-sm text-dark-muted border border-dark-border rounded-lg hover:text-white hover:border-white transition-colors">View All History</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="cards" class="py-20 bg-dark-panel border-y border-dark-border overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-2 gap-16 items-center">
                
                <div class="relative flex justify-center perspective-1000">
                    <div class="absolute inset-0 bg-brand-primary blur-[80px] opacity-20"></div>
                    
                    <div class="w-full max-w-[380px] aspect-[1.586/1] rounded-2xl credit-card-bg relative p-6 text-white shadow-neon transform rotate-3 hover:rotate-0 transition-transform duration-500 z-10 animate-float flex flex-col justify-between border border-white/20 h-auto">
                        <div class="flex justify-between items-start">
                            <i class="fa-solid fa-wifi text-2xl opacity-80"></i>
                            <span class="font-display font-bold text-xl italic"><?php echo $sitename; ?></span>
                        </div>
                        <div class="my-2 md:my-4">
                            <i class="fa-solid fa-microchip text-4xl text-yellow-300 opacity-80"></i>
                        </div>
                        <div>
                            <p class="font-mono text-lg md:text-xl tracking-widest mb-2 md:mb-4 drop-shadow-md">**** **** **** 4289</p>
                            <div class="flex justify-between items-end">
                                <div>
                                    <p class="text-[10px] md:text-xs opacity-70 uppercase">Card Holder</p>
                                    <p class="font-bold tracking-wide text-sm md:text-base">ALEXANDER DOE</p>
                                </div>
                                <div class="flex flex-col items-end">
                                    <p class="text-[10px] md:text-xs opacity-70 uppercase">Expires</p>
                                    <p class="font-bold text-sm md:text-base">12/28</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <div class="inline-flex items-center gap-2 text-brand-secondary font-bold mb-4">
                        <i class="fa-regular fa-credit-card"></i> SPEND ANYWHERE
                    </div>
                    <h2 class="text-4xl font-display font-bold text-white mb-6">The <span class="text-brand-primary">Virtual Card</span> for the DeFi Era.</h2>
                    <p class="text-dark-muted text-lg mb-8">
                        Instantly issue a virtual Visa/Mastercard funded by your crypto balance. Shop online, pay for subscriptions, and withdraw cash globally.
                    </p>

                    <div class="grid grid-cols-2 gap-6 mb-8">
                        <div class="flex gap-3">
                            <div class="w-8 h-8 rounded bg-dark-bg border border-dark-border flex items-center justify-center text-brand-primary"><i class="fa-brands fa-apple"></i></div>
                            <div>
                                <h4 class="font-bold text-white">Apple Pay</h4>
                                <p class="text-xs text-dark-muted">Instant provisioning</p>
                            </div>
                        </div>
                        <div class="flex gap-3">
                            <div class="w-8 h-8 rounded bg-dark-bg border border-dark-border flex items-center justify-center text-brand-primary"><i class="fa-brands fa-google-pay"></i></div>
                            <div>
                                <h4 class="font-bold text-white">Google Pay</h4>
                                <p class="text-xs text-dark-muted">Tap to pay anywhere</p>
                            </div>
                        </div>
                        <div class="flex gap-3">
                            <div class="w-8 h-8 rounded bg-dark-bg border border-dark-border flex items-center justify-center text-brand-primary"><i class="fa-solid fa-percent"></i></div>
                            <div>
                                <h4 class="font-bold text-white">3% Cashback</h4>
                                <p class="text-xs text-dark-muted">On all crypto spends</p>
                            </div>
                        </div>
                        <div class="flex gap-3">
                            <div class="w-8 h-8 rounded bg-dark-bg border border-dark-border flex items-center justify-center text-brand-primary"><i class="fa-solid fa-globe"></i></div>
                            <div>
                                <h4 class="font-bold text-white">No FX Fees</h4>
                                <p class="text-xs text-dark-muted">Perfect for travel</p>
                            </div>
                        </div>
                    </div>
                    
                    <a href="auth/register.php" class="inline-block">
                        <button class="bg-brand-primary hover:bg-brand-accent text-white px-8 py-3 rounded-lg font-bold transition-all shadow-neon">
                            Get Your Card
                        </button>
                    </a>
                </div>

            </div>
        </div>
    </section>

    <?php

include 'config.php';

$plans_query = mysqli_query($link, "SELECT * FROM investment_plans ORDER BY min_deposit ASC LIMIT 3");
?>

<section id="invest" class="py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-4xl font-display font-bold text-white mb-4">Investment <span class="text-brand-primary">Plans</span></h2>
        <p class="text-dark-muted max-w-2xl mx-auto mb-16">Choose a staking plan that fits your financial goals. Earn passive income with daily payouts.</p>

        <div class="grid md:grid-cols-3 gap-8">
            
            <?php 
            if(mysqli_num_rows($plans_query) > 0): 
                $count = 0;
                while($plan = mysqli_fetch_assoc($plans_query)): 
                    $count++;
                    
                    // We make the 2nd plan the "Popular" highlighted one automatically
                    $is_popular = ($count == 2); 
            ?>

                <?php if($is_popular): ?>
                    <!-- POPULAR / HIGHLIGHTED CARD -->
                    <div class="relative glass-card p-8 rounded-2xl border-brand-primary bg-brand-primary/5 text-left transform md:-translate-y-4 shadow-neon">
                        <div class="absolute top-0 right-0 bg-brand-primary text-white text-xs px-3 py-1 rounded-bl-lg rounded-tr-lg font-bold">POPULAR</div>
                        
                        <h3 class="text-2xl font-bold text-white mb-2"><?php echo htmlspecialchars($plan['name']); ?></h3>
                        <p class="text-sm text-dark-muted mb-6"><?php echo htmlspecialchars($plan['description']); ?></p>
                        
                        <div class="mb-6">
                            <span class="text-4xl font-bold text-brand-primary"><?php echo $plan['roi']; ?>%</span>
                            <span class="text-dark-muted">/ Daily ROI</span>
                        </div>
                        
                        <ul class="space-y-3 mb-8 text-sm text-gray-300">
                            <li class="flex items-center gap-2"><i class="fa-solid fa-check text-brand-primary"></i> Min Invest: $<?php echo number_format($plan['min_deposit']); ?></li>
                            <li class="flex items-center gap-2"><i class="fa-solid fa-check text-brand-primary"></i> Duration: <?php echo $plan['duration']; ?> Days</li>
                            <li class="flex items-center gap-2"><i class="fa-solid fa-check text-brand-primary"></i> Risk Level: <?php echo ucfirst($plan['risk_level']); ?></li>
                            <li class="flex items-center gap-2"><i class="fa-solid fa-check text-brand-primary"></i> 24/7 Premium Support</li>
                        </ul>
                        
                        <button onclick="window.location.href='auth/login.php';" class="w-full bg-brand-primary text-white py-3 rounded-lg hover:bg-brand-accent transition-all font-bold shadow-lg">Invest Now</button>
                    </div>

                <?php else: ?>
                    <!-- REGULAR CARD -->
                    <div class="glass-card p-8 rounded-2xl hover:border-brand-primary transition-colors text-left group">
                        <h3 class="text-2xl font-bold text-white mb-2"><?php echo htmlspecialchars($plan['name']); ?></h3>
                        <p class="text-sm text-dark-muted mb-6"><?php echo htmlspecialchars($plan['description']); ?></p>
                        
                        <div class="mb-6">
                            <span class="text-4xl font-bold <?php echo ($count == 3) ? 'text-purple-400' : 'text-brand-secondary'; ?>"><?php echo $plan['roi']; ?>%</span>
                            <span class="text-dark-muted">/ Daily ROI</span>
                        </div>
                        
                        <ul class="space-y-3 mb-8 text-sm text-gray-300">
                            <li class="flex items-center gap-2"><i class="fa-solid fa-check text-green-400"></i> Min Invest: $<?php echo number_format($plan['min_deposit']); ?></li>
                            <li class="flex items-center gap-2"><i class="fa-solid fa-check text-green-400"></i> Duration: <?php echo $plan['duration']; ?> Days</li>
                            <li class="flex items-center gap-2"><i class="fa-solid fa-check text-green-400"></i> Risk Level: <?php echo ucfirst($plan['risk_level']); ?></li>
                            <li class="flex items-center gap-2"><i class="fa-solid fa-check text-green-400"></i> Capital Back: Yes</li>
                        </ul>
                        
                        <button onclick="window.location.href='auth/login.php';" class="w-full bg-dark-bg border border-dark-border text-white py-3 rounded-lg group-hover:<?php echo ($count == 3) ? 'bg-purple-600' : 'bg-brand-primary'; ?> group-hover:border-transparent transition-all font-bold">Invest Now</button>
                    </div>
                <?php endif; ?>

            <?php 
                endwhile; 
            else: 
            ?>
                <!-- FALLBACK IF NO PLANS EXIST IN DB -->
                <div class="col-span-3 text-center py-10">
                    <p class="text-dark-muted">Investment plans are currently being updated. Please check back soon.</p>
                </div>
            <?php endif; ?>

        </div>
    </div>
</section>

    <section id="market" class="py-20 bg-dark-panel border-t border-dark-border">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-display font-bold mb-8">Market <span class="text-brand-primary">Live Data</span></h2>
            <div class="overflow-x-auto rounded-xl border border-dark-border bg-dark-bg">
                <table class="w-full text-left">
                    <thead class="bg-dark-panel text-xs uppercase text-dark-muted">
                        <tr>
                            <th class="px-6 py-4">Asset</th>
                            <th class="px-6 py-4">Price</th>
                            <th class="px-6 py-4">Change</th>
                            <th class="px-6 py-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody id="market-body" class="divide-y divide-dark-border text-sm">
                        <tr><td colspan="4" class="p-6 text-center text-dark-muted">Loading data...</td></tr>
                    </tbody>
                </table>
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
                        <li><a href="auth/register.php" class="hover:text-brand-primary">Wallet</a></li>
                        <li><a href="auth/register.php" class="hover:text-brand-primary">Card</a></li>
                        <li><a href="auth/register.php" class="hover:text-brand-primary">Plans</a></li>
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
                        <li><?php echo $site_phone; ?></li>
                        <p class="text-sm text-dark-muted">189 Tiyu West Road, Tianhe District, Guangzhou, Guangdong Province, China (Postal Code: 510620).</p>
                    </ul>
                </div>
            </div>
            <div class="text-center text-xs text-dark-muted pt-8 border-t border-dark-border">
                &copy; <?php echo date("Y"); ?> <?php echo $sitename; ?>. All rights reserved.
            </div>
        </div>
    </footer>

    <script>
      
        const menuBtn = document.getElementById('mobile-menu-btn');
        const menu = document.getElementById('mobile-menu');
        menuBtn.addEventListener('click', () => {
            menu.classList.toggle('open');
        });

       
        function switchTab(tabName) {
            // Hide all
            document.getElementById('tab-send').classList.add('hidden');
            document.getElementById('tab-receive').classList.add('hidden');
            document.getElementById('tab-swap').classList.add('hidden');
            document.getElementById('tab-receive').classList.remove('flex'); 
           
            const el = document.getElementById('tab-' + tabName);
            el.classList.remove('hidden');
            
            if(tabName === 'receive') el.classList.add('flex');
        }

        // Global variable for current ETH live price from the API pull for calculations
        let liveEthPrice = 3000; 
       
        async function fetchData() {
            try {
                const res = await fetch('https://api.coingecko.com/api/v3/coins/markets?vs_currency=usd&order=market_cap_desc&per_page=5&sparkline=false');
                const data = await res.json();
                
                const ticker = document.getElementById('crypto-ticker');
                let tickerHtml = '';
                
                let tableHtml = '';

                data.forEach(coin => {
                    const color = coin.price_change_percentage_24h >= 0 ? 'text-green-400' : 'text-red-400';
                    tickerHtml += `<span class="mx-4"><span class="font-bold text-white">${coin.symbol.toUpperCase()}</span> $${coin.current_price} <span class="${color}">${coin.price_change_percentage_24h.toFixed(2)}%</span></span>`;
                    
                    // FIXED: Save live ETH price to make SWAP calculator accurate 
                    if(coin.symbol.toLowerCase() === 'eth') {
                        liveEthPrice = coin.current_price;
                    }

                    // FIXED TRADE BUTTON: Attached Auth login route to the trade button output
                    tableHtml += `
                        <tr class="hover:bg-dark-panel transition-colors">
                            <td class="px-6 py-4 flex items-center gap-3">
                                <img src="${coin.image}" class="w-6 h-6 rounded-full">
                                <span class="text-white font-bold">${coin.name}</span>
                            </td>
                            <td class="px-6 py-4 text-white font-mono">$${coin.current_price}</td>
                            <td class="px-6 py-4 ${color} font-medium">${coin.price_change_percentage_24h.toFixed(2)}%</td>
                            <td class="px-6 py-4 text-right"><button onclick="window.location.href='auth/login.php';" class="text-brand-primary hover:text-white border border-brand-primary hover:bg-brand-primary px-3 py-1 rounded text-xs transition-colors">Trade</button></td>
                        </tr>
                    `;
                });
                
                ticker.innerHTML = tickerHtml + tickerHtml;
                const table = document.getElementById('market-body');
                table.innerHTML = tableHtml;

            } catch (e) {
                console.log(e);
            }
        }
        
        fetchData();

        // FIXED SWAP CALCULATOR: Functional real-time conversions
        const swapFromInput = document.getElementById('swap-from');
        const swapToInput = document.getElementById('swap-to');
        const usdEstimate = document.getElementById('swap-usd-estimate');

        if(swapFromInput && swapToInput) {
            // Typing in ETH converts to USDT equivalent using CoinGecko live prices fetched above
            swapFromInput.addEventListener('input', () => {
                if(swapFromInput.value && swapFromInput.value >= 0) {
                    const usdtValue = parseFloat(swapFromInput.value) * liveEthPrice;
                    swapToInput.value = usdtValue.toFixed(2);
                    usdEstimate.textContent = '~ $' + usdtValue.toFixed(2);
                } else {
                    swapToInput.value = '';
                    usdEstimate.textContent = '~ $0.00';
                }
            });

            // Typing in USDT back converts accurately into ETH equivalent
            swapToInput.addEventListener('input', () => {
                if(swapToInput.value && swapToInput.value >= 0) {
                    const ethValue = parseFloat(swapToInput.value) / liveEthPrice;
                    swapFromInput.value = ethValue.toFixed(6);
                    usdEstimate.textContent = '~ $' + parseFloat(swapToInput.value).toFixed(2);
                } else {
                    swapFromInput.value = '';
                    usdEstimate.textContent = '~ $0.00';
                }
            });
        }
    </script>

    <script type="text/javascript">
    var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
    (function(){
    var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
    s1.async=true;
    s1.src='https://embed.tawk.to/69f30761c065a21c340c4b58/1jnel9lki';
    s1.charset='UTF-8';
    s1.setAttribute('crossorigin','*');
    s0.parentNode.insertBefore(s1,s0);
    })();
    </script>
</body>
</html>