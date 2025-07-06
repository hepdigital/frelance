<?php
// public/brands.php

require_once __DIR__ . '/../app/database.php';
require_once __DIR__ . '/../app/models/Brand.php';

$brandModel = new Brand();
$message = '';

// Form gönderimi işlemleri
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        if ($action === 'create' || $action === 'update') {
            $brandModel->name = $_POST['name'] ?? '';
            $brandModel->contact_person = $_POST['contact_person'] ?? '';
            $brandModel->email = $_POST['email'] ?? '';
            $brandModel->phone = $_POST['phone'] ?? '';
            $brandModel->address = $_POST['address'] ?? '';

            if ($action === 'create') {
                if ($brandModel->create()) {
                    $message = '<div class="success-message">Marka başarıyla eklendi!</div>';
                } else {
                    $message = '<div class="error-message">Marka eklenirken bir hata oluştu.</div>';
                }
            } elseif ($action === 'update') {
                $brandModel->id = $_POST['brand_id'] ?? 0;
                if ($brandModel->update()) {
                    $message = '<div class="success-message">Marka başarıyla güncellendi!</div>';
                } else {
                    $message = '<div class="error-message">Marka güncellenirken bir hata oluştu.</div>';
                }
            }
        } elseif ($action === 'delete') {
            $brandModel->id = $_POST['brand_id'] ?? 0;
            if ($brandModel->delete()) {
                $message = '<div class="success-message">Marka başarıyla silindi!</div>';
            } else {
                $message = '<div class="error-message">Marka silinirken bir hata oluştu.</div>';
            }
        }
    }
}

// Marka bilgilerini çekme (düzenleme formu için)
$editBrand = null;
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $brandId = $_GET['id'];
    if ($brandModel->getById($brandId)) {
        $editBrand = $brandModel;
    }
}

$brands = $brandModel->getAll();

include __DIR__ . '/../app/includes/header.php';
?>

<section class="page-header">
    <h2>Marka Yönetimi</h2>
</section>

<?php echo $message; ?>

<section class="form-section">
    <h3><?php echo $editBrand ? 'Markayı Düzenle' : 'Yeni Marka Ekle'; ?></h3>
    <form action="brands.php" method="POST">
        <input type="hidden" name="action" value="<?php echo $editBrand ? 'update' : 'create'; ?>">
        <?php if ($editBrand): ?>
            <input type="hidden" name="brand_id" value="<?php echo htmlspecialchars($editBrand->id); ?>">
        <?php endif; ?>

        <div class="form-group">
            <label for="name">Marka Adı:</label>
            <input type="text" id="name" name="name" value="<?php echo $editBrand ? htmlspecialchars($editBrand->name) : ''; ?>" required>
        </div>
        <div class="form-group">
            <label for="contact_person">İlgili Kişi:</label>
            <input type="text" id="contact_person" name="contact_person" value="<?php echo $editBrand ? htmlspecialchars($editBrand->contact_person) : ''; ?>">
        </div>
        <div class="form-group">
            <label for="email">E-posta:</label>
            <input type="email" id="email" name="email" value="<?php echo $editBrand ? htmlspecialchars($editBrand->email) : ''; ?>">
        </div>
        <div class="form-group">
            <label for="phone">Telefon:</label>
            <input type="tel" id="phone" name="phone" value="<?php echo $editBrand ? htmlspecialchars($editBrand->phone) : ''; ?>">
        </div>
        <div class="form-group">
            <label for="address">Adres:</label>
            <textarea id="address" name="address"><?php echo $editBrand ? htmlspecialchars($editBrand->address) : ''; ?></textarea>
        </div>
        <button type="submit" class="button primary"><?php echo $editBrand ? 'Markayı Güncelle' : 'Marka Ekle'; ?></button>
        <?php if ($editBrand): ?>
            <a href="brands.php" class="button secondary">İptal</a>
        <?php endif; ?>
    </form>
</section>

<section class="data-list">
    <h3>Mevcut Markalar</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Marka Adı</th>
                <th>İlgili Kişi</th>
                <th>E-posta</th>
                <th>Telefon</th>
                <th>İşlemler</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($brands->rowCount() > 0): ?>
                <?php while ($brand = $brands->fetch(PDO::FETCH_ASSOC)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($brand['id']); ?></td>
                        <td><?php echo htmlspecialchars($brand['name']); ?></td>
                        <td><?php echo htmlspecialchars($brand['contact_person']); ?></td>
                        <td><?php echo htmlspecialchars($brand['email']); ?></td>
                        <td><?php echo htmlspecialchars($brand['phone']); ?></td>
                        <td class="actions">
                            <a href="projects.php?brand_id=<?php echo $brand['id']; ?>" class="button small">Projeler <span data-feather="folder"></span></a>
                            <a href="brands.php?action=edit&id=<?php echo $brand['id']; ?>" class="button small">Düzenle <span data-feather="edit"></span></a>
                            <form action="brands.php" method="POST" style="display:inline-block;" onsubmit="return confirm('Bu markayı silmek istediğinizden emin misiniz? Bu işlem, markayla ilişkili tüm proje ve işleri de silecektir.');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="brand_id" value="<?php echo htmlspecialchars($brand['id']); ?>">
                                <button type="submit" class="button small danger">Sil <span data-feather="trash-2"></span></button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">Henüz hiç marka eklenmemiş.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</section>

<?php include __DIR__ . '/../app/includes/footer.php'; ?>