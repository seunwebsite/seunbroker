<?php
include 'header.php';

$alert = "";

// --- HANDLE DEPOSIT APPROVAL ---
if (isset($_POST['approve_deposit'])) {
    $tx_id = intval($_POST['tx_id']);

    // Fetch Transaction & User
    $query = mysqli_query($link, "SELECT t.*, u.username, u.email FROM transactions t JOIN users u ON t.user_id = u.id WHERE t.id='$tx_id' AND t.status='pending' AND t.type='deposit'");
    
    if (mysqli_num_rows($query) > 0) {
        $tx = mysqli_fetch_assoc($query);
        $user_id = $tx['user_id'];
        $amount_usd = floatval($tx['amount_usd']);

        // Since you only have amount_usd, we update the main balance directly
        $update_user = mysqli_query($link, "UPDATE users SET balance = balance + $amount_usd WHERE id='$user_id'");

        if ($update_user) {
            mysqli_query($link, "UPDATE transactions SET status='completed' WHERE id='$tx_id'");
            $alert = "Swal.fire({icon: 'success', title: 'Approved!', text: 'Funds credited successfully.'});";
        } else {
            $alert = "Swal.fire({icon: 'error', title: 'Error', text: 'Failed to update user balance.'});";
        }
    } else {
        $alert = "Swal.fire({icon: 'warning', title: 'Action Failed', text: 'Transaction already processed or invalid.'});";
    }
}

// --- HANDLE DELETE ---
if (isset($_POST['delete_tx'])) {
    $tx_id = intval($_POST['tx_id']);
    mysqli_query($link, "DELETE FROM transactions WHERE id='$tx_id'");
    $alert = "Swal.fire({icon: 'success', title: 'Deleted', text: 'Transaction removed.'});";
}

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

    <div class="glass-panel rounded-2xl bg-white shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="text-xs text-slate-400 uppercase bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="py-3 pl-6 font-semibold">User</th>
                        <th class="py-3 font-semibold">Amount (USD)</th>
                        <th class="py-3 font-semibold">TX Hash</th>
                        <th class="py-3 font-semibold text-center">Status</th>
                        <th class="py-3 pr-6 text-right font-semibold">Action</th>
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
                                        <button type="submit" name="approve_deposit" class="bg-green-600 text-white text-xs font-bold py-1.5 px-3 rounded shadow">Approve</button>
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