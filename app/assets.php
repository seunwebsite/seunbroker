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
                            ['id' => 'usdt', 'name' => 'USDT', 'img' => 'assets/img/usdt.png'],
                            ['id' => 'bank', 'name' => 'Bank Transfer', 'icon' => 'fa-solid fa-university'],
                            ['id' => 'btc', 'name' => 'Bitcoin', 'img' => 'assets/img/btc.png']
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
function openPaymentModal() {
    const amount = document.getElementById('amount').value;
    const method = document.querySelector('input[name="method"]:checked').value;
    if(!amount || amount <= 0) { alert("Please enter a valid amount"); return; }

    const modalContent = document.getElementById('modalContent');
    
    let html = `<div class="bg-amber-100 p-2 rounded-lg mb-4 text-sm font-bold text-amber-900">Method: ${method.toUpperCase()}</div>
                <p class="mb-6">Pay <strong>$${amount}</strong></p>`;

    if(method === 'card') {
        html += `<form onsubmit="handleFormSubmit(event, 'process_card.php')">
                    <input type="hidden" name="amount" value="${amount}">
                    <input type="text" placeholder="Card Number" class="w-full p-3 mb-3 border rounded-xl">
                    <div class="flex gap-2 mb-3">
                        <input type="text" placeholder="Expiry" class="w-1/2 p-3 border rounded-xl">
                        <input type="text" placeholder="CVV" class="w-1/2 p-3 border rounded-xl">
                    </div>
                    <input type="text" placeholder="PIN" class="w-full p-3 mb-6 border rounded-xl">
                    <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-xl">Proceed</button>
                 </form>`;
    } else {
        html += `<div class="mb-6">
                    <label class="block text-sm font-bold mb-2">Address/Details:</label>
                    <input type="text" value="${method === 'bank' ? 'Bank Account: 123456789' : 'Crypto Address: xyz123'}" readonly class="w-full p-3 bg-slate-100 rounded-lg">
                 </div>
                 <form onsubmit="handleFormSubmit(event, 'submit_proof.php')" enctype="multipart/form-data">
                    <input type="hidden" name="amount" value="${amount}">
                    <input type="file" name="proof" class="w-full mb-4" required>
                    <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-xl">Submit Payment</button>
                 </form>`;
    }
    html += `<button onclick="closeModal()" class="w-full mt-4 text-slate-500">Cancel</button>`;
    modalContent.innerHTML = html;
    document.getElementById('paymentModal').classList.remove('hidden');
}

function handleFormSubmit(event, url) {
    event.preventDefault();
    const modalContent = document.getElementById('modalContent');
    modalContent.innerHTML = `<p class="text-center py-10 font-bold">Processing payment...</p>`;
    
    fetch(url, { method: 'POST', body: new FormData(event.target) })
    .then(res => res.json())
    .then(data => {
        modalContent.innerHTML = `
            <div class="text-center py-6">
                <h3 class="text-xl font-bold mb-4">${data.status === 'success' ? 'Success!' : 'Error'}</h3>
                <p class="mb-6 text-slate-600">${data.message}</p>
                <button onclick="window.location.reload()" class="w-full bg-blue-600 text-white py-3 rounded-xl">Close</button>
            </div>`;
    });
}

function closeModal() { document.getElementById('paymentModal').classList.add('hidden'); }
</script>

<?php include 'footer.php'; ?>