@extends('layouts.app')

@section('styles')
<style>
    .reporte-page {
        page-break-after: always;
        min-height: 100vh;
        padding: 40px;
        background: white;
        margin-bottom: 20px;
    }
    
    .reporte-page:last-child {
        page-break-after: auto;
    }
    
    

    .page-header {
        text-align: center;          
        padding: 30px 20px;          
        margin-bottom: 40px;         
        border-bottom: 3px solid #5A2828;  
    }
    
    
    .page-title {
        color: #5A2828;             
        font-size: 36px;            
        font-weight: 700;           
        margin-bottom: 15px;        
        letter-spacing: 0.5px;      
    }
        
    .page-subtitle {
        color: #5A2828;                  
        font-size: 16px;              
        font-weight: 400;             
        margin: 0;                    
    }
    
    .stats-row {
        display: flex;
        gap: 20px;
        margin-bottom: 30px;
    }
    
    .stat-card {
        flex: 1;
        background: #FFFBF5;
        border: 1px solid #5A2828;
        border-radius: 8px;
        padding: 20px;
        text-align: center;
    }
    
    .stat-label {
        color: #5A2828;
        font-size: 14px;
        font-weight: 600;
        text-transform: uppercase;
        margin-bottom: 10px;
    }
    
    .stat-value {
        color: #5A2828;
        font-size: 32px;
        font-weight: bold;
    }
    
    .chart-section {
        background: #f9f9f9;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
    }
    
    .chart-title {
        color: #5A2828;
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 15px;
    }
    
    .navigation-controls {
        position: fixed;
        bottom: 30px;
        right: 30px;
        display: flex;
        gap: 10px;
        z-index: 1000;
    }
    
    .nav-btn {
        background: #5A2828;
        color: white;
        border: none;
        padding: 12px 24px;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
        transition: background 0.3s;
        box-shadow: 0 4px 6px rgba(0,0,0,0.2);
    }
    
    .nav-btn:hover {
        background: #8B4513;
    }
    
    .nav-btn:disabled {
        background: #ccc;
        cursor: not-allowed;
    }
    
    .page-indicator {
        position: fixed;
        top: 20px;
        right: 30px;
        background: #5A2828;
        color: white;
        padding: 10px 20px;
        border-radius: 20px;
        font-weight: 600;
        z-index: 1000;
    }
    
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.95);
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }
    
    .loading-spinner {
        border: 5px solid #f3f3f3;
        border-top: 5px solid #5A2828;
        border-radius: 50%;
        width: 60px;
        height: 60px;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    .loading-text {
        margin-top: 20px;
        color: #5A2828;
        font-size: 18px;
        font-weight: 600;
    }
    
    @media print {
        .navigation-controls,
        .page-indicator,
        .no-print {
            display: none !important;
        }
        
        .reporte-page {
            margin-bottom: 0;
            padding: 20px;
        }
        
        body {
            background: white;
        }
    }
</style>
@endsection

@section('content')
<div id="loadingOverlay" class="loading-overlay">
    <div class="loading-spinner"></div>
    <div class="loading-text">Cargando reportes...</div>
    <div id="loadingProgress" style="margin-top: 10px; color: #666;"></div>
</div>

<div id="reportesContainer"></div>
<div class="container-fluid no-print mb-3">
    <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded shadow-sm">
        
        <h5 class="mb-0" style="color: #5A2828;">
            Página <span id="currentPageNum">1</span> de <span id="totalPagesNum">0</span>
        </h5>
        
        <div class="btn-group" role="group">
            <button id="btnPrevPage" type="button" class="btn btn-outline-secondary" disabled>
                ← Anterior
            </button>
            <button id="btnPrint" type="button" class="btn btn-primary fw-bold">
                🖨️ Imprimir Todo
            </button>
            <button id="btnNextPage" type="button" class="btn btn-outline-secondary">
                Siguiente →
            </button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', async function() {
    const reportesContainer = document.getElementById('reportesContainer');
    const loadingOverlay = document.getElementById('loadingOverlay');
    const loadingProgress = document.getElementById('loadingProgress');
    const currentPageNum = document.getElementById('currentPageNum');
    const totalPagesNum = document.getElementById('totalPagesNum');
    const btnPrevPage = document.getElementById('btnPrevPage');
    const btnNextPage = document.getElementById('btnNextPage');
    const btnPrint = document.getElementById('btnPrint');
    
    let currentPage = 0;
    let totalPages = 0;
    let charts = [];
    
    // Obtener datos de la IA desde sessionStorage o URL
    const datosIA = JSON.parse(sessionStorage.getItem('datosReporteIA') || '{"platillos":[]}');
    
    if (!datosIA.platillos || datosIA.platillos.length === 0) {
        alert('No hay datos de reporte. Redirigiendo...');
        window.location.href = '/reportes';
        return;
    }
    
    totalPages = datosIA.platillos.length;
    totalPagesNum.textContent = totalPages;
    
    // Función para formatear moneda
    function formatearMoneda(valor) {
        return 'Bs. ' + parseFloat(valor || 0).toLocaleString('es-BO', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }
    
    // Función para formatear fechas
    function formatearRangoFecha(inicio, fin) {
        const opciones = { day: 'numeric', month: 'long', year: 'numeric' };
        const fechaInicio = new Date(inicio).toLocaleDateString('es-BO', opciones);
        const fechaFin = new Date(fin).toLocaleDateString('es-BO', opciones);
        return `${fechaInicio} - ${fechaFin}`;
    }
    
    // Función para obtener datos de Laravel
    async function obtenerDatosPlatillo(platillo, index) {
        loadingProgress.textContent = `Cargando ${platillo.nombre} (${index + 1}/${totalPages})...`;
        
        const body = {
            fecha_inicio: platillo.fecha_inicio,
            fecha_fin: platillo.fecha_fin,
            granularidad: platillo.granularidad,
            intervalo: platillo.intervalo_sql
        };
        
        if (platillo.nombre && platillo.nombre.toLowerCase() !== 'ventas' && platillo.nombre.toLowerCase() !== 'venta') {
            body.nombre_receta = platillo.nombre;
        }
        //consultar a endpoint propio de laravel
        const response = await fetch('/api/ventas/agrupadas', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(body)
        });
        
        if (!response.ok) {
            throw new Error(`Error al cargar ${platillo.nombre}`);
        }
        
        return await response.json();
    }

    function crearPaginaReporte(platillo, datos, index) {
        const resumen = datos.resumen || {};
        const datosGrafico = datos.data || [];
        
        const page = document.createElement('div');
        page.className = 'reporte-page';
        page.id = `page-${index}`;
        page.style.display = index === 0 ? 'block' : 'none';
        
        const labels = datosGrafico.map(item => item.etiqueta);
        const ventasData = datosGrafico.map(item => parseFloat(item.total_ventas) || 0);
        const platosData = datosGrafico.map(item => parseInt(item.platos_vendidos) || 0);
        const btnImprimirPaginaId = `btn-imprimir-pagina-${index}`;
        page.innerHTML = `
             <div class="card mb-4 shadow-sm">
                <div class="card-body header-card-body text-center">
                    <div class="page-title-card">${platillo.nombre.toUpperCase()}</div>
                    <div class="page-subtitle">
                        Reporte ${platillo.tipo} • ${formatearRangoFecha(platillo.fecha_inicio, platillo.fecha_fin)}
                    </div>
                    <button type="button" 
                            id="${btnImprimirPaginaId}"
                            class="btn btn-outline-primary btn-sm no-print btn-imprimir-pagina"
                            title="Imprimir esta página">
                        🖨️ Imprimir esta Página
                    </button>
                </div>
            </div>
            
            <div class="col-md-12 mb-4">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title" style="color: #5A2828;">Ingresos Totales</h5>
                                <h3 class="mb-0" style="color: #5A2828;">${formatearMoneda(resumen.ingresos_totales)}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title" style="color: #5A2828;">Promedio por Venta</h5>
                                <h3 class="mb-0" style="color: #5A2828;">${formatearMoneda(resumen.promedio_venta)}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title" style="color: #5A2828;">Platos Vendidos</h5>
                                <h3 class="mb-0" style="color: #5A2828;">${resumen.platos_vendidos || 0}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="chartsContainer-${index}"></div>
        `;
        // ==========================================================
        
        reportesContainer.appendChild(page);
        
        // Crear gráficos según lo solicitado
        const chartsContainer = document.getElementById(`chartsContainer-${index}`);
        
        // Iteramos y creamos un <div class="card"> por cada gráfico
        platillo.graficas.forEach((tipoGrafica, chartIndex) => {
            const chartId = `chart-${index}-${chartIndex}`;
            const tipoTexto = {
                'barra': 'Gráfico de Barras',
                'linea': 'Gráfico de Líneas',
                'pastel': 'Gráfico de Pastel'
            }[tipoGrafica] || 'Gráfico';
            
            // Crear el HTML del Card
            const cardHtml = `
                <div class="card mb-4">
                    <div class="card-body">
                        <h4 class="card-title" style="color: #5A2828;">${tipoTexto}</h4>
                        <div class="chart-container" style="position: relative; height: 400px;">
                            <canvas id="${chartId}"></canvas>
                        </div>
                    </div>
                </div>
            `;
            
            // Insertar el HTML del Card en el contenedor
            chartsContainer.insertAdjacentHTML('beforeend', cardHtml);
            
            // Crear el gráfico (con un pequeño retraso para que el DOM se actualice)
            setTimeout(() => {
                const canvas = document.getElementById(chartId);
                const ctx = canvas.getContext('2d');
                const chart = new Chart(ctx, getChartConfig(tipoGrafica, labels, ventasData, platosData));
                charts.push(chart);
            }, 100);
        });
        const btnImprimirPagina = document.getElementById(btnImprimirPaginaId);
        btnImprimirPagina.addEventListener('click', () => {
            //imprimir pagina actual
            window.print();
        });
    }
    
    
    // Configuración de gráficos
    function getChartConfig(tipo, labels, ventasData, platosData) {
        if (tipo === 'pastel') {
            return {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Total Ventas (Bs)',
                        data: ventasData,
                        backgroundColor: ['#5A2828', '#8B4513', '#A0522D', '#CD853F', '#D2B48C', '#F4A460', '#DEB887', '#FFE4C4', '#BC8F8F', '#FFDAB9', '#E9967A', '#FFB6C1']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'right' },
                        title: { display: false }
                    }
                }
            };
        }
        
        return {
            type: tipo === 'barra' ? 'bar' : 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Total Ventas (Bs)',
                        data: ventasData,
                        borderColor: '#5A2828',
                        backgroundColor: 'rgba(90, 40, 40, 0.7)',
                        yAxisID: 'y',
                        fill: tipo === 'linea'
                    },
                    {
                        label: 'Platos Vendidos',
                        data: platosData,
                        borderColor: '#FF9800',
                        backgroundColor: 'rgba(255, 152, 0, 0.5)',
                        yAxisID: 'y1',
                        fill: tipo === 'linea'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: { display: true, text: 'Total Ventas (Bs)' },
                        ticks: { callback: value => formatearMoneda(value) }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: { display: true, text: 'Platos Vendidos' },
                        grid: { drawOnChartArea: false }
                    }
                }
            }
        };
    }
    
    // Cargar todos los reportes -  magia para ejecutar todas las peticiones async
    try {
        for (let i = 0; i < datosIA.platillos.length; i++) {
            const platillo = datosIA.platillos[i];
            const datos = await obtenerDatosPlatillo(platillo, i);
            crearPaginaReporte(platillo, datos, i);
        }
        
        loadingOverlay.style.display = 'none';
        actualizarNavegacion();
    } catch (error) {
        console.error('Error cargando reportes:', error);
        alert('Error al cargar los reportes: ' + error.message);
        loadingOverlay.style.display = 'none';
    }
    
    // Navegación entre páginas
    function mostrarPagina(index) {
        document.querySelectorAll('.reporte-page').forEach((page, i) => {
            page.style.display = i === index ? 'block' : 'none';
        });
        currentPage = index;
        currentPageNum.textContent = currentPage + 1;
        actualizarNavegacion();
    }
    
    function actualizarNavegacion() {
        btnPrevPage.disabled = currentPage === 0;
        btnNextPage.disabled = currentPage === totalPages - 1;
    }
    
    btnPrevPage.addEventListener('click', () => {
        if (currentPage > 0) {
            mostrarPagina(currentPage - 1);
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    });
    
    btnNextPage.addEventListener('click', () => {
        if (currentPage < totalPages - 1) {
            mostrarPagina(currentPage + 1);
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    });
    
    btnPrint.addEventListener('click', () => {
        // Mostrar todas las páginas para imprimir
        document.querySelectorAll('.reporte-page').forEach(page => {
            page.style.display = 'block';
        });
        
        setTimeout(() => {
            window.print();
            // Volver a mostrar solo la página actual
            mostrarPagina(currentPage);
        }, 500);
    });
    
    // Atajos de teclado
    document.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowLeft' && currentPage > 0) {
            btnPrevPage.click();
        } else if (e.key === 'ArrowRight' && currentPage < totalPages - 1) {
            btnNextPage.click();
        } else if (e.ctrlKey && e.key === 'p') {
            e.preventDefault();
            btnPrint.click();
        }
    });
});
</script>
@endsection