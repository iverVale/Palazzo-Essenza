<?php
require 'conexao.php';

if (isset($_GET['id'])) {
    $pedido_id = $_GET['id'];

    try {
        // Atualiza o pedido para finalizado
        $stmt = $pdo->prepare("UPDATE pedidos SET status = 'finalizado' WHERE id = ?");
        $stmt->execute([$pedido_id]);
        
        $mensagem_exibicao = "Pedido #$pedido_id confirmado com sucesso! Grazie!";
    } catch (Exception $e) {
        $mensagem_exibicao = "Erro ao confirmar pedido.";
    }
} else {
    die("Pedido não encontrado.");
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Confirmação de Entrega</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; display: flex; align-items: center; justify-content: center; height: 100vh; background: #f4f6f9; margin: 0; }
        .card { background: white; padding: 40px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); text-align: center; }
        h1 { color: #8b6932; }
    </style>
</head>
<body>
    <div class="card">
        <h1>✅</h1>
        <p><?= $mensagem_exibicao ?></p>
        <button onclick="window.close()" style="padding: 10px 20px; cursor: pointer; border: none; border-radius: 5px; background: #333; color: white;">Fechar Janela</button>
    </div>
</body>
</html>