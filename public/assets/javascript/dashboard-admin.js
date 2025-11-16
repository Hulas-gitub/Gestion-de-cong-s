// Variables globales pour les graphiques et la pagination
let congesChart, departmentChart, typesCongesChart, absenteismeChart;
let currentPage = 1;
let itemsPerPage = 10;
let allDepartements = [];
let currentPeriode = 'mois';

// ========== CHARGEMENT DES KPIs ==========
async function loadKpiStats() {
    try {
        const response = await fetch('/admin/api/dashboard/kpi-stats');
        const result = await response.json();

        if (result.success) {
            const data = result.data;
            // Mise à jour des panneaux KPI
            document.getElementById('kpi-employes-actifs').textContent = data.employes_actifs;
            document.getElementById('kpi-departements').textContent = data.total_departements;
            document.getElementById('kpi-chefs').textContent = data.total_chefs;
            // IMPORTANT: Employés actuellement en congé (date_debut <= aujourd'hui <= date_fin)
            document.getElementById('kpi-en-conge').textContent = data.employes_en_conge;
            document.getElementById('kpi-en-attente').textContent = data.demandes_en_attente;
        }
    } catch (error) {
        console.error('Erreur lors du chargement des KPIs:', error);
    }
}

// ========== GRAPHIQUE ÉVOLUTION DES CONGÉS ==========
async function loadEvolutionConges() {
    try {
        const response = await fetch('/admin/api/dashboard/evolution-conges');
        const result = await response.json();

        if (result.success) {
            const data = result.data;
            const labels = data.map(item => item.mois);
            const values = data.map(item => item.nombre);

            const congesCtx = document.getElementById('congesChart').getContext('2d');

            // Détruire le graphique existant s'il y en a un
            if (congesChart) {
                congesChart.destroy();
            }

            congesChart = new Chart(congesCtx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Congés approuvés',
                        data: values,
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointBackgroundColor: 'rgb(59, 130, 246)',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                color: getComputedStyle(document.documentElement).getPropertyValue('--text-color') || '#374151',
                                font: { size: 12 }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(156, 163, 175, 0.1)' },
                            ticks: { color: getComputedStyle(document.documentElement).getPropertyValue('--text-color') || '#6B7280' }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { color: getComputedStyle(document.documentElement).getPropertyValue('--text-color') || '#6B7280' }
                        }
                    }
                }
            });
        }
    } catch (error) {
        console.error('Erreur lors du chargement de l\'évolution des congés:', error);
    }
}

// ========== GRAPHIQUE EMPLOYÉS PAR DÉPARTEMENT ==========
async function loadEmployesDepartement() {
    try {
        const response = await fetch('/admin/api/dashboard/employes-departement');
        const result = await response.json();

        if (result.success) {
            const data = result.data;
            const labels = data.map(item => item.nom);
            const values = data.map(item => item.nombre);
            const colors = data.map(item => item.couleur);

            const departmentCtx = document.getElementById('departmentChart').getContext('2d');

            // Détruire le graphique existant s'il y en a un
            if (departmentChart) {
                departmentChart.destroy();
            }

            departmentChart = new Chart(departmentCtx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: values,
                        backgroundColor: colors,
                        borderWidth: 3,
                        borderColor: '#fff',
                        hoverOffset: 10
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                color: getComputedStyle(document.documentElement).getPropertyValue('--text-color') || '#374151',
                                padding: 15,
                                font: { size: 12 },
                                usePointStyle: true,
                                pointStyle: 'rect'
                            }
                        }
                    },
                    cutout: '70%'
                }
            });
        }
    } catch (error) {
        console.error('Erreur lors du chargement des employés par département:', error);
    }
}

// ========== GRAPHIQUE TYPES DE CONGÉS ==========
async function loadTypesConges() {
    try {
        const response = await fetch('/admin/api/dashboard/types-conges');
        const result = await response.json();

        if (result.success) {
            const data = result.data;
            const labels = data.map(item => item.nom);
            const values = data.map(item => item.nombre);
            const colors = data.map(item => item.couleur);

            const typesCongesCtx = document.getElementById('typesCongesChart').getContext('2d');

            // Détruire le graphique existant s'il y en a un
            if (typesCongesChart) {
                typesCongesChart.destroy();
            }

            typesCongesChart = new Chart(typesCongesCtx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Nombre de demandes',
                        data: values,
                        backgroundColor: colors,
                        borderWidth: 0,
                        borderRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(156, 163, 175, 0.1)' },
                            ticks: { color: getComputedStyle(document.documentElement).getPropertyValue('--text-color') || '#6B7280' }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { color: getComputedStyle(document.documentElement).getPropertyValue('--text-color') || '#6B7280' }
                        }
                    }
                }
            });
        }
    } catch (error) {
        console.error('Erreur lors du chargement des types de congés:', error);
    }
}

