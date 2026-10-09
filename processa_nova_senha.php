<?php
session_start();
require 'conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_SESSION['email_recuperacao'])) {
        header("Location: login.php");
        exit;
    }

    $email = $_SESSION['email_recuperacao'];
    $codigo = trim($_POST['codigo']);
    $nova_senha = $_POST['nova_senha'];
    $confirma_senha = $_POST['confirma_senha'];

    if ($nova_senha !== $confirma_senha) {
        header("Location: nova_senha.php?erro=senha_diferente");
        exit;
    }

    try {
        // Verifica se o código bate
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ? AND codigo_ativacao = ?");
        $stmt->execute([$email, $codigo]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario) {
            // Criptografa a nova senha e limpa o código de ativação
            $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
            $stmtUpdate = $pdo->prepare("UPDATE usuarios SET senha = ?, codigo_ativacao = NULL WHERE id = ?");
            $stmtUpdate->execute([$senha_hash, $usuario['id']]);

            // Limpa a sessão e manda para o login
            unset($_SESSION['email_recuperacao']);
            header("Location: login.php?recuperacao=sucesso");
            exit;
        } else {
            header("Location: nova_senha.php?erro=codigo_invalido");
            exit;
        }

    } catch (PDOException $e) {
        die("Erro ao redefinir senha: " . $e->getMessage());
    }
} else {
    header("Location: login.php");
    exit;
}
?>