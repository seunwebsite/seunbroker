<?php
include 'header.php';

// Fetch the history from the database
$history_query = mysqli_query($link, "SELECT * FROM support_messages ORDER BY created_at DESC");
?>

<div class="flex-1 overflow-y-auto p-4 md:p-8 pb-24 space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Sent Email History</h1>
        <a href="support.php" class="text-sm bg-indigo-600 text-white px-4 py-2 rounded-xl font-bold hover:bg-indigo-700">Send New Email</a>
    </div>

    <div class="glass-panel rounded-3xl p-6 border border-slate-200 dark:border-white/5">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-slate-500 border-b dark:border-white/5 text-left">
                    <th class="py-3">Date</th>
                    <th class="py-3">Recipient</th>
                    <th class="py-3">Subject</th>
                    <th class="py-3">Message Preview</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($history_query)): ?>
                <tr class="border-b border-slate-100 dark:border-white/5">
                    <td class="py-4 text-slate-400"><?php echo date('M d, Y H:i', strtotime($row['created_at'])); ?></td>
                    <td class="py-4 font-bold text-slate-900 dark:text-white"><?php echo $row['recipient_email']; ?></td>
                    <td class="py-4 text-slate-700 dark:text-slate-300"><?php echo $row['subject']; ?></td>
                    <td class="py-4 text-slate-500 truncate max-w-[200px]"><?php echo substr($row['message'], 0, 50); ?>...</td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'footer.php'; ?>