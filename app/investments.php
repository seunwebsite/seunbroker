<?php
include 'header.php';

$alert = "";

// 1. Handle Investment Submission
if(isset($_POST['start_investment'])){
    $plan_name = mysqli_real_escape_string($link, $_POST['plan_name']);
    $amount    = floatval($_POST['amount']);
    $roi       = floatval($_POST['roi']);

    // Fetch plan details to verify limits
    $plan_q = mysqli_query($link, "SELECT min_deposit, max_deposit FROM investment_plans WHERE name='$plan_name'");
    $p = mysqli_fetch_assoc($plan_q);
    
    // Verify limits
    $is_valid = ($amount >= $p['min_deposit']);
    if ($p['max_deposit'] > 0 && $amount > $p['max_deposit']) { $is_valid = false; }

    if ($is_valid) {
        $q = mysqli_query($link, "SELECT balance FROM users WHERE id='$user_id'");
        $r = mysqli_fetch_assoc($q);
        
        if(floatval($r['balance']) >= $amount){
            mysqli_query($link, "UPDATE users SET balance = balance - $amount WHERE id='$user_id'");
            $sql = "INSERT INTO investments (user_id, plan_name, amount, roi_percent, status, start_date) 
                    VALUES ('$user_id', '$plan_name', '$amount', '$roi', 'active', NOW())";
            
            if(mysqli_query($link, $sql)){
                mysqli_query($link, "INSERT INTO transactions (user_id, type, amount_usd, status, tx_hash) 
                                   VALUES ('$user_id', 'investment', '$amount', 'completed', 'INV-" . time() . "')");
                $alert = "Swal.fire({icon:'success', title:'Success!', text:'Investment started successfully.'}).then(()=>{ window.location.href='investments.php'; });";
            }
        } else {
            $alert = "Swal.fire({icon:'error', title:'Insufficient Funds', text:'Your wallet balance is too low.'});";
        }
    } else {
        $alert = "Swal.fire({icon:'error', title:'Invalid Amount', text:'Amount is outside the allowed limits for this plan.'});";
    }
}

$plans_query = mysqli_query($link, "SELECT * FROM investment_plans");
$history_query = mysqli_query($link, "SELECT * FROM investments WHERE user_id='$user_id' ORDER BY start_date DESC");
?>

<div class="flex-1 overflow-y-auto p-4 md:p-8 pb-24 space-y-6">
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Investment Plans</h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <?php while($plan = mysqli_fetch_assoc($plans_query)): ?>
        <div class="glass-panel rounded-3xl p-6 border border-slate-200 dark:border-white/5">
            <h2 class="text-xl font-bold mb-2 text-slate-900 dark:text-white"><?php echo $plan['name']; ?></h2>
            <div class="space-y-2 mb-6 text-sm text-slate-500 dark:text-slate-400">
                <p>Min: $<?php echo number_format($plan['min_deposit']); ?></p>
                <p>Profit: <span class="font-bold text-indigo-500"><?php echo $plan['roi']; ?>% <?php echo $plan['roi_type'] ?? 'Daily'; ?></span></p>
            </div>
            <button onclick="openModal(
                '<?php echo addslashes($plan['name']); ?>', 
                <?php echo $plan['min_deposit']; ?>, 
                <?php echo $plan['max_deposit'] ?? 0; ?>, 
                '<?php echo $plan['roi']; ?>', 
                '<?php echo $plan['duration']; ?>', 
                '<?php echo $plan['roi_type'] ?? 'Daily'; ?>'
            )" class="w-full py-3 rounded-xl bg-indigo-600 text-white font-bold hover:bg-indigo-700 transition-all">
                Select Plan
            </button>
        </div>
        <?php endwhile; ?>
    </div>

    <!-- History -->
    <div class="glass-panel rounded-3xl p-6 border border-slate-200 dark:border-white/5">
        <h3 class="text-xl font-bold mb-4 text-slate-900 dark:text-white">Investment History</h3>
        <table class="w-full text-sm">
            <thead><tr class="text-slate-500 border-b dark:border-white/5 text-left"><th class="py-3">Plan</th><th class="py-3">Amount</th><th class="py-3">Status</th></tr></thead>
            <tbody>
                <?php while($inv = mysqli_fetch_assoc($history_query)): ?>
                <tr class="border-b border-slate-100 dark:border-white/5">
                    <td class="py-3 font-bold"><?php echo $inv['plan_name']; ?></td>
                    <td class="py-3">$<?php echo number_format($inv['amount'], 2); ?></td>
                    <td class="py-3 text-green-500 font-bold"><?php echo ucfirst($inv['status']); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal -->
