<?php
session_start(); 
require 'conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $senha_digitada = $_POST['senha'];

    try {
        $stmt = $pdo->prepare("SELECT id, nome, email, senha, telefone, is_admin, foto_perfil, status_conta FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($senha_digitada, $usuario['senha'])) {
            
            if ($usuario['status_conta'] === 'pendente') {
                $_SESSION['email_ativacao'] = $usuario['email'];
                header("Location: ativar_conta.php");
                exit;
            }
            
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            $_SESSION['usuario_telefone'] = $usuario['telefone'];
            $_SESSION['is_admin'] = $usuario['is_admin']; 
            $_SESSION['usuario_email'] = $usuario['email'];
            
            if (!empty($usuario['foto_perfil'])) {
                $_SESSION['usuario_foto'] = 'data:image/jpeg;base64,' . base64_encode($usuario['foto_perfil']);
            } else {
                $_SESSION['usuario_foto'] = '';
            }
            
            header("Location: index.php");
            exit;
        } else {
            header("Location: login.php?erro=login");
            exit;
        }
    } catch (PDOException $e) {
        die("Erro no login: " . $e->getMessage());
    }
} else {
    header("Location: login.php");
    exit;
}
?>