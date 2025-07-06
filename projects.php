<?php
// public/projects.php

require_once __DIR__ . '/../app/database.php';
require_once __DIR__ . '/../app/models/Project.php';
require_once __DIR__ . '/../app/models/Brand.php';

$projectModel = new Project();
$brandModel = new Brand();
$message = '';

$selectedBrandId = isset($_GET['brand_id']) ? intval($_GET['brand_id']) : null;
$brands = $brandModel->getAll(); // Marka seçimi için tüm markaları çek

// Form gönderimi işlemleri
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        if ($action === 'create' || $action === 'update') {
            $projectModel->brand_id = $_POST['brand_id'] ?? '';
            $projectModel->name = $_POST['name'] ?? '';
            $projectModel->description = $_POST['description'] ?? '';
            $projectModel->status = $_POST['status'] ?? 'active';
            $projectModel->start_date = $_POST['start_date'] ?? null;
            $projectModel->end_date = $_POST['end_date'] ?? null;

            if ($action === 'create') {
                if ($projectModel->create()) {
                    $message = '<div class="success-message">Proje başarıyla eklendi!</div>';
                } else {
                    $message = '<div class="error-message">Proje eklenirken bir hata oluştu.</div>';
                }
            } elseif ($action === 'update') {
                $projectModel->id = $_POST['project_id'] ?? 0;
                if ($projectModel->update()) {
                    $message = '<div class="success-message">Proje başarıyla güncellendi!</div>';
                } else {
                    $message = '<div class="error-message">Proje güncellenirken bir hata oluştu.</div>';
                }
            }
        } elseif ($action === 'delete') {
            $projectModel->id = $_POST['project_id'] ?? 0;
            if ($projectModel->delete()) {
                $message = '<div class="success-message">Proje başarıyla silindi!</div>';
            } else {
                $message = '<div class="error-message">Proje silinirken bir hata oluştu.</div>';
            }
        }
    }
}

// Proje bilgilerini çekme (düzenleme formu için)
$editProject = null;
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $projectId = $_GET['id'];
    if ($projectModel->getById($projectId)) {
        $editProject = $projectModel;
    }
}

$projects = $projectModel->getAll($selectedBrandId);

include __DIR__ . '/../app/includes/header.php';
?>

<section class="page-header">
    <h2>Proje Yönetimi</h2>
</section>

<?php echo $message; ?>

<section class="form-section">
    <h3><?php echo $editProject ? 'Projeyi Düzenle' : 'Yeni Proje Ekle'; ?></h3>
    <form action="projects.php" method="POST">
        <input type="hidden" name="action" value="<?php echo $editProject ? 'update' : 'create'; ?>">
        <?php if ($editProject): ?>
            <input type="hidden" name="project_id" value="<?php echo htmlspecialchars($editProject->id); ?>">
        <?php endif; ?>

        <div class="form-group">
            <label for="brand_id">Marka:</label>
            <select id="brand_id" name="brand_id" required>
                <option value="">Bir marka seçin</option>
                <?php while ($brand = $brands->fetch(PDO::FETCH_ASSOC)): ?>
                    <option value="<?php echo htmlspecialchars($brand['id']); ?>"
                        <?php echo (($editProject && $editProject->brand_id == $brand['id']) || ($selectedBrandId && $selectedBrandId == $brand['id'])) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($brand['name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="name">Proje Adı:</label>
            <input type="text" id="name" name="name" value="<?php echo $editProject ? htmlspecialchars($editProject->name) : ''; ?>" required>
        </div>
        <div class="form-group">
            <label for="description">Açıklama:</label>
            <textarea id="description" name="description"><?php echo $editProject ? htmlspecialchars($editProject->description) : ''; ?></textarea>
        </div>
        <div class="form-group">
            <label for="status">Durum:</label>
            <select id="status" name="status" required>
                <option value="active" <?php echo ($editProject && $editProject->status === 'active') ? 'selected' : ''; ?>>Aktif</option>
                <option value="completed" <?php echo ($editProject && $editProject->status === 'completed') ? 'selected' : ''; ?>>Tamamlandı</option>
                <option value="on_hold" <?php echo ($editProject && $editProject->status === 'on_hold') ? 'selected' : ''; ?>>Beklemede</option>
                <option value="cancelled" <?php echo ($editProject && $editProject->status === 'cancelled') ? 'selected' : ''; ?>>İptal Edildi</option>
            </select>
        </div>
        <div class="form-group">
            <label for="start_date">Başlangıç Tarihi:</label>
            <input type="text" id="start_date" name="start_date" class="datepicker" value="<?php echo $editProject ? htmlspecialchars($editProject->start_date) : ''; ?>">
        </div>
        <div class="form-group">
            <label for="end_date">Bitiş Tarihi:</label>
            <input type="text" id="end_date" name="end_date" class="datepicker" value="<?php echo $editProject ? htmlspecialchars($editProject->end_date) : ''; ?>">
        </div>
        <button type="submit" class="button primary"><?php echo $editProject ? 'Projeyi Güncelle' : 'Proje Ekle'; ?></button>
        <?php if ($editProject): ?>
            <a href="projects.php" class="button secondary">İptal</a>
        <?php endif; ?>
    </form>
</section>

<section class="data-list">
    <h3>Mevcut Projeler</h3>
    <?php if ($selectedBrandId): ?>
        <h4>Marka: <?php
            $selectedBrandName = '';
            $brands->execute(); // Cursor'ı sıfırla
            while ($brand = $brands->fetch(PDO::FETCH_ASSOC)) {
                if ($brand['id'] == $selectedBrandId) {
                    $selectedBrandName = $brand['name'];
                    break;
                }
            }
            echo htmlspecialchars($selectedBrandName);
        ?></h4>
    <?php endif; ?>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Marka</th>
                <th>Proje Adı</th>
                <th>Durum</th>
                <th>Başlangıç</th>
                <th>Bitiş</th>
                <th>İşlemler</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($projects->rowCount() > 0): ?>
                <?php while ($project = $projects->fetch(PDO::FETCH_ASSOC)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($project['id']); ?></td>
                        <td><?php echo htmlspecialchars($project['brand_name']); ?></td>
                        <td><?php echo htmlspecialchars($project['name']); ?></td>
                        <td><?php echo htmlspecialchars($project['status']); ?></td>
                        <td><?php echo htmlspecialchars($project['start_date']); ?></td>
                        <td><?php echo htmlspecialchars($project['end_date']); ?></td>
                        <td class="actions">
                            <a href="jobs.php?project_id=<?php echo $project['id']; ?>" class="button small">İşler <span data-feather="briefcase"></span></a>
                            <a href="projects.php?action=edit&id=<?php echo $project['id']; ?>" class="button small">Düzenle <span data-feather="edit"></span></a>
                            <form action="projects.php" method="POST" style="display:inline-block;" onsubmit="return confirm('Bu projeyi silmek istediğinizden emin misiniz? Bu işlem, projeyle ilişkili tüm işleri de silecektir.');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="project_id" value="<?php echo htmlspecialchars($project['id']); ?>">
                                <button type="submit" class="button small danger">Sil <span data-feather="trash-2"></span></button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7">Henüz hiç proje eklenmemiş veya seçili markaya ait proje bulunmuyor.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</section>

<?php include __DIR__ . '/../app/includes/footer.php'; ?>