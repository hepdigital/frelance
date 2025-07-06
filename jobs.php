<?php
// public/jobs.php

require_once __DIR__ . '/../app/database.php';
require_once __DIR__ . '/../app/models/Job.php';
require_once __DIR__ . '/../app/models/Project.php';

$jobModel = new Job();
$projectModel = new Project();
$message = '';

$selectedProjectId = isset($_GET['project_id']) ? intval($_GET['project_id']) : null;
$projects = $projectModel->getAll(); // Proje seçimi için tüm projeleri çek

// Form gönderimi işlemleri
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        if ($action === 'create' || $action === 'update') {
            $jobModel->project_id = $_POST['project_id'] ?? '';
            $jobModel->title = $_POST['title'] ?? '';
            $jobModel->description = $_POST['description'] ?? '';
            $jobModel->is_monthly_retainer = isset($_POST['is_monthly_retainer']) ? 1 : 0;
            $jobModel->price = null;
            $jobModel->monthly_retainer_amount = null;

            if ($jobModel->is_monthly_retainer) {
                $jobModel->monthly_retainer_amount = $_POST['monthly_retainer_amount'] ?? 0;
            } else {
                $jobModel->price = $_POST['price'] ?? 0;
            }

            $jobModel->completed_at = $_POST['completed_at'] ?? null;
            $jobModel->status = $_POST['status'] ?? 'pending';

            if ($action === 'create') {
                if ($jobModel->create()) {
                    $message = '<div class="success-message">İş başarıyla eklendi!</div>';
                } else {
                    $message = '<div class="error-message">İş eklenirken bir hata oluştu.</div>';
                }
            } elseif ($action === 'update') {
                $jobModel->id = $_POST['job_id'] ?? 0;
                if ($jobModel->update()) {
                    $message = '<div class="success-message">İş başarıyla güncellendi!</div>';
                } else {
                    $message = '<div class="error-message">İş güncellenirken bir hata oluştu.</div>';
                }
            }
        } elseif ($action === 'delete') {
            $jobModel->id = $_POST['job_id'] ?? 0;
            if ($jobModel->delete()) {
                $message = '<div class="success-message">İş başarıyla silindi!</div>';
            } else {
                $message = '<div class="error-message">İş silinirken bir hata oluştu.</div>';
            }
        }
    }
}

// İş bilgilerini çekme (düzenleme formu için)
$editJob = null;
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $jobId = $_GET['id'];
    if ($jobModel->getById($jobId)) {
        $editJob = $jobModel;
    }
}

$jobs = $jobModel->getAll($selectedProjectId);

include __DIR__ . '/../app/includes/header.php';
?>

<section class="page-header">
    <h2>İş Yönetimi</h2>
</section>

<?php echo $message; ?>

