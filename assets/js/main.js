// public/assets/js/main.js

document.addEventListener('DOMContentLoaded', () => {
    console.log('Main JavaScript yüklendi.');

    // Feather Icons zaten footer.php'de başlatılıyor, burada tekrar etmeye gerek yok.

    // Flatpickr (Tarih Seçici) Entegrasyonu
    // Tarih giriş alanlarına flatpickr ekle
    flatpickr(".datepicker", {
        dateFormat: "Y-m-d", // YYYY-MM-DD formatı
        locale: "tr",       // Türkçe dil paketi
        altInput: true,     // Alternatif, daha okunabilir bir giriş kutusu göster
        altFormat: "d.m.Y", // Alternatif giriş kutusunun formatı
        allowInput: true    // Kullanıcının manuel giriş yapmasına izin ver
    });

    // İş Ekle/Düzenle sayfasındaki fiyatlandırma tipi değişimini yönetme
    const isMonthlyRetainerCheckbox = document.getElementById('is_monthly_retainer');
    const priceField = document.getElementById('price_field');
    const monthlyRetainerAmountField = document.getElementById('monthly_retainer_amount_field');
    const priceInput = document.getElementById('price');
    const monthlyRetainerAmountInput = document.getElementById('monthly_retainer_amount');

    // Bu fonksiyon jobs.php içinde de var, dilerseniz buraya taşıyıp yeniden kullanılabilir hale getirebilirsiniz.
    // Ancak jobs.php'nin özel ihtiyaçları için şimdilik orada kalabilir.
    // Yine de, eğer global olarak kullanmak isterseniz:
    /*
    function togglePriceFields() {
        if (isMonthlyRetainerCheckbox && priceField && monthlyRetainerAmountField) {
            if (isMonthlyRetainerCheckbox.checked) {
                priceField.style.display = 'none';
                monthlyRetainerAmountField.style.display = 'block';
                // priceInput'a 'required' varsa kaldır, retainer'a ekle (DOM'da kontrol et)
                if (priceInput) priceInput.removeAttribute('required');
                if (monthlyRetainerAmountInput) monthlyRetainerAmountInput.setAttribute('required', 'required');
            } else {
                priceField.style.display = 'block';
                monthlyRetainerAmountField.style.display = 'none';
                // priceInput'a 'required' ekle, retainer'dan kaldır
                if (priceInput) priceInput.setAttribute('required', 'required');
                if (monthlyRetainerAmountInput) monthlyRetainerAmountInput.removeAttribute('required');
            }
        }
    }

    if (isMonthlyRetainerCheckbox) {
        isMonthlyRetainerCheckbox.addEventListener('change', togglePriceFields);
        togglePriceFields(); // Sayfa yüklendiğinde başlangıç durumunu ayarla
    }
    */

    // Onay mesajlarını otomatik gizleme
    const messages = document.querySelectorAll('.success-message, .error-message');
    messages.forEach(message => {
        setTimeout(() => {
            message.style.opacity = '0';
            message.style.height = '0';
            message.style.margin = '0';
            message.style.padding = '0';
            setTimeout(() => message.remove(), 500); // Geçişten sonra kaldır
        }, 5000); // 5 saniye sonra gizle
    });
});