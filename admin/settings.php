<?php
include 'header.php';

$alert = "";

// --- HANDLE SETTINGS UPDATE ---
if (isset($_POST['update_settings'])) {
    // 1. General Info
    $sitename_val = mysqli_real_escape_string($link, $_POST['sitename']);
    $siteurl_val = mysqli_real_escape_string($link, $_POST['siteurl']);
    $site_email_val = mysqli_real_escape_string($link, $_POST['site_email']);
    $site_phone_val = mysqli_real_escape_string($link, $_POST['site_phone']);
    
    // 2. Payment Methods (FIXED with ?? '')
    $btc_address_val = mysqli_real_escape_string($link, $_POST['btc_address'] ?? '');
    $cashapp_tag_val = mysqli_real_escape_string($link, $_POST['cashapp_tag'] ?? '');
    $bank_details_val = mysqli_real_escape_string($link, $_POST['bank_details'] ?? '');
    
    // 3. Financial Config
    $ref_bonus = floatval($_POST['referral_bonus_percentage'] ?? 0);
    $card_fee = floatval($_POST['virtual_card_fee'] ?? 0);
    
    // 4. System Toggles
    $enable_email_ver = isset($_POST['enable_email_verification']) ? 1 : 0;
    $enable_phrase = isset($_POST['enable_wallet_phrase_step']) ? 1 : 0;
    $enable_pin = isset($_POST['enable_pin_on_login']) ? 1 : 0;
    $enable_kyc = isset($_POST['enable_kyc']) ? 1 : 0;

    // Update Query
    $sql = "UPDATE settings SET 
            sitename='$sitename_val', 
            siteurl='$siteurl_val', 
            site_email='$site_email_val', 
            site_phone='$site_phone_val', 
            btc_address='$btc_address_val',
            cashapp_tag='$cashapp_tag_val',
            bank_details='$bank_details_val',
            referral_bonus_percentage='$ref_bonus', 
            virtual_card_fee='$card_fee', 
            enable_email_verification='$enable_email_ver', 
            enable_wallet_phrase_step='$enable_phrase', 
            enable_pin_on_login='$enable_pin', 
            enable_kyc='$enable_kyc' 
            WHERE id=1";

    if (mysqli_query($link, $sql)) {
        $alert = "Swal.fire({icon: 'success', title: 'Saved', text: 'System configuration updated successfully.'});";
        $settings_q = mysqli_query($link, "SELECT * FROM settings WHERE id=1");
        $settings = mysqli_fetch_assoc($settings_q);
    } else {
        $alert = "Swal.fire({icon: 'error', title: 'Error', text: 'Failed to update settings.'});";
    }
}
// Fetch Current Settings
$settings_q = mysqli_query($link, "SELECT * FROM settings WHERE id=1");
$settings = mysqli_fetch_assoc($settings_q);
?>

<div class="flex-1 overflow-y-auto p-6 space-y-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">System Settings</h1>
            <p class="text-sm text-slate-500">Configure global platform parameters and toggles.</p>
        </div>
        <button type="submit" form="settingsForm" name="update_settings" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-xl shadow-lg transition-all flex items-center gap-2">
            <i class="fa-solid fa-save"></i> Save Changes
        </button>
    </div>

    <form id="settingsForm" method="POST" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <div class="glass-panel p-6 rounded-2xl bg-white shadow-sm border border-slate-200">
            <h3 class="font-bold text-lg text-slate-800 mb-4 border-b border-slate-100 pb-2">
                <i class="fa-solid fa-globe text-indigo-500 mr-2"></i> General Info
            </h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Website Name</label>
                    <input type="text" name="sitename" value="<?php echo htmlspecialchars($settings['sitename']); ?>" class="w-full border border-slate-200 rounded-lg p-2.5 text-sm focus:border-indigo-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Website URL</label>
                    <input type="text" name="siteurl" value="<?php echo htmlspecialchars($settings['siteurl']); ?>" class="w-full border border-slate-200 rounded-lg p-2.5 text-sm focus:border-indigo-500 outline-none">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Support Email</label>
                        <input type="email" name="site_email" value="<?php echo htmlspecialchars($settings['site_email']); ?>" class="w-full border border-slate-200 rounded-lg p-2.5 text-sm focus:border-indigo-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Support Phone</label>
                        <input type="text" name="site_phone" value="<?php echo htmlspecialchars($settings['site_phone']); ?>" class="w-full border border-slate-200 rounded-lg p-2.5 text-sm focus:border-indigo-500 outline-none">
                    </div>
                </div>
            </div>
        </div>
        <!-- New Payment Methods Panel -->
