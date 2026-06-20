<?php 
session_start();
require_once '../config/db.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$sitename = $site_settings['sitename'] ?? 'Fchain Capital';
// 1. Fetch User Data
$user_query = mysqli_query($link, "SELECT * FROM users WHERE id = '$user_id'");
$user_data = mysqli_fetch_assoc($user_query);

$username = $user_data['username'] ?? 'User';
$total_balance = $user_data['balance'] ?? 0;
$ref_bonus = $user_data['referral_earnings'] ?? 0;


// 1. Fetch all investment rows for this user
$profit_query = mysqli_query($link, "SELECT current_profit FROM investments WHERE user_id = '$user_id'");

$total_profit = 0; // Initialize at 0

// 2. Loop through every row and add the profit to the total
while ($row = mysqli_fetch_assoc($profit_query)) {
    $total_profit += (float)$row['current_profit'];
}

// Now $total_profit holds the accurate sum of all 13 rows

// 3. Fetch Total Deposit (Filter by type 'deposit' and status 'completed')
$dep_query = mysqli_query($link, "SELECT SUM(amount_usd) as total FROM transactions WHERE user_id = '$user_id' AND type = 'deposit' AND status = 'completed'");
$total_deposit = mysqli_fetch_assoc($dep_query)['total'] ?? 0;

// 4. Fetch Total Withdrawal (Filter by type 'withdrawal' and status 'completed')
$with_trans_query = mysqli_query($link, "SELECT SUM(amount_usd) as total FROM transactions WHERE user_id = '$user_id' AND type = 'withdrawal' AND status = 'completed'");
$with_trans_data = mysqli_fetch_assoc($with_trans_query)['total'] ?? 0;

$with_table_query = mysqli_query($link, "SELECT SUM(amount) as total FROM withdrawals WHERE user_id = '$user_id' AND status = 'completed'");
$with_table_data = mysqli_fetch_assoc($with_table_query)['total'] ?? 0;

$total_withdrawal = $with_trans_data + $with_table_data;

// 5. Transactions Count (Total count of all transactions for this user)
$trans_count_query = mysqli_query($link, "SELECT COUNT(*) as count FROM transactions WHERE user_id = '$user_id'");
$transactions_count = mysqli_fetch_assoc($trans_count_query)['count'] ?? 0;
$recent_tx_query = mysqli_query($link, "SELECT * FROM transactions WHERE user_id = '$user_id' ORDER BY created_at DESC LIMIT 5");

// 6. Active Plans Count
$plan_query = mysqli_query($link, "SELECT COUNT(*) as count FROM investments WHERE user_id = '$user_id' AND status = 'active'");
$active_plans = mysqli_fetch_assoc($plan_query)['count'] ?? 0;

// 7. Bonus 
$bonus = 0; 

include 'header.php'; 
?>

<div class="flex-1 overflow-y-auto p-4 md:p-8 pb-24 space-y-6">

    <!-- Welcome Section -->
    <div class="space-y-2">
        <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white">Welcome, <?php echo htmlspecialchars($username); ?>!</h1>
        <div class="inline-flex items-center gap-2 bg-blue-600 text-white py-2 px-4 rounded-xl text-sm font-bold shadow-lg shadow-blue-500/20">
            <span class="bg-white/20 px-2 py-0.5 rounded text-[10px] uppercase tracking-wider">New</span>
            <span>Welcome. Invest and earn with a lot of benefits</span>
            <i class="fa-solid fa-chevron-right ml-2 text-xs"></i>
        </div>
    </div>

    <!-- Account Summary Panel -->
    <div class="glass-panel rounded-3xl p-6 md:p-8 border border-slate-200 dark:border-white/5">
        <h2 class="text-xl font-bold text-slate-800 dark:text-white mb-6">Account Summary</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <?php 
            $summary = [
                ['Account balance', number_format($total_balance, 2), 'fa-wallet', 'text-blue-500'],
                ['Total Profit', number_format($total_profit, 2), 'fa-coins', 'text-yellow-500'],
                ['Bonus', number_format($bonus, 2), 'fa-gift', 'text-pink-500'],
                ['Referral Bonus', number_format($ref_bonus, 2), 'fa-users', 'text-indigo-500'],
                ['Total Deposit', number_format($total_deposit, 2), 'fa-arrow-down', 'text-green-500'],
                ['Total Withdrawal', number_format($total_withdrawal, 2), 'fa-arrow-up', 'text-red-500']
            ];
            foreach($summary as $item): ?>
                <div class="bg-slate-50 dark:bg-slate-900/50 p-5 rounded-2xl border border-slate-200 dark:border-white/5 flex items-center justify-between">
                    <div>
                        <p class="text-xs text-slate-500 uppercase tracking-wider font-bold mb-1"><?php echo $item[0]; ?></p>
                        <h3 class="text-2xl font-extrabold text-slate-800 dark:text-white">$<?php echo $item[1]; ?></h3>
                    </div>
                    <i class="fa-solid <?php echo $item[2]; ?> <?php echo $item[3]; ?> text-2xl opacity-80"></i>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <?php
