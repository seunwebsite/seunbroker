<?php
include 'header.php';

$alert = "";

// --- HANDLE APPROVAL (Mark Complete & Add Hash) ---
if (isset($_POST['confirm_approval'])) {
    $tx_id = intval($_POST['tx_id']);
    $real_tx_hash = clean($_POST['real_tx_hash']);

    $query = mysqli_query($link, "SELECT t.*, u.username, u.email FROM transactions t JOIN users u ON t.user_id = u.id WHERE t.id='$tx_id'");
    $tx = mysqli_fetch_assoc($query);

    if ($tx && $tx['status'] == 'pending') {
        $update = mysqli_query($link, "UPDATE transactions SET status='completed', tx_hash='$real_tx_hash' WHERE id='$tx_id'");
        
        if ($update) {
            // Email Notification (Simplified)
            $subject = "Withdrawal Sent - " . $sitename;
            $body = "<p>Hello " . $tx['username'] . ", your withdrawal of $" . number_format($tx['amount_usd'], 2) . " has been processed. Transaction ID: " . $real_tx_hash . "</p>";
            sendMail($tx['email'], $subject, $body);
            
            $alert = "Swal.fire({icon: 'success', title: 'Processed', text: 'Withdrawal marked as complete.'});";
        }
    }
}

// --- HANDLE DELETE ---
if (isset($_POST['delete_tx'])) {
    $tx_id = intval($_POST['tx_id']);
    mysqli_query($link, "DELETE FROM transactions WHERE id='$tx_id'");
    $alert = "Swal.fire({icon: 'success', title: 'Deleted', text: 'Record removed.'});";
}

// --- FETCH DATA ---
$sql = "SELECT t.*, u.username, u.email 
        FROM transactions t 
        JOIN users u ON t.user_id = u.id 
        WHERE t.type = 'withdrawal' 
        ORDER BY t.created_at DESC";
$result = mysqli_query($link, $sql);
?>

<div class="flex-1 overflow-y-auto p-6 space-y-6">
    <h1 class="text-2xl font-bold text-slate-800">Manage Withdrawals</h1>

    <div class="glass-panel rounded-2xl bg-white shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="text-xs text-slate-400 uppercase bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="py-3 pl-6 font-semibold">User Info</th>
                        <th class="py-3 font-semibold">Amount</th>
                        <th class="py-3 font-semibold">Status</th>
                        <th class="py-3 font-semibold">Hash / Proof</th>
                        <th class="py-3 pr-6 text-right font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-slate-100">
                    <?php while($row = mysqli_fetch_assoc($result)): 
                        $status_badge = ($row['status'] == 'completed') ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700';
                    ?>
                    <tr class="hover:bg-slate-50">
                        <td class="py-4 pl-6">
                            <p class="font-bold text-slate-800"><?php echo htmlspecialchars($row['username']); ?></p>
                            <p class="text-xs text-slate-500"><?php echo htmlspecialchars($row['email']); ?></p>
                        </td>
                        <td class="py-4 font-bold text-slate-800">$<?php echo number_format($row['amount_usd'], 2); ?></td>
                        <td class="py-4">
                            <span class="px-2.5 py-1 rounded-md text-xs font-bold <?php echo $status_badge; ?> capitalize">
                                <?php echo $row['status']; ?>
                            </span>
                        </td>
                        <td class="py-4 text-xs font-mono text-slate-500"><?php echo htmlspecialchars($row['tx_hash'] ?: 'Waiting...'); ?></td>
                        <td class="py-4 pr-6 text-right">
                            <div class="flex justify-end gap-2">
                                <?php if($row['status'] == 'pending'): ?>
                                    <button onclick="openApproveModal(<?php echo $row['id']; ?>)" class="bg-indigo-600 text-white text-xs font-bold py-1.5 px-3 rounded shadow">Send</button>
                                <?php else: ?>
                                    <span class="text-xs text-slate-400 py-1.5 px-3">Closed</span>
                                <?php endif; ?>
                                <form method="POST"><input type="hidden" name="tx_id" value="<?php echo $row['id']; ?>"><button type="submit" name="delete_tx" class="text-red-400"><i class="fa-solid fa-trash"></i></button></form>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal & Scripts remain the same -->
<div id="approveModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 hidden p-4">
    <div class="bg-white w-full max-w-sm rounded-2xl p-6">
        <h3 class="font-bold mb-4">Confirm Withdrawal</h3>
        <form method="POST">
            <input type="hidden" name="tx_id" id="modalTxId">
            <input type="text" name="real_tx_hash" required placeholder="Enter Transaction Hash" class="w-full border rounded-xl py-3 px-4 mb-4">
            <button type="submit" name="confirm_approval" class="w-full bg-indigo-600 text-white py-3 rounded-xl">Confirm Payment</button>
        </form>
    </div>
</div>

<script>
    function openApproveModal(id) {
        document.getElementById('modalTxId').value = id;
        document.getElementById('approveModal').classList.remove('hidden');
    }
    <?php echo $alert; ?>
</script>

<?php include 'footer.php'; ?>