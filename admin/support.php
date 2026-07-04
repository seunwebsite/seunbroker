<?php
include 'header.php'; 

$alert = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['send_email'])) {
    // 1. Use your custom clean function
    $to = clean($_POST['recipient']);
    $subject = clean($_POST['subject']);
    $message = $_POST['message']; // Raw message for the database
    
    // 2. Call the support function defined in functions.php
    // We pass nl2br($message) to ensure line breaks are preserved in the HTML email
    $result = sendSupportMail($to, $subject, nl2br(htmlspecialchars($message)), 'gabbyseun0@gmail.com');

    // 3. Handle the result
    if ($result === true) {
        // Save to DB using your $link and $user_id
        $db_message = mysqli_real_escape_string($link, $message);
        mysqli_query($link, "INSERT INTO support_messages (user_id, recipient_email, subject, message) 
                             VALUES ('$user_id', '$to', '$subject', '$db_message')");
        
        $alert = "Swal.fire({icon:'success', title:'Sent!', text:'Email sent successfully. Replies will go to your inbox.'});";
    } else {
        // Access the error returned by the function
        $error_msg = $result['error'];
        $alert = "Swal.fire({icon:'error', title:'Error', text:'$error_msg'});";
    }
}
?>

<div class="flex-1 overflow-y-auto p-4 md:p-8 pb-24 space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Support Center</h1>
        <a href="support_history.php" class="text-sm text-indigo-500 font-bold">View Sent History</a>
    </div>

    <div class="glass-panel rounded-3xl p-8 border border-slate-200 dark:border-white/5 max-w-2xl">
        <form method="POST" class="space-y-4">
            <!-- Hidden field to keep track if needed -->
            <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
            
            <div>
                <label class="text-xs font-bold uppercase text-slate-500">Recipient Email</label>
                <input type="email" name="recipient" required class="w-full p-3 rounded-xl bg-slate-100 dark:bg-black border border-white/10 mt-1 font-bold">
            </div>
            
            <div>
                <label class="text-xs font-bold uppercase text-slate-500">Subject</label>
                <input type="text" name="subject" required class="w-full p-3 rounded-xl bg-slate-100 dark:bg-black border border-white/10 mt-1 font-bold">
            </div>
            
            <div>
                <label class="text-xs font-bold uppercase text-slate-500">Message</label>
                <textarea name="message" rows="5" required class="w-full p-3 rounded-xl bg-slate-100 dark:bg-black border border-white/10 mt-1 font-bold"></textarea>
            </div>
            
            <button type="submit" name="send_email" class="w-full py-3 rounded-xl bg-indigo-600 text-white font-bold hover:bg-indigo-700 transition-all">
                Send Email
            </button>
        </form>
    </div>
</div>

<script>
    // Trigger the SweetAlert if an alert exists
    <?php if($alert) echo $alert; ?>
</script>

<?php include 'footer.php'; ?>