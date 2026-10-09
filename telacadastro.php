<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="logoP.ico" type="image/x-icon">
    <title>Palazzo Essenza | Cadastro</title>
    <link rel="stylesheet" href="telacadastro.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
</head>
<body>

<div class="container">
    <div class="logo">Palazzo Essenza</div>
    <div class="subtitle">Crie sua conta para realizar pedidos</div>
    
    <form action="processa_cadastro.php" method="POST">
        <div class="input-group">
            <label>Nome Completo</label>
            <input type="text" name="nome" placeholder="Seu nome" required>
        </div>
        
        <div class="input-group">
            <label>WhatsApp (com DDD)</label>
            <input type="text" name="telefone" id="telefone" placeholder="Ex: (11) 99999-9999" required>
        </div>
        
        <div class="input-group">
            <label>Email</label>
            <input type="email" name="email" placeholder="seu@email.com" required>
        </div>
        
        <div class="input-group">
            <label>Senha</label>
            <div class="password-container">
                <input type="password" name="senha" id="senha" placeholder="••••••••" required>
                <button type="button" id="toggle"></button>
            </div>
        </div>
        
        <button type="submit">Cadastrar</button>
    </form>
    
    <div class="footer-text">Já tem uma conta? <a href="login.php">Entrar</a></div>
</div>

<script>
    const inputSenha = document.getElementById("senha");
    const btnToggle = document.getElementById("toggle");

    btnToggle.addEventListener("click", () => {
        if (inputSenha.type === "password") {
            inputSenha.type = "text";
            btnToggle.classList.add("active");
        } else {
            inputSenha.type = "password";
            btnToggle.classList.remove("active");
        }
    });

    const inputTelefone = document.getElementById('telefone');
    
    if (inputTelefone) {
        inputTelefone.addEventListener('input', function (e) {
            let digits = e.target.value.replace(/\D/g, '').substring(0, 11);
            let formatted = '';

            if (digits.length > 0) {
                formatted = '(' + digits.substring(0, 2);
                if (digits.length > 2) {
                    formatted += ') ' + digits.substring(2, 7);
                    if (digits.length > 7) {
                        formatted += '-' + digits.substring(7, 11);
                    }
                }
            }
            e.target.value = formatted;
        });

        const formCadastro = document.querySelector('form');
        formCadastro.addEventListener('submit', function(e) {
            let digits = inputTelefone.value.replace(/\D/g, '');
            if (digits.length < 11) {
                e.preventDefault();
                alert("Por favor, digite um número de WhatsApp válido com DDD (11 dígitos).");
                inputTelefone.focus();
            }
        });
    }

    // Mantém a preferência de tema do usuário
    const body = document.body;
    const savedTheme = localStorage.getItem('palazzo_theme');
    if (savedTheme === 'dark') { 
        body.classList.add('dark-theme'); 
    }
</script>
</body>
</html>