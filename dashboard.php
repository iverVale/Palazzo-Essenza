<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel Palazzo Essenza</title>
    <!-- Bootstrap para design rápido -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-light">

<div class="container mt-5">
    <h2 class="mb-4">Painel Administrativo</h2>
    
    <div class="row">
        <div class="col-md-6">
            <div class="card p-3">
                <h5>Produtos Mais Vendidos</h5>
                <canvas id="chartProdutos"></canvas>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card p-3">
                <h5>Pedidos na Última Semana</h5>
                <canvas id="chartVendas"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
    fetch('api_dashboard.php')
        .then(response => response.json())
        .then(data => {
            // Gráfico de Produtos
            const ctx1 = document.getElementById('chartProdutos').getContext('2d');
            new Chart(ctx1, {
                type: 'bar',
                data: {
                    labels: data.topProdutos.map(p => p.nome_produto),
                    datasets: [{ label: 'Quantidade Vendida', data: data.topProdutos.map(p => p.total), backgroundColor: '#d9534f' }]
                }
            });

            // Gráfico de Vendas
            const ctx2 = document.getElementById('chartVendas').getContext('2d');
            new Chart(ctx2, {
                type: 'line',
                data: {
                    labels: data.vendasSemana.map(v => v.data),
                    datasets: [{ label: 'Pedidos por dia', data: data.vendasSemana.map(v => v.total), borderColor: '#5cb85c', fill: true }]
                }
            });
        });
</script>

</body>
</html>