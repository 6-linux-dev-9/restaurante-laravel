@extends('layouts.app')
<!-- reporte de ventas -->
@section('styles')
<style>
    .periodo-btn {
        background-color: #5A2828;
        color: white;
        border: none;
        padding: 8px 20px;
        border-radius: 5px;
        margin-right: 8px;
        cursor: pointer;
        font-weight: 500;
        text-transform: uppercase;
        font-size: 14px;
        transition: background-color 0.3s ease;
    }
    .periodo-btn:hover {
        background-color: #8B4513;
        color: white;
    }
    .periodo-btn.active {
        background-color: #8B4513;
        color: white;
    }
    .card-reporte {
        background-color: white;
        border-radius: 10px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }
    .btn-group {
        display: flex;
        gap: 8px;
    }
    .stats-card {
        background-color: #5A2828;
        color: white !important;
    }
    .stats-card .card-title,
    .stats-card h3,
    .stats-card p,
    .stats-card span {
        color: white !important;
    }
    .variation-badge {
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 0.875rem;
    }
    .variation-positive {
        background-color: rgba(76, 175, 80, 0.2);
        color: #4CAF50 !important;
    }
    .variation-negative {
        background-color: rgba(244, 67, 54, 0.2);
        color: #F44336 !important;
    }
    
    /* fernando estilos */
  
    /* fin fernando estilos */

    @media print {
        .no-print {
            display: none !important;
        }
        
        /* Ocultar el panel de IA al imprimir */
        
    }
</style>
@endsection

