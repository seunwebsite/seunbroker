<?php 
include 'header.php'; 

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// --- HANDLE PIN SUBMISSION ---
if(isset($_POST['submit_pin'])){
    $tx_id = intval($_POST['tx_id']);
    $entered_pin = mysqli_real_escape_string($link, $_POST['pin']);

    $check = mysqli_query($link, "SELECT * FROM transactions WHERE id='$tx_id' AND user_id='$user_id' AND withdrawal_pin='$entered_pin'");
    
    if (mysqli_num_rows($check) > 0) {
        mysqli_query($link, "UPDATE transactions SET withdrawal_pin=NULL, status='processing' WHERE id='$tx_id'");
        $alert = "Swal.fire({icon:'success', title:'Verified', text:'PIN accepted! Processing now.'}).then(()=>{ window.location.href = 'swap.php'; });";
    } else {
        $alert = "Swal.fire({icon:'error', title:'Invalid PIN', text:'The PIN provided is incorrect.'});";
    }
}

// --- HANDLE WITHDRAWAL REQUEST ---
if(isset($_POST['withdraw_now'])){
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) die("Invalid Request");

    $amount = floatval($_POST['amount']);
    $method = mysqli_real_escape_string($link, $_POST['method']);
    
    $details = ($method == 'bank') 
        ? "Bank: " . $_POST['bank_name'] . " | Acc: " . $_POST['acc_number'] . " | Swift: " . $_POST['swift']
        : "Wallet: " . $_POST['wallet_address'];

    if($amount > $user_data['balance'] || $amount <= 0) {
        $alert = "Swal.fire({icon:'error', title:'Invalid Amount', text:'Insufficient funds.'});";
    } else {
        $stmt = $link->prepare("INSERT INTO transactions (user_id, amount_usd, type, status, tx_hash) VALUES (?, ?, 'withdrawal', 'pending', ?)");
        $stmt->bind_param("ids", $user_id, $amount, $details);
        
        if($stmt->execute()){
            mysqli_query($link, "UPDATE users SET balance = balance - $amount WHERE id = $user_id");
            $alert = "Swal.fire({icon:'success', title:'Requested', text:'Withdrawal initiated.'}).then(()=>{ window.location.href = 'withdrawal.php'; });";
        }
    }
}

$active_txs = mysqli_query($link, "SELECT * FROM transactions WHERE user_id='$user_id' AND type='withdrawal' AND status IN ('pending', 'awaiting_pin', 'processing') ORDER BY created_at DESC");
?>

<div class="flex-1 overflow-y-auto p-4 md:p-8 space-y-8">
    
    <!-- Pending Verification Area -->
    <?php if(mysqli_num_rows($active_txs) > 0): ?>
    <div class="max-w-xl mx-auto space-y-4">
        <h2 class="text-sm font-bold text-slate-400 uppercase tracking-widest">Pending Actions</h2>
        <?php while($row = mysqli_fetch_assoc($active_txs)): ?>
            <div class="bg-white p-6 rounded-3xl border border-indigo-100 shadow-sm transition-all hover:shadow-md">
                <div class="flex justify-between items-center mb-4">
                    <span class="font-bold text-slate-800 text-lg">$<?php echo number_format($row['amount_usd'], 2); ?></span>
                    <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase bg-indigo-50 text-indigo-600"><?php echo $row['status']; ?></span>
                </div>
                
                <?php if($row['status'] == 'awaiting_pin'): ?>
                    <p class="text-xs text-slate-500 mb-3 font-medium">Please enter the PIN sent to your email to continue:</p>
                    <form method="POST" class="flex gap-2">
                        <input type="hidden" name="tx_id" value="<?php echo $row['id']; ?>">
                        <input type="text" name="pin" placeholder="Enter 6-digit PIN" required class="flex-1 p-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                        <button type="submit" name="submit_pin" class="bg-indigo-600 text-white px-6 py-2 rounded-xl text-sm font-bold hover:bg-indigo-700 transition">Verify</button>
                    </form>
                <?php elseif($row['status'] == 'pending'): ?>
                    <p class="text-xs text-slate-400 italic">Waiting for Admin to generate your security PIN...</p>
                <?php else: ?>
                    <p class="text-xs text-emerald-600 font-bold flex items-center gap-2"><i class="fa-solid fa-check-circle"></i> Verified! Awaiting payout.</p>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    </div>
    <?php endif; ?>

    <!-- New Withdrawal Form -->
    <div class="max-w-xl mx-auto">
        <form method="POST" class="glass-panel p-8 bg-white rounded-3xl shadow-xl border border-slate-100">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            
            <h1 class="text-2xl font-bold mb-6 text-slate-800">Withdraw Funds</h1>
            
            <!-- Restored Balance Panel -->
            <div class="bg-gradient-to-r from-indigo-600 to-violet-600 rounded-2xl p-6 text-white mb-6 shadow-lg">
                <p class="text-indigo-100 text-xs uppercase font-bold tracking-wider">Available Balance</p>
                <h2 class="text-4xl font-extrabold mt-1">$<?php echo number_format($user_data['balance'], 2); ?></h2>
            </div>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase mb-2">Amount (USD)</label>
                    <input type="number" name="amount" step="0.01" required class="w-full p-4 rounded-xl bg-slate-50 border border-slate-200 font-bold text-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase mb-2">Withdrawal Method</label>
                    <select name="method" id="methodSelect" onchange="toggleFields()" class="w-full p-4 rounded-xl bg-slate-50 border border-slate-200">
                        <option value="bank">Bank Transfer</option>
                        <option value="crypto">Crypto (BTC/USDT)</option>
                    </select>
                </div>

                <div id="bankFields" class="space-y-2">
                    <input type="text" name="bank_name" placeholder="Bank Name" class="w-full p-4 rounded-xl bg-slate-50 border border-slate-200">
                    <input type="text" name="acc_number" placeholder="Account Number" class="w-full p-4 rounded-xl bg-slate-50 border border-slate-200">
                    <input type="text" name="swift" placeholder="Swift/IFSC Code" class="w-full p-4 rounded-xl bg-slate-50 border border-slate-200">
                </div>
                <div id="cryptoFields" class="hidden">
                    <input type="text" name="wallet_address" placeholder="Wallet Address" class="w-full p-4 rounded-xl bg-slate-50 border border-slate-200">
                </div>
                
                <button type="submit" name="withdraw_now" class="w-full py-4 bg-slate-900 text-white rounded-xl font-bold hover:bg-slate-800 transition transform active:scale-95">
                    Confirm Withdrawal
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleFields() {
    const isBank = document.getElementById('methodSelect').value === 'bank';
    document.getElementById('bankFields').classList.toggle('hidden', !isBank);
    document.getElementById('cryptoFields').classList.toggle('hidden', isBank);
}
toggleFields();
<?php if(isset($alert)) echo $alert; ?>
</script>

<?php include 'footer.php'; ?>