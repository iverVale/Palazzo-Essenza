<?php
session_start();
require 'conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    die("Acesso negado.");
}

if (!isset($_GET['id'])) {
    die("ID do pedido não informado.");
}

$pedido_id = $_GET['id'];


try {
    $stmt = $pdo->prepare("
        SELECT p.*, u.nome, u.telefone 
        FROM pedidos p 
        JOIN usuarios u ON p.usuario_id = u.id 
        WHERE p.id = ?
    ");
    $stmt->execute([$pedido_id]);
    $pedido = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$pedido) {
        die("Pedido não encontrado.");
    }


    $stmtItens = $pdo->prepare("SELECT * FROM itens_pedido WHERE pedido_id = ?");
    $stmtItens->execute([$pedido_id]);
    $itens = $stmtItens->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erro ao buscar pedido: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Imprimir Pedido #<?= $pedido['id'] ?></title>
    <style>
  
        * { box-sizing: border-box; }
        body { 
            font-family: 'Courier New', Courier, monospace; 
            width: 300px; 
            margin: 0 auto; 
            padding: 10px;
            color: #000; 
            background: #fff;
        }
        h2, h3, h4 { text-align: center; margin: 5px 0; }
        p { margin: 5px 0; font-size: 14px; line-height: 1.4; }
        .divider { border-top: 1px dashed #000; margin: 10px 0; }
        
        .item { display: flex; justify-content: space-between; font-weight: bold; font-size: 14px; margin-top: 5px;}
        .details { font-size: 12px; margin-left: 15px; margin-bottom: 5px; }
        
        .total-box { margin-top: 15px; font-size: 16px; font-weight: bold; text-align: right; }
        .troco { font-size: 14px; text-align: right; margin-top: 5px; }
        

        .controls { text-align: center; margin-bottom: 20px; padding: 10px; background: #f0f0f0; border-radius: 5px; }
        .controls button { padding: 8px 15px; margin: 0 5px; cursor: pointer; border: 1px solid #ccc; background: #fff; }
        
        @media print {
            .controls { display: none !important; }
            body { width: 100%; margin: 0; padding: 0; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="controls">
        <button onclick="window.print()">🖨️ Imprimir</button>
        <button onclick="window.close()">❌ Fechar</button>
    </div>

    <h2>PALAZZO ESSENZA</h2>
    <h3>Pedido #<?= $pedido['id'] ?></h3>
    <div class="divider"></div>
    
    <p>
        <strong>Cliente:</strong> <?= htmlspecialchars(explode(' ', $pedido['nome'])[0]) ?><br>
        <strong>Telefone:</strong> <?= htmlspecialchars($pedido['telefone']) ?><br>
        <strong>Impressão:</strong> <?= date('d/m/Y H:i') ?>
    </p>
    
    <div class="divider"></div>
    <h4>ITENS DO PEDIDO</h4>
    <div class="divider"></div>

    <?php foreach ($itens as $item): ?>
        <div class="item">
            <span>1x <?= htmlspecialchars($item['nome_produto']) ?></span>
            <span>R$ <?= number_format($item['preco_unitario'], 2, ',', '.') ?></span>
        </div>
        <?php if (!empty($item['detalhes'])): ?>
            <div class="details"><?= htmlspecialchars($item['detalhes']) ?></div>
        <?php endif; ?>
    <?php endforeach; ?>

    <div class="divider"></div>
    
    <div class="total-box">
        TOTAL: R$ <?= number_format($pedido['total'], 2, ',', '.') ?>
    </div>
    
    <?php if ($pedido['troco_para'] > $pedido['total']): ?>
        <div class="troco">
            Troco para: R$ <?= number_format($pedido['troco_para'], 2, ',', '.') ?><br>
            <strong>Levar Troco: R$ <?= number_format($pedido['troco_para'] - $pedido['total'], 2, ',', '.') ?></strong>
        </div>
    <?php endif; ?>

    <div class="divider"></div>
    <p style="text-align: center; font-size: 12px;">Obrigado pela preferência!</p>
    <p style="text-align: center; font-size: 12px;">---------- ✂ ----------</p>

</body>
</html>