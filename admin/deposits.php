<?php
include 'header.php';

$alert = "";

// --- HANDLE DEPOSIT APPROVAL ---
if (isset($_POST['approve_deposit'])) {
    $tx_id = intval($_POST['tx_id']);
    $query = mysqli_query($link, "SELECT * FROM transactions WHERE id='$tx_id' AND status='pending' AND type='deposit'");
    
    if (mysqli_num_rows($query) > 0) {
        $tx = mysqli_fetch_assoc($query);
        $user_id = $tx['user_id'];
        $amount = floatval($tx['amount_usd']);

        if (mysqli_query($link, "UPDATE users SET balance = balance + $amount WHERE id='$user_id'")) {
            mysqli_query($link, "UPDATE transactions SET status='completed' WHERE id='$tx_id'");
            $alert = "Swal.fire({icon: 'success', title: 'Approved!', text: 'Funds credited successfully.'});";
        }
    }
}

// --- HANDLE MANUAL DEPOSIT ---
if (isset($_POST['manual_deposit'])) {
    $user_id = intval($_POST['user_id']);
    $amount = floatval($_POST['amount']);

    if ($user_id > 0 && $amount > 0) {
        mysqli_query($link, "UPDATE users SET balance = balance + $amount WHERE id='$user_id'");
        mysqli_query($link, "INSERT INTO transactions (user_id, amount_usd, status, type, tx_hash, created_at) 
                            VALUES ('$user_id', '$amount', 'completed', 'deposit', 'ADMIN_MANUAL', NOW())");
        $alert = "Swal.fire({icon: 'success', title: 'Added!', text: 'Manual deposit credited successfully.'});";
    }
}

// --- HANDLE DELETE ---
if (isset($_POST['delete_tx'])) {
    $tx_id = intval($_POST['tx_id']);
    mysqli_query($link, "DELETE FROM transactions WHERE id='$tx_id'");
    $alert = "Swal.fire({icon: 'success', title: 'Deleted', text: 'Transaction removed.'});";
}

// --- FETCH USERS FOR DROPDOWN ---
$users_query = mysqli_query($link, "SELECT id, username, email FROM users ORDER BY username ASC");

// --- FETCH DEPOSITS ---
$sql = "SELECT t.*, u.username, u.email 
        FROM transactions t 
        JOIN users u ON t.user_id = u.id 
        WHERE t.type = 'deposit' 
        ORDER BY t.created_at DESC";
$result = mysqli_query($link, $sql);
?>

<div class="flex-1 overflow-y-auto p-6 space-y-6">
    <h1 class="text-2xl font-bold text-slate-800">Manage Deposits</h1>

    <!-- Manual Deposit Section -->
    <div class="glass-panel p-6 bg-white rounded-2xl shadow-sm border border-slate-200">
        <h2 class="text-md font-bold text-slate-700 mb-4">Manual Add Deposit</h2>
        <form method="POST" class="flex flex-wrap gap-4 items-end">
            <!-- Changed User ID input to a Select dropdown -->
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Select User</label>
                <select name="user_id" required class="border border-slate-300 rounded px-3 py-2 text-sm w-full focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="">-- Choose a user --</option>
                    <?php while($user = mysqli_fetch_assoc($users_query)): ?>
                        <option value="<?php echo $user['id']; ?>">
                            <?php echo htmlspecialchars($user['username'] . ' (' . $user['email'] . ')'); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Amount (USD)</label>
                <input type="number" step="0.01" name="amount" required class="border border-slate-300 rounded px-3 py-2 text-sm w-32 focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
            <button type="submit" name="manual_deposit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold py-2 px-6 rounded shadow">Credit Account</button>
        </form>
    </div>

    <!-- Transactions Table -->
    <div class="glass-panel rounded-2xl bg-white shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="text-xs text-slate-400 uppercase bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="py-3 pl-6">User</th>
                        <th class="py-3">Amount</th>
                        <th class="py-3">TX Hash / Type</th>
                        <th class="py-3 text-center">Status</th>
                        <th class="py-3 pr-6 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-slate-100">
                    <?php while($row = mysqli_fetch_assoc($result)): 
                        $status_badge = ($row['status'] == 'completed') ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700';
                    ?>
                    <tr class="hover:bg-slate-50">
                        <td class="py-4 pl-6">
                            <p class="font-bold text-slate-800"><?php echo htmlspecialchars($row['username']); ?></p>
                            <p class="text-xs text-slate-500"><?php echo htmlspecialchars($row['email']); ?></p>
                        </td>
                        <td class="py-4 font-bold text-slate-800">$<?php echo number_format($row['amount_usd'], 2); ?></td>
                        <td class="py-4 font-mono text-xs text-slate-500"><?php echo htmlspecialchars($row['tx_hash']); ?></td>
                        <td class="py-4 text-center">
                            <span class="px-2.5 py-1 rounded-md text-xs font-bold <?php echo $status_badge; ?> capitalize">
                                <?php echo $row['status']; ?>
                            </span>
                        </td>
                        <td class="py-4 pr-6 text-right">
                            <div class="flex justify-end gap-2">
                                <?php if($row['status'] == 'pending'): ?>
                                    <form method="POST">
                                        <input type="hidden" name="tx_id" value="<?php echo $row['id']; ?>">
                                        <button type="submit" name="approve_deposit" class="bg-green-600 text-white text-xs font-bold py-1.5 px-3 rounded shadow hover:bg-green-700">Approve</button>
                                    </form>
                                <?php endif; ?>
                                <form method="POST">
                                    <input type="hidden" name="tx_id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" name="delete_tx" class="text-red-500 hover:text-red-700"><i class="fa-solid fa-trash"></i></button>
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

<script><?php echo $alert; ?></script>
<?php include 'footer.php'; ?>