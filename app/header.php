<?php 
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// --- 1. DATABASE CONNECTION ---
$configPath = 'C:/xampp/htdocs/fichain/config/db.php';
if (file_exists($configPath)) {
    require_once $configPath;
} else {
    die("CRITICAL ERROR: File not found at " . $configPath);
}

if (!isset($link)) { die("CRITICAL ERROR: Database connection link not found."); }

// --- 2. FETCH GLOBAL SETTINGS ---
$settings_query = mysqli_query($link, "SELECT * FROM settings LIMIT 1");
$site_settings = mysqli_fetch_assoc($settings_query);

$username = $user_data['username'] ?? 'User';
$email    = $user_data['email'] ?? 'Not set';

$sitename = $site_settings['sitename'] ?? 'Fchain Capital';
$siteurl  = $site_settings['siteurl'] ?? 'http://localhost/fichain';
$referral_bonus_percentage = $site_settings['referral_bonus_percentage'] ?? '15.00';

function clean($data) {
    global $link;
    return mysqli_real_escape_string($link, htmlspecialchars(strip_tags($data)));
}

// --- 3. USER SESSION & DATA ---
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_query = mysqli_query($link, "SELECT * FROM users WHERE id = '$user_id'");
$user_data = mysqli_fetch_assoc($user_query);

$fullname    = $user_data['full_name'] ?? 'User';
$kyc_status  = $user_data['kyc_status'] ?? 'none';
$account_id  = $user_data['id'];
$avatar_url  = "https://ui-avatars.com/api/?name=" . urlencode($fullname) . "&background=random";

$status_label = "Unverified";
$status_color = "text-slate-500";
if ($kyc_status == 'approved') { $status_label = "Verified Pro"; $status_color = "text-green-500"; }
elseif ($kyc_status == 'pending') { $status_label = "Pending Review"; $status_color = "text-yellow-500"; }

