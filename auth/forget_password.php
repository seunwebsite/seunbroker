<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

require_once '../config/bootstrap.php';
require_once '../config/function.php';

$email = "";
$err = "";
$msg = "";

if (isset($_POST['reset_request'])) {
    if (empty($_POST['email'])) {
        $err = 'Enter your email';
    } else {
        $email = clean($_POST['email']);

        // Check if email exists in the database
        $stmt = mysqli_prepare($link, "SELECT email, full_name FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) == 0) {
            echo "<script>alert('Account does not exist'); window.history.back();</script>";
        } else {
            $row = mysqli_fetch_assoc($result);
            
            // Generate a secure token
            $token = bin2hex(random_bytes(18));
            
            // Insert the email and token using prepared statement
            $insert_stmt = mysqli_prepare($link, "INSERT INTO password_reset (email, token) VALUES (?, ?)");
            mysqli_stmt_bind_param($insert_stmt, "ss", $email, $token);
            $insert = mysqli_stmt_execute($insert_stmt);

            if ($insert) {
                $fullname = $row['full_name'];
                
                $subject = "Password Recovery";
                $body = "
                <div style='font-family: sans-serif; line-height: 1.6; max-width: 600px; margin: auto;'>
                    <h2 style='color: #333;'>Password Reset Request</h2>
                    <p>Dear <strong>$fullname</strong>,</p>
                    <p>You've requested a password recovery for your account on <strong>$sitename</strong>.</p>
                    <p>Click the link below to reset your password:</p>
                    <p>
                        <a href='".$siteurl."/auth/reset_password.php?token=".$token."' 
                           style='background: #4f46e5; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 8px; font-weight: bold; display: inline-block;'>
                           Reset Password
                        </a>
                    </p>
                    <p style='color: #666; font-size: 14px;'>If you didn't request this, you can safely ignore this email.</p>
                    <p>Best regards,<br>$sitename Team</p>
                </div>";

                // Call the function and handle the array response
                $mail_status = sendMail($email, $subject, $body);

                if ($mail_status === true) {
                    echo "<script>alert('Password recovery email has been sent to your email address'); window.location.href='login.php';</script>";
                } else {
                    // Extract error message from array if it exists
                    $error_msg = isset($mail_status['error']) ? $mail_status['error'] : 'Unknown error occurred.';
                    echo "<script>alert('Failed to send email: " . addslashes($error_msg) . "'); window.history.back();</script>";
                }
            } else {
                echo "<script>alert('Error processing your request. Please try again.'); window.history.back();</script>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | <?php echo isset($sitename) ? $sitename : 'App'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { sans: ['"Plus Jakarta Sans"', 'sans-serif'] },
                }
            }
        }
    </script>
    <style>
        .glass-panel {
            @apply bg-white/70 dark:bg-[#121826]/70;
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            @apply border border-slate-200 dark:border-white/5;
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.1);
        }
        .fade-in { animation: fadeIn 0.4s ease-out forwards; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body class="flex h-screen bg-slate-50 dark:bg-[#02040a] text-slate-800 dark:text-slate-300 items-center justify-center p-4 relative overflow-hidden">

    <div class="absolute -top-40 -left-40 w-96 h-96 bg-indigo-500/10 rounded-full blur-[100px] pointer-events-none"></div>
    <div class="absolute top-1/2 -right-20 w-80 h-80 bg-purple-500/10 rounded-full blur-[80px] pointer-events-none"></div>

    <div class="glass-panel w-full max-w-sm rounded-3xl p-8 relative z-10 fade-in">
        
        <div class="text-center mb-8">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-600 to-violet-600 flex items-center justify-center text-white font-bold text-xl shadow-lg mx-auto mb-4">
                <i class="fa-solid fa-key"></i>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Reset Password</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-2">Enter your email and we'll send you instructions to reset your password.</p>
        </div>

        <form method="POST" method="post">
            <div class="space-y-4 mb-6">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Email Address</label>
                    <div class="relative">
                        <input type="email" name="email" placeholder="name@example.com" required 
                            class="w-full bg-slate-50 dark:bg-[#0B0F19] text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl py-3 px-4 pl-10 focus:outline-none focus:border-indigo-500 transition-all">
                        <i class="fa-regular fa-envelope absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-400"></i>
                    </div>
                </div>
            </div>

            <button type="submit" name="reset_request" class="w-full py-4 rounded-xl bg-slate-900 dark:bg-white text-white dark:text-black font-bold shadow-lg hover:opacity-90 transition-all hover:scale-[1.02] mb-4">
                Send Reset Link
            </button>
            
            <a href="login.php" class="block text-center text-sm font-semibold text-slate-500 hover:text-indigo-500 transition-colors">
                <i class="fa-solid fa-arrow-left-long mr-2"></i> Back to Login
            </a>
        </form>

    </div>

</body>
</html>