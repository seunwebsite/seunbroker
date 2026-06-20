<?php
session_start();

require_once '../config/bootstrap.php';
require_once '../config/function.php';

// Security Check
if(!isset($_SESSION['admin_id'])){
    echo '<script>window.location.href="login.php"</script>';
}

$admin_email = $_SESSION['admin_email'];
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $sitename; ?> Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; color: #0f172a; }
        
        .glass-panel { 
            background: rgba(255, 255, 255, 0.7); 
            backdrop-filter: blur(10px); 
            border: 1px solid #e2e8f0; 
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
        
        .nav-item.active { 
            background: #eff6ff; 
            color: #4f46e5; 
            border-right: 3px solid #4f46e5; 
            font-weight: 600;
        }
        
        /* Mobile Sidebar Overlay */
        #mobileOverlay { background: rgba(0,0,0,0.5); }
    </style>
</head>
<body class="flex h-screen overflow-hidden text-slate-800 bg-slate-50">

    <div id="mobileOverlay" onclick="toggleSidebar()" class="fixed inset-0 z-20 hidden md:hidden glass"></div>

    <aside id="sidebar" class="fixed inset-y-0 left-0 w-64 bg-white border-r border-slate-200 flex flex-col transform -translate-x-full md:relative md:translate-x-0 transition-transform duration-300 z-30 shadow-xl md:shadow-none">
        <div class="p-6 flex items-center justify-between border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-indigo-600 flex items-center justify-center text-white font-bold text-lg shadow-md">
                    <i class="fa-solid fa-shield-cat"></i>
                </div>
                <span class="text-lg font-bold tracking-wide text-slate-800">ADMIN</span>
            </div>
            <button onclick="toggleSidebar()" class="md:hidden text-slate-400 hover:text-red-500">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>

        <nav class="flex-1 px-4 space-y-1 overflow-y-auto mt-4 pb-4">
            
            <p class="px-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2 mt-2">Overview</p>
            <a href="index.php" class="nav-item flex items-center gap-3 px-4 py-3 text-sm font-medium text-slate-500 hover:bg-slate-50 hover:text-slate-900 rounded-xl transition-all <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">
                <i class="fa-solid fa-grid-2"></i> Dashboard
            </a>
            <a href="users.php" class="nav-item flex items-center gap-3 px-4 py-3 text-sm font-medium text-slate-500 hover:bg-slate-50 hover:text-slate-900 rounded-xl transition-all <?php echo ($current_page == 'users.php') ? 'active' : ''; ?>">
                <i class="fa-solid fa-users"></i> Users
            </a>
            <a href="kyc.php" class="nav-item flex items-center gap-3 px-4 py-3 text-sm font-medium text-slate-500 hover:bg-slate-50 hover:text-slate-900 rounded-xl transition-all <?php echo ($current_page == 'kyc.php') ? 'active' : ''; ?>">
                <i class="fa-solid fa-id-card"></i> KYC Requests
            </a>

            <p class="px-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2 mt-6">Finance</p>
            <a href="deposits.php" class="nav-item flex items-center gap-3 px-4 py-3 text-sm font-medium text-slate-500 hover:bg-slate-50 hover:text-slate-900 rounded-xl transition-all <?php echo ($current_page == 'deposits.php') ? 'active' : ''; ?>">
                <i class="fa-solid fa-circle-down"></i> Deposits
            </a>
            <a href="withdrawals.php" class="nav-item flex items-center gap-3 px-4 py-3 text-sm font-medium text-slate-500 hover:bg-slate-50 hover:text-slate-900 rounded-xl transition-all <?php echo ($current_page == 'withdrawals.php') ? 'active' : ''; ?>">
                <i class="fa-solid fa-circle-up"></i> Withdrawals
            </a>

            <p class="px-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2 mt-6">Features</p>
            <a href="investments.php" class="nav-item flex items-center gap-3 px-4 py-3 text-sm font-medium text-slate-500 hover:bg-slate-50 hover:text-slate-900 rounded-xl transition-all <?php echo ($current_page == 'investments.php') ? 'active' : ''; ?>">
                <i class="fa-solid fa-chart-line"></i> User Investments
            </a>
            <a href="plans.php" class="nav-item flex items-center gap-3 px-4 py-3 text-sm font-medium text-slate-500 hover:bg-slate-50 hover:text-slate-900 rounded-xl transition-all <?php echo ($current_page == 'plans.php') ? 'active' : ''; ?>">
                <i class="fa-solid fa-sliders"></i> Manage Plans
            </a>

            <p class="px-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2 mt-6">System</p>
            <a href="settings.php" class="nav-item flex items-center gap-3 px-4 py-3 text-sm font-medium text-slate-500 hover:bg-slate-50 hover:text-slate-900 rounded-xl transition-all <?php echo ($current_page == 'settings.php') ? 'active' : ''; ?>">
                <i class="fa-solid fa-gear"></i> Settings
            </a>
            <a href="logout.php" class="nav-item flex items-center gap-3 px-4 py-3 text-sm font-medium text-red-500 hover:bg-red-50 hover:text-red-600 rounded-xl transition-all">
                <i class="fa-solid fa-right-from-bracket"></i> Logout
            </a>
        </nav>
    </aside>

    <main class="flex-1 flex flex-col h-screen overflow-hidden relative">
        <header class="h-16 bg-white/80 backdrop-blur-md border-b border-slate-200 flex items-center justify-between px-6 sticky top-0 z-20 shadow-sm">
            <div class="flex items-center gap-4">
                <button onclick="toggleSidebar()" class="md:hidden text-slate-600 hover:text-indigo-600 transition-colors">
                    <i class="fa-solid fa-bars text-xl"></i>
                </button>
                <h2 class="font-bold text-slate-800 text-lg">Dashboard</h2>
            </div>
            
            <div class="flex items-center gap-4">
                <div class="text-right hidden md:block">
                    <p class="text-xs text-slate-400 font-medium">Logged in as</p>
                    <p class="text-sm font-bold text-slate-700"><?php echo $admin_email; ?></p>
                </div>
                <div class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold border border-indigo-200 shadow-sm">A</div>
            </div>
        </header>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('mobileOverlay');
        
        if (sidebar.classList.contains('-translate-x-full')) {
            // Open
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden');
        } else {
            // Close
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        }
    }
</script>