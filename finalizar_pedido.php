<?php
session_start();
require 'conexao.php';

header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['sucesso' => false, 'erro' => 'Você precisa fazer login para finalizar o pedido.']);
    exit;
}

$jsonRecebido = file_get_contents('php://input');
$dados = json_decode($jsonRecebido, true);

if ($dados) {
    $id_usuario = $_SESSION['usuario_id']; 
    $nome_cliente = $_SESSION['usuario_nome'];
    $telefone_cliente = $_SESSION['usuario_telefone']; 
    
    $email_cliente = isset($_SESSION['usuario_email']) ? $_SESSION['usuario_email'] : ''; 
    
    $total = $dados['total'];
    $troco_para = $dados['troco_para'];
    $itens = $dados['itens'];
    
    $tipo_entrega = $dados['tipo_entrega'];
    $endereco = $dados['endereco'];
    $forma_pagamento = $dados['forma_pagamento'];
    $observacoes = isset($dados['observacoes']) && !empty($dados['observacoes']) ? $dados['observacoes'] : 'Nenhuma';

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("INSERT INTO pedidos (usuario_id, total, status, troco_para) VALUES (?, ?, 'preparando', ?)");
        $stmt->execute([$id_usuario, $total, $troco_para]);
        $numero_pedido = $pdo->lastInsertId();

        $stmtItem = $pdo->prepare("INSERT INTO itens_pedido (pedido_id, nome_produto, detalhes, preco_unitario) VALUES (?, ?, ?, ?)");
        
        $lista_pratos = "";
        $lista_pratos_html = "";
        
        foreach ($itens as $item) {
            $detalhes_ingredientes = isset($item['detalhes']) ? $item['detalhes'] : '';
            $stmtItem->execute([$numero_pedido, $item['nome'], $detalhes_ingredientes, $item['preco']]);
            
            $preco_formatado = number_format($item['preco'], 2, ',', '.');
            
            $lista_pratos .= "▪️ 1x *" . $item['nome'] . "* (R$ " . $preco_formatado . ")\n";
            if (!empty($detalhes_ingredientes)) {
                $lista_pratos .= "   _" . $detalhes_ingredientes . "_\n";
            }
            
            $lista_pratos_html .= "<li>1x <b>" . htmlspecialchars($item['nome']) . "</b> (R$ " . $preco_formatado . ")<br>";
            if (!empty($detalhes_ingredientes)) {
                $lista_pratos_html .= "<i>" . htmlspecialchars($detalhes_ingredientes) . "</i>";
            }
            $lista_pratos_html .= "</li>";
        }

        $pdo->commit();

        $metodos = [
            'pix' => 'Pix',
            'cartao_credito' => 'Cartão de Crédito',
            'cartao_debito' => 'Cartão de Débito',
            'dinheiro' => 'Dinheiro'
        ];
        $pagamento_texto = isset($metodos[$forma_pagamento]) ? $metodos[$forma_pagamento] : $forma_pagamento;

        // --- MONTAGEM DA MENSAGEM ---
        $mensagem = "🍝 *Palazzo Essenza* 🍝\n\n";
        $mensagem .= "Olá *" . explode(' ', $nome_cliente)[0] . "*! Recebemos o seu pedido.\n";
        $mensagem .= "Pedido: *#$numero_pedido*\n";
        $mensagem .= "Status: *Sendo preparado* 👨‍🍳\n\n";
        $mensagem .= "*📋 Resumo do Pedido:*\n";
        $mensagem .= $lista_pratos . "\n";
        $mensagem .= "Valor Total: *R$ " . number_format($total, 2, ',', '.') . "*\n\n";
        $mensagem .= "*🚚 Entrega:* " . ($tipo_entrega == 'delivery' ? 'Delivery' : 'Retirar no Local') . "\n";
        
        if ($tipo_entrega == 'delivery') {
            $mensagem .= "*📍 Endereço:* $endereco\n";
        }

        $mensagem .= "*💳 Pagamento:* " . $pagamento_texto . "\n";
        
        if ($forma_pagamento == 'dinheiro' && $troco_para > $total) {
            $troco = $troco_para - $total;
            $mensagem .= "Troco para R$ " . number_format($troco_para, 2, ',', '.') . "\n";
            $mensagem .= "*(O entregador levará R$ " . number_format($troco, 2, ',', '.') . " de troco)*\n";
        }

        $mensagem .= "\nAssim que estiver a caminho, avisaremos por aqui! Grazie!";

        if ($observacoes !== 'Nenhuma') {
            $mensagem .= "*📝 Observações:* " . $observacoes . "\n\n";
        }

        // Formatação do telefone para a Green API
        $telefone_limpo = preg_replace('/[^0-9]/', '', $telefone_cliente);
        if (substr($telefone_limpo, 0, 2) !== '55') {
            $telefone_limpo = '55' . $telefone_limpo;
        }
        $chat_id = $telefone_limpo . "@c.us";

        // ==========================================
        // INTEGRAÇÃO WHATSAPP (Via Green API - Nuvem Gratuita)
        // ==========================================
        
        $idInstance = "710722704429"; 
        $apiTokenInstance = "d53ad8de9a0d49b39ccb9a99715ec2e0de9adb576c994935b7"; 

        // Utilizando o link do servidor 7107 que a Green API te forneceu
        $url_greenapi = "https://7107.api.greenapi.com/waInstance" . $idInstance . "/sendMessage/" . $apiTokenInstance;

        $dados_greenapi = [
            "chatId" => $chat_id,
            "message" => $mensagem
        ];

        $curl_greenapi = curl_init();
        curl_setopt_array($curl_greenapi, array(
            CURLOPT_URL => $url_greenapi,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($dados_greenapi),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $response_greenapi = curl_exec($curl_greenapi);
        curl_close($curl_greenapi);
        // ==========================================

        // INTEGRAÇÃO E-MAIL (Via Webhook Google HTTP)
        if (!empty($email_cliente)) {
            $assunto_email = "Confirmação do Pedido #$numero_pedido - Palazzo Essenza";
            
            $corpo_email = "
            <html>
            <head><title>Seu Pedido</title></head>
            <body style='font-family: Arial, sans-serif; color: #333; line-height: 1.6;'>
                <h2>🍝 Palazzo Essenza 🍝</h2>
                <p>Olá <b>" . htmlspecialchars(explode(' ', $nome_cliente)[0]) . "</b>! Recebemos o seu pedido.</p>
                <p>Pedido: <b>#$numero_pedido</b><br>
                Status: <b>Sendo preparado</b> 👨‍🍳</p>
                
                <h3>📋 Resumo do Pedido:</h3>
                <ul>
                    $lista_pratos_html
                </ul>
                <p>Valor Total: <b>R$ " . number_format($total, 2, ',', '.') . "</b></p>
                
                <h3>🚚 Entrega:</h3>
                <p>" . ($tipo_entrega == 'delivery' ? 'Delivery' : 'Retirar no Local') . "</p>";

            if ($tipo_entrega == 'delivery') {
                $corpo_email .= "<p>📍 Endereço: " . htmlspecialchars($endereco) . "</p>";
            }

            $corpo_email .= "<h3>💳 Pagamento:</h3><p>" . $pagamento_texto . "</p>";

            if ($forma_pagamento == 'dinheiro' && $troco_para > $total) {
                $troco = $troco_para - $total;
                $corpo_email .= "<p>Troco para R$ " . number_format($troco_para, 2, ',', '.') . "<br>
                <i>(O entregador levará R$ " . number_format($troco, 2, ',', '.') . " de troco)</i></p>";
            }

            if ($observacoes !== 'Nenhuma') {
                $corpo_email .= "<p><b>📝 Observações:</b> " . htmlspecialchars($observacoes) . "</p>";
            }

            $corpo_email .= "<p>Assim que estiver a caminho, avisaremos! Grazie!</p>
            </body>
            </html>";

          
            $url_google_script = "https://script.google.com/macros/s/AKfycbwr73-Fbps91Zejn4auUVFKt1_t_EEvkBlJKQwWlYCCQ-OZ3MT5F_T_3BkrhkZiPBqKtQ/exec";

            $dados_email = json_encode([
                "para" => $email_cliente,
                "assunto" => $assunto_email,
                "corpo" => $corpo_email
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

            $resposta_email = curl_exec($curl_email);
            curl_close($curl_email);
        }

        echo json_encode(['sucesso' => true, 'pedido' => $numero_pedido]);
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['sucesso' => false, 'erro' => 'Erro no Banco: ' . $e->getMessage()]);
        exit;
    }
} else {
    echo json_encode(['sucesso' => false, 'erro' => 'Nenhum dado recebido.']);
    exit;
}
?>