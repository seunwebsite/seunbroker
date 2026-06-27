<?php include 'header.php'; ?>

<div class="flex-1 overflow-y-auto p-4 md:p-8 pb-24">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white mb-1">Fund account</h1>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 glass-panel rounded-3xl p-6 md:p-8">
            <form id="depositForm">
                <div class="mb-8">
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Enter Amount</label>
                    <input type="number" id="amount" required class="w-full bg-slate-100 dark:bg-white/5 border border-slate-200 rounded-xl p-4 text-slate-900 dark:text-white outline-none" placeholder="0.00">
                </div>

                <div class="mb-8">
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-4">Payment Method</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <?php 
                        $methods = [
                            ['id' => 'card', 'name' => 'Credit Card', 'icon' => 'fa-brands fa-cc-visa'],
                            ['id' => 'cashapp', 'name' => 'Cash App', 'icon' => 'fa-solid fa-dollar-sign'],
                            ['id' => 'bank', 'name' => 'Bank Transfer', 'icon' => 'fa-solid fa-university'],
                            ['id' => 'btc', 'name' => 'Bitcoin', 'icon' => 'fa-brands fa-bitcoin']
                        ];
                        foreach($methods as $m) { ?>
                            <label class="flex items-center justify-between p-4 border rounded-xl cursor-pointer hover:bg-slate-50 dark:hover:bg-white/5">
                                <div class="flex items-center gap-3">
                                    <?php if(isset($m['icon'])) { echo '<i class="'.$m['icon'].' text-xl text-blue-600"></i>'; } else { echo '<img src="'.$m['img'].'" class="w-6 h-6">'; } ?>
                                    <span class="font-bold text-slate-700 dark:text-slate-300"><?php echo $m['name']; ?></span>
                                </div>
                                <input type="radio" name="method" value="<?php echo $m['id']; ?>" class="text-indigo-600" <?php echo $m['id']=='card'?'checked':''; ?>>
                            </label>
                        <?php } ?>
                    </div>
                </div>
                <button type="button" onclick="openPaymentModal()" class="w-full bg-blue-600 text-white font-bold py-4 rounded-xl">Proceed to Payment</button>
            </form>
        </div>
    </div>
</div>

<!-- MODAL -->
<div id="paymentModal" class="fixed inset-0 bg-black/50 hidden flex items-center justify-center p-4 z-50">
    <div class="bg-white dark:bg-slate-900 rounded-3xl w-full max-w-lg p-8 shadow-2xl">
        <div id="modalContent"></div>
    </div>
</div>

<script>
function copyToClipboard(text, btnId) {
    navigator.clipboard.writeText(text).then(() => {
        const btn = document.getElementById(btnId);
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fa-solid fa-check"></i> Copied';
        setTimeout(() => { btn.innerHTML = originalText; }, 2000);
    });
}

function openPaymentModal() {
    const amount = document.getElementById('amount').value;
    const method = document.querySelector('input[name="method"]:checked').value;

    // CUSTOMIZED POPUP INSTEAD OF ALERT
    if(!amount || amount <= 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Invalid Amount',
            text: 'Please enter a valid deposit amount to continue.',
            confirmButtonColor: '#2563eb', // Blue-600
            background: document.documentElement.classList.contains('dark') ? '#1e293b' : '#ffffff',
            color: document.documentElement.classList.contains('dark') ? '#ffffff' : '#000000'
        });
        return; 
    }

    const modalContent = document.getElementById('modalContent');
    
    // PHP variables injected into JS
    const btcAddress = "<?php echo $site_settings['btc_address'] ?? 'N/A'; ?>";
    const cashappTag = "<?php echo $site_settings['cashapp_tag'] ?? 'N/A'; ?>";
    const bankDetails = "<?php echo addslashes(str_replace(["\r", "\n"], ' ', $site_settings['bank_details'] ?? 'N/A')); ?>";

    let detailText = "";
    if(method === 'btc') detailText = btcAddress;
    else if(method === 'cashapp') detailText = cashappTag;
    else if(method === 'bank') detailText = bankDetails;

    let html = `<div class="text-center mb-6">
                    <h3 class="text-lg font-bold">Payment Details</h3>
                    <p class="text-slate-500">Pay <strong>$${amount}</strong> via ${method.toUpperCase()}</p>
                </div>`;

    if(method === 'card') {
        html += `<form onsubmit="handleFormSubmit(event, 'process_card.php')">
                    <input type="hidden" name="amount" value="${amount}">
                    <input type="text" placeholder="Card Number" class="w-full p-3 mb-3 border rounded-xl dark:bg-slate-800">
                    <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-xl">Proceed</button>
                 </form>`;
    } else {
        html += `<div class="mb-6">
                    <label class="block text-sm font-bold mb-2">Transfer to:</label>
                    <div class="flex items-center gap-2 bg-slate-100 dark:bg-slate-800 p-3 rounded-lg">
                        <input type="text" value="${detailText}" readonly class="w-full bg-transparent outline-none text-sm">
                        <button id="copyBtn" onclick="copyToClipboard('${detailText}', 'copyBtn')" class="text-blue-600 text-xs font-bold"><i class="fa-solid fa-copy"></i> Copy</button>
                    </div>
                 </div>
                 <form onsubmit="handleFormSubmit(event, 'submit_proof.php')" enctype="multipart/form-data">
                    <input type="hidden" name="amount" value="${amount}">
                    <p class="text-xs mb-2">Upload Proof of Payment:</p>
                    <input type="file" name="proof" class="w-full mb-4" required>
                    <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-xl">Submit Payment</button>
                 </form>`;
    }
    html += `<button onclick="closeModal()" class="w-full mt-4 text-slate-500 hover:text-slate-300">Cancel</button>`;
    modalContent.innerHTML = html;
    document.getElementById('paymentModal').classList.remove('hidden');
}
function closeModal() { document.getElementById('paymentModal').classList.add('hidden'); }
</script>

<?php include 'footer.php'; ?>