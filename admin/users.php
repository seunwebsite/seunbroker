<?php
include 'header.php';

$alert = "";

// --- HANDLE 1: UPDATE USER DETAILS (Profile + Balances) ---
if (isset($_POST['update_user'])) {
    $uid = intval($_POST['user_id']);
    
    // 1. Personal Info
    $fullname = clean($_POST['full_name']);
    $username = clean($_POST['username']);
    $email = clean($_POST['email']);
    $phone = clean($_POST['phone']);
    $country = clean($_POST['country']);
    $address = clean($_POST['address']);
    $kyc_status = clean($_POST['kyc_status']); // 'approved', 'pending', etc.
    
    // 2. Security (Only update if not empty)
    $password_sql = "";
    if(!empty($_POST['password'])) {
        $pass_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $password_sql = ", password='$pass_hash'";
    }
    
    $pin_sql = "";
    if(!empty($_POST['transaction_pin'])) {
        $pin = clean($_POST['transaction_pin']);
        $pin_sql = ", transaction_pin='$pin'";
    }

    // 3. Balances
    $btc = floatval($_POST['btc_balance']);
    $eth = floatval($_POST['eth_balance']);
    $usdt_trc = floatval($_POST['usdt_trc20_balance']);
    $usdt_erc = floatval($_POST['usdt_erc20_balance']);
    $bnb = floatval($_POST['bnb_balance']);
    $trx = floatval($_POST['trx_balance']);
    $ltc = floatval($_POST['ltc_balance']);
    $doge = floatval($_POST['doge_balance']);
    $sol = floatval($_POST['sol_balance']);
    $matic = floatval($_POST['matic_balance']);
    $balance = floatval($_POST['balance']);
$profit = floatval($_POST['profit_balance']);
$referral = floatval($_POST['referral_earnings']);

    // Construct Query
    $sql = "UPDATE users SET 
        full_name='$fullname',
        username='$username',
        email='$email',
        phone='$phone',
        country='$country',
        address='$address',
        kyc_status='$kyc_status',
        balance='$balance',
        profit_balance='$profit',
        referral_earnings='$referral',
        btc_balance='$btc', 
        eth_balance='$eth', 
        usdt_trc20_balance='$usdt_trc', 
        usdt_erc20_balance='$usdt_erc', 
        bnb_balance='$bnb', 
        trx_balance='$trx', 
        ltc_balance='$ltc', 
        doge_balance='$doge', 
        sol_balance='$sol', 
        matic_balance='$matic'
        $password_sql
        $pin_sql
        WHERE id='$uid'";

    if (mysqli_query($link, $sql)) {
        $alert = "Swal.fire({icon: 'success', title: 'Profile Updated', text: 'User details and balances saved successfully.'});";
    } else {
        $alert = "Swal.fire({icon: 'error', title: 'Error', text: 'Failed to update user.'});";
    }
}

// --- HANDLE 2: DELETE USER ---
if (isset($_POST['delete_user'])) {
    $uid = intval($_POST['user_id']);
    // Cascade delete
    mysqli_query($link, "DELETE FROM investments WHERE user_id='$uid'");
    mysqli_query($link, "DELETE FROM transactions WHERE user_id='$uid'");
    mysqli_query($link, "DELETE FROM user_bots WHERE user_id='$uid'");
    mysqli_query($link, "DELETE FROM virtual_cards WHERE user_id='$uid'");
    mysqli_query($link, "DELETE FROM linked_banks WHERE user_id='$uid'");
    
    if(mysqli_query($link, "DELETE FROM users WHERE id='$uid'")){
        $alert = "Swal.fire({icon: 'success', title: 'Deleted', text: 'User account removed.'});";
    }
}

// --- FETCH USERS ---
$search_sql = "";
if(isset($_GET['search']) && !empty($_GET['search'])) {
    $s = clean($_GET['search']);
    $search_sql = "WHERE username LIKE '%$s%' OR email LIKE '%$s%' OR full_name LIKE '%$s%'";
}

