<?php
// freelance_tracker/public/index.php

// Tüm yönlendirme (routing) işlemlerini buradan yapabilir veya
// direkt dashboard'a yönlendirebiliriz.
// Şimdilik dashboard'a yönlendirelim.

header("Location: dashboard.php");
exit();
?>