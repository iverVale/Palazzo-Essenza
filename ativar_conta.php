<?php
session_start();
require 'conexao.php';

if (!isset($_SESSION['email_ativacao'])) {
    header("Location: login.php");
    exit;
}

$email = $_SESSION['email_ativacao'];
$erro = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $codigo_email_digitado = trim($_POST['codigo_email']);
    $codigo_wa_digitado = trim($_POST['codigo_whatsapp']);
    
    // 1. Verifica se o código do WhatsApp bate com o que está na memória
    if (isset($_SESSION['wa_ativacao']) && $_SESSION['wa_ativacao'] === $codigo_wa_digitado) {
        
        // 2. Verifica se o código do E-mail bate com o que está no Banco de Dados
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ? AND codigo_ativacao = ? AND status_conta = 'pendente'");
        $stmt->execute([$email, $codigo_email_digitado]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario) {
            // Sucesso Total! Ativa a conta e limpa tudo.
            $stmtUpdate = $pdo->prepare("UPDATE usuarios SET status_conta = 'ativo', codigo_ativacao = NULL WHERE id = ?");
            $stmtUpdate->execute([$usuario['id']]);

            unset($_SESSION['email_ativacao']);
            unset($_SESSION['wa_ativacao']);
            
            header("Location: login.php?cadastro=sucesso");
            exit;
        } else {
            $erro = "O código do E-mail está incorreto. Verifique sua caixa de entrada.";
        }
    } else {
        $erro = "O código do WhatsApp está incorreto. Verifique suas mensagens.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="assets/img/logoP.ico" type="image/x-icon">
    <title>Ativar Conta | Palazzo Essenza</title>
    <link rel="stylesheet" href="assets/css/reserva.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        .codigo-input {
            font-size: 24px;
            letter-spacing: 10px;
            text-align: center;
            font-weight: bold;
            margin-bottom: 15px;
        }
        .subtitle-email {
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            color: #666;
            margin-bottom: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">Verificação Dupla</div>
            <div class="subtitle-email">Enviamos um código para o seu <strong>E-mail</strong> e outro para o seu <strong>WhatsApp</strong>.<br>Digite ambos para prosseguir.</div>
        </div>

        <?php if ($erro): ?>
            <div class="mensagem-erro"><?= $erro ?></div>
        <?php endif; ?>

        <form action="ativar_conta.php" method="POST">
            <div class="input-group">
                <label style="text-align: center;">✉️ Código do E-mail</label>
                <input type="text" name="codigo_email" class="codigo-input" maxlength="6" required autocomplete="off" placeholder="000000">
            </div>
            
            <div class="input-group">
                <label style="text-align: center;">💬 Código do WhatsApp</label>
                <input type="text" name="codigo_whatsapp" class="codigo-input" maxlength="6" required autocomplete="off" placeholder="000000">
            </div>
            
            <button type="submit" class="btn-submit">Validar Minha Conta</button>
        </form>
    </div>
    
    <script>
        // Forçar apenas números nos inputs
        document.querySelectorAll('.codigo-input').forEach(input => {
            input.addEventListener('input', function(e) {
                this.value = this.value.replace(/\D/g, '');
            });
        });

        const body = document.body;
        const savedTheme = localStorage.getItem('palazzo_theme');
        if (savedTheme === 'dark') { body.classList.add('dark-theme'); } 
        else { body.classList.remove('dark-theme'); }
    </script>
</body>
</html>