@section('content')
    <!-- ========== title-wrapper start ========== -->
    <div class="title-wrapper pt-30">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="title mb-30">
                    <h2>{{ __('Reportes de Ventas') }}</h2>
                </div>
            </div>
            <div class="col-md-6 text-end no-print">
                <button type="button" class="btn btn-primary" onclick="window.print()">Imprimir</button>
            </div>

        </div>
    </div>
    <!-- ========== title-wrapper end ========== -->

    <div class="container-fluid">
        
        <div class="row">
            <!-- Selector de Fechas -->
            <div class="col-md-12 mb-3">
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <div>
                        <label for="fechaInicio" class="form-label mb-0 me-2">Fecha inicio:</label>
                        <input type="date" id="fechaInicio" class="form-control" style="display:inline-block; width:auto;" placeholder="yyyy-mm-dd">
                    </div>
                    <div>
                        <label for="fechaFin" class="form-label mb-0 me-2">Fecha fin:</label>
                        <input type="date" id="fechaFin" class="form-control" style="display:inline-block; width:auto;" placeholder="yyyy-mm-dd">
                    </div>
                    <!-- modificando fernando -->
                    <div class="ms-auto">
                        <button type="button" id="btnIA" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm">
                            <svg class="icon-gemini" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2L2 7L12 12L22 7L12 2Z" fill="url(#gradient1)"/>
                            <path d="M2 17L12 22L22 17" stroke="url(#gradient2)" stroke-width="2"/>
                            <defs>
                                <linearGradient id="gradient1" x1="2" y1="2" x2="22" y2="12">
                                    <stop offset="0%" style="stop-color:#00d4ff;stop-opacity:1" />
                                    <stop offset="100%" style="stop-color:#0099ff;stop-opacity:1" />
                                </linearGradient>
                                <linearGradient id="gradient2" x1="2" y1="17" x2="22" y2="22">
                                    <stop offset="0%" style="stop-color:#00ffcc;stop-opacity:1" />
                                    <stop offset="100%" style="stop-color:#00d4ff;stop-opacity:1" />
                                </linearGradient>
                            </defs>
                            </svg>
                            <span>Asistente IA</span>
                            <svg class="icon-chevron" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                <path d="M4 6l4 4 4-4"/>
                            </svg>
                        </button>
                    </div>
                    <!-- fin fernando -->
                </div>
            </div>
            <!-- panel de IA -->
            <div id="iaPanel" class="collapse mt-3">
            <div class="card border-0 shadow-lg">
                <div class="card-header bg-gradient text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="d-flex align-items-center">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" class="me-2">
                            <circle cx="12" cy="12" r="10" stroke="white" stroke-width="2"/>
                            <path d="M8 12h8M12 8v8" stroke="white" stroke-width="2"/>
                        </svg>
                        <div>
                            <h5 class="mb-0">Pregúntale a la IA sobre tus reportes</h5>
                            <small class="text-white-50">Analiza tus datos de ventas con inteligencia artificial</small>
                        </div>
                    </div>
                </div>
                
                <div class="card-body bg-light">
                    <div class="mb-3">
                        <textarea 
                            id="iaPrompt" 
                            class="form-control form-control-lg border-primary" 
                            rows="4" 
                            placeholder="Escribe tu pregunta aquí..."
                            style="border-width: 2px;"
                        ></textarea>
                    </div>
                    
                    <div class="d-flex justify-content-end">
                        <button type="button" id="btnEnviarIA" class="btn btn-success btn-lg d-flex align-items-center gap-2 shadow">
                            <svg class="icon-send" width="20" height="20" viewBox="0 0 24 24" fill="none">
                                <path d="M22 2L11 13M22 2L15 22L11 13M22 2L2 8L11 13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span>Enviar</span>
                            <div class="spinner-border spinner-border-sm" role="status" style="display: none;">
                                <span class="visually-hidden">Cargando...</span>
                            </div>
                        </button>
                    </div>
                    
                    <!-- Respuesta de la IA -->
                    <div id="iaRespuesta" class="alert alert-info border-0 shadow-sm mt-4" role="alert" style="display: none;">
                        <div class="d-flex align-items-start mb-2">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" class="me-2 flex-shrink-0">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z" fill="currentColor"/>
                            </svg>
                            <div class="flex-grow-1">
                                <h6 class="alert-heading mb-2">
                                    <i class="bi bi-stars me-1"></i>
                                    Respuesta de la IA:
                                </h6>
                                <div id="iaRespuestaTexto" class="bg-white p-3 rounded border"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- fin fernando -->
    </div>
        </div>
            <!-- Tarjetas de estadísticas -->
            <div class="col-md-12 mb-4">
                <div class="row">
                    <!-- Total de Ventas -->
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title" style="color: #5A2828;">Total de Ventas</h5>
                                <h3 class="mb-0" id="totalVentas" style="color: #5A2828;">0</h3>
                                <p class="text-muted">Ventas realizadas en el periodo</p>
                            </div>
                        </div>
                    </div>
                    <!-- Ingresos Totales -->
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title" style="color: #5A2828;">Ingresos Totales</h5>
                                <h3 class="mb-0" id="ingresosTotales" style="color: #5A2828;">Bs. 0.00</h3>
                                <p class="text-muted">Total de ingresos del periodo</p>
                            </div>
                        </div>
                    </div>
                    <!-- Promedio por Venta -->
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title" style="color: #5A2828;">Promedio por Venta</h5>
                                <h3 class="mb-0" id="promedioVenta" style="color: #5A2828;">Bs. 0.00</h3>
                                <p class="text-muted">Valor promedio por venta</p>
                            </div>
                        </div>
                    </div>
                    <!-- Platos Vendidos -->
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title" style="color: #5A2828;">Platos Vendidos</h5>
                                <h3 class="mb-0" id="platosVendidos" style="color: #5A2828;">0</h3>
                                <p class="text-muted">Cantidad de platos vendidos en el periodo</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gráfico de Ventas -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4>Gráfico de Ventas</h4>
                            <div class="btn-group no-print">
                                <button type="button" class="main-btn dark-btn btn-hover active" data-periodo="semana" style="background-color: #5A2828; border-color: #5A2828; font-size: 14px; padding: 5px 15px; border-radius: 6px; color: white;">SEMANAL</button>
                                <button type="button" class="main-btn dark-btn btn-hover" data-periodo="mes" style="background-color: #5A2828; border-color: #5A2828; font-size: 14px; padding: 5px 15px; border-radius: 6px; color: white;">MENSUAL</button>
                                <button type="button" class="main-btn dark-btn btn-hover" data-periodo="año" style="background-color: #5A2828; border-color: #5A2828; font-size: 14px; padding: 5px 15px; border-radius: 6px; color: white;">ANUAL</button>
                            </div>
                            <div class="ms-3">
                                <select id="tipoGrafico" class="form-select" style="width: 140px;">
                                    <option value="bar">Barras</option>
                                    <option value="line">Líneas</option>
                                    <option value="pie">Pastel</option>
                                </select>
                            </div>
                        </div>
                        <div id="chart-error" class="alert" style="display: none;"></div>
                        <div class="chart-container" style="position: relative; height:400px; background-color: #f9f9f9; border-radius: 8px; padding: 15px;">
                            <canvas id="ventasChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detalles del Periodo -->
            <div class="col-md-12 mt-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title" style="color: #5A2828;">Detalles del Periodo</h5>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <p><strong>Venta más alta:</strong> <span id="ventaMaxima">Bs. 0.00</span> <span id="platoMaxima"></span></p>
                                <p><strong>Venta más baja:</strong> <span id="ventaMinima">Bs. 0.00</span> <span id="platoMinima"></span></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let ventasChart = null;
    let ultimoPeriodo = 'semana';
    let ultimoData = null;
    let ultimoLabels = [];
    let ultimoVentasData = [];
    let ultimoCantidadData = [];
    let tipoGrafico = 'bar';
    //fernando

    // Funcionalidad del botón IA
    const btnIA = document.getElementById('btnIA');
    const iaPanel = document.getElementById('iaPanel');
    const botonEnviarIA = document.getElementById('btnEnviarIA');
    const spinnerIA = botonEnviarIA.querySelector('.spinner-border');
    const iaPrompt = document.getElementById('iaPrompt');
    const iaRespuesta = document.getElementById('iaRespuesta');
    const iaRespuestaTexto = document.getElementById('iaRespuestaTexto');



    async function generarPDFconIA() {
        const texto = iaPrompt.value.trim();
        if (!texto) {
            alert('Por favor escribe una instrucción o pregunta para la IA');
            return;
        }

        const controller = new AbortController();
        const timeout = setTimeout(() => controller.abort(), 300000);
        
        botonEnviarIA.disabled = true;
        spinnerIA.style.display = 'inline-block';
        botonEnviarIA.querySelector('span').textContent = 'Procesando...';
        
        const fastApiURL = 'https://zqmdpzg7-7000.brs.devtunnels.ms/api';
        
        //const fastApiURL = 'https://python-restaurante.krakenwebservice.dev/api';
        
        
        try {
            // Llamar a la IA para obtener el JSON estructurado
            const response = await fetch(`${fastApiURL}/AI/generar-json/`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ texto: texto }),
                signal: controller.signal
            });

            clearTimeout(timeout);

            if (!response.ok) {
                const errMsg = await response.text();
                throw new Error(`Error ${response.status}: ${errMsg}`);
            }

            const datosIA = await response.json();
            console.log('Datos de la IA:', datosIA);

            // Validar que tengamos platillos
            if (!datosIA.platillos || datosIA.platillos.length === 0) {
                throw new Error('La IA no pudo interpretar la consulta correctamente');
            }

            // Guardar los datos en sessionStorage para la nueva vista
            sessionStorage.setItem('datosReporteIA', JSON.stringify(datosIA));

            // Redirigir a la nueva vista de reportes múltiples
            window.location.href = '/reportes/vista-multiple';

        } catch (error) {
            if (error.name === 'AbortError') {
                alert('⏱ Tiempo de espera agotado. Intenta nuevamente.');
            } else {
                alert(`Error: ${error.message}`);
            }
            console.error('Error procesando consulta:', error);
        } finally {
            botonEnviarIA.disabled = false;
            spinnerIA.style.display = 'none';
            botonEnviarIA.querySelector('span').textContent = 'Enviar';
            clearTimeout(timeout);
        }
    }

    function getCSRFToken() {
        
        const metaTag = document.querySelector('meta[name="csrf-token"]');
        if (metaTag && metaTag.getAttribute('content')) {
            return metaTag.getAttribute('content');
        }
        
        
        const hiddenInput = document.querySelector('input[name="_token"]');
        if (hiddenInput && hiddenInput.value) {
            return hiddenInput.value;
        }
        
        
        return '{{ csrf_token() }}';
    }

    const bsCollapse = new bootstrap.Collapse(iaPanel, {
        toggle: false
    });
    // Toggle del panel
    btnIA.addEventListener('click', function() {
        btnIA.classList.toggle('active');
        bsCollapse.toggle();
        
        // Focus en el textarea cuando se abre
        if (btnIA.classList.contains('active')) {
            setTimeout(() => iaPrompt.focus(), 350);
        }
    });

    botonEnviarIA.addEventListener('click', generarPDFconIA);

    

    //fin fernando
    function mostrarError(mensaje) {
        const errorDiv = document.getElementById('chart-error');
        errorDiv.textContent = mensaje;
        errorDiv.style.display = 'block';
        errorDiv.style.backgroundColor = '#f8d7da';
        errorDiv.style.color = '#721c24';
        errorDiv.style.padding = '10px';
        errorDiv.style.marginBottom = '20px';
        errorDiv.style.borderRadius = '4px';
    }
    function formatearMoneda(valor) {
        return 'Bs. ' + parseFloat(valor || 0).toLocaleString('es-BO', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }
    function formatearFecha(fecha, periodo) {
        try {
            if (periodo === 'año') {
                // Si es anual, la fecha es solo el año
                return fecha;
            }
            if (periodo === 'mes' && /^\d{4}-\d{2}$/.test(fecha)) {
                // Si es mensual y formato YYYY-MM
                const [anio, mes] = fecha.split('-');
                const meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
                return `${meses[parseInt(mes, 10) - 1]} ${anio}`;
            }
            // Para semanal o fechas completas
            const date = new Date(fecha);
            if (isNaN(date.getTime())) {
                return fecha;
            }
            switch(periodo) {
                case 'semana':
                    return date.toLocaleDateString('es-ES', { weekday: 'short', day: 'numeric' });
                default:
                    return date.toLocaleDateString('es-ES', { day: 'numeric', month: 'short' });
            }
        } catch (error) {
            return fecha;
        }
    }
    async function cargarDatos(periodo = 'semana') {
        const errorDiv = document.getElementById('chart-error');
        const canvas = document.getElementById('ventasChart');
        const fechaInicio = document.getElementById('fechaInicio').value;
        const fechaFin = document.getElementById('fechaFin').value;
        tipoGrafico = document.getElementById('tipoGrafico').value;
        try {
            errorDiv.style.display = 'none';
            let url = `/reportes/ventas-data?periodo=${periodo}`;
            if (fechaInicio) url += `&fecha_inicio=${fechaInicio}`;
            if (fechaFin) url += `&fecha_fin=${fechaFin}`;
            const response = await fetch(url);
            if (!response.ok) throw new Error('Error al cargar los datos. Estado: ' + response.status);
            const data = await response.json();
            console.log('Datos recibidos del backend:', data);
            if (!data || !data.datos || !data.estadisticas) throw new Error('Formato de datos inválido');
            if (data.datos.length === 0) {
                mostrarError('No hay datos de ventas para mostrar en el período seleccionado');
                canvas.style.display = 'none';
                return;
            }
            // Mostrar mensaje de éxito en consola
            console.log('Procesando datos para el gráfico...');
            document.getElementById('totalVentas').textContent = data.estadisticas.total_ventas || '0';
            document.getElementById('ingresosTotales').textContent = formatearMoneda(data.estadisticas.ingresos_totales);
            document.getElementById('promedioVenta').textContent = formatearMoneda(data.estadisticas.promedio_venta);
            document.getElementById('platosVendidos').textContent = data.estadisticas.platos_vendidos || '0';
            document.getElementById('ventaMaxima').textContent = data.venta_maxima ? formatearMoneda(data.venta_maxima.total) : 'Bs. 0.00';
            document.getElementById('platoMaxima').textContent = data.venta_maxima ? `(${data.venta_maxima.plato})` : '';
            document.getElementById('ventaMinima').textContent = data.venta_minima ? formatearMoneda(data.venta_minima.total) : 'Bs. 0.00';
            document.getElementById('platoMinima').textContent = data.venta_minima ? `(${data.venta_minima.plato})` : '';
            // Gráfico
            const labels = [];
            const ventasData = [];
            const cantidadData = [];
            data.datos.forEach((item, idx) => {
                console.log(`Item[${idx}]:`, item);
                labels.push(formatearFecha(item.fecha, periodo));
                ventasData.push(parseFloat(item.total_ventas) || 0);
                cantidadData.push(parseInt(item.cantidad_ventas) || 0);
            });
            console.log('Labels:', labels);
            console.log('VentasData:', ventasData);
            console.log('CantidadData:', cantidadData);
            ultimoPeriodo = periodo;
            ultimoData = data;
            ultimoLabels = labels;
            ultimoVentasData = ventasData;
            ultimoCantidadData = cantidadData;
            if (ventasChart) ventasChart.destroy();
            if (labels.length > 0) {
                const ctx = canvas.getContext('2d');
                ventasChart = new Chart(ctx, getChartConfig(tipoGrafico, labels, ventasData, cantidadData));
                canvas.style.display = 'block';
                canvas.style.opacity = '1';
                errorDiv.style.display = 'none';
            } else {
                mostrarError('No hay datos de ventas para mostrar en el período seleccionado');
                canvas.style.display = 'none';
            }
        } catch (error) {
            mostrarError('Error al cargar los datos: ' + error.message);
            canvas.style.opacity = '0.5';
            console.error('Error en cargarDatos:', error);
        }
    }
    function getChartConfig(tipo, labels, ventasData, cantidadData) {
        if (tipo === 'pie') {
            return {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Total Ventas (Bs)',
                        data: ventasData,
                        backgroundColor: [
                            '#5A2828', '#8B4513', '#A0522D', '#CD853F', '#D2B48C', '#F4A460', '#DEB887', '#FFE4C4', '#BC8F8F', '#FFDAB9', '#E9967A', '#FFB6C1'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'top' },
                        title: { display: true, text: 'Distribución de Ventas' }
                    }
                }
            };
        }
        // Barras o líneas
        return {
            type: tipo,
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Total Ventas (Bs)',
                        data: ventasData,
                        borderColor: '#5A2828',
                        backgroundColor: 'rgba(90, 40, 40, 0.7)',
                        yAxisID: 'y',
                        fill: true
                    },
                    {
                        label: 'Cantidad de Ventas',
                        data: cantidadData,
                        borderColor: '#FF9800',
                        backgroundColor: 'rgba(255, 152, 0, 0.5)',
                        yAxisID: 'y1',
                        fill: true
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
                        title: { display: true, text: 'Cantidad de Ventas' },
                        grid: { drawOnChartArea: false }
                    }
                }
            }
        };
    }
    document.querySelectorAll('[data-periodo]').forEach(button => {
        button.addEventListener('click', function() {
            document.querySelectorAll('[data-periodo]').forEach(btn => {
                btn.classList.remove('active');
            });
            this.classList.add('active');
            cargarDatos(this.dataset.periodo);
        });
    });
    document.getElementById('fechaInicio').addEventListener('change', function() {
        const periodoActivo = document.querySelector('[data-periodo].active').dataset.periodo;
        cargarDatos(periodoActivo);
    });
    document.getElementById('fechaFin').addEventListener('change', function() {
        const periodoActivo = document.querySelector('[data-periodo].active').dataset.periodo;
        cargarDatos(periodoActivo);
    });
    document.getElementById('tipoGrafico').addEventListener('change', function() {
        tipoGrafico = this.value;
        if (ultimoLabels.length > 0) {
            if (ventasChart) ventasChart.destroy();
            const canvas = document.getElementById('ventasChart');
            ventasChart = new Chart(canvas.getContext('2d'), getChartConfig(tipoGrafico, ultimoLabels, ultimoVentasData, ultimoCantidadData));
        }
    });
    cargarDatos('semana');
});
</script>
@endsection
