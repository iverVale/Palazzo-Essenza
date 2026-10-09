<?php
session_start();
require 'conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_usuario = $_SESSION['usuario_id'];
    $data = $_POST['data_reserva'];
    $hora = $_POST['hora_reserva'];
    $qtd_pessoas = (int) $_POST['qtd_pessoas'];
    $observacoes = $_POST['observacoes'];
    $metodo_pagamento = $_POST['metodo_pagamento'];


    $data_hoje = date('Y-m-d');
    $ultimo_dia_mes = date('Y-m-t'); 

   
    $horario_valido = ($hora >= '18:00' && $hora <= '23:59') || ($hora >= '00:00' && $hora <= '00:30');

    if ($data < $data_hoje || $data > $ultimo_dia_mes) {
        $mensagem = "<div class='mensagem-erro'>Só é possível reservar datas dentro do mês atual.</div>";
    } elseif (!$horario_valido) {
        $mensagem = "<div class='mensagem-erro'>O horário selecionado é inválido. Nosso funcionamento é das 18:00 às 00:30.</div>";
    } elseif ($qtd_pessoas < 1 || $qtd_pessoas > 10) { 
        $mensagem = "<div class='mensagem-erro'>O limite máximo é de 10 pessoas por mesa.</div>";
    } elseif (empty($metodo_pagamento)) {
        $mensagem = "<div class='mensagem-erro'>Selecione um método de pagamento válido.</div>";
    } else {
        try {
          
            $stmt = $pdo->prepare("INSERT INTO reservas (usuario_id, data_reserva, hora_reserva, quantidade_pessoas, metodo_pagamento, observacoes) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$id_usuario, $data, $hora, $qtd_pessoas, $metodo_pagamento, $observacoes]);
            
            $mensagem = "<div class='mensagem-sucesso'>Reserva e pagamento confirmados com sucesso! Aguardamos você.</div>";

            $nome_cliente = explode(' ', $_SESSION['usuario_nome'])[0];
            $data_formatada = date('d/m/Y', strtotime($data));

            $telefone_cliente = preg_replace('/[^0-9]/', '', $_SESSION['usuario_telefone']);
            if (substr($telefone_cliente, 0, 2) !== '55') {
                $telefone_cliente = '55' . $telefone_cliente;
            }

            // INTEGRAÇÃO WHATSAPP
            $mensagem_whats = "🗓️ *Palazzo Essenza* 🗓️\n\n";
            $mensagem_whats .= "Olá *$nome_cliente*! Sua reserva foi confirmada. 🎉\n\n";
            $mensagem_whats .= "*📅 Data:* $data_formatada\n";
            $mensagem_whats .= "*⏰ Horário:* $hora\n";
            $mensagem_whats .= "*👥 Pessoas:* $qtd_pessoas\n";
            $mensagem_whats .= "*💳 Pagamento:* $metodo_pagamento\n";
            if (!empty($observacoes)) {
                $mensagem_whats .= "*📝 Obs:* $observacoes\n";
            }
            $mensagem_whats .= "\nTe esperamos ansiosos! 🍷🍝";

            $url_api = "https://api.periskope.app/v1/message/send";
            $token = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZCIgOiAiN2IyNDE2YjQtOGRiYS00NmUzLTkzZDYtNWVhY2Y2MjBiMmI3IiwgInJvbGUiIDogImFwaSIsICJ0eXBlIiA6ICJhcGkiLCAibmFtZSIgOiAiYml6ZXJyYSIsICJleHAiIDogMjA5MTExNDczOSwgImlhdCIgOiAxNzc1NDk1NTM5LCAic3ViIiA6ICJiYmE4NGFjNS1kNDRiLTQ2MjQtYWJlMy1iZmYxMmYzODAwNzgiLCAiaXNzIiA6ICJwZXJpc2tvcGUuYXBwIiwgIm1ldGFkYXRhIiA6IHsic2NvcGVzIjogWyI1NTE3OTkxODYzMDE0QGMudXMiXX19.CTm-dPUcpHRcyK-hfrgn3HzJpVNlGhKnPMZpHswAMFU"; 
            $telefone_origem = "5517991863014";

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $url_api,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode(array(
                    "chat_id" => $telefone_cliente . "@c.us",
                    "message" => $mensagem_whats
                )),
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer ' . $token,
                    'x-phone: ' . $telefone_origem,
                    'Content-Type: application/json'
                ),
            ));
            curl_exec($curl);
            curl_close($curl);

            // INTEGRAÇÃO E-MAIL
            $stmtEmail = $pdo->prepare("SELECT email FROM usuarios WHERE id = ?");
            $stmtEmail->execute([$id_usuario]);
            $dados_cliente = $stmtEmail->fetch(PDO::FETCH_ASSOC);
            $email_cliente = $dados_cliente['email'];

            $assunto_email = "Confirmação de Reserva - Palazzo Essenza";
            
            $html_email = "
                <div style='font-family: Arial, sans-serif; color: #333; max-width: 600px; margin: 0 auto; border: 1px solid #e0e0e0; border-radius: 10px; padding: 20px;'>
                    <h2 style='color: #8b6932; text-align: center;'>Palazzo Essenza</h2>
                    <hr style='border: none; border-top: 1px solid #e0e0e0;'>
                    <h3>Olá, $nome_cliente!</h3>
                    <p>Sua reserva foi confirmada com sucesso em nosso restaurante. 🎉</p>
                    <div style='background-color: #f9f9f9; padding: 15px; border-radius: 8px;'>
                        <ul style='list-style: none; padding: 0;'>
                            <li style='margin-bottom: 10px;'><strong>📅 Data:</strong> $data_formatada</li>
                            <li style='margin-bottom: 10px;'><strong>⏰ Horário:</strong> $hora</li>
                            <li style='margin-bottom: 10px;'><strong>👥 Pessoas:</strong> $qtd_pessoas</li>
                            <li style='margin-bottom: 10px;'><strong>💳 Pagamento:</strong> $metodo_pagamento</li>
                            <li><strong>📝 Observações:</strong> ".(!empty($observacoes) ? $observacoes : "Nenhuma")."</li>
                        </ul>
                    </div>
                    <p style='text-align: center; font-size: 16px; margin-top: 20px;'>Te esperamos ansiosos! 🍷🍝</p>
                </div>
            ";

            $url_google_script = "https://script.google.com/macros/s/AKfycbwr73-Fbps91Zejn4auUVFKt1_t_EEvkBlJKQwWlYCCQ-OZ3MT5F_T_3BkrhkZiPBqKtQ/exec";

            $dados_email = json_encode([
                "para" => $email_cliente,
                "assunto" => $assunto_email,
                "corpo" => $html_email
            ]);

            $curl_email = curl_init();
            curl_setopt_array($curl_email, array(
                CURLOPT_URL => $url_google_script,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $dados_email,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
            ));
            curl_exec($curl_email);
            curl_close($curl_email);

        } catch (PDOException $e) {
            $mensagem = "<div class='mensagem-erro'>Erro ao realizar reserva: " . $e->getMessage() . "</div>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="shortcut icon" href="logoP.ico" type="image/x-icon">
<title>Palazzo Essenza | Reservas</title>
<link rel="stylesheet" href="reserva.css">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
<style>
    .modal-overlay {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0, 0, 0, 0.7); display: flex; align-items: center; justify-content: center;
        z-index: 1000; backdrop-filter: blur(3px);
    }
    .modal-content {
        background: var(--bg-color, #fff); color: var(--text-color, #333);
        padding: 30px; border-radius: 12px; max-width: 400px; text-align: center;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    }
    body.dark-theme .modal-content { background: #1e1e1e; color: #f1f1f1; border: 1px solid #333; }
    .modal-content h3 { font-family: 'Playfair Display', serif; color: #8b6932; margin-bottom: 15px; font-size: 24px; }
    .modal-content p { font-family: 'Poppins', sans-serif; font-size: 15px; margin-bottom: 15px; line-height: 1.5; }
    .modal-buttons { display: flex; gap: 10px; margin-top: 20px; justify-content: center; }
    .btn-cancelar, .btn-confirmar { padding: 10px 20px; border: none; border-radius: 6px; font-family: 'Poppins', sans-serif; font-weight: 500; cursor: pointer; transition: 0.3s; }
    .btn-cancelar { background: #ccc; color: #333; }
    .btn-cancelar:hover { background: #bbb; }
    .btn-confirmar { background: #8b6932; color: #fff; }
    .btn-confirmar:hover { background: #705527; }
</style>
</head>
<body>

<div class="container">
    <div class="header">
        <div class="logo">Palazzo Essenza</div>
        <div class="subtitle">Reserve sua mesa conosco</div>
    </div>
    <?= $mensagem ?>
    
    <form id="formReserva" action="reserva.php" method="POST">
        <div class="row">
            <div class="input-group">
                <label>Data</label>
                <!-- Limitado: do dia atual (min) até o último dia do mês atual (max) -->
                <input type="date" name="data_reserva" required min="<?= date('Y-m-d') ?>" max="<?= date('Y-m-t') ?>">
            </div>
            <div class="input-group">
                <label>Horário</label>
                <input type="time" name="hora_reserva" required>
            </div>
        </div>
        
        <div class="row">
            <div class="input-group">
                <label>Número de Pessoas</label>
                <input type="number" name="qtd_pessoas" min="1" max="10" placeholder="Ex: 4" required>
            </div>
            <div class="input-group">
                <!-- Novo select de Pagamento -->
                <label>Forma de Pagamento (Garantia de Mesa)</label>
                <select name="metodo_pagamento" required>
                    <option value="" disabled selected>Selecione</option>
                    <option value="PIX">PIX</option>
                    <option value="Cartão de Crédito">Cartão de Crédito</option>
                </select>
            </div>
        </div>

        <div class="input-group">
            <label>Observações (Opcional)</label>
            <textarea name="observacoes" rows="3" placeholder="Aniversário, pedido de casamento, alergias..."></textarea>
        </div>
        <button type="submit" class="btn-submit">Avançar para Pagamento</button>
    </form>
    <a href="index.php" class="voltar">← Voltar para a Tela Inicial</a>
</div>

<div id="modalConfirmacao" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        <h3>Atenção ao Horário ⏰</h3>
        <p>Nosso horário de funcionamento é das <strong>18:00 às 00:30</strong>.</p>
        <p>Com a reserva às <strong id="modalHora"></strong>, você terá apenas <strong id="modalTempo" style="color: #d9534f;"></strong> de permanência no restaurante antes do encerramento.</p>
        <p>Deseja realmente confirmar esta reserva e ir para o pagamento?</p>
        <div class="modal-buttons">
            <button type="button" class="btn-cancelar" onclick="fecharModal()">Cancelar</button>
            <button type="button" class="btn-confirmar" onclick="enviarFormulario()">Sim, Confirmar</button>
        </div>
    </div>
</div>

<script>
    const body = document.body;
    const savedTheme = localStorage.getItem('palazzo_theme');
    if (savedTheme === 'dark') { body.classList.add('dark-theme'); } 
    else { body.classList.remove('dark-theme'); }

    const form = document.getElementById('formReserva');
    const modal = document.getElementById('modalConfirmacao');
    
    form.addEventListener('submit', function(event) {
        event.preventDefault(); 
        
        const horaInput = document.querySelector('input[name="hora_reserva"]').value;
        const [horas, minutos] = horaInput.split(':').map(Number);
        
        if ((horas >= 18 && horas <= 23) || (horas === 0 && minutos <= 30)) {
            let minutosRestantes = 0;
            
            if (horas >= 18) {
                minutosRestantes = ((23 - horas) * 60) + (60 - minutos) + 30;
            } else {
                minutosRestantes = 30 - minutos;
            }
            
            let horasRestantes = Math.floor(minutosRestantes / 60);
            let minsRestantes = minutosRestantes % 60;
            
            let textoTempo = '';
            if (horasRestantes > 0) textoTempo += `${horasRestantes} hora(s) `;
            if (minsRestantes > 0) {
                textoTempo += `${horasRestantes > 0 ? 'e ' : ''}${minsRestantes} minuto(s)`;
            }
            
            if (minutosRestantes === 0) {
                textoTempo = "0 minutos";
            }

            document.getElementById('modalHora').innerText = horaInput;
            document.getElementById('modalTempo').innerText = textoTempo.trim();
            modal.style.display = 'flex';
        } else {
            HTMLFormElement.prototype.submit.call(form);
        }
    });

    function fecharModal() {
        modal.style.display = 'none';
    }

    function enviarFormulario() {
        HTMLFormElement.prototype.submit.call(form);
    }
</script>
</body>
</html>