$query = "SELECT * FROM users $search_sql ORDER BY created_at DESC";
$result = mysqli_query($link, $query);
?>

<div class="flex-1 overflow-y-auto p-6 space-y-6">
    
    <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">User Management</h1>
            <p class="text-sm text-slate-500">Edit profiles, security, and wallet funds.</p>
        </div>
        
        <form class="flex items-center gap-2 w-full md:w-auto">
            <div class="relative w-full md:w-64">
                <i class="fa-solid fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-slate-400"></i>
                <input type="text" name="search" placeholder="Search users..." class="pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 bg-white text-sm w-full">
            </div>
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-xl text-sm font-bold hover:bg-indigo-700 transition-colors">Search</button>
        </form>
    </div>

    <div class="glass-panel rounded-2xl bg-white shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="text-xs text-slate-400 uppercase bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="py-4 pl-6 font-semibold">User Profile</th>
                        <th class="py-4 font-semibold">Contact</th>
                        <th class="py-4 font-semibold">Status</th>
                        <th class="py-4 font-semibold">Joined</th>
                        <th class="py-4 pr-6 text-right font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-slate-100">
                    <?php if(mysqli_num_rows($result) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($result)): 
                            $user_data = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');
                            $kyc_badge = ($row['kyc_status'] == 'approved') ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700';
                        ?>
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="py-4 pl-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-indigo-50 flex items-center justify-center text-sm font-bold text-indigo-600 border border-indigo-100">
                                        <?php echo strtoupper(substr($row['username'], 0, 1)); ?>
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-800"><?php echo htmlspecialchars($row['full_name']); ?></p>
                                        <p class="text-xs text-slate-500">@<?php echo htmlspecialchars($row['username']); ?></p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4">
                                <p class="text-slate-600"><?php echo htmlspecialchars($row['email']); ?></p>
                                <p class="text-xs text-slate-400"><?php echo htmlspecialchars($row['phone']); ?></p>
                            </td>
                            <td class="py-4">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase <?php echo $kyc_badge; ?>">
                                    KYC: <?php echo $row['kyc_status']; ?>
                                </span>
                                <p class="text-xs text-slate-500 mt-1"><i class="fa-solid fa-flag"></i> <?php echo $row['country']; ?></p>
                            </td>
                            <td class="py-4 text-xs text-slate-500">
                                <?php echo date("M d, Y", strtotime($row['created_at'])); ?>
                            </td>
                            <td class="py-4 pr-6 text-right">
                                <div class="flex justify-end gap-2">
                                    <button onclick="openEditModal('<?php echo $user_data; ?>')" class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold py-1.5 px-3 rounded border border-slate-200 transition-all flex items-center gap-1">
                                        <i class="fa-solid fa-pen-to-square"></i> Edit
                                    </button>
                                    <form method="POST" onsubmit="return confirm('Permanently delete this user?');">
                                        <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                                        <button type="submit" name="delete_user" class="text-red-400 hover:text-red-600 p-1.5 transition-colors">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="py-12 text-center text-slate-400 italic">No users found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="editModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm hidden p-4">
    <div class="bg-white w-full max-w-4xl rounded-2xl p-6 md:p-8 shadow-2xl relative max-h-[90vh] overflow-y-auto">
        <button onclick="closeEditModal()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-xl"></i></button>
        
        <h3 class="text-xl font-bold text-slate-800 mb-6">Edit User Account</h3>
        
        <form method="POST">
            <input type="hidden" name="user_id" id="editUserId">
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                
                <div class="space-y-4">
                    <h4 class="text-sm font-bold text-indigo-600 uppercase border-b border-indigo-100 pb-2 mb-4">Personal Info</h4>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Full Name</label>
                            <input type="text" name="full_name" id="fullName" class="w-full border border-slate-200 rounded-lg p-2 text-sm focus:border-indigo-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Username</label>
                            <input type="text" name="username" id="userName" class="w-full border border-slate-200 rounded-lg p-2 text-sm focus:border-indigo-500 outline-none">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Email</label>
                            <input type="email" name="email" id="userEmail" class="w-full border border-slate-200 rounded-lg p-2 text-sm focus:border-indigo-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Phone</label>
                            <input type="text" name="phone" id="userPhone" class="w-full border border-slate-200 rounded-lg p-2 text-sm focus:border-indigo-500 outline-none">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Country</label>
                            <input type="text" name="country" id="userCountry" class="w-full border border-slate-200 rounded-lg p-2 text-sm focus:border-indigo-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">KYC Status</label>
                            <select name="kyc_status" id="kycStatus" class="w-full border border-slate-200 rounded-lg p-2 text-sm bg-white focus:border-indigo-500 outline-none">
                                <option value="pending">Pending</option>
                                <option value="approved">Approved</option>
                                <option value="rejected">Rejected</option>
                                <option value="unverified">Unverified</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Address</label>
                        <input type="text" name="address" id="userAddress" class="w-full border border-slate-200 rounded-lg p-2 text-sm focus:border-indigo-500 outline-none">
                    </div>

                    <h4 class="text-sm font-bold text-red-500 uppercase border-b border-red-100 pb-2 mt-6 mb-4">Security Reset</h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">New Password</label>
                            <input type="text" name="password" placeholder="Leave empty to keep" class="w-full border border-slate-200 rounded-lg p-2 text-sm focus:border-red-500 outline-none bg-slate-50">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">New Trans. PIN</label>
                            <input type="text" name="transaction_pin" id="userPin" placeholder="Leave empty to keep" class="w-full border border-slate-200 rounded-lg p-2 text-sm focus:border-red-500 outline-none bg-slate-50">
                        </div>
                    </div>
                </div>

                <div class="space-y-4 bg-slate-50 p-6 rounded-xl border border-slate-200">
                    <h4 class="text-sm font-bold text-green-600 uppercase border-b border-green-200 pb-2 mb-4">Wallet Balances</h4>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase">Main Balance</label>
                            <input type="number" step="any" name="balance" id="main_balance" class="w-full bg-white border border-slate-300 rounded-lg p-2 text-sm font-bold text-indigo-600">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase">Referral Earnings</label>
                            <input type="number" step="any" name="referral_earnings" id="referral_balance" class="w-full bg-white border border-slate-300 rounded-lg p-2 text-sm font-bold text-orange-600">                       
                    </div>
                </div>

            </div>

            <div class="mt-8 pt-4 border-t border-slate-100 flex justify-end gap-3">
                <button type="button" onclick="closeEditModal()" class="px-6 py-2.5 rounded-xl text-slate-500 hover:bg-slate-100 font-bold text-sm">Cancel</button>
                <button type="submit" name="update_user" class="px-8 py-2.5 rounded-xl bg-indigo-600 text-white font-bold text-sm hover:bg-indigo-700 shadow-lg transition-all">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>

<script>
    function openEditModal(json) {
        const user = JSON.parse(json);
        
        // IDs
        document.getElementById('editUserId').value = user.id;
        
        // Profile
        document.getElementById('fullName').value = user.full_name || '';
        document.getElementById('userName').value = user.username || '';
        document.getElementById('userEmail').value = user.email || '';
        document.getElementById('userPhone').value = user.phone || '';
        document.getElementById('userCountry').value = user.country || '';
        document.getElementById('userAddress').value = user.address || '';
        document.getElementById('kycStatus').value = user.kyc_status || 'unverified';
        document.getElementById('main_balance').value = user.balance;
        document.getElementById('referral_balance').value = user.referral_earnings;
        
        // PIN (Placeholder)
        document.getElementById('userPin').value = user.transaction_pin || '';

        
        
        document.getElementById('editModal').classList.remove('hidden');
    }
    
    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }

    document.getElementById('editModal').addEventListener('click', function(e) {
        if (e.target === this) closeEditModal();
    });

    <?php echo $alert; ?>
</script>

