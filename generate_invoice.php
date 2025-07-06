<?php
// public/generate_invoice.php

require_once __DIR__ . '/../app/database.php';
require_once __DIR__ . '/../app/models/Brand.php';
require_once __DIR__ . '/../app/models/Job.php';

// Dompdf entegrasyonu için:
// require_once __DIR__ . '/../vendor/autoload.php'; // Composer ile Dompdf yüklüyse
// use Dompdf\Dompdf;
// use Dompdf\Options;

$brandModel = new Brand();
$jobModel = new Job();

$selectedBrandId = $_GET['brand_id'] ?? null;
$selectedMonth = $_GET['month'] ?? date('m');
$selectedYear = $_GET['year'] ?? date('Y');

$brands = $brandModel->getAll(); // Marka seçimi için

$invoiceBrand = null;
$invoiceJobs = null;
$totalAmount = 0;
$invoiceTitle = 'Fatura Özeti';

if ($selectedBrandId && $brandModel->getById($selectedBrandId)) {
    $invoiceBrand = $brandModel;
    $invoiceJobs = $jobModel->getJobsForInvoice($selectedBrandId, $selectedMonth, $selectedYear);
    $invoiceTitle = htmlspecialchars($invoiceBrand->name) . ' - ' . date('F Y', mktime(0, 0, 0, $selectedMonth, 1, $selectedYear)) . ' İş Detayları';

    while ($job = $invoiceJobs->fetch(PDO::FETCH_ASSOC)) {
        if ($job['is_monthly_retainer']) {
            $totalAmount += $job['monthly_retainer_amount'];
        } else {
            $totalAmount += $job['price'];
        }
    }
    // Cursor'ı tekrar başa al
    $invoiceJobs->execute();
}

include __DIR__ . '/../app/includes/header.php';
?>

<section class="page-header">
    <h2>Fatura Oluştur</h2>
</section>

<section class="form-section">
    <h3>Fatura Oluşturma Seçenekleri</h3>
    <form action="generate_invoice.php" method="GET">
        <div class="form-group">
            <label for="brand_id">Marka:</label>
            <select id="brand_id" name="brand_id" required>
                <option value="">Bir marka seçin</option>
                <?php while ($brand = $brands->fetch(PDO::FETCH_ASSOC)): ?>
                    <option value="<?php echo htmlspecialchars($brand['id']); ?>"
                        <?php echo ($selectedBrandId == $brand['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($brand['name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="month">Ay:</label>
            <select id="month" name="month" required>
                <?php for ($i = 1; $i <= 12; $i++): ?>
                    <option value="<?php echo str_pad($i, 2, '0', STR_PAD_LEFT); ?>"
                        <?php echo ($selectedMonth == str_pad($i, 2, '0', STR_PAD_LEFT)) ? 'selected' : ''; ?>>
                        <?php echo date('F', mktime(0, 0, 0, $i, 10)); // Ay adını al ?>
                    </option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="year">Yıl:</label>
            <select id="year" name="year" required>
                <?php for ($i = date('Y'); $i >= date('Y') - 5; $i--): // Son 5 yıl ?>
                    <option value="<?php echo $i; ?>"
                        <?php echo ($selectedYear == $i) ? 'selected' : ''; ?>>
                        <?php echo $i; ?>
                    </option>
                <?php endfor; ?>
            </select>
        </div>
        <button type="submit" class="button primary">Faturayı Görüntüle</button>
    </form>
</section>

<?php if ($invoiceBrand && $invoiceJobs): ?>
    <section class="invoice-preview">
        <h3><?php echo $invoiceTitle; ?></h3>
        <div class="invoice-details">
            <p><strong>Marka Adı:</strong> <?php echo htmlspecialchars($invoiceBrand->name); ?></p>
            <p><strong>İlgili Kişi:</strong> <?php echo htmlspecialchars($invoiceBrand->contact_person); ?></p>
            <p><strong>E-posta:</strong> <?php echo htmlspecialchars($invoiceBrand->email); ?></p>
            <p><strong>Telefon:</strong> <?php echo htmlspecialchars($invoiceBrand->phone); ?></p>
            <p><strong>Adres:</strong> <?php echo nl2br(htmlspecialchars($invoiceBrand->address)); ?></p>
        </div>

        <h4>Yapılan İşler</h4>
        <table>
            <thead>
                <tr>
                    <th>Tamamlanma Tarihi</th>
                    <th>Proje</th>
                    <th>İş Başlığı</th>
                    <th>Açıklama</th>
                    <th>Fiyat / Retainer</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($invoiceJobs->rowCount() > 0): ?>
                    <?php while ($job = $invoiceJobs->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($job['completed_at']); ?></td>
                            <td><?php echo htmlspecialchars($job['project_name']); ?></td>
                            <td><?php echo htmlspecialchars($job['title']); ?></td>
                            <td><?php echo htmlspecialchars($job['description']); ?></td>
                            <td>
                                <?php
                                    if ($job['is_monthly_retainer']) {
                                        echo 'Aylık Sabit: ' . number_format($job['monthly_retainer_amount'], 2, ',', '.') . ' TL';
                                    } else {
                                        echo number_format($job['price'], 2, ',', '.') . ' TL';
                                    }
                                ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    <tr>
                        <td colspan="4" class="total-label">TOPLAM TUTAR:</td>
                        <td class="total-amount"><?php echo number_format($totalAmount, 2, ',', '.'); ?> TL</td>
                    </tr>
                <?php else: ?>
                    <tr>
                        <td colspan="5">Bu ay için bu markaya ait tamamlanmış iş bulunmuyor.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="invoice-actions">
            <button class="button primary" onclick="generatePdf()">PDF Olarak İndir</button>
            <?php
            $invoice_url_base = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
            $invoice_url = $invoice_url_base . '/invoice_landing.php?brand_id=' . $selectedBrandId . '&month=' . $selectedMonth . '&year=' . $selectedYear;
            ?>
            <a href="<?php echo $invoice_url; ?>" target="_blank" class="button secondary">Landing Page Olarak Görüntüle</a>
        </div>
    </section>

    <script>
        // PDF oluşturma işlevi (Dompdf entegrasyonu sonrası doldurulacak)
        function generatePdf() {
            // Bu kısım Dompdf kütüphanesi entegre edildiğinde doldurulacak.
            // Örneğin: window.location.href = 'generate_pdf.php?brand_id=...&month=...&year=...';
            alert('PDF oluşturma özelliği henüz entegre edilmedi. Dompdf gibi bir kütüphane kullanmanız gerekecek.');
            // Geçici olarak mevcut sayfanın yazdırılmasını tetikleyebiliriz
            window.print();
        }
    </script>

<?php elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['brand_id'])): ?>
    <section class="invoice-preview">
        <p class="error-message">Seçilen marka veya dönem için bilgi bulunamadı.</p>
    </section>
<?php endif; ?>

<?php include __DIR__ . '/../app/includes/footer.php'; ?>