<div class="glass-panel p-6 rounded-2xl bg-white shadow-sm border border-slate-200 lg:col-span-2">
    <h3 class="font-bold text-lg text-slate-800 mb-4 border-b border-slate-100 pb-2">
        <i class="fa-solid fa-wallet text-indigo-500 mr-2"></i> Deposit & Payment Methods
    </h3>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- BTC Address -->
        <div>
            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Bitcoin (BTC) Address</label>
            <input type="text" name="btc_address" value="<?php echo htmlspecialchars($settings['btc_address'] ?? ''); ?>" class="w-full border border-slate-200 rounded-lg p-2.5 text-sm focus:border-indigo-500 outline-none">
        </div>

        <!-- Cash App -->
        <div>
            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Cash App Tag ($Cashtag)</label>
            <input type="text" name="cashapp_tag" value="<?php echo htmlspecialchars($settings['cashapp_tag'] ?? ''); ?>" placeholder="$YourTag" class="w-full border border-slate-200 rounded-lg p-2.5 text-sm focus:border-indigo-500 outline-none">
        </div>

        <!-- Bank Transfer -->
        <div class="md:col-span-2">
            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Bank Transfer Details</label>
            <textarea name="bank_details" rows="3" class="w-full border border-slate-200 rounded-lg p-2.5 text-sm focus:border-indigo-500 outline-none"><?php echo htmlspecialchars($settings['bank_details'] ?? ''); ?></textarea>
            <p class="text-[10px] text-slate-400 mt-1">Include Bank Name, Account Name, Account Number, and SWIFT/Routing.</p>
        </div>
    </div>
</div>

        <div class="glass-panel p-6 rounded-2xl bg-white shadow-sm border border-slate-200">
            <h3 class="font-bold text-lg text-slate-800 mb-4 border-b border-slate-100 pb-2">
                <i class="fa-solid fa-coins text-yellow-500 mr-2"></i> Financial Config
            </h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Referral Bonus (%)</label>
                    <div class="relative">
                        <input type="number" step="0.01" name="referral_bonus_percentage" value="<?php echo $settings['referral_bonus_percentage']; ?>" class="w-full border border-slate-200 rounded-lg p-2.5 text-sm focus:border-indigo-500 outline-none pl-4">
                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 font-bold">%</span>
                    </div>
                    <p class="text-[10px] text-slate-400 mt-1">Percentage users earn from referrals.</p>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Virtual Card Fee ($)</label>
                        <input type="number" step="0.01" name="virtual_card_fee" value="<?php echo $settings['virtual_card_fee']; ?>" class="w-full border border-slate-200 rounded-lg p-2.5 text-sm focus:border-indigo-500 outline-none">
                    </div>
                    <!--<div>-->
                    <!--    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Min Withdrawal ($)</label>-->
                    <!--    <input type="number" step="0.01" name="min_withdrawal_limit" value="<?php echo $settings['min_withdrawal_limit']; ?>" class="w-full border border-slate-200 rounded-lg p-2.5 text-sm focus:border-indigo-500 outline-none">-->
                    <!--</div>-->
                </div>
            </div>
        </div>

        <div class="glass-panel p-6 rounded-2xl bg-white shadow-sm border border-slate-200 lg:col-span-2">
            <h3 class="font-bold text-lg text-slate-800 mb-4 border-b border-slate-100 pb-2">
                <i class="fa-solid fa-toggle-on text-green-500 mr-2"></i> Feature Control
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                
                <div class="flex items-center justify-between p-4 bg-slate-50 rounded-xl border border-slate-100">
                    <div>
                        <h4 class="font-bold text-slate-700 text-sm">Email Verification</h4>
                        <p class="text-[10px] text-slate-400">Require email verify on signup</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="enable_email_verification" class="sr-only peer" <?php echo ($settings['enable_email_verification'] == 1) ? 'checked' : ''; ?>>
                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                    </label>
                </div>

                <div class="flex items-center justify-between p-4 bg-slate-50 rounded-xl border border-slate-100">
                    <div>
                        <h4 class="font-bold text-slate-700 text-sm">Force KYC</h4>
                        <p class="text-[10px] text-slate-400">Must verify ID to withdraw</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="enable_kyc" class="sr-only peer" <?php echo ($settings['enable_kyc'] == 1) ? 'checked' : ''; ?>>
                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                    </label>
                </div>

                <!--<div class="flex items-center justify-between p-4 bg-slate-50 rounded-xl border border-slate-100">-->
                <!--    <div>-->
                <!--        <h4 class="font-bold text-slate-700 text-sm">Wallet Connect</h4>-->
                <!--        <p class="text-[10px] text-slate-400">Show 'Connect Wallet' option</p>-->
                <!--    </div>-->
                <!--    <label class="relative inline-flex items-center cursor-pointer">-->
                <!--        <input type="checkbox" name="enable_wallet_connect" class="sr-only peer" <?php echo ($settings['enable_wallet_connect'] == 1) ? 'checked' : ''; ?>>-->
                <!--        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>-->
                <!--    </label>-->
                <!--</div>-->

                <div class="flex items-center justify-between p-4 bg-slate-50 rounded-xl border border-slate-100">
                    <div>
                        <h4 class="font-bold text-slate-700 text-sm">Login PIN</h4>
                        <p class="text-[10px] text-slate-400">Ask for PIN after password</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="enable_pin_on_login" class="sr-only peer" <?php echo ($settings['enable_pin_on_login'] == 1) ? 'checked' : ''; ?>>
                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                    </label>
                </div>

                <div class="flex items-center justify-between p-4 bg-slate-50 rounded-xl border border-slate-100">
                    <div>
                        <h4 class="font-bold text-slate-700 text-sm">Phrase Step</h4>
                        <p class="text-[10px] text-slate-400">Ask for phrase during Register</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="enable_wallet_phrase_step" class="sr-only peer" <?php echo ($settings['enable_wallet_phrase_step'] == 1) ? 'checked' : ''; ?>>
                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                    </label>
                </div>

            </div>
        </div>

    </form>
</div>

<script>
    <?php echo $alert; ?>
</script>

<?php include 'footer.php'; ?>