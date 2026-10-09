<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="shortcut icon" href="logoP.ico" type="image/x-icon">
<title>Palazzo | Login</title>
<link rel="stylesheet" href="login.css">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
</head>
<body>

<?php if (isset($_GET['erro']) && $_GET['erro'] == 'login'): ?>
    <div id="toast-erro" style="position: fixed; top: 30px; left: 50%; transform: translateX(-50%); background: #ff4d4d; color: white; padding: 12px 25px; border-radius: 8px; font-weight: bold; box-shadow: 0 4px 15px rgba(0,0,0,0.3); z-index: 9999; transition: opacity 0.5s ease;">
        ⚠️ Email ou senha incorretos!
    </div>
    <script>
        setTimeout(() => {
            const toast = document.getElementById('toast-erro');
            if(toast){ toast.style.opacity = '0'; setTimeout(() => toast.remove(), 500); }
        }, 2000);
    </script>
<?php endif; ?>

<div class="container">
    <div class="logo">Palazzo Essenza</div>
    <div class="subtitle">Alta Cucina Italiana</div>
    
    <?php if (isset($_GET['cadastro']) && $_GET['cadastro'] == 'sucesso'): ?>
        <div style="color: #4CAF50; background: rgba(76, 175, 80, 0.1); padding: 10px; border-radius: 8px; font-size: 13px; margin-bottom: 20px;">Conta ativada com sucesso! Faça seu login.</div>
    <?php endif; ?>
    <?php if (isset($_GET['recuperacao']) && $_GET['recuperacao'] == 'sucesso'): ?>
        <div style="color: #4CAF50; background: rgba(76, 175, 80, 0.1); padding: 10px; border-radius: 8px; font-size: 13px; margin-bottom: 20px;">Senha alterada com sucesso! Faça seu login.</div>
    <?php endif; ?>

    <form action="processa_login.php" method="POST">
        <div class="input-group">
            <label>Email</label>
            <input type="email" name="email" placeholder="seu@email.com" required>
        </div>
        <div class="input-group">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <label>Senha</label>
                <a href="recuperar_senha.php" style="font-size: 12px; color: var(--link-color); text-decoration: none;">Esqueceu a senha?</a>
            </div>
            <div class="password-container">
                <input type="password" name="senha" id="senha" placeholder="••••••••" required>
                <span class="toggle-password" onclick="togglePassword('senha', this)">👁️</span>
            </div>
        </div>
        <button type="submit" class="btn-login">Entrar</button>
    </form>
    <div class="footer-text">Não tem uma conta? <a href="telacadastro.php">Cadastre-se aqui</a><br><span style="opacity: 0.7;">© 2026 Palazzo Essenza Restaurante</span></div>
</div>

<script>
    function togglePassword(inputId, icon) {
        const input = document.getElementById(inputId);
        if (input.type === "password") {
            input.type = "text";
            icon.innerText = "🙈";
        } else {
            input.type = "password";
            icon.innerText = "👁️";
        }
    }

    const body = document.body;
    const savedTheme = localStorage.getItem('palazzo_theme');
    if (savedTheme === 'dark') { body.classList.add('dark-theme'); } 
</script>
</body>
</html>