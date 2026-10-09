<?php
session_start();
if (!isset($_SESSION['email_recuperacao'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="shortcut icon" href="assets/img/logoP.ico" type="image/x-icon">
<title>Palazzo | Nova Senha</title>
<link rel="stylesheet" href="assets/css/index.css">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
<style>
    body { height: 100vh; background: var(--bg-gradient), url('https://images.unsplash.com/photo-1600891964599-f61ba0e24092') no-repeat center center/cover; display: flex; align-items: center; justify-content: center; font-family: 'Poppins', sans-serif;}
    .container { background: var(--box-bg); backdrop-filter: blur(15px); padding: 40px; border-radius: 15px; width: 380px; text-align: center; border: 1px solid var(--box-border); box-shadow: 0 15px 35px rgba(0,0,0,0.4); }
    .logo { font-family: 'Playfair Display', serif; font-size: 32px; color: var(--title-color); margin-bottom: 5px; }
    .subtitle { font-size: 13px; color: var(--subtitle-color); margin-bottom: 25px; line-height: 1.5; }
    .input-group { margin-bottom: 20px; text-align: left; }
    .input-group label { font-size: 13px; color: var(--label-color); font-weight: 500; }
    .input-group input { width: 100%; padding: 12px; margin-top: 5px; border-radius: 8px; border: none; outline: none; font-size: 14px; background-color: var(--input-bg); color: var(--input-text); transition: 0.3s; font-family: 'Poppins', sans-serif;}
    .input-group input.codigo-input { font-size: 20px; letter-spacing: 8px; text-align: center; font-weight: bold; }
    .input-group input:focus { border: 1px solid var(--link-color); }
    button { width: 100%; padding: 12px; border: none; border-radius: 8px; background: var(--btn-bg); color: var(--btn-text); font-weight: 600; font-size: 15px; cursor: pointer; transition: 0.3s; margin-top: 10px; }
    button:hover { transform: translateY(-2px); opacity: 0.9; }
    .aviso-erro { color: #ff4d4d; background: rgba(255, 77, 77, 0.1); padding: 10px; border-radius: 8px; font-size: 13px; margin-bottom: 20px; font-weight: 500;}
</style>
</head>
<body>

<div class="container">
    <div class="logo">Criar Nova Senha</div>
    <div class="subtitle">Digite o código de 6 dígitos que enviamos para você e crie sua nova senha.</div>
    
    <?php if (isset($_GET['erro']) && $_GET['erro'] == 'codigo_invalido'): ?>
        <div class="aviso-erro">⚠️ O código está incorreto. Tente novamente.</div>
    <?php endif; ?>
    <?php if (isset($_GET['erro']) && $_GET['erro'] == 'senha_diferente'): ?>
        <div class="aviso-erro">⚠️ As senhas não conferem. Digite senhas iguais.</div>
    <?php endif; ?>

    <form action="processa_nova_senha.php" method="POST">
        <div class="input-group">
            <label style="text-align: center;">Código de Verificação</label>
            <input type="text" name="codigo" class="codigo-input" placeholder="000000" maxlength="6" required autocomplete="off">
        </div>
        
        <div class="input-group">
            <label>Nova Senha</label>
            <input type="password" name="nova_senha" placeholder="••••••••" required>
        </div>
        
        <div class="input-group">
            <label>Confirmar Nova Senha</label>
            <input type="password" name="confirma_senha" placeholder="••••••••" required>
        </div>
        
        <button type="submit">Redefinir Senha</button>
    </form>
</div>

<script>
    document.querySelector('.codigo-input').addEventListener('input', function(e) {
        this.value = this.value.replace(/\D/g, '');
    });

    const body = document.body;
    const savedTheme = localStorage.getItem('palazzo_theme');
    if (savedTheme === 'dark') { body.classList.add('dark-theme'); } 
</script>
</body>
</html>