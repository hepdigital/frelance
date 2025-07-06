<?php
// app/includes/footer.php

// config.php zaten header.php'de dahil edildiği için tekrar etmeye gerek yok.
?>
        </main>
        <footer>
            <p>&copy; <?php echo date("Y"); ?> Freelance Takip Sistemi. Tüm Hakları Saklıdır.</p>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <script>
        feather.replace(); // Feather ikonlarını başlat
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/tr.js"></script>

    <script src="<?php echo BASE_URL; ?>/assets/js/main.js"></script>
    <script src="<?php echo BASE_URL; ?>/js/charts.js"></script>
</body>
</html>