$wallet_connected = (mysqli_num_rows(mysqli_query($link, "SELECT id FROM crypto_wallets WHERE user_id = '$user_id' LIMIT 1")) > 0);
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?php echo $sitename; ?> - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <script type="text/javascript">
        function googleTranslateElementInit() {
            new google.translate.TranslateElement({
                pageLanguage: 'en',
                includedLanguages: 'en,es,fr,de,zh-CN,ar,hi,pt,ru,ja,ko,it,nl,tr',
                layout: google.translate.TranslateElement.InlineLayout.SIMPLE,
                autoDisplay: false
            }, 'google_translate_element');
        }
    </script>
    <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; transition: background-color 0.3s ease, color 0.3s ease; }
        #google_translate_element { display: flex; align-items: center; }
        .goog-te-gadget-simple { background-color: transparent !important; border: 1px solid #334155 !important; border-radius: 9999px !important; padding: 6px 14px !important; display: flex !important; align-items: center !important; }
        .goog-te-gadget-icon, .goog-te-gadget-simple img { display: none !important; }
        .goog-te-gadget-simple span { color: #94a3b8 !important; font-size: 13px !important; }
        .dropdown-menu { display: none; }
        .dropdown-menu.show { display: block; }
    </style>
</head>

<body class="flex h-screen bg-slate-50 dark:bg-[#02040a] text-slate-800 dark:text-slate-300">

    <div id="sidebar-overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-black/80 z-40 hidden backdrop-blur-sm transition-opacity"></div>

    <aside id="mobile-sidebar" class="fixed inset-y-0 left-0 z-50 w-72 bg-white dark:bg-[#050810] border-r border-slate-200 dark:border-white/5 flex flex-col transform -translate-x-full md:translate-x-0 md:relative transition-transform duration-300 shadow-2xl">
        <div class="h-20 flex items-center gap-3 px-8 border-b border-slate-200 dark:border-white/5">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-600 to-violet-600 flex items-center justify-center text-white font-bold text-xl shadow-lg shadow-indigo-500/30">
                <i class="fa-solid fa-bolt"></i>
            </div>
            <span class="text-xl font-bold text-slate-900 dark:text-white tracking-tight"><?php echo $sitename; ?></span>
        </div>
    
        <nav class="flex-1 overflow-y-auto py-6 px-4 space-y-2">
            <div class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest px-4 mb-2">Platform</div>
            <a href="dashboard.php" class="flex items-center gap-4 px-4 py-3.5 bg-indigo-50 dark:bg-gradient-to-r dark:from-indigo-600/20 dark:to-transparent border-l-4 border-indigo-500 text-indigo-700 dark:text-white rounded-r-xl transition-all">
                <i class="fa-solid fa-grid-2 text-indigo-500 dark:text-indigo-400"></i> Home
            </a>
            <a href="assets.php" class="nav-item flex items-center gap-4 px-4 py-3.5 text-slate-600 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-white/5 rounded-xl transition-all"><i class="fa-solid fa-coins"></i> Deposits</a>
            <a href="investments.php" class="nav-item flex items-center gap-4 px-4 py-3.5 text-slate-600 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-white/5 rounded-xl transition-all"><i class="fa-solid fa-chart-line"></i> Trading Plans</a>
            <a href="auto-trading.php" class="nav-item flex items-center gap-4 px-4 py-3.5 text-slate-600 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-white/5 rounded-xl transition-all"><i class="fa-solid fa-robot"></i> Live Trading</a>
            <a href="swap.php" class="nav-item flex items-center gap-4 px-4 py-3.5 text-slate-600 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-white/5 rounded-xl transition-all"><i class="fa-solid fa-arrow-right-arrow-left"></i> Withdrawals</a>
            <div class="h-px bg-slate-200 dark:bg-white/5 my-4 mx-4"></div>
            <div class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest px-4 mb-2">Banking</div>
            <a href="kyc.php" class="nav-item flex items-center gap-4 px-4 py-3.5 text-slate-600 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-white/5 rounded-xl transition-all"><i class="fa-solid fa-shield-halved"></i> AML / KYC</a>
            <a href="referrals.php" class="nav-item flex items-center gap-4 px-4 py-3.5 text-slate-600 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-white/5 rounded-xl transition-all"><i class="fa-solid fa-users"></i> Referrals</a>
        </nav>
    </aside>

    <main class="flex-1 flex flex-col h-full overflow-hidden relative">
        <div class="absolute top-0 left-0 w-full h-96 bg-indigo-500/10 dark:bg-indigo-900/20 blur-[120px] pointer-events-none"></div>

        <header class="h-20 glass-header flex items-center justify-between px-4 md:px-8 sticky top-0 z-30 transition-colors duration-300">
            <button onclick="toggleSidebar()" class="md:hidden text-slate-600 dark:text-white p-2 mr-2"><i class="fa-solid fa-bars text-xl"></i></button>

            <div class="hidden md:flex flex-1 items-center gap-4 max-w-lg">
                <?php if ($wallet_connected): ?>
                    <a href="connect-wallet.php" class="flex items-center gap-2 px-4 py-2 rounded-full bg-green-500/10 border border-green-500/20 text-green-600 dark:text-green-400 text-xs font-bold transition-all"><span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span> Wallet Connected</a>
                <?php else: ?>
                    <a href="connect-wallet.php" class="flex items-center gap-2 px-4 py-2 rounded-full bg-indigo-600 text-white text-xs font-bold shadow-lg shadow-indigo-500/20 transition-all"><i class="fa-solid fa-link"></i> Connect Wallet</a>
                <?php endif; ?>
            </div>

            <div class="flex items-center gap-3 md:gap-5 ml-auto">
                <button onclick="toggleTheme()" class="w-10 h-10 rounded-full bg-slate-100 dark:bg-white/5 text-slate-600 dark:text-yellow-400 hover:bg-slate-200 dark:hover:bg-white/10 transition-colors flex items-center justify-center border border-slate-200 dark:border-white/5">
                    <i class="fa-solid fa-sun text-lg hidden dark:block"></i> 
                    <i class="fa-solid fa-moon text-lg block dark:hidden"></i> 
                </button>
                
                <div id="google_translate_element" class="hidden md:flex"></div>

                <div class="relative">
                    <button id="profile-btn" class="flex items-center gap-3 border-l border-slate-200 dark:border-white/10 pl-4 md:pl-6 focus:outline-none">
                        <div class="hidden md:block text-right">
                            <p class="text-sm font-bold text-slate-800 dark:text-white leading-tight"><?php echo $fullname; ?></p>
                            <p class="text-[10px] <?php echo $status_color; ?> font-bold uppercase tracking-wider"><?php echo $status_label; ?></p>
                        </div>
                        <img src="<?php echo $avatar_url; ?>" alt="User" class="w-10 h-10 rounded-full object-cover border-2 border-indigo-500">
                        <i id="profile-arrow" class="fa-solid fa-chevron-down text-slate-400 text-xs ml-1 transition-transform"></i>
                    </button>

                    <div id="profile-dropdown" class="dropdown-menu absolute right-0 top-full mt-4 w-60 bg-white dark:bg-[#121826] border border-slate-200 dark:border-white/10 rounded-2xl shadow-xl z-50">
                        <div class="p-4 border-b border-slate-100 dark:border-white/5 md:hidden">
                            <p class="font-bold"><?php echo $fullname; ?></p>
                        </div>
                        <ul class="py-2">
                            <li><a href="profile.php" class="block px-5 py-3 text-sm hover:bg-slate-50 dark:hover:bg-white/5 flex items-center gap-3"><i class="fa-regular fa-user w-4"></i> My Profile</a></li>
                            <li><a href="logout.php" class="block px-5 py-3 text-sm text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 flex items-center gap-3"><i class="fa-solid fa-right-from-bracket w-4"></i> Logout</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </header>

<script>
    function toggleSidebar() {
        document.getElementById('mobile-sidebar').classList.toggle('-translate-x-full');
        document.getElementById('sidebar-overlay').classList.toggle('hidden');
    }

    function toggleTheme() {
        document.documentElement.classList.toggle('dark');
    }

    document.addEventListener('DOMContentLoaded', () => {
        const btn = document.getElementById('profile-btn');
        const menu = document.getElementById('profile-dropdown');
        const arrow = document.getElementById('profile-arrow');

        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            menu.classList.toggle('show');
            arrow.classList.toggle('rotate-180');
        });

        window.addEventListener('click', () => {
            menu.classList.remove('show');
            arrow.classList.remove('rotate-180');
        });
    });
</script>