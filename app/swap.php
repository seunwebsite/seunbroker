<?php 
include 'header.php'; 

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

if(isset($_POST['withdraw_now'])){
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) die("Invalid Request");

    $amount = floatval($_POST['amount']);
    $method = mysqli_real_escape_string($link, $_POST['method']);
    
    // Construct details based on method
    if($method == 'bank') {
        $details = "Bank: " . mysqli_real_escape_string($link, $_POST['bank_name']) . 
                   " | Acc: " . mysqli_real_escape_string($link, $_POST['acc_number']) . 
                   " | Swift: " . mysqli_real_escape_string($link, $_POST['swift']);
    } else {
        $details = "Wallet: " . mysqli_real_escape_string($link, $_POST['wallet_address']);
    }

    if($amount > $user_data['balance'] || $amount <= 0) {
        $alert = "Swal.fire({icon:'error', title:'Invalid Amount', text:'You have insufficient funds or invalid amount.'});";
    } else {
        mysqli_begin_transaction($link);
        try {
            // Save withdrawal request in transactions table
            $stmt = $link->prepare("
                INSERT INTO transactions (
                    user_id,
                    amount_usd,
                    type,
                    status,
                    tx_hash
                ) VALUES (
                    ?, ?, 'withdrawal', 'pending', ?
                )
            ");

            $stmt->bind_param("ids", $user_id, $amount, $details);
            $stmt->execute();
            
            $stmt2 = $link->prepare("UPDATE users SET balance = balance - ? WHERE id = ?");
            $stmt2->bind_param("di", $amount, $user_id);
            $stmt2->execute();

            mysqli_commit($link);
            $alert = "Swal.fire({icon:'success', title:'Success', text:'Withdrawal request submitted!'}).then(()=>window.location.href='dashboard.php');";
        } catch (Exception $e) {
            mysqli_rollback($link);
            $alert = "Swal.fire({icon:'error', title:'Error', text:'System failure. Please try again.'});";
        }
    }
}
?>

<div class="flex-1 overflow-y-auto p-4 md:p-8 pb-24 flex flex-col items-center">
    <div class="w-full max-w-xl">
        <form method="POST" id="withdrawForm">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            
            <div class="glass-panel rounded-3xl p-8 border border-slate-200 dark:border-white/5 shadow-2xl bg-white dark:bg-slate-900">
                <h1 class="text-2xl font-bold mb-6 text-slate-900 dark:text-white">Withdraw Funds</h1>
                
                <!-- Restored Balance Panel -->
                <div class="bg-indigo-600 rounded-2xl p-6 text-white mb-6">
                    <p class="text-indigo-100 text-xs uppercase font-bold">Available Balance</p>
                    <h2 class="text-3xl font-extrabold">$<?php echo number_format($user_data['balance'], 2); ?></h2>
                </div>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Amount (USD)</label>
                        <input type="number" name="amount" step="0.01" max="<?php echo $user_data['balance']; ?>" required 
                               class="w-full p-4 rounded-xl bg-slate-100 dark:bg-[#0B0F19] border border-slate-200 dark:border-white/5 font-bold text-lg">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Withdrawal Method</label>
                        <select name="method" id="methodSelect" onchange="toggleFields()" class="w-full p-4 rounded-xl bg-slate-100 dark:bg-[#0B0F19] border border-slate-200 dark:border-white/5">
                            <option value="bank">Bank Transfer</option>
                            <option value="bitcoin">Bitcoin (BTC)</option>
                            <option value="usdt">USDT (TRC20)</option>
                        </select>
                    </div>

                    <!-- Bank Fields -->
                    <div id="bankFields" class="space-y-4">
                        <input type="text" name="bank_name" id="bank_name" placeholder="Bank Name" class="w-full p-4 rounded-xl bg-slate-100 dark:bg-[#0B0F19] border border-slate-200">
                        <input type="text" name="acc_number" id="acc_number" placeholder="Account Number" class="w-full p-4 rounded-xl bg-slate-100 dark:bg-[#0B0F19] border border-slate-200">
                        <input type="text" name="swift" id="swift" placeholder="Swift/IFSC Code" class="w-full p-4 rounded-xl bg-slate-100 dark:bg-[#0B0F19] border border-slate-200">
                    </div>

                    <!-- Crypto Fields -->
                    <div id="cryptoFields" class="hidden">
                        <input type="text" name="wallet_address" id="wallet_address" placeholder="Wallet Address" class="w-full p-4 rounded-xl bg-slate-100 dark:bg-[#0B0F19] border border-slate-200">
                    </div>

                    <button type="submit" name="withdraw_now" class="w-full py-4 rounded-xl bg-gradient-to-r from-indigo-600 to-violet-600 text-white font-bold hover:shadow-lg transition-transform active:scale-95">
                        Confirm Withdrawal
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function toggleFields() {
    const method = document.getElementById('methodSelect').value;
    const bankFields = document.getElementById('bankFields');
    const cryptoFields = document.getElementById('cryptoFields');
    
    if (method === 'bank') {
        bankFields.classList.remove('hidden');
        cryptoFields.classList.add('hidden');
        document.getElementById('bank_name').required = true;
        document.getElementById('acc_number').required = true;
        document.getElementById('swift').required = true;
        document.getElementById('wallet_address').required = false;
    } else {
        bankFields.classList.add('hidden');
        cryptoFields.classList.remove('hidden');
        document.getElementById('bank_name').required = false;
        document.getElementById('acc_number').required = false;
        document.getElementById('swift').required = false;
        document.getElementById('wallet_address').required = true;
    }
}
// Run on load
toggleFields();

<?php if(isset($alert)) echo $alert; ?>
</script>

<?php include 'footer.php'; ?>