<section class="form-section">
    <h3><?php echo $editJob ? 'İşi Düzenle' : 'Yeni İş Ekle'; ?></h3>
    <form action="jobs.php" method="POST">
        <input type="hidden" name="action" value="<?php echo $editJob ? 'update' : 'create'; ?>">
        <?php if ($editJob): ?>
            <input type="hidden" name="job_id" value="<?php echo htmlspecialchars($editJob->id); ?>">
        <?php endif; ?>

        <div class="form-group">
            <label for="project_id">Proje:</label>
            <select id="project_id" name="project_id" required>
                <option value="">Bir proje seçin</option>
                <?php $projects->execute(); // Cursor'ı sıfırla ?>
                <?php while ($project = $projects->fetch(PDO::FETCH_ASSOC)): ?>
                    <option value="<?php echo htmlspecialchars($project['id']); ?>"
                        <?php echo (($editJob && $editJob->project_id == $project['id']) || ($selectedProjectId && $selectedProjectId == $project['id'])) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($project['brand_name'] . ' - ' . $project['name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="title">İş Başlığı:</label>
            <input type="text" id="title" name="title" value="<?php echo $editJob ? htmlspecialchars($editJob->title) : ''; ?>" required>
        </div>
        <div class="form-group">
            <label for="description">Açıklama:</label>
            <textarea id="description" name="description"><?php echo $editJob ? htmlspecialchars($editJob->description) : ''; ?></textarea>
        </div>

        <div class="form-group checkbox-group">
            <input type="checkbox" id="is_monthly_retainer" name="is_monthly_retainer" <?php echo ($editJob && $editJob->is_monthly_retainer) ? 'checked' : ''; ?>>
            <label for="is_monthly_retainer">Aylık Sabit Fiyat (Retainer)</label>
        </div>

        <div class="form-group" id="price_field" style="display: <?php echo ($editJob && $editJob->is_monthly_retainer) ? 'none' : 'block'; ?>;">
            <label for="price">Fiyat (TL):</label>
            <input type="number" id="price" name="price" step="0.01" value="<?php echo $editJob ? htmlspecialchars($editJob->price) : ''; ?>">
        </div>
        <div class="form-group" id="monthly_retainer_amount_field" style="display: <?php echo ($editJob && $editJob->is_monthly_retainer) ? 'block' : 'none'; ?>;">
            <label for="monthly_retainer_amount">Aylık Retainer Miktarı (TL):</label>
            <input type="number" id="monthly_retainer_amount" name="monthly_retainer_amount" step="0.01" value="<?php echo $editJob ? htmlspecialchars($editJob->monthly_retainer_amount) : ''; ?>">
        </div>

        <div class="form-group">
            <label for="completed_at">Tamamlanma Tarihi:</label>
            <input type="text" id="completed_at" name="completed_at" class="datepicker" value="<?php echo $editJob ? htmlspecialchars($editJob->completed_at) : date('Y-m-d'); ?>" required>
        </div>
        <div class="form-group">
            <label for="status">Durum:</label>
            <select id="status" name="status" required>
                <option value="pending" <?php echo ($editJob && $editJob->status === 'pending') ? 'selected' : ''; ?>>Beklemede</option>
                <option value="completed" <?php echo ($editJob && $editJob->status === 'completed') ? 'selected' : ''; ?>>Tamamlandı</option>
                <option value="billed" <?php echo ($editJob && $editJob->status === 'billed') ? 'selected' : ''; ?>>Faturalandırıldı</option>
            </select>
        </div>
        <button type="submit" class="button primary"><?php echo $editJob ? 'İşi Güncelle' : 'İş Ekle'; ?></button>
        <?php if ($editJob): ?>
            <a href="jobs.php" class="button secondary">İptal</a>
        <?php endif; ?>
    </form>
</section>

<section class="data-list">
    <h3>Mevcut İşler</h3>
    <?php if ($selectedProjectId): ?>
        <h4>Proje: <?php
            $projectModel->getById($selectedProjectId);
            echo htmlspecialchars($projectModel->brand_name . ' - ' . $projectModel->name);
        ?></h4>
    <?php endif; ?>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Marka / Proje</th>
                <th>Başlık</th>
                <th>Fiyat / Retainer</th>
                <th>Tamamlanma Tarihi</th>
                <th>Durum</th>
                <th>İşlemler</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($jobs->rowCount() > 0): ?>
                <?php while ($job = $jobs->fetch(PDO::FETCH_ASSOC)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($job['id']); ?></td>
                        <td><?php echo htmlspecialchars($job['brand_name'] . ' / ' . $job['project_name']); ?></td>
                        <td><?php echo htmlspecialchars($job['title']); ?></td>
                        <td>
                            <?php
                                if ($job['is_monthly_retainer']) {
                                    echo 'Aylık Sabit: ' . number_format($job['monthly_retainer_amount'], 2, ',', '.') . ' TL';
                                } else {
                                    echo number_format($job['price'], 2, ',', '.') . ' TL';
                                }
                            ?>
                        </td>
                        <td><?php echo htmlspecialchars($job['completed_at']); ?></td>
                        <td><?php echo htmlspecialchars($job['status']); ?></td>
                        <td class="actions">
                            <a href="jobs.php?action=edit&id=<?php echo $job['id']; ?>" class="button small">Düzenle <span data-feather="edit"></span></a>
                            <form action="jobs.php" method="POST" style="display:inline-block;" onsubmit="return confirm('Bu işi silmek istediğinizden emin misiniz?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="job_id" value="<?php echo htmlspecialchars($job['id']); ?>">
                                <button type="submit" class="button small danger">Sil <span data-feather="trash-2"></span></button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7">Henüz hiç iş eklenmemiş veya seçili projeye ait iş bulunmuyor.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const isMonthlyRetainerCheckbox = document.getElementById('is_monthly_retainer');
    const priceField = document.getElementById('price_field');
    const monthlyRetainerAmountField = document.getElementById('monthly_retainer_amount_field');
    const priceInput = document.getElementById('price');
    const monthlyRetainerAmountInput = document.getElementById('monthly_retainer_amount');

    function togglePriceFields() {
        if (isMonthlyRetainerCheckbox.checked) {
            priceField.style.display = 'none';
            monthlyRetainerAmountField.style.display = 'block';
            priceInput.removeAttribute('required'); // Opsiyonel: form validation için
            monthlyRetainerAmountInput.setAttribute('required', 'required'); // Opsiyonel: form validation için
        } else {
            priceField.style.display = 'block';
            monthlyRetainerAmountField.style.display = 'none';
            priceInput.setAttribute('required', 'required');
            monthlyRetainerAmountInput.removeAttribute('required');
        }
    }

    isMonthlyRetainerCheckbox.addEventListener('change', togglePriceFields);

    // Sayfa yüklendiğinde başlangıç durumunu ayarla
    togglePriceFields();
});
</script>

<?php include __DIR__ . '/../app/includes/footer.php'; ?>