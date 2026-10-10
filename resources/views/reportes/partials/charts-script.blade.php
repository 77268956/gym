<script>
    (() => {
        const drawReportCharts = () => {
            const reportCharts = {{ Illuminate\Support\Js::from($graficas) }};
            const palette = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796'];
            const theme = getComputedStyle(document.documentElement);
            const themePrimary = theme.getPropertyValue('--primary').trim() || '#2563EB';
            const themeText = theme.getPropertyValue('--text-main').trim() || '#334155';
            const themePrimaryHover = theme.getPropertyValue('--primary-hover').trim() || themePrimary;
            const currencySymbol = {{ Illuminate\Support\Js::from($gymConfig->simbolo_moneda ?? '$') }};
            Chart.defaults.color = themeText;

            const createReportChart = (canvasId, type, labels, values, label, options = {}) => {
                const canvas = document.getElementById(canvasId);
                if (!canvas) return null;

                return new Chart(canvas, {
                    type,
                    data: {
                        labels,
                        datasets: [{
                            label,
                            data: values,
                            backgroundColor: type === 'line' ? `color-mix(in srgb, ${themePrimary} 14%, transparent)` : palette,
                            borderColor: type === 'line' ? themePrimary : '#fff',
                            borderWidth: type === 'line' ? 2 : 1,
                            borderRadius: type === 'bar' ? 5 : 0,
                            fill: type === 'line',
                            tension: .32,
                            pointRadius: type === 'line' ? 2 : 0,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: { duration: 0 },
                        plugins: { legend: { display: type === 'doughnut', position: 'bottom' } },
                        scales: type === 'doughnut' ? {} : {
                            y: { beginAtZero: true, ticks: { precision: 0 } },
                            x: { grid: { display: false } },
                        },
                        ...options,
                    },
                });
            };

            createReportChart('chartIngresos', 'line', reportCharts.etiquetasDias, reportCharts.ingresosPorDia, `Ingresos (${currencySymbol})`, {
                scales: { y: { beginAtZero: true, ticks: { callback: value => currencySymbol + ' ' + value } }, x: { grid: { display: false } } },
            });
            createReportChart('chartMetodosPago', 'doughnut', reportCharts.metodosPago.labels, reportCharts.metodosPago.valores, 'Pagos por método');

            const clientesChart = createReportChart('chartClientes', 'line', reportCharts.etiquetasDias, reportCharts.visitasPorDia, 'Visitas');
            if (clientesChart) {
                clientesChart.data.datasets.push({
                    label: 'Clientes nuevos',
                    data: reportCharts.clientesNuevosPorDia,
                    borderColor: themePrimaryHover,
                    backgroundColor: `color-mix(in srgb, ${themePrimaryHover} 12%, transparent)`,
                    tension: .32,
                    fill: true,
                });
                clientesChart.update();
            }

            createReportChart('chartEmpleados', 'bar', reportCharts.incidenciasEmpleados.labels, reportCharts.incidenciasEmpleados.valores, 'Incidencias');
            createReportChart('chartSistema', 'doughnut', reportCharts.alertas.labels, reportCharts.alertas.valores, 'Alertas por tipo');
            document.dispatchEvent(new Event('reportChartsReady'));
        };

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', drawReportCharts, { once: true });
        } else {
            drawReportCharts();
        }
    })();
</script>
