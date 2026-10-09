<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="shortcut icon" href="assets/img/logoP.ico" type="image/x-icon">
<title>Palazzo | Recuperar Senha</title>
<link rel="stylesheet" href="assets/css/index.css">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
<style>
    body { height: 100vh; background: var(--bg-gradient), url('https://images.unsplash.com/photo-1600891964599-f61ba0e24092') no-repeat center center/cover; display: flex; align-items: center; justify-content: center; font-family: 'Poppins', sans-serif;}
    .container { background: var(--box-bg); backdrop-filter: blur(15px); padding: 40px; border-radius: 15px; width: 380px; text-align: center; border: 1px solid var(--box-border); box-shadow: 0 15px 35px rgba(0,0,0,0.4); }
    .logo { font-family: 'Playfair Display', serif; font-size: 32px; color: var(--title-color); margin-bottom: 5px; }
    .subtitle { font-size: 13px; color: var(--subtitle-color); margin-bottom: 25px; line-height: 1.5; }
    .input-group { margin-bottom: 20px; text-align: left; }
    .input-group label { font-size: 13px; color: var(--label-color); font-weight: 500; }
    
    .input-group input, .input-group select { width: 100%; padding: 12px; margin-top: 5px; border-radius: 8px; border: none; outline: none; font-size: 14px; background-color: var(--input-bg); color: var(--input-text); transition: 0.3s; font-family: 'Poppins', sans-serif;}
    .input-group input:focus, .input-group select:focus { border: 1px solid var(--link-color); }
    
    /* CORREÇÃO DO CONFLITO DE CORES NAS OPÇÕES */
    .input-group select option { background-color: #ffffff; color: #333333; }
    body.dark-theme .input-group select option { background-color: #2c2c2c; color: #ffffff; }

    button { width: 100%; padding: 12px; border: none; border-radius: 8px; background: var(--btn-bg); color: var(--btn-text); font-weight: 600; font-size: 15px; cursor: pointer; transition: 0.3s; margin-top: 10px; }
    button:hover { transform: translateY(-2px); opacity: 0.9; }
    .aviso-erro { color: #ff4d4d; background: rgba(255, 77, 77, 0.1); padding: 10px; border-radius: 8px; font-size: 13px; margin-bottom: 20px; font-weight: 500;}
</style>
</head>
<body>

<div class="container">
    <div class="logo">Recuperar Senha</div>
    <div class="subtitle">Digite seu e-mail cadastrado e escolha como deseja receber o código de verificação.</div>
    
    <?php if (isset($_GET['erro']) && $_GET['erro'] == 'nao_encontrado'): ?>
        <div class="aviso-erro">⚠️ E-mail não encontrado ou conta inativa.</div>
    <?php endif; ?>

    <form action="processa_recuperacao.php" method="POST">
        <div class="input-group">
            <label>E-mail Cadastrado</label>
            <input type="email" name="email" placeholder="seu@email.com" required>
        </div>
        
        <div class="input-group">
            <label>Como deseja receber o código?</label>
            <select name="metodo_envio" required>
                <option value="whatsapp">💬 WhatsApp (Mais rápido)</option>
                <option value="email">✉️ E-mail</option>
            </select>
        </div>
        
        <button type="submit">Enviar Código</button>
    </form>
    
    <div style="margin-top: 20px; font-size: 12px;">
        <a href="login.php" style="color: var(--link-color); text-decoration: none;">← Voltar para o Login</a>
    </div>
</div>

<script>
    const body = document.body;
    const savedTheme = localStorage.getItem('palazzo_theme');
    if (savedTheme === 'dark') { body.classList.add('dark-theme'); } 
</script>
</body>
</html>