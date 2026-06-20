<?php 
include 'header.php'; 

// Fetch all active investments for the user
$active_investments = mysqli_query($link, "SELECT * FROM investments WHERE user_id='$user_id' AND status='active'");
$has_investments = mysqli_num_rows($active_investments) > 0;
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="flex-1 overflow-y-auto p-4 md:p-8 pb-24 space-y-8">
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Live Trading Performance</h1>

    <?php if (!$has_investments): ?>
        <!-- EMPTY STATE -->
        <div class="flex flex-col items-center justify-center py-20 text-center glass-panel rounded-3xl p-8 border border-white/5">
            <div class="text-6xl mb-6 text-slate-300 dark:text-slate-600">
                <i class="fa-solid fa-chart-line"></i>
            </div>
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-2">No Active Trades</h2>
            <p class="text-slate-500 mb-8 max-w-sm">You currently don't have any running investments. Start growing your capital today.</p>
            <a href="investments.php" class="bg-blue-600 text-white font-bold py-4 px-8 rounded-xl hover:bg-blue-700 transition-all">
                Open New Trade
            </a>
        </div>
    <?php else: ?>
        <!-- ACTIVE TRADES GRID -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <?php while($inv = mysqli_fetch_assoc($active_investments)): 
                $chartId = "chart_" . $inv['id'];
                $amount = floatval($inv['amount']);
                $roi_percent = floatval($inv['roi_percent']);
                $target_profit = $amount * ($roi_percent / 100);
            ?>
            <div class="glass-panel rounded-3xl p-6 border border-white/5 shadow-xl">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h2 class="text-xl font-bold text-slate-900 dark:text-white"><?php echo $inv['plan_name']; ?></h2>
                        <p class="text-xs text-slate-500">Capital: $<?php echo number_format($amount, 2); ?></p>
                    </div>
                    <div class="text-right">
                        <p id="total_val_<?php echo $inv['id']; ?>" class="font-extrabold text-slate-900 dark:text-white text-xl">
                            $<?php echo number_format($amount + $inv['current_profit'], 2); ?>
                        </p>
                        <span class="text-[9px] uppercase tracking-wider text-slate-400">Total Asset Value</span>
                    </div>
                </div>
                
                <div class="flex justify-between items-center mb-4">
                     <span class="text-xs text-slate-400 uppercase">Live Profit:</span>
                     <p id="profit_<?php echo $inv['id']; ?>" class="font-bold text-green-500">+$<?php echo number_format($inv['current_profit'], 2); ?></p>
                </div>

                <div class="h-48 w-full">
                    <canvas id="<?php echo $chartId; ?>"></canvas>
                </div>
            </div>

            <script>
    (function() {
        const ctx = document.getElementById('<?php echo $chartId; ?>').getContext('2d');
        const initialCapital = <?php echo $amount; ?>;
        const targetProfit = <?php echo $target_profit; ?>;
        let currentProfit = parseFloat(<?php echo $inv['current_profit']; ?>) || 0;
        
        // Binance-style Gradient
        const gradient = ctx.createLinearGradient(0, 0, 0, 200);
        gradient.addColorStop(0, 'rgba(16, 185, 129, 0.3)');
        gradient.addColorStop(1, 'rgba(16, 185, 129, 0.0)');

        let dataPoints = Array(40).fill(initialCapital + currentProfit);

        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: Array(40).fill(''),
                datasets: [{
                    data: dataPoints,
                    borderColor: '#10b981', // Binance Green
                    borderWidth: 2,
                    fill: true,
                    backgroundColor: gradient,
                    tension: 0.1, // Sharper lines like real trading
                    pointRadius: 0
                }]
            },
            options: { 
                responsive: true, 
                maintainAspectRatio: false,
                scales: { 
                    y: { 
                        display: true, 
                        position: 'right', // Binance charts have axis on the right
                        grid: { color: 'rgba(255,255,255,0.05)' }, 
                        ticks: { color: '#94a3b8', font: { size: 10 } } 
                    }, 
                    x: { display: false } 
                },
                plugins: { legend: { display: false } }
            }
        });

        setInterval(() => {
            if (currentProfit < targetProfit) {
                // More realistic "tick" movement
                let volatility = (Math.random() - 0.48) * (initialCapital * 0.001);
                currentProfit += (targetProfit / 86400) + volatility; // Simulated 24hr growth
                
                if (currentProfit > targetProfit) currentProfit = targetProfit;

                dataPoints.push(initialCapital + currentProfit);
                dataPoints.shift();
                
                // Color flip for "Candlestick" feel
                chart.data.datasets[0].borderColor = (volatility >= 0) ? '#10b981' : '#ef4444';
                chart.update('none'); // Smoother performance
                
                document.getElementById('profit_<?php echo $inv['id']; ?>').innerText = '+$' + currentProfit.toFixed(2);
                document.getElementById('total_val_<?php echo $inv['id']; ?>').innerText = '$' + (initialCapital + currentProfit).toLocaleString(undefined, {minimumFractionDigits: 2});
            }
        }, 1000);
    })();
</script>
            <?php endwhile; ?>
        </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>