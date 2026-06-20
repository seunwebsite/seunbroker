<?php
session_start();

require_once '../config/bootstrap.php';
require_once '../config/function.php'; 

$alert = "";

$ref_default = "";
if(isset($_GET['ref'])) {
    $ref_default = htmlspecialchars($_GET['ref']); 
}

if (isset($_POST['register'])) {
    // 1. Clean Inputs
    $fullname = clean($_POST['fullname']);
    $username = clean($_POST['username']);
    $email    = clean($_POST['email']);
    $password = password_hash(clean($_POST['password']), PASSWORD_DEFAULT);
    $entered_referral = clean($_POST['referral']); 
    
    // 2. Generate Unique User Data
    $account_id = rand(10000000, 99999999);
    $otp_code   = rand(100000, 999999);
    
    // Handle Wallet Phrase
    if(function_exists('generateWalletPhrase')){
        $wallet_phrase = generateWalletPhrase(); 
    } else {
        $wallet_phrase = "pending_generation"; 
    }
    
    // User's OWN referral code (is their username)
    $my_referral_code = $username; 

    // 3. Check for Duplicate Email/Username
    $check = mysqli_query($link, "SELECT * FROM users WHERE email='$email' OR username='$username'");
    
    if (mysqli_num_rows($check) > 0) {
        $alert = "Swal.fire({icon: 'error', title: 'Oops...', text: 'Email or Username already exists!'});";
    } else {
        // 4. Handle Referral Logic (Who referred them?)
        $referred_by = NULL; 
        
        if (!empty($entered_referral)) {
            $ref_check = mysqli_query($link, "SELECT username FROM users WHERE referral_code='$entered_referral'");
            if (mysqli_num_rows($ref_check) > 0) {
                $referred_by = $entered_referral; 
            }
        }

        // 5. Insert User into Database
        $sql = "INSERT INTO users (
            account_id, full_name, username, email, password, 
            referral_code, referred_by, otp_code, secret_phrase, created_at
        ) VALUES (
            '$account_id', '$fullname', '$username', '$email', '$password', 
            '$my_referral_code', '$referred_by', '$otp_code', '$wallet_phrase', NOW()
        )";
        
        if (mysqli_query($link, $sql)) {
            $new_user_id = mysqli_insert_id($link);
            $_SESSION['user_id'] = $new_user_id;
            $_SESSION['USER_LOGIN'] = $email; 
            
           $subject = "Verify Your Account - Action Required";

$body = '
<div style="font-family: Helvetica, Arial, sans-serif; background-color: #f9fafb; padding: 50px 0;">
    <div style="max-width: 500px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); overflow: hidden;">
        
        <div style="padding: 40px 0 20px; text-align: center;">
            <div style="background-color: #eef2ff; width: 64px; height: 64px; border-radius: 50%; display: inline-block; line-height: 64px; font-size: 32px; margin-bottom: 20px;">
                🔐
            </div>
            <h1 style="color: #111827; margin: 0; font-size: 24px; font-weight: bold;">Verify it\'s you</h1>
        </div>

        <div style="padding: 20px 40px 40px;">
            <p style="color: #4b5563; font-size: 16px; text-align: center; margin-bottom: 30px;">
                Hello <strong>' . $fullname . '</strong>, enter the code below to finish signing in to <strong>' . $sitename . '</strong>.
            </p>

            <div style="background-color: #f3f4f6; border-radius: 8px; padding: 20px; text-align: center; margin: 0 20px 30px;">
                <span style="color: #111827; font-size: 36px; font-weight: 800; letter-spacing: 6px; font-family: sans-serif;">' . $otp_code . '</span>
            </div>

            <div style="border-top: 1px solid #e5e7eb; padding-top: 20px; margin-top: 30px;">
                <p style="color: #9ca3af; font-size: 12px; text-align: center; margin: 0;">
                    This code will expire shortly. Do not share this code.
                </p>
            </div>
        </div>
    </div>
</div>';

$mail_status = sendMail($email, $subject, $body);

if ($mail_status !== true) {

    echo "<pre style='background:#111;color:#0f0;padding:15px;overflow:auto;font-size:13px;'>";
    echo "EMAIL SEND FAILED DEBUG\n\n";
    print_r($mail_status);
    echo "</pre>";

    die();
}

            // 7. Redirect Logic (will only happen if the email sent successfully)
            $check_email = isset($enable_email_verification) ? $enable_email_verification : 1;
            $check_wallet = isset($enable_wallet_phrase) ? $enable_wallet_phrase : 1;

            if ($check_email == 1) {
                echo "<script>window.location.href='verify-email.php';</script>";
            } elseif ($check_wallet == 1) {
                echo "<script>window.location.href='wallet-phrase.php';</script>";
            } else {
                echo "<script>window.location.href='set-pin.php';</script>";
            }

        } else {
            $alert = "Swal.fire({icon: 'error', title: 'Error', text: 'Database error: " . mysqli_error($link) . "'});";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account | <?php echo isset($sitename) ? $sitename : 'App'; ?></title>
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
                    colors: {
                        dark: { bg: '#02040a', panel: '#0B0F19', border: '#1E293B' },
                        light: { bg: '#F8FAFC', panel: '#FFFFFF', border: '#E2E8F0' }
                    }
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
    </style>
</head>
<body class="flex min-h-screen bg-slate-50 dark:bg-[#02040a] text-slate-800 dark:text-slate-300 items-center justify-center p-4 relative">

    <div class="fixed -top-40 -left-40 w-96 h-96 bg-indigo-500/10 rounded-full blur-[100px] pointer-events-none"></div>
    <div class="fixed top-1/2 -right-20 w-80 h-80 bg-purple-500/10 rounded-full blur-[80px] pointer-events-none"></div>

    <div class="glass-panel w-full max-w-md rounded-3xl p-8 md:p-10 relative z-10 my-10">
        
        <div class="text-center mb-8">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-600 to-violet-600 flex items-center justify-center text-white font-bold text-xl shadow-lg mx-auto mb-4">
                <i class="fa-solid fa-bolt"></i>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Create Account</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Join <?php echo isset($sitename) ? $sitename : 'Us'; ?> today.</p>
        </div>

        <form method="POST" action="">
            <div class="space-y-4 mb-6">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Full Name</label>
                    <div class="relative">
                        <input type="text" name="fullname" placeholder="Your Name" required class="w-full bg-slate-50 dark:bg-[#0B0F19] text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl py-3 px-4 pl-10 focus:outline-none focus:border-indigo-500 transition-all">
                        <i class="fa-regular fa-id-card absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-400"></i>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Username</label>
                    <div class="relative">
                        <input type="text" name="username" placeholder="johndoe123" required class="w-full bg-slate-50 dark:bg-[#0B0F19] text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl py-3 px-4 pl-10 focus:outline-none focus:border-indigo-500 transition-all">
                        <i class="fa-solid fa-at absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-400"></i>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Email Address</label>
                    <div class="relative">
                        <input type="email" name="email" placeholder="name@example.com" required class="w-full bg-slate-50 dark:bg-[#0B0F19] text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl py-3 px-4 pl-10 focus:outline-none focus:border-indigo-500 transition-all">
                        <i class="fa-regular fa-envelope absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-400"></i>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Password</label>
                    <div class="relative">
                        <input type="password" name="password" placeholder="••••••••" required class="w-full bg-slate-50 dark:bg-[#0B0F19] text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl py-3 px-4 pl-10 focus:outline-none focus:border-indigo-500 transition-all">
                        <i class="fa-solid fa-lock absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-400"></i>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Referral Code <span class="text-slate-400 font-normal normal-case">(Optional)</span></label>
                    <div class="relative">
                        <input type="text" name="referral" value="<?php echo $ref_default; ?>" placeholder="123456" class="w-full bg-slate-50 dark:bg-[#0B0F19] text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl py-3 px-4 pl-10 focus:outline-none focus:border-indigo-500 transition-all">
                        <i class="fa-solid fa-user-group absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-400"></i>
                    </div>
                </div>
            </div>

            <div class="flex items-start gap-3 mb-6">
                <input type="checkbox" required class="mt-1 w-4 h-4 text-indigo-600 rounded focus:ring-indigo-500 border-gray-300 dark:border-white/10 dark:bg-[#0B0F19]">
                <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                    I agree to the <a href="#" class="text-indigo-500 hover:underline">Terms of Service</a> and <a href="#" class="text-indigo-500 hover:underline">Privacy Policy</a>.
                </p>
            </div>

            <button type="submit" name="register" class="w-full py-4 rounded-xl bg-gradient-to-r from-indigo-600 to-violet-600 text-white font-bold shadow-lg hover:opacity-90 transition-all hover:scale-[1.02]">
                Create Account
            </button>
        </form>

        <p class="mt-8 text-center text-sm text-slate-500 dark:text-slate-400">
            Already have an account? <a href="login.php" class="text-indigo-500 font-bold hover:underline">Log in</a>
        </p>
    </div>

    <script>
        <?php echo $alert; ?>
    </script>

</body>
</html>