<?php
// app/includes/header.php

require_once __DIR__ . '/../config.php'; // BASE_URL'e erişim için
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Freelance Takip Sistemi</title>
    
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Freelance İş Takip</h1>
            <nav>
                <ul>
                    <li><a href="<?php echo BASE_URL; ?>dashboard.php">Dashboard</a></li>
                    <li><a href="<?php echo BASE_URL; ?>brands.php">Markalar</a></li>
                    <li><a href="<?php echo BASE_URL; ?>projects.php">Projeler</a></li>
                    <li><a href="<?php echo BASE_URL; ?>jobs.php">İşler</a></li>
                    <li><a href="<?php echo BASE_URL; ?>generate_invoice.php">Fatura</a></li>
                </ul>
            </nav>
        </header>
        <main>