<div id="investModal" class="fixed inset-0 z-[60] flex items-center justify-center bg-black/80 hidden p-4">
    <div class="glass-panel w-full max-w-lg rounded-3xl p-8 border border-white/10 flex flex-col md:flex-row gap-8">
        <form method="POST" class="flex-1">
            <input type="hidden" name="plan_name" id="hiddenPlanName">
            <input type="hidden" name="roi" id="hiddenRoi">
            <h2 class="text-xl font-bold mb-4 text-slate-900 dark:text-white" id="modalPlanName">Confirm Investment</h2>
            <div class="mb-4">
                <label class="text-xs font-bold uppercase text-slate-500">Amount (USD)</label>
                <input type="number" name="amount" id="amtInput" required class="w-full p-3 rounded-xl bg-slate-100 dark:bg-black border border-white/10 mt-1 font-bold">
            </div>
            <button type="submit" name="start_investment" class="w-full py-3 rounded-xl bg-indigo-600 text-white font-bold">Confirm & Invest</button>
            <button type="button" onclick="closeModal()" class="w-full py-3 mt-2 text-slate-500 font-bold text-sm">Cancel</button>
        </form>

        <div class="flex-1 bg-slate-100 dark:bg-black/30 p-5 rounded-2xl text-sm space-y-3">
            <h3 class="font-bold border-b border-white/10 pb-2 mb-2">Investment Details</h3>
            <div class="flex justify-between text-slate-500"><span>Duration</span> <span id="mDuration" class="text-slate-900 dark:text-white font-bold"></span></div>
            <div class="flex justify-between text-slate-500"><span>Profit</span> <span id="mRoi" class="text-slate-900 dark:text-white font-bold"></span></div>
            <div class="flex justify-between text-slate-500"><span>Min</span> <span id="mMin" class="text-slate-900 dark:text-white font-bold"></span></div>
            <div class="flex justify-between text-slate-500"><span>Max</span> <span id="mMax" class="text-slate-900 dark:text-white font-bold"></span></div>
        </div>
    </div>
</div>

<script>
    let minVal, maxVal;
    function openModal(name, min, max, roi, duration, type) {
        minVal = min; maxVal = max;
        document.getElementById('hiddenPlanName').value = name;
        document.getElementById('hiddenRoi').value = roi;
        document.getElementById('modalPlanName').innerText = "Invest in " + name;
        document.getElementById('mDuration').innerText = duration + " Days";
        document.getElementById('mRoi').innerText = roi + "% " + type;
        document.getElementById('mMin').innerText = "$" + min.toLocaleString();
        document.getElementById('mMax').innerText = (max > 0) ? "$" + max.toLocaleString() : "Unlimited";
        document.getElementById('investModal').classList.remove('hidden');
    }
    function closeModal() { document.getElementById('investModal').classList.add('hidden'); }
    
    document.getElementById('amtInput').addEventListener('input', function() {
        let val = parseFloat(this.value);
        if (val < minVal) this.setCustomValidity("Below minimum!");
        else if (maxVal > 0 && val > maxVal) this.setCustomValidity("Exceeds maximum!");
        else this.setCustomValidity("");
    });
    
    <?php echo $alert; ?>
</script>

<?php include 'footer.php'; ?>