// Count active plans
$plan_query = mysqli_query($link, "SELECT COUNT(*) as count FROM investments WHERE user_id = '$user_id' AND status = 'active'");
$plan_count = mysqli_fetch_assoc($plan_query)['count'] ?? 0;
?>

<div class="glass-panel rounded-3xl p-6 md:p-8 border border-slate-200 dark:border-white/5">
    <?php
$count_query = mysqli_query($link, "SELECT COUNT(*) as total FROM investments WHERE user_id = '$user_id' AND status = 'active'");
$plan_count = mysqli_fetch_assoc($count_query)['total'] ?? 0;
?>

<h2 class="text-xl font-bold text-slate-800 dark:text-white mb-6">Active Plans (<?php echo $plan_count; ?>)</h2>
    <?php if ($plan_count > 0): ?>
        <!-- You can add a loop here to display the plans -->
        <p class="text-green-500">You have <?php echo $plan_count; ?> active investment(s).</p>
    <?php else: ?>
        <div class="text-center py-12 border-2 border-dashed border-slate-200 dark:border-white/10 rounded-2xl">
            <p class="text-slate-500 mb-6">You do not have an active investment plan at the moment.</p>
            <a href="investments.php" class="bg-indigo-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-indigo-700 transition-colors">Buy a plan</a>
        </div>
    <?php endif; ?>
</div>

    <!-- Transactions -->
    <!-- Transactions -->
    <div class="glass-panel rounded-3xl p-6 border border-slate-200 dark:border-white/5">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold text-slate-800 dark:text-white">Recent Transactions (<?php echo $transactions_count; ?>)</h2>
            <a href="transactions.php" class="text-indigo-500 text-sm font-bold flex items-center gap-1 hover:underline">
                <i class="fa-solid fa-clipboard-list"></i> View all
            </a>
        </div>
        
        <?php if (mysqli_num_rows($recent_tx_query) > 0): ?>
            <div class="space-y-4">
                <?php while($tx = mysqli_fetch_assoc($recent_tx_query)): ?>
                    <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-900/50 rounded-xl border border-slate-200 dark:border-white/5">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center <?php echo ($tx['type'] == 'deposit') ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600'; ?>">
                                <i class="fa-solid <?php echo ($tx['type'] == 'deposit') ? 'fa-arrow-down' : 'fa-arrow-up'; ?> text-xs"></i>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-800 dark:text-white capitalize"><?php echo $tx['type']; ?></p>
                                <p class="text-[10px] text-slate-500"><?php echo date('M d, Y', strtotime($tx['created_at'])); ?></p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-bold <?php echo ($tx['type'] == 'deposit') ? 'text-green-500' : 'text-red-500'; ?>">
                                <?php echo ($tx['type'] == 'deposit') ? '+' : '-'; ?>$<?php echo number_format($tx['amount_usd'], 2); ?>
                            </p>
                            <span class="text-[9px] uppercase text-slate-400"><?php echo $tx['status']; ?></span>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="w-full text-center py-8 text-slate-400 italic">No transactions record yet</div>
        <?php endif; ?>
    </div>

    <!-- Refer Us -->
    <div class="glass-panel rounded-3xl p-6 border border-slate-200 dark:border-white/5">
        <h2 class="text-xl font-bold text-slate-800 dark:text-white mb-2">Refer Us & Earn</h2>
        <p class="text-slate-500 text-sm mb-6">Use the below link to invite your friends.</p>
        <div class="flex gap-2">
            <input type="text" readonly value="https://globalx-investment.com//ref/<?php echo $username; ?>" 
                   class="flex-1 bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-white/5 rounded-xl px-4 py-3 text-sm font-mono text-slate-600 dark:text-slate-300">
            <button onclick="copyToClipboard()" class="bg-slate-800 dark:bg-white text-white dark:text-black px-6 py-3 rounded-xl font-bold hover:bg-slate-700 dark:hover:bg-slate-200 transition-colors">
                <i class="fa-solid fa-copy"></i>
            </button>
        </div>
    </div>

</div>

<script>
function copyToClipboard() {
    const el = document.querySelector('input[readonly]');
    el.select();
    document.execCommand('copy');
    alert('Link copied!');
}
</script>

<?php include 'footer.php'; ?>