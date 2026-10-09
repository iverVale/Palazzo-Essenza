<?php
session_start();
require 'conexao.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: telaInicial.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['acao']) && $_POST['acao'] == 'atualizar_status') {
    $pedido_id = $_POST['pedido_id'];
    $novo_status = $_POST['status'];
    
    $stmt = $pdo->prepare("UPDATE pedidos SET status = ? WHERE id = ?");
    $stmt->execute([$novo_status, $pedido_id]);

    $stmtDados = $pdo->prepare("SELECT u.telefone, u.nome, u.email FROM pedidos p JOIN usuarios u ON p.usuario_id = u.id WHERE p.id = ?");
    $stmtDados->execute([$pedido_id]);
    $cliente = $stmtDados->fetch(PDO::FETCH_ASSOC);

    if ($cliente) {
        $telefone_cliente = preg_replace('/[^0-9]/', '', $cliente['telefone']);
        if (substr($telefone_cliente, 0, 2) !== '55') {
            $telefone_cliente = '55' . $telefone_cliente;
        }
        
        $nome_simples = explode(' ', $cliente['nome'])[0];
        $email_cliente = $cliente['email'];

        $mensagem = "";
        $assunto_email = "";
        $status_texto_email = "";

        if ($novo_status == 'preparando') {
            $mensagem = "👨‍🍳 *Palazzo Essenza*\n\nOlá $nome_simples, seu pedido *#$pedido_id* já está sendo preparado com muito carinho!";
            $assunto_email = "Seu pedido #$pedido_id está sendo preparado! - Palazzo Essenza";
            $status_texto_email = "Seu pedido <b>#$pedido_id</b> já está sendo preparado com muito carinho pelo nosso chef! 👨‍🍳";
        } elseif ($novo_status == 'enviado') {
            $mensagem = "🛵 *Palazzo Essenza*\n\nÓtimas notícias! Seu pedido *#$pedido_id* saiu para entrega.\n\n*Assim que o entregador chegar, por favor, responda esta mensagem com o número 1 para confirmar o recebimento!*";
            $assunto_email = "Seu pedido #$pedido_id saiu para entrega! - Palazzo Essenza";
            $status_texto_email = "Ótimas notícias! Seu pedido <b>#$pedido_id</b> acabou de sair para entrega e logo chegará até você. 🛵";
        } elseif ($novo_status == 'finalizado') {
            $mensagem = "✅ *Palazzo Essenza*\n\nPedido *#$pedido_id* finalizado! Esperamos que tenha uma ótima experiência. Bom apetite! 🍝";
            $assunto_email = "Pedido #$pedido_id concluído com sucesso! - Palazzo Essenza";
            $status_texto_email = "Seu pedido <b>#$pedido_id</b> foi finalizado com sucesso! Esperamos que tenha uma excelente refeição. Buon appetito! 🍝🍷";
        }

        // WhatsApp (Green API)
        if ($mensagem != "") {
            $idInstance = "710722704429"; 
            $apiTokenInstance = "d53ad8de9a0d49b39ccb9a99715ec2e0de9adb576c994935b7"; 

            $url_greenapi = "https://7107.api.greenapi.com/waInstance" . $idInstance . "/sendMessage/" . $apiTokenInstance;
            $dados_greenapi = [
                "chatId" => $telefone_cliente . "@c.us",
                "message" => $mensagem
            ];

            $curl_greenapi = curl_init();
            curl_setopt_array($curl_greenapi, array(
                CURLOPT_URL => $url_greenapi,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 15,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($dados_greenapi),
                CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
            ));
            curl_exec($curl_greenapi);
            curl_close($curl_greenapi);
        }

        // E-mail (Google Apps Script)
        if (!empty($email_cliente) && !empty($assunto_email)) {
            $html_email = "
            <div style='font-family: Arial, sans-serif; color: #333; max-width: 600px; margin: 0 auto; border: 1px solid #e0e0e0; border-radius: 10px; padding: 20px;'>
                <h2 style='color: #8b6932; text-align: center;'>Palazzo Essenza</h2>
                <hr style='border: none; border-top: 1px solid #e0e0e0;'>
                <h3>Olá, $nome_simples!</h3>
                <p style='font-size: 15px; color: #555;'>Atualização sobre o status do seu pedido:</p>
                <div style='background-color: #f9f9f9; padding: 15px; border-radius: 8px; border-left: 4px solid #8b6932; margin: 20px 0;'>
                    <p style='margin: 0; font-size: 16px;'>$status_texto_email</p>
                </div>
                <p style='text-align: center; font-size: 14px; color: #888; margin-top: 25px;'>Agradecemos a preferência! Te aguardamos ansiosos. 🍷🍝</p>
            </div>";

            $url_google_script = "https://script.google.com/macros/s/AKfycbwr73-Fbps91Zejn4auUVFKt1_t_EEvkBlJKQwWlYCCQ-OZ3MT5F_T_3BkrhkZiPBqKtQ/exec";
            $dados_email = json_encode(["para" => $email_cliente, "assunto" => $assunto_email, "corpo" => $html_email]);

            $curl_email = curl_init();
            curl_setopt_array($curl_email, array(
                CURLOPT_URL => $url_google_script,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $dados_email,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
            ));
            curl_exec($curl_email);
            curl_close($curl_email);
        }
    }

    header("Location: admin.php"); 
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo | Palazzo Essenza</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,600&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <!-- Link com quebra automática de cache -->
    <link rel="stylesheet" href="admin.css?v=<?= time() ?>">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Forçando os estilos exatos da Imagem 2 diretamente na página -->
    <style>
        .seletor-container {
            background-color: #ffff !important;
            border: 1px dashed rgba(230, 201, 122, 0.4) !important;
            border-radius: 18px !important;
            padding: 28px 36px !important;
            max-width: 10000px !important;
            margin: 0 auto 30px auto !important;
            text-align: center !important;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.35) !important;
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            justify-content: center !important;
        }

        .seletor-container h2 {
            font-family: 'Playfair Display', Georgia, serif !important;
            color: #B8860B !important;
            font-size: 23px !important;
            font-weight: 600 !important;
            margin: 0 0 20px 0 !important;
            letter-spacing: 0.5px !important;
        }

        .botoes-seletor {
            display: flex !important;
            justify-content: center !important;
            align-items: center !important;
            gap: 16px !important;
            flex-wrap: wrap !important;
            width: 100% !important;
        }

        .btn-seletor {
            background-color: #1e1e1e !important;
            color: #ffffff !important;
            border: 1.5px solid #B8860B !important;
            border-radius: 12px !important;
            padding: 12px 24px !important;
            font-size: 14.5px !important;
            font-family: 'Poppins', sans-serif !important;
            font-weight: 500 !important;
            cursor: pointer !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 8px !important;
            transition: all 0.25s ease !important;
            outline: none !important;
            box-shadow: none !important;
        }

        .btn-seletor:hover {
            background-color: black !important;
            transform: translateY(-2px) !important;
        }

        .btn-seletor.ativo {
            background: black !important;
            color: white !important;
            font-weight: 600 !important;
            border-color: #B8860B !important;
            box-shadow: 0 4px 15px rgba(230, 201, 122, 0.3) !important;
        }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <header class="header">
            <h1>Painel Administrativo</h1>
            <a href="index.php" class="btn-back">← Voltar ao Cardápio</a>
        </header>

        <!-- Gráficos Lado a Lado -->
        <section class="charts-grid">
            <div class="chart-card">
                <h3>Produtos Mais Vendidos</h3>
                <div class="chart-container">
                    <canvas id="chartProdutos"></canvas>
                </div>
            </div>
            <div class="chart-card">
                <h3>Pedidos na Última Semana</h3>
                <div class="chart-container">
                    <canvas id="chartVendas"></canvas>
                </div>
            </div>
        </section>

        <!-- Container Seletor Exato da Imagem 2 -->
        <div class="seletor-container">
            <h2>O que deseja ver?</h2>
            <div class="botoes-seletor">
                <button type="button" id="btn-reservas" class="btn-seletor" onclick="mostrarAba('reservas')">
                    🍽️ Reservas
                </button>
                <button type="button" id="btn-pedidos" class="btn-seletor ativo" onclick="mostrarAba('pedidos')">
                    🛍️ Pedidos
                </button>
            </div>
        </div>

        <!-- Filtros e Busca -->
        <div class="filter-bar">
            <input 
                type="text" 
                id="searchBar" 
                class="search-input" 
                placeholder="Pesquisar por cliente, dia, mês ou ano..." 
                oninput="filtrarEOrdenar()"
            >
            <select id="sortOrder" class="select-filter" onchange="filtrarEOrdenar()">
                <option value="recentes">Mais recentes primeiro</option>
                <option value="antigos">Mais antigos primeiro</option>
            </select>
        </div>

        <!-- Aba Pedidos -->
        <div id="aba-pedidos" class="conteudo-aba" style="display: block;">
            <div class="pedidos-table-container">
                <table class="data-table" id="tabelaPedidos">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Itens</th>
                            <th>Total</th>
                            <th>Status Atual</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody id="corpoTabelaPedidos">
                        <?php
                        $stmt = $pdo->query("SELECT p.*, u.nome AS cliente_nome FROM pedidos p JOIN usuarios u ON p.usuario_id = u.id ORDER BY p.id DESC");
                        while ($pedido = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            $timestamp = isset($pedido['created_at']) ? strtotime($pedido['created_at']) : (int)$pedido['id'];
                            $dataLegivel = isset($pedido['created_at']) ? date('d/m/Y H:i', strtotime($pedido['created_at'])) : '';
                            
                            $stmtItens = $pdo->prepare("SELECT nome_produto FROM itens_pedido WHERE pedido_id = ?");
                            $stmtItens->execute([$pedido['id']]);
                            $itensNomes = [];
                            while ($item = $stmtItens->fetch(PDO::FETCH_ASSOC)) { 
                                $itensNomes[] = $item['nome_produto']; 
                            }
                            $listaItens = implode(", ", $itensNomes);
                            $busca = strtolower($pedido['id'] . ' ' . $pedido['cliente_nome'] . ' ' . $listaItens . ' ' . $dataLegivel . ' ' . str_replace('/', ' ', $dataLegivel) . ' ' . $pedido['status']);
                        ?>
                            <tr data-timestamp="<?= $timestamp ?>" data-search="<?= htmlspecialchars($busca) ?>">
                                <td><strong>#<?= $pedido['id'] ?></strong></td>
                                <td><?= htmlspecialchars($pedido['cliente_nome']) ?></td>
                                <td><small style="color: var(--subtext-color);"><?= htmlspecialchars($listaItens) ?></small></td>
                                <td><strong>R$ <?= number_format($pedido['total'], 2, ',', '.') ?></strong></td>
                                <td>
                                    <span class="status-tag <?= strtolower($pedido['status']) ?>">
                                        <?= htmlspecialchars(ucfirst($pedido['status'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="actions-cell">
                                        <form method="POST" style="display:flex; gap:6px; margin: 0;">
                                            <input type="hidden" name="acao" value="atualizar_status">
                                            <input type="hidden" name="pedido_id" value="<?= $pedido['id'] ?>">
                                            <select name="status" class="select-status">
                                                <option value="preparando" <?= $pedido['status'] == 'preparando' ? 'selected' : '' ?>>Preparando</option>
                                                <option value="enviado" <?= $pedido['status'] == 'enviado' ? 'selected' : '' ?>>Enviado</option>
                                                <option value="finalizado" <?= $pedido['status'] == 'finalizado' ? 'selected' : '' ?>>Finalizado</option>
                                            </select>
                                            <button type="submit" class="btn-update">Atualizar</button>
                                        </form>
                                        <a href="imprimir_pedido.php?id=<?= $pedido['id'] ?>" target="_blank" class="btn-print">🖨️ Imprimir</a>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <div id="semResultadosPedidos" class="no-data">Nenhum pedido encontrado.</div>
            </div>
        </div>

        <!-- Aba Reservas -->
        <div id="aba-reservas" class="conteudo-aba">
            <div class="pedidos-table-container">
                <table class="data-table" id="tabelaReservas">
                    <thead>
                        <tr>
                            <th>Data & Hora</th>
                            <th>Cliente & Contato</th>
                            <th>Qtd. Pessoas</th>
                            <th>Observações</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="corpoTabelaReservas">
                        <?php
                        $stmtRes = $pdo->query("SELECT r.*, u.nome, u.telefone FROM reservas r JOIN usuarios u ON r.usuario_id = u.id ORDER BY r.data_reserva DESC, r.hora_reserva DESC");
                        while ($reserva = $stmtRes->fetch(PDO::FETCH_ASSOC)) {
                            $st = strtolower(!empty($reserva['status']) ? $reserva['status'] : 'pendente');
                            $classe_badge = 'badge-pendente';
                            if ($st == 'cancelada') {
                                $classe_badge = 'badge-cancelada';
                            } elseif ($st == 'confirmada') {
                                $classe_badge = 'badge-confirmada';
                            }

                            $timestamp = strtotime($reserva['data_reserva'] . ' ' . $reserva['hora_reserva']);
                            $dataFmt = date('d/m/Y', strtotime($reserva['data_reserva']));
                            $horaFmt = date('H:i', strtotime($reserva['hora_reserva']));
                            $buscaRes = strtolower($reserva['nome'] . ' ' . $reserva['telefone'] . ' ' . $dataFmt . ' ' . str_replace('/', ' ', $dataFmt) . ' ' . $horaFmt . ' ' . $st . ' ' . $reserva['observacoes']);
                        ?>
                            <tr data-timestamp="<?= $timestamp ?>" data-search="<?= htmlspecialchars($buscaRes) ?>">
                                <td><strong>📅 <?= $dataFmt ?></strong> às <?= $horaFmt ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($reserva['nome']) ?></strong><br>
                                    <small style="color: var(--subtext-color);"><?= htmlspecialchars($reserva['telefone']) ?></small>
                                </td>
                                <td><?= $reserva['quantidade_pessoas'] ?> pessoas</td>
                                <td><?= !empty($reserva['observacoes']) ? htmlspecialchars($reserva['observacoes']) : '<em>Sem observações</em>' ?></td>
                                <td>
                                    <span class="badge-status <?= $classe_badge ?>">
                                        <?= strtoupper($st) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <div id="semResultadosReservas" class="no-data">Nenhuma reserva encontrada.</div>
            </div>
        </div>
    </div>

    <script>
    let abaAtual = 'pedidos';

    function mostrarAba(aba) {
        abaAtual = aba;
        document.getElementById('aba-reservas').style.display = 'none';
        document.getElementById('aba-pedidos').style.display = 'none';
        document.getElementById('btn-reservas').classList.remove('ativo');
        document.getElementById('btn-pedidos').classList.remove('ativo');

        if (aba === 'pedidos') {
            document.getElementById('aba-pedidos').style.display = 'block';
            document.getElementById('btn-pedidos').classList.add('ativo');
        } else if (aba === 'reservas') {
            document.getElementById('aba-reservas').style.display = 'block';
            document.getElementById('btn-reservas').classList.add('ativo');
        }

        filtrarEOrdenar();
    }

    function filtrarEOrdenar() {
        const query = document.getElementById('searchBar').value.trim().toLowerCase();
        const sortType = document.getElementById('sortOrder').value;

        const tbodyAtivo = (abaAtual === 'pedidos') 
            ? document.getElementById('corpoTabelaPedidos') 
            : document.getElementById('corpoTabelaReservas');
            
        const msgVazio = (abaAtual === 'pedidos') 
            ? document.getElementById('semResultadosPedidos') 
            : document.getElementById('semResultadosReservas');

        const linhas = Array.from(tbodyAtivo.querySelectorAll('tr'));
        let visiveis = 0;

        linhas.forEach(linha => {
            const dadosBusca = linha.getAttribute('data-search') || linha.innerText.toLowerCase();
            if (query === "" || dadosBusca.includes(query)) {
                linha.style.display = "";
                visiveis++;
            } else {
                linha.style.display = "none";
            }
        });

        linhas.sort((a, b) => {
            const timeA = parseFloat(a.getAttribute('data-timestamp')) || 0;
            const timeB = parseFloat(b.getAttribute('data-timestamp')) || 0;
            return (sortType === 'recentes') ? (timeB - timeA) : (timeA - timeB);
        });

        linhas.forEach(linha => tbodyAtivo.appendChild(linha));

        msgVazio.style.display = (visiveis === 0) ? 'block' : 'none';
    }

    // Chart.js
    fetch('api_dashboard.php')
        .then(response => response.json())
        .then(data => {
            if (data.error) return;

            const ctx1 = document.getElementById('chartProdutos').getContext('2d');
            new Chart(ctx1, {
                type: 'bar',
                data: {
                    labels: data.topProdutos.map(p => p.nome_produto),
                    datasets: [{
                        label: 'Qtd. Vendida',
                        data: data.topProdutos.map(p => p.total),
                        backgroundColor: '#8b6932',
                        borderRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
                }
            });

            const ctx2 = document.getElementById('chartVendas').getContext('2d');
            new Chart(ctx2, {
                type: 'line',
                data: {
                    labels: data.vendasSemana.map(v => v.data),
                    datasets: [{
                        label: 'Pedidos',
                        data: data.vendasSemana.map(v => v.total),
                        borderColor: '#8b6932',
                        backgroundColor: 'rgba(139, 105, 50, 0.08)',
                        fill: true,
                        tension: 0.35,
                        pointBackgroundColor: '#8b6932',
                        pointRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
                }
            });
        })
        .catch(err => console.error('Erro ao buscar dados do dashboard:', err));
    </script>
</body>
</html>