// ========== GRAPHIQUE TAUX D'ABSENTÉISME ==========
async function loadTauxAbsenteisme() {
    try {
        const response = await fetch('/admin/api/dashboard/taux-absenteisme');
        const result = await response.json();

        if (result.success) {
            const data = result.data;
            const labels = data.map(item => item.nom);
            const values = data.map(item => item.taux);

            const absenteismeCtx = document.getElementById('absenteismeChart').getContext('2d');

            // Détruire le graphique existant s'il y en a un
            if (absenteismeChart) {
                absenteismeChart.destroy();
            }

            absenteismeChart = new Chart(absenteismeCtx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Taux d\'absentéisme (%)',
                        data: values,
                        backgroundColor: values.map(val => {
                            if (val < 5) return 'rgba(34, 197, 94, 0.8)';  // Vert: taux faible
                            if (val < 10) return 'rgba(234, 179, 8, 0.8)'; // Jaune: taux moyen
                            return 'rgba(239, 68, 68, 0.8)';                // Rouge: taux élevé
                        }),
                        borderWidth: 0,
                        borderRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Taux d\'absentéisme: ' + context.parsed.y + '%';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 15,
                            grid: { color: 'rgba(156, 163, 175, 0.1)' },
                            ticks: {
                                color: getComputedStyle(document.documentElement).getPropertyValue('--text-color') || '#6B7280',
                                callback: function(value) { return value + '%'; }
                            }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { color: getComputedStyle(document.documentElement).getPropertyValue('--text-color') || '#6B7280' }
                        }
                    }
                }
            });
        }
    } catch (error) {
        console.error('Erreur lors du chargement du taux d\'absentéisme:', error);
    }
}

// ========== TABLEAU VUE D'ENSEMBLE ==========
async function loadVueEnsemble(periode = 'mois') {
    try {
        currentPeriode = periode;
        const response = await fetch(`/admin/api/dashboard/vue-ensemble?periode=${periode}`);
        const result = await response.json();

        if (result.success) {
            allDepartements = result.data;
            currentPage = 1;
            renderTablePage();
            renderPagination();
        }
    } catch (error) {
        console.error('Erreur lors du chargement de la vue d\'ensemble:', error);
    }
}

