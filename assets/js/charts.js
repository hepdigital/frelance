// public/assets/js/charts.js

document.addEventListener('DOMContentLoaded', () => {
    console.log('Charts JavaScript yüklendi.');

    // Aylık Gelir Grafiği
    const monthlyRevenueCtx = document.getElementById('monthlyRevenueChart');
    if (monthlyRevenueCtx && typeof Chart !== 'undefined') {
        new Chart(monthlyRevenueCtx, {
            type: 'line',
            data: {
                labels: monthlyRevenueLabels, // PHP'den gelen veri
                datasets: [{
                    label: 'Aylık Gelir (TL)',
                    data: monthlyRevenueValues, // PHP'den gelen veri
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1,
                    fill: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Gelir (TL)'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Ay'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY' }).format(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
    }

    // İş Durumu Dağılımı Grafiği (Pasta Grafik)
    const jobStatusCtx = document.getElementById('jobStatusChart');
    if (jobStatusCtx && typeof Chart !== 'undefined') {
        new Chart(jobStatusCtx, {
            type: 'pie',
            data: {
                labels: jobStatusLabels.map(label => {
                    // Durum etiketlerini daha okunabilir hale getirme
                    switch(label) {
                        case 'pending': return 'Beklemede';
                        case 'completed': return 'Tamamlandı';
                        case 'billed': return 'Faturalandırıldı';
                        default: return label;
                    }
                }),
                datasets: [{
                    label: 'İş Durumu',
                    data: jobStatusValues, // PHP'den gelen veri
                    backgroundColor: [
                        'rgba(255, 159, 64, 0.8)', // Pending (Turuncu)
                        'rgba(75, 192, 192, 0.8)', // Completed (Yeşilimsi mavi)
                        'rgba(54, 162, 235, 0.8)'  // Billed (Mavi)
                    ],
                    borderColor: [
                        'rgba(255, 159, 64, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(54, 162, 235, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed !== null) {
                                    label += context.parsed + ' adet';
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
    }
});