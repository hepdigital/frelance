<?php
// public/dashboard.php

require_once __DIR__ . '/../app/database.php';
require_once __DIR__ . '/../app/models/Job.php';
require_once __DIR__ . '/../app/models/Brand.php'; // Markaları çekmek için

$jobModel = new Job();
$brandModel = new Brand();

// --- Dashboard Metrikleri ---

// Bu Ay Kazanılan Para
$currentMonth = date('m');
$currentYear = date('Y');
$totalRevenueThisMonth = 0;

$stmtJobsThisMonth = $jobModel->getJobsForInvoice(null, $currentMonth, $currentYear); // Tüm markaların bu ayki işleri
while ($job = $stmtJobsThisMonth->fetch(PDO::FETCH_ASSOC)) {
    if ($job['is_monthly_retainer']) {
        $totalRevenueThisMonth += $job['monthly_retainer_amount'];
    } else {
        $totalRevenueThisMonth += $job['price'];
    }
}

// Bekleyen İş Sayısı
$stmtPendingJobs = $jobModel->getAll();
$pendingJobsCount = 0;
while ($job = $stmtPendingJobs->fetch(PDO::FETCH_ASSOC)) {
    if ($job['status'] === 'pending') {
        $pendingJobsCount++;
    }
}

// Bu Ay Tamamlanan İş Sayısı
$completedJobsThisMonthCount = 0;
$stmtCompletedJobsThisMonth = $jobModel->getJobsForInvoice(null, $currentMonth, $currentYear);
while ($job = $stmtCompletedJobsThisMonth->fetch(PDO::FETCH_ASSOC)) {
    if ($job['status'] === 'completed' || $job['status'] === 'billed') {
        $completedJobsThisMonthCount++;
    }
}

// --- Grafik Verileri İçin (Örnek) ---
// Aylık gelir trendi için basit bir örnek veri seti
// Gerçek uygulamada daha sofistike sorgularla çekilebilir
$monthlyRevenueData = [];
for ($i = 0; $i < 6; $i++) { // Son 6 ay
    $month = date('m', strtotime("-$i month"));
    $year = date('Y', strtotime("-$i month"));
    $monthName = date('M', strtotime("-$i month")); // Örn: Jan, Feb

    $monthlyTotal = 0;
    $stmtJobs = $jobModel->getJobsForInvoice(null, $month, $year);
    while ($job = $stmtJobs->fetch(PDO::FETCH_ASSOC)) {
        if ($job['is_monthly_retainer']) {
            $monthlyTotal += $job['monthly_retainer_amount'];
        } else {
            $monthlyTotal += $job['price'];
        }
    }
    $monthlyRevenueData[$monthName] = $monthlyTotal;
}
$monthlyRevenueLabels = json_encode(array_reverse(array_keys($monthlyRevenueData)));
$monthlyRevenueValues = json_encode(array_reverse(array_values($monthlyRevenueData)));

// İş Durumu Dağılımı (Örnek)
$jobStatusData = [
    'pending' => 0,
    'completed' => 0,
    'billed' => 0
];
$stmtAllJobs = $jobModel->getAll();
while($job = $stmtAllJobs->fetch(PDO::FETCH_ASSOC)) {
    if (isset($jobStatusData[$job['status']])) {
        $jobStatusData[$job['status']]++;
    }
}
$jobStatusLabels = json_encode(array_keys($jobStatusData));
$jobStatusValues = json_encode(array_values($jobStatusData));


include __DIR__ . '/../app/includes/header.php';
?>

<section class="dashboard-metrics">
    <div class="metric-card">
        <h3><span data-feather="dollar-sign"></span> Bu Ay Kazanılan</h3>
        <p><?php echo number_format($totalRevenueThisMonth, 2, ',', '.'); ?> TL</p>
    </div>
    <div class="metric-card">
        <h3><span data-feather="alert-triangle"></span> Bekleyen İşler</h3>
        <p><?php echo $pendingJobsCount; ?> Adet</p>
    </div>
    <div class="metric-card">
        <h3><span data-feather="check-circle"></span> Bu Ay Tamamlanan</h3>
        <p><?php echo $completedJobsThisMonthCount; ?> Adet</p>
    </div>
</section>

<section class="dashboard-charts">
    <div class="chart-card">
        <h3>Aylık Gelir Trendi</h3>
        <canvas id="monthlyRevenueChart"></canvas>
    </div>
    <div class="chart-card">
        <h3>İş Durumu Dağılımı</h3>
        <canvas id="jobStatusChart"></canvas>
    </div>
</section>

<script>
    // PHP'den gelen verileri JavaScript'e aktarma
    const monthlyRevenueLabels = <?php echo $monthlyRevenueLabels; ?>;
    const monthlyRevenueValues = <?php echo $monthlyRevenueValues; ?>;
    const jobStatusLabels = <?php echo $jobStatusLabels; ?>;
    const jobStatusValues = <?php echo $jobStatusValues; ?>;
</script>

<?php include __DIR__ . '/../app/includes/footer.php'; ?>