// ========== RENDU DE LA PAGE DU TABLEAU ==========
function renderTablePage() {
    const tbody = document.getElementById('tableau-vue-ensemble');
    tbody.innerHTML = '';

    const startIndex = (currentPage - 1) * itemsPerPage;
    const endIndex = startIndex + itemsPerPage;
    const pageData = allDepartements.slice(startIndex, endIndex);

    if (pageData.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                    <i class="fas fa-inbox text-4xl mb-2"></i>
                    <p>Aucune donnée disponible</p>
                </td>
            </tr>
        `;
        return;
    }

    pageData.forEach(dept => {
        // Colorisation du taux d'absence
        const tauxClass = dept.taux_absence < 5 ? 'bg-green-500/10 text-green-600 dark:text-green-400' :
                         dept.taux_absence < 10 ? 'bg-yellow-500/10 text-yellow-600 dark:text-yellow-400' :
                         'bg-red-500/10 text-red-600 dark:text-red-400';

        const row = `
            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-colors">
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-blue-500/10 flex items-center justify-center">
                            <i class="fas fa-building text-blue-500"></i>
                        </div>
                        <div>
                            <div class="font-semibold text-gray-900 dark:text-white">${dept.departement}</div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 text-gray-700 dark:text-gray-300">${dept.chef}</td>
                <td class="px-6 py-4 text-center">
                    <span class="inline-flex items-center justify-center w-12 h-8 rounded-lg bg-blue-500/10 text-blue-600 dark:text-blue-400 font-semibold">${dept.total_employes}</span>
                </td>
                <td class="px-6 py-4 text-center">
                    <span class="inline-flex items-center justify-center w-12 h-8 rounded-lg bg-green-500/10 text-green-600 dark:text-green-400 font-semibold" title="Employés actuellement en congé (date_debut <= aujourd'hui <= date_fin)">${dept.en_conge}</span>
                </td>
                <td class="px-6 py-4 text-center">
                    <span class="inline-flex items-center justify-center w-12 h-8 rounded-lg bg-orange-500/10 text-orange-600 dark:text-orange-400 font-semibold">${dept.demandes}</span>
                </td>
                <td class="px-6 py-4 text-center text-gray-700 dark:text-gray-300 font-medium">${dept.solde_moyen}j</td>
                <td class="px-6 py-4 text-center">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold ${tauxClass}">${dept.taux_absence}%</span>
                </td>
            </tr>
        `;
        tbody.insertAdjacentHTML('beforeend', row);
    });
}

// ========== RENDU DE LA PAGINATION ==========
function renderPagination() {
    const totalPages = Math.ceil(allDepartements.length / itemsPerPage);
    const paginationContainer = document.getElementById('pagination-container');

    if (totalPages <= 1) {
        paginationContainer.innerHTML = '';
        return;
    }

    let paginationHTML = `
        <div class="flex items-center justify-between px-6 py-4 border-t border-gray-200/50 dark:border-gray-700/50">
            <div class="text-sm text-gray-600 dark:text-gray-400">
                Affichage de ${((currentPage - 1) * itemsPerPage) + 1} à ${Math.min(currentPage * itemsPerPage, allDepartements.length)} sur ${allDepartements.length} départements
            </div>
            <div class="flex items-center gap-2">
    `;

    // Bouton Précédent
    paginationHTML += `
        <button onclick="changePage(${currentPage - 1})"
                ${currentPage === 1 ? 'disabled' : ''}
                class="px-3 py-2 rounded-lg bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed transition-all">
            <i class="fas fa-chevron-left"></i>
        </button>
    `;

    // Numéros de pages
    for (let i = 1; i <= totalPages; i++) {
        if (i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
            const activeClass = i === currentPage
                ? 'bg-blue-500 text-white'
                : 'bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600';

            paginationHTML += `
                <button onclick="changePage(${i})"
                        class="px-4 py-2 rounded-lg ${activeClass} transition-all">
                    ${i}
                </button>
            `;
        } else if (i === currentPage - 2 || i === currentPage + 2) {
            paginationHTML += `<span class="px-2 text-gray-500">...</span>`;
        }
    }

    // Bouton Suivant
    paginationHTML += `
        <button onclick="changePage(${currentPage + 1})"
                ${currentPage === totalPages ? 'disabled' : ''}
                class="px-3 py-2 rounded-lg bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed transition-all">
            <i class="fas fa-chevron-right"></i>
        </button>
    `;

    paginationHTML += `
            </div>
        </div>
    `;

    paginationContainer.innerHTML = paginationHTML;
}

// ========== CHANGEMENT DE PAGE ==========
function changePage(page) {
    const totalPages = Math.ceil(allDepartements.length / itemsPerPage);

    if (page < 1 || page > totalPages) return;

    currentPage = page;
    renderTablePage();
    renderPagination();

    // Scroll vers le tableau
    document.getElementById('tableau-vue-ensemble').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

// ========== EXPORT PDF ==========
function exportToPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF('l', 'mm', 'a4');

    const periodeText = currentPeriode === 'mois' ? 'Ce mois' :
                       currentPeriode === 'trimestre' ? 'Ce trimestre' : 'Cette année';

    doc.setFontSize(18);
    doc.text('Vue d\'ensemble par département', 14, 20);
    doc.setFontSize(11);
    doc.text(`Période: ${periodeText}`, 14, 28);
    doc.text(`Généré le: ${new Date().toLocaleDateString('fr-FR')}`, 14, 34);

    const headers = [['Département', 'Chef', 'Employés', 'En congé', 'Demandes', 'Solde moyen', 'Taux absence']];
    const data = allDepartements.map(dept => [
        dept.departement,
        dept.chef,
        dept.total_employes,
        dept.en_conge,
        dept.demandes,
        dept.solde_moyen + 'j',
        dept.taux_absence + '%'
    ]);

    doc.autoTable({
        head: headers,
        body: data,
        startY: 40,
        theme: 'grid',
        headStyles: { fillColor: [59, 130, 246] },
        styles: { fontSize: 9 }
    });

    doc.save(`vue_ensemble_${currentPeriode}_${Date.now()}.pdf`);
}

// ========== EXPORT EXCEL ==========
function exportToExcel() {
    const periodeText = currentPeriode === 'mois' ? 'Ce mois' :
                       currentPeriode === 'trimestre' ? 'Ce trimestre' : 'Cette année';

    const data = [
        ['Vue d\'ensemble par département'],
        [`Période: ${periodeText}`],
        [`Généré le: ${new Date().toLocaleDateString('fr-FR')}`],
        [],
        ['Département', 'Chef', 'Employés', 'En congé', 'Demandes', 'Solde moyen', 'Taux absence'],
        ...allDepartements.map(dept => [
            dept.departement,
            dept.chef,
            dept.total_employes,
            dept.en_conge,
            dept.demandes,
            dept.solde_moyen + 'j',
            dept.taux_absence + '%'
        ])
    ];

    const ws = XLSX.utils.aoa_to_sheet(data);
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, 'Vue ensemble');

    XLSX.writeFile(wb, `vue_ensemble_${currentPeriode}_${Date.now()}.xlsx`);
}

// ========== EXPORT WORD ==========
function exportToWord() {
    const periodeText = currentPeriode === 'mois' ? 'Ce mois' :
                       currentPeriode === 'trimestre' ? 'Ce trimestre' : 'Cette année';

    let htmlContent = `
        <html>
        <head>
            <meta charset="utf-8">
            <style>
                body { font-family: Arial, sans-serif; }
                h1 { color: #3b82f6; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #3b82f6; color: white; }
                tr:nth-child(even) { background-color: #f2f2f2; }
            </style>
        </head>
        <body>
            <h1>Vue d'ensemble par département</h1>
            <p><strong>Période:</strong> ${periodeText}</p>
            <p><strong>Généré le:</strong> ${new Date().toLocaleDateString('fr-FR')}</p>
            <table>
                <thead>
                    <tr>
                        <th>Département</th>
                        <th>Chef</th>
                        <th>Employés</th>
                        <th>En congé</th>
                        <th>Demandes</th>
                        <th>Solde moyen</th>
                        <th>Taux absence</th>
                    </tr>
                </thead>
                <tbody>
    `;

    allDepartements.forEach(dept => {
        htmlContent += `
            <tr>
                <td>${dept.departement}</td>
                <td>${dept.chef}</td>
                <td>${dept.total_employes}</td>
                <td>${dept.en_conge}</td>
                <td>${dept.demandes}</td>
                <td>${dept.solde_moyen}j</td>
                <td>${dept.taux_absence}%</td>
            </tr>
        `;
    });

    htmlContent += `
                </tbody>
            </table>
        </body>
        </html>
    `;

    const blob = new Blob(['\ufeff', htmlContent], {
        type: 'application/msword'
    });

    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = `vue_ensemble_${currentPeriode}_${Date.now()}.doc`;
    link.click();
    URL.revokeObjectURL(url);
}

// ========== GESTION DU MENU EXPORT ==========
function showExportMenu() {
    const menu = document.createElement('div');
    menu.id = 'export-menu';
    menu.className = 'absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 z-50';
    menu.innerHTML = `
        <div class="py-2">
            <button onclick="exportToPDF()" class="w-full px-4 py-2 text-left text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2">
                <i class="fas fa-file-pdf text-red-500"></i>
                <span>Exporter en PDF</span>
            </button>
            <button onclick="exportToExcel()" class="w-full px-4 py-2 text-left text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2">
                <i class="fas fa-file-excel text-green-500"></i>
                <span>Exporter en Excel</span>
            </button>
            <button onclick="exportToWord()" class="w-full px-4 py-2 text-left text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2">
                <i class="fas fa-file-word text-blue-500"></i>
                <span>Exporter en Word</span>
            </button>
        </div>
    `;

    // Supprimer l'ancien menu s'il existe
    const oldMenu = document.getElementById('export-menu');
    if (oldMenu) oldMenu.remove();

    // Ajouter le menu
    const exportBtn = document.getElementById('export-btn');
    exportBtn.parentElement.style.position = 'relative';
    exportBtn.parentElement.appendChild(menu);

    // Fermer le menu en cliquant ailleurs
    setTimeout(() => {
        document.addEventListener('click', function closeMenu(e) {
            if (!menu.contains(e.target) && e.target.id !== 'export-btn') {
                menu.remove();
                document.removeEventListener('click', closeMenu);
            }
        });
    }, 100);
}

// ========== EVENT LISTENERS ==========
document.addEventListener('DOMContentLoaded', function() {
    // Filtre de période
    const periodeFilter = document.getElementById('periodeFilter');
    if (periodeFilter) {
        periodeFilter.addEventListener('change', function() {
            loadVueEnsemble(this.value);
        });
    }

    // Bouton export
    const exportBtn = document.getElementById('export-btn');
    if (exportBtn) {
        exportBtn.addEventListener('click', showExportMenu);
    }

    // Chargement initial de toutes les données
    loadKpiStats();
    loadEvolutionConges();
    loadEmployesDepartement();
    loadTypesConges();
    loadTauxAbsenteisme();
    loadVueEnsemble('mois');
});
