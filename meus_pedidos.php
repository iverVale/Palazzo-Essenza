<?php
session_start();
require 'conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: loguin.php");
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancelar_reserva_id'])) {
    $id_reserva = $_POST['cancelar_reserva_id'];
    

    $stmtVerifica = $pdo->prepare("
        SELECT r.*, u.nome AS cliente_nome, u.email AS cliente_email 
        FROM reservas r 
        JOIN usuarios u ON r.usuario_id = u.id 
        WHERE r.id = ? AND r.usuario_id = ? AND r.status != 'cancelada'
    ");
    $stmtVerifica->execute([$id_reserva, $usuario_id]);
    $reserva_cancelar = $stmtVerifica->fetch(PDO::FETCH_ASSOC);

    if ($reserva_cancelar) {
        // 1. Atualiza o status para 'cancelada' no banco de dados
        $stmtCancel = $pdo->prepare("UPDATE reservas SET status = 'cancelada' WHERE id = ?");
        $stmtCancel->execute([$id_reserva]);

        $data_res = date('d/m/Y', strtotime($reserva_cancelar['data_reserva']));
        $hora_res = date('H:i', strtotime($reserva_cancelar['hora_reserva']));
        $nome_cliente = $reserva_cancelar['cliente_nome'];
        $email_cliente = $reserva_cancelar['cliente_email'];

        $url_google_script = "https://script.google.com/macros/s/AKfycbwr73-Fbps91Zejn4auUVFKt1_t_EEvkBlJKQwWlYCCQ-OZ3MT5F_T_3BkrhkZiPBqKtQ/exec";

        $stmtAdmin = $pdo->query("SELECT email FROM usuarios WHERE is_admin = 1 LIMIT 1");
        $adminInfo = $stmtAdmin->fetch(PDO::FETCH_ASSOC);
        
        if ($adminInfo) {
            $email_admin = $adminInfo['email'];
            $assunto_admin = "🚨 Reserva Cancelada - Palazzo Essenza";
            $corpo_admin = "
            <div style='font-family: Arial, sans-serif; padding: 20px; border: 1px solid #ccc; border-radius: 10px; max-width: 500px;'>
                <h2 style='color: #dc3545;'>Aviso de Cancelamento (Admin)</h2>
                <p>O cliente <b>$nome_cliente</b> acabou de cancelar uma reserva pelo site.</p>
                <div style='background: #f8f9fa; padding: 15px; border-radius: 8px;'>
                    <p><b>Data:</b> $data_res</p>
                    <p><b>Hora:</b> $hora_res</p>
                    <p><b>Quantidade:</b> {$reserva_cancelar['quantidade_pessoas']} pessoas</p>
                </div>
            </div>";

            $dados_admin = json_encode(["para" => $email_admin, "assunto" => $assunto_admin, "corpo" => $corpo_admin]);
            
            $ch = curl_init($url_google_script);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $dados_admin,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            ]);
            curl_exec($ch);
            curl_close($ch);
        }

     
        if (!empty($email_cliente)) {
            $assunto_cliente = "Confirmação de Cancelamento de Reserva - Palazzo Essenza";
            $corpo_cliente = "
            <div style='font-family: Arial, sans-serif; padding: 20px; border: 1px solid #e0e0e0; border-radius: 10px; max-width: 500px; margin: 0 auto;'>
                <h2 style='color: #8b6932; text-align: center;'>Palazzo Essenza</h2>
                <hr style='border: none; border-top: 1px solid #e0e0e0;'>
                <p>Olá, <b>$nome_cliente</b>.</p>
                <p>Confirmamos que a sua reserva de mesa foi <b>cancelada com sucesso</b> conforme solicitado.</p>
                <div style='background: #f9f9f9; padding: 15px; border-radius: 8px; border-left: 4px solid #dc3545;'>
                    <p style='margin: 5px 0;'><b>Data:</b> $data_res</p>
                    <p style='margin: 5px 0;'><b>Hora:</b> $hora_res</p>
                    <p style='margin: 5px 0;'><b>Pessoas:</b> {$reserva_cancelar['quantidade_pessoas']} pessoas</p>
                </div>
                <p style='font-size: 14px; color: #777; text-align: center; margin-top: 20px;'>Caso mude de ideia, sinta-se à vontade para realizar uma nova reserva em nosso site!</p>
            </div>";

            $dados_cliente = json_encode(["para" => $email_cliente, "assunto" => $assunto_cliente, "corpo" => $corpo_cliente]);

            $ch2 = curl_init($url_google_script);
            curl_setopt_array($ch2, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $dados_cliente,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            ]);
            curl_exec($ch2);
            curl_close($ch2);
        }

        header("Location: meus_pedidos.php?sucesso=cancelado");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <link rel="shortcut icon" href="assets/img/logoP.ico" type="image/x-icon">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Palazzo Essenza | Meus Pedidos e Reservas</title>
    <link rel="stylesheet" href="assets/css/index.css">
    <link rel="stylesheet" href="assets/css/meus_pedidos.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
    
    <style>
        .seletor-container {
            text-align: center; margin: 20px auto 40px; padding: 30px 20px;
            background: #ffffff; border-radius: 16px; border: 2px dashed var(--btn-outline-border);
            max-width: 600px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08); 
        }
        body.dark-theme .seletor-container {
            background: #1c1c1c; border-color: rgba(255, 255, 255, 0.15); box-shadow: 0 10px 30px rgba(0, 0, 0, 0.6);
        }
        .seletor-container h2 { font-family: 'Playfair Display', serif; color: var(--title-color); margin-top: 0; margin-bottom: 25px; font-size: 26px; }
        .botoes-seletor { display: flex; justify-content: center; gap: 20px; flex-wrap: wrap; }
        .btn-seletor {
            padding: 14px 30px; font-size: 16px; font-family: 'Poppins', sans-serif;
            font-weight: 500; border: 2px solid var(--btn-outline-border); background: transparent;
            color: var(--text-color); border-radius: 12px; cursor: pointer; transition: all 0.3s ease;
            display: flex; align-items: center; gap: 10px;
        }
        .btn-seletor:hover { background: rgba(139, 105, 50, 0.1); transform: translateY(-2px); }
        .btn-seletor.ativo {
            background: var(--btn-primary-bg); color: #ffffff; border-color: var(--btn-primary-bg);
            box-shadow: 0 6px 20px rgba(139, 105, 50, 0.35);
        }
        .conteudo-aba { display: none; animation: fadeIn 0.4s ease-out forwards; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        
        .badge-status { padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: bold; text-transform: uppercase; }
        .badge-pendente { background: rgba(255, 193, 7, 0.15); color: #b58500; }
        body.dark-theme .badge-pendente { background: rgba(255, 193, 7, 0.15); color: #ffc107; }
        .badge-cancelada { background: rgba(220, 53, 69, 0.15); color: #dc3545; }
        body.dark-theme .badge-cancelada { background: rgba(220, 53, 69, 0.15); color: #ff6b7a; }
        .badge-confirmada { background: rgba(40, 167, 69, 0.15); color: #28a745; }
        body.dark-theme .badge-confirmada { background: rgba(40, 167, 69, 0.15); color: #51cf66; }
        
        .btn-cancelar-res {
            background: transparent; border: 1px solid #dc3545; color: #dc3545; padding: 6px 14px; 
            border-radius: 8px; cursor: pointer; font-family: 'Poppins', sans-serif; font-size: 13px; transition: 0.3s;
        }
        .btn-cancelar-res:hover { background: #dc3545; color: white; }
    </style>
</head>
<body>

    <div class="top-controls">
        <button class="btn-back" onclick="voltarPagina()">
            <span class="arrow">←</span>
            <span class="text">Voltar</span>
        </button>

        <div class="theme-switch">
            <input type="checkbox" id="theme-checkbox" />
            <label for="theme-checkbox">
                <div></div>
                <span><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path fill-rule="evenodd" d="M9.528 1.718a.75.75 0 01.162.819A8.97 8.97 0 009 6a9 9 0 009 9 8.97 8.97 0 003.463-.69.75.75 0 01.981.98 10.503 10.503 0 01-9.694 6.46c-5.799 0-10.5-4.701-10.5-10.5 0-4.368 2.667-8.112 6.46-9.694a.75.75 0 01.818.162z" clip-rule="evenodd"></path></svg></span>
                <span><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.25a.75.75 0 01.75.75v2.25a.75.75 0 01-1.5 0V3a.75.75 0 01.75-.75zM7.5 12a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0zM18.894 6.166a.75.75 0 00-1.06-1.06l-1.591 1.59a.75.75 0 101.06 1.061l1.591-1.59zM21.75 12a.75.75 0 01-.75.75h-2.25a.75.75 0 010-1.5H21a.75.75 0 01.75.75zM17.834 18.894a.75.75 0 001.06-1.06l-1.59-1.591a.75.75 0 10-1.061 1.06l1.59 1.591zM12 18a.75.75 0 01.75.75V21a.75.75 0 01-1.5 0v-2.25A.75.75 0 0112 18zM7.758 17.303a.75.75 0 00-1.061-1.06l-1.591 1.59a.75.75 0 001.06 1.061l1.591-1.59zM6 12a.75.75 0 01-.75.75H3a.75.75 0 010-1.5h2.25A.75.75 0 016 12zM6.697 7.757a.75.75 0 001.06-1.06l-1.59-1.591a.75.75 0 00-1.061 1.06l1.59 1.591z"></path></svg></span>
            </label>
        </div>
    </div>

    <div class="seletor-container">
        <h2>O que deseja ver?</h2>
        <div class="botoes-seletor">
            <button id="btn-reservas" class="btn-seletor" onclick="mostrarAba('reservas')">🍽️ Minhas Reservas</button>
            <button id="btn-pedidos" class="btn-seletor" onclick="mostrarAba('pedidos')">🛍️ Meus Pedidos</button>
        </div>
    </div>

    <div id="aba-reservas" class="conteudo-aba">
        <div class="pedidos-container" style="margin-bottom: 40px;">
            <h2 style="text-align: center; margin-bottom: 30px;">Minhas Reservas</h2>
            
            <?php
            $stmtRes = $pdo->prepare("SELECT * FROM reservas WHERE usuario_id = ? ORDER BY data_reserva DESC, hora_reserva DESC");
            $stmtRes->execute([$usuario_id]);
            $reservas = $stmtRes->fetchAll(PDO::FETCH_ASSOC);

            if (count($reservas) > 0) {
                foreach ($reservas as $reserva) {
                    $dataFormatada = date('d/m/Y', strtotime($reserva['data_reserva']));
                    $horaFormatada = date('H:i', strtotime($reserva['hora_reserva']));
                    $status_reserva = strtolower(isset($reserva['status']) ? $reserva['status'] : 'pendente');
                    
                    if ($status_reserva == 'cancelada') {
                        $classe_badge = 'badge-cancelada';
                    } elseif ($status_reserva == 'confirmada') {
                        $classe_badge = 'badge-confirmada';
                    } else {
                        $classe_badge = 'badge-pendente';
                    }
                    
                    echo "<div class='pedido-card'>";
                    echo "<div class='pedido-header'>";
                    echo "<strong>📅 " . $dataFormatada . " às " . $horaFormatada . "</strong>";
                    echo "<span class='badge-status $classe_badge'>" . ucfirst($status_reserva) . "</span>";
                    echo "</div>";
                    
                    echo "<div class='item-lista'><strong>Pessoas:</strong> " . $reserva['quantidade_pessoas'] . "</div>";
                    
                    if (!empty($reserva['observacoes'])) {
                        echo "<div class='item-lista' style='margin-top: 5px;'>";
                        echo "<strong>Observações:</strong> " . htmlspecialchars($reserva['observacoes']);
                        echo "</div>";
                    }

                    if ($status_reserva != 'cancelada') {
                        echo "<form method='POST' style='margin-top: 15px; text-align: right;' onsubmit=\"return confirm('Tem certeza que deseja cancelar esta reserva?');\">";
                        echo "<input type='hidden' name='cancelar_reserva_id' value='" . $reserva['id'] . "'>";
                        echo "<button type='submit' class='btn-cancelar-res'>Cancelar Reserva</button>";
                        echo "</form>";
                    }

                    echo "</div>";
                }
            } else {
                echo "<p style='color: var(--subtext-color, #888); font-size: 15px; text-align: center;'>Você ainda não tem mesas reservadas.</p>";
            }
            ?>
        </div>
    </div>

    <div id="aba-pedidos" class="conteudo-aba">
        <div class="pedidos-container" style="margin-bottom: 40px;">
            <h2 style="text-align: center; margin-bottom: 30px;">Meus Pedidos</h2>
            
            <?php
            $stmt = $pdo->prepare("SELECT * FROM pedidos WHERE usuario_id = ? ORDER BY id DESC");
            $stmt->execute([$usuario_id]);
            $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($pedidos) > 0) {
                foreach ($pedidos as $pedido) {
                    echo "<div class='pedido-card'>";
                    echo "<div class='pedido-header'>";
                    echo "<strong>Pedido #" . $pedido['id'] . "</strong>";
                    echo "<span class='status " . strtolower($pedido['status']) . "'>" . ucfirst($pedido['status']) . "</span>";
                    echo "</div>";
                    
                    $stmtItens = $pdo->prepare("SELECT * FROM itens_pedido WHERE pedido_id = ?");
                    $stmtItens->execute([$pedido['id']]);
                    $itens = $stmtItens->fetchAll(PDO::FETCH_ASSOC);
                    
                    foreach ($itens as $item) {
                        echo "<div class='item-lista'>• 1x " . htmlspecialchars($item['nome_produto']);
                        if (!empty($item['detalhes'])) {
                            echo " <small>(" . htmlspecialchars($item['detalhes']) . ")</small>";
                        }
                        echo "</div>";
                    }
                    
                    echo "<div style='margin-top: 15px; font-weight: bold; font-size: 16px; color: var(--title-color);'>Total: R$ " . number_format($pedido['total'], 2, ',', '.') . "</div>";
                    echo "</div>";
                }
            } else {
                echo "<p style='color: var(--subtext-color, #888); font-size: 15px; text-align: center;'>Você ainda não fez nenhum pedido no cardápio.</p>";
            }
            ?>
        </div>
    </div>

    <script>
        function mostrarAba(aba) {
            document.getElementById('aba-reservas').style.display = 'none';
            document.getElementById('aba-pedidos').style.display = 'none';
            document.getElementById('btn-reservas').classList.remove('ativo');
            document.getElementById('btn-pedidos').classList.remove('ativo');

            if (aba === 'reservas') {
                document.getElementById('aba-reservas').style.display = 'block';
                document.getElementById('btn-reservas').classList.add('ativo');
            } else if (aba === 'pedidos') {
                document.getElementById('aba-pedidos').style.display = 'block';
                document.getElementById('btn-pedidos').classList.add('ativo');
            }
        }

        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('sucesso')) {
            mostrarAba('reservas');
        }

        function voltarPagina() { window.location.href = "index.php"; }

        const themeSwitch = document.getElementById('theme-checkbox');
        const body = document.body;
        const savedTheme = localStorage.getItem('palazzo_theme');

        if (savedTheme === 'dark') {
            body.classList.add('dark-theme');
            themeSwitch.checked = true; 
        } else {
            body.classList.remove('dark-theme');
            themeSwitch.checked = false;
        }

        themeSwitch.addEventListener('change', () => {
            if (themeSwitch.checked) {
                body.classList.add('dark-theme');
                localStorage.setItem('palazzo_theme', 'dark');
            } else {
                body.classList.remove('dark-theme');
                localStorage.setItem('palazzo_theme', 'light');
            }
        });
    </script>
</body>
</html>