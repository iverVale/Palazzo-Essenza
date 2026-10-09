<?php
session_start();

// Verifica se o usuário é administrador
if (!isset($_SESSION['is_admin'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Acesso negado']);
    exit;
}

header('Content-Type: application/json');
require_once 'conexao.php'; // Certifique-se de que este é o nome correto do seu arquivo de conexão

try {
    // Garante que o PDO vai avisar sobre qualquer erro no banco de dados
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. Busca os 5 produtos mais vendidos
    $stmt1 = $pdo->query("SELECT nome_produto, COUNT(*) as total 
                          FROM itens_pedido 
                          GROUP BY nome_produto 
                          ORDER BY total DESC LIMIT 5");
    $topProdutos = $stmt1->fetchAll(PDO::FETCH_ASSOC);

    // 2. Busca as vendas dos últimos 7 dias (CORRIGIDO: usando 'criado_em')
    $stmt2 = $pdo->query("SELECT DATE(criado_em) as data, COUNT(*) as total 
                          FROM pedidos 
                          WHERE criado_em >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                          GROUP BY DATE(criado_em)");
    $vendasSemana = $stmt2->fetchAll(PDO::FETCH_ASSOC);

    // Retorna os dados em formato JSON para o JavaScript
    echo json_encode([
        'topProdutos' => $topProdutos,
        'vendasSemana' => $vendasSemana
    ]);

} catch (Exception $e) {
    // Caso dê algum erro, retorna a mensagem exata para facilitar encontrar o problema
    echo json_encode(['error' => 'Erro no banco: ' . $e->getMessage()]);
}
?>