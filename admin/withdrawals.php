<?php
// Include essential files
require_once 'header.php'; 
require_once '../config/function.php';

// --- HANDLE ALERTS VIA REDIRECT (PRG Pattern) ---
$alert = "";
if (isset($_GET['status'])) {
    if ($_GET['status'] == 'success') $alert = "Swal.fire({icon: 'success', title: 'PIN Generated', text: 'PIN has been sent to the user.'});";
    if ($_GET['status'] == 'completed') $alert = "Swal.fire({icon: 'success', title: 'Processed', text: 'Withdrawal completed successfully.'});";
    if ($_GET['status'] == 'deleted') $alert = "Swal.fire({icon: 'success', title: 'Deleted', text: 'Record removed.'});";
    if ($_GET['status'] == 'error') $alert = "Swal.fire({icon: 'error', title: 'Error', text: 'Something went wrong.'});";
}

// --- HANDLE PIN GENERATION ---
if (isset($_POST['generate_pin'])) {
    $tx_id = intval($_POST['tx_id']);
    
    // Check if status is still pending to prevent accidental double-generation
    $check = mysqli_query($link, "SELECT status FROM transactions WHERE id='$tx_id' AND status='pending'");
    
    if (mysqli_num_rows($check) > 0) {
        $pin = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT); 
        $update = mysqli_query($link, "UPDATE transactions SET withdrawal_pin='$pin', status='awaiting_pin' WHERE id='$tx_id'");
        
        if ($update) {
            $query = mysqli_query($link, "SELECT u.email, u.username FROM transactions t JOIN users u ON t.user_id = u.id WHERE t.id='$tx_id'");
            $tx = mysqli_fetch_assoc($query);
            $subject = "Withdrawal PIN Required - " . $sitename;
            $body = "<p>Hello " . htmlspecialchars($tx['username']) . ",</p><p>Your withdrawal PIN is: <h2>" . $pin . "</h2></p>";
            sendMail($tx['email'], $subject, $body);
            header("Location: " . $_SERVER['PHP_SELF'] . "?status=success");
            exit();
        }
    }
}

// --- HANDLE FINAL APPROVAL ---
if (isset($_POST['confirm_approval'])) {
    $tx_id = intval($_POST['tx_id']);
    $real_tx_hash = clean($_POST['real_tx_hash']);
    mysqli_query($link, "UPDATE transactions SET status='completed', tx_hash='$real_tx_hash', withdrawal_pin=NULL WHERE id='$tx_id'");
    header("Location: " . $_SERVER['PHP_SELF'] . "?status=completed");
    exit();
}

// --- HANDLE DELETE ---
if (isset($_POST['delete_tx'])) {
    $tx_id = intval($_POST['tx_id']);
    mysqli_query($link, "DELETE FROM transactions WHERE id='$tx_id'");
    header("Location: " . $_SERVER['PHP_SELF'] . "?status=deleted");
    exit();
}

// --- FETCH DATA ---
$result = mysqli_query($link, "SELECT t.*, u.username, u.email FROM transactions t JOIN users u ON t.user_id = u.id WHERE t.type = 'withdrawal' ORDER BY t.created_at DESC");
?>

<div class="flex-1 overflow-y-auto p-6 space-y-6">
    <h1 class="text-2xl font-bold text-slate-800">Manage Withdrawals</h1>
    <div class="glass-panel rounded-2xl bg-white shadow-sm border border-slate-200 overflow-hidden">
        <table class="w-full text-left">
            <thead class="text-xs text-slate-400 uppercase bg-slate-50 border-b">
                <tr>
                    <th class="py-3 pl-6">User Info</th>
                    <th class="py-3">Amount</th>
                    <th class="py-3">Status</th>
                    <th class="py-3">PIN</th>
                    <th class="py-3 pr-6 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y">
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                <tr class="hover:bg-slate-50">
                    <td class="py-4 pl-6">
                        <p class="font-bold"><?php echo htmlspecialchars($row['username']); ?></p>
                        <p class="text-[10px] text-slate-400"><?php echo htmlspecialchars($row['email']); ?></p>
                    </td>
                    <td class="py-4 font-bold">$<?php echo number_format($row['amount_usd'], 2); ?></td>
                    <td class="py-4 capitalize font-semibold">
                        <span class="px-2 py-1 rounded-full text-[10px] <?php echo $row['status'] == 'completed' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700'; ?>">
                            <?php echo $row['status']; ?>
                        </span>
                    </td>
                    <td class="py-4 font-mono font-bold text-indigo-600"><?php echo $row['withdrawal_pin'] ?: '-'; ?></td>
                    <td class="py-4 pr-6 text-right">
                        <div class="flex justify-end gap-2">
                            <?php if($row['status'] == 'pending'): ?>
                                <form method="POST"><input type="hidden" name="tx_id" value="<?php echo $row['id']; ?>"><button type="submit" name="generate_pin" class="bg-amber-500 hover:bg-amber-600 text-white text-xs px-3 py-1.5 rounded shadow">Generate PIN</button></form>
                            <?php elseif($row['status'] == 'awaiting_pin'): ?>
                                <button onclick="openApproveModal(<?php echo $row['id']; ?>)" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs px-3 py-1.5 rounded shadow">Finalize</button>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="approveModal" class="fixed inset-0 z-50 bg-black/50 hidden flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-sm p-6 rounded-2xl">
        <h3 class="font-bold mb-4">Complete Transaction</h3>
        <form method="POST">
            <input type="hidden" name="tx_id" id="modalTxId">
            <input type="text" name="real_tx_hash" required placeholder="Enter Transaction Hash" class="w-full border p-3 mb-4 rounded-xl">
            <button type="submit" name="confirm_approval" class="w-full bg-green-600 text-white py-3 rounded-xl font-bold">Complete Payment</button>
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