<?php 
include 'header.php'; 

// Fetch ONLY the incoming emails from the 'emails' table
$query = "SELECT * FROM emails ORDER BY received_at DESC";
$result = mysqli_query($link, $query);
?>

<div class="flex-1 overflow-y-auto p-4 md:p-8 pb-24 space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Gmail Inbox</h1>
            <p class="text-sm text-slate-500">Emails received via IMAP sync</p>
        </div>
        <a href="support.php" class="text-sm text-indigo-500 font-bold">← Back to Support</a>
    </div>

    <div class="glass-panel rounded-3xl p-6 border border-slate-200 dark:border-white/5 overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="text-xs uppercase text-slate-500 border-b border-slate-200 dark:border-white/10">
                    <th class="p-3">Date</th>
                    <th class="p-3">From</th>
                    <th class="p-3">Subject</th>
                    <th class="p-3">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr class="border-b border-slate-200 dark:border-white/10 hover:bg-slate-50 dark:hover:bg-white/5">
                    <td class="p-3 text-sm text-slate-500"><?php echo date('M d, H:i', strtotime($row['received_at'])); ?></td>
                    <td class="p-3 text-sm font-bold"><?php echo htmlspecialchars($row['sender']); ?></td>
                    <td class="p-3 text-sm"><?php echo htmlspecialchars($row['subject']); ?></td>
                    <td class="p-3">
                        <button onclick="openInboxModal('<?php echo htmlspecialchars($row['subject']); ?>', '<?php echo addslashes(htmlspecialchars($row['body'])); ?>')" 
                                class="text-indigo-500 font-bold text-sm">Read</button>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Dedicated Modal -->
<div id="inboxModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4 z-50">
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-8 max-w-2xl w-full shadow-2xl border border-white/10">
        <h2 id="modalSubject" class="text-xl font-bold mb-4 text-slate-900 dark:text-white"></h2>
        <div id="modalBody" class="text-slate-600 dark:text-slate-300 bg-slate-50 dark:bg-black p-4 rounded-xl max-h-96 overflow-y-auto whitespace-pre-line text-sm"></div>
        <button onclick="document.getElementById('inboxModal').classList.add('hidden')" class="mt-6 w-full py-3 bg-slate-200 dark:bg-slate-800 rounded-xl font-bold">Close</button>
    </div>
</div>

<script>
function openInboxModal(subject, body) {
    document.getElementById('modalSubject').innerText = subject;
    document.getElementById('modalBody').innerText = body;
    document.getElementById('inboxModal').classList.remove('hidden');
}
</script>

<?php include 'footer.php'; ?>