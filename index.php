<?php session_start(); ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="assets/img/logoP.ico" type="image/x-icon">
    <title>Palazzo Essenza | Início</title>
    <!-- Certifique-se de que o nome do seu arquivo CSS principal esteja correto aqui -->
    <link rel="stylesheet" href="assets/css/index.css?v=<?php echo time(); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
</head>
<body>
    <nav>
    <div class="logo">
        <img src="assets/img/logo.png" alt="Logo" width="120px">
    </div>
    <div class="nav-links <?= !isset($_SESSION['usuario_id']) ? 'deslogado' : '' ?>" id="nav-links">
        <a href="cardapio.php">Cardápio</a>
        <?php if (isset($_SESSION['usuario_id'])): ?>
            <a href="reserva.php">Reservar Mesa</a>
        <?php endif; ?>
    </div>

    <div class="nav-actions">
        <?php if (isset($_SESSION['usuario_id'])): ?>
            <div class="profile-container">
                <button class="profile-btn" onclick="toggleMenu(event)">
                    
                    <div class="avatar" style="padding: 0; overflow: hidden; display: flex; align-items: center; justify-content: center;">
                        <?php if (!empty($_SESSION['usuario_foto'])): ?>
                            <img src="<?= $_SESSION['usuario_foto'] ?>" alt="Perfil" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                        <?php else: ?>
                            <?= strtoupper(substr($_SESSION['usuario_nome'], 0, 1)) ?>
                        <?php endif; ?>
                    </div>
                    
                    <span class="profile-name">Olá, <?= explode(' ', $_SESSION['usuario_nome'])[0] ?></span>
                </button>

                <div class="dropdown" id="dropdownMenu">
                    <div class="user-info">
                        <strong><?= $_SESSION['usuario_nome'] ?></strong>
                        <span><?= $_SESSION['usuario_telefone'] ?></span>
                    </div>
                    <hr>
                    <a href="meus_pedidos.php">Meus Pedidos</a>
                    <a href="perfil.php">Meu Perfil</a>
                    
                    <?php if(isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
                        <a href="admin.php" class="admin-link">Painel Admin</a>
                    <?php endif; ?>
                    <hr>
                    <a href="logout.php">Sair</a>
                </div>
            </div>
        <?php else: ?>
            <a href="login.php" class="desktop-auth-link">Entrar</a>
            <a href="telacadastro.php" class="desktop-auth-btn"> Cadastrar-se</a>
        <?php endif; ?>

        <div class="theme-switch">
            <input type="checkbox" id="theme-checkbox" />
            <label for="theme-checkbox">
                <div></div>
                <span>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path fill-rule="evenodd" d="M9.528 1.718a.75.75 0 01.162.819A8.97 8.97 0 009 6a9 9 0 009 9 8.97 8.97 0 003.463-.69.75.75 0 01.981.98 10.503 10.503 0 01-9.694 6.46c-5.799 0-10.5-4.701-10.5-10.5 0-4.368 2.667-8.112 6.46-9.694a.75.75 0 01.818.162z" clip-rule="evenodd"></path></svg>
                </span>
               <span>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6"><path d="M12 2.25a.75.75 0 01.75.75v2.25a.75.75 0 01-1.5 0V3a.75.75 0 01.75-.75zM7.5 12a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0zM18.894 6.166a.75.75 0 00-1.06-1.06l-1.591 1.59a.75.75 0 101.06 1.061l1.591-1.59zM21.75 12a.75.75 0 01-.75.75h-2.25a.75.75 0 010-1.5H21a.75.75 0 01.75.75zM17.834 18.894a.75.75 0 001.06-1.06l-1.59-1.591a.75.75 0 10-1.061 1.06l1.59 1.591zM12 18a.75.75 0 01.75.75V21a.75.75 0 01-1.5 0v-2.25A.75.75 0 0112 18zM7.758 17.303a.75.75 0 00-1.061-1.06l-1.591 1.59a.75.75 0 001.06 1.061l1.591-1.59zM6 12a.75.75 0 01-.75.75H3a.75.75 0 010-1.5h2.25A.75.75 0 016 12zM6.697 7.757a.75.75 0 001.06-1.06l-1.59-1.591a.75.75 0 00-1.061 1.06l1.59 1.591z"></path></svg>
            </span>
            </label>
        </div>

        <div class="hamburger" id="hamburger"><span></span><span></span><span></span></div>
    </div>
</nav>

    <div class="hero">
        <h1>Palazzo Essenza</h1>
        <p>Uma experiência gastronômica inspirada na tradição italiana, onde cada prato é preparado artesanalmente com ingredientes selecionados e um toque de sofisticação.</p>

        <div class="buttons">
            <?php if (isset($_SESSION['usuario_id'])): ?>
                <a href="reserva.php" class="btn btn-outline">Reservar uma Mesa</a>
                <a href="cardapio.php" class="btn btn-primary">Fazer Pedido</a>
            <?php else: ?>
                <a href="telacadastro.php" class="btn btn-outline">Cadastrar-se</a>
                <a href="login.php" class="btn btn-primary">Entrar</a>
            <?php endif; ?>
            
          <a href="https://www.mediafire.com/file/u5kpdlqt3rsfvro/app-debug.apk/file" target="_blank" class="btn-download-app">
                <span class="button__text">Baixar App Android</span>
                <span class="button__icon">
                    <svg class="svg" data-name="Layer 2" id="bdd05811-e15d-428c-bb53-8661459f9307" viewBox="0 0 35 35" xmlns="http://www.w3.org/2000/svg">
                        <path d="M17.5,22.131a1.249,1.249,0,0,1-1.25-1.25V2.187a1.25,1.25,0,0,1,2.5,0V20.881A1.25,1.25,0,0,1,17.5,22.131Z"></path>
                        <path d="M17.5,22.693a3.189,3.189,0,0,1-2.262-.936L8.487,15.006a1.249,1.249,0,0,1,1.767-1.767l6.751,6.751a.7.7,0,0,0,.99,0l6.751-6.751a1.25,1.25,0,0,1,1.768,1.767l-6.752,6.751A3.191,3.191,0,0,1,17.5,22.693Z"></path>
                        <path d="M31.436,34.063H3.564A3.318,3.318,0,0,1,.25,30.749V22.011a1.25,1.25,0,0,1,2.5,0v8.738a.815.815,0,0,0,.814.814H31.436a.815.815,0,0,0,.814-.814V22.011a1.25,1.25,0,1,1,2.5,0v8.738A3.318,3.318,0,0,1,31.436,34.063Z"></path>
                    </svg>
                </span>
            </a>
        </div>
    </div>
    
    <!-- Botões de Contato Flutuantes -->
    <div class="floating-contacts">
        <a href="mailto:bezerraannaluiza7@gmail.com?subject=Dúvida%20-%20Palazzo%20Essenza" class="fab-btn email-fab" title="Enviar E-mail">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M1.5 8.67v8.58a3 3 0 003 3h15a3 3 0 003-3V8.67l-8.928 5.493a3 3 0 01-3.144 0L1.5 8.67z" /><path d="M22.5 6.908V6.75a3 3 0 00-3-3h-15a3 3 0 00-3 3v.158l9.714 5.978a1.5 1.5 0 001.572 0L22.5 6.908z" /></svg>
        </a>
        <a href="https://wa.me/5517991863014?text=Olá,%20Palazzo%20Essenza!%20Gostaria%20de%20tirar%20uma%20dúvida." target="_blank" class="fab-btn whatsapp-fab" title="Falar no WhatsApp">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12.013 2.012a9.96 9.96 0 0 0-9.96 9.96c0 1.956.54 3.864 1.566 5.541L2.012 21.99l4.582-1.573a9.968 9.968 0 0 0 5.419 1.573h.005a9.957 9.957 0 0 0 9.962-9.962 9.946 9.946 0 0 0-2.918-7.042 9.947 9.947 0 0 0-7.049-2.974zm0 16.634a8.315 8.315 0 0 1-4.238-1.15l-.304-.18-3.149 1.08.84-3.072-.198-.315A8.32 8.32 0 0 1 3.676 11.97a8.307 8.307 0 0 1 8.337-8.31 8.28 8.28 0 0 1 5.88 2.43 8.282 8.282 0 0 1 2.44 5.88 8.307 8.307 0 0 1-8.32 8.311v-.005zm4.568-6.236c-.25-.125-1.482-.731-1.712-.815-.231-.084-.4-.125-.568.125-.168.25-.648.815-.794.981-.147.166-.293.187-.543.063-.25-.125-1.057-.39-2.013-1.246-.743-.666-1.246-1.488-1.393-1.738-.147-.25-.015-.385.11-.51.112-.112.25-.291.375-.438.125-.146.166-.25.25-.416.084-.167.041-.313-.021-.438-.063-.125-.568-1.37-.777-1.875-.204-.492-.41-.425-.568-.433-.146-.007-.313-.007-.481-.007s-.438.062-.667.313c-.23.25-.876.854-.876 2.083s.897 2.417 1.022 2.583c.125.167 1.761 2.688 4.267 3.771 2.053.89 2.551.71 3.013.605.534-.121 1.482-.605 1.691-1.189.208-.583.208-1.082.146-1.187-.062-.105-.229-.168-.479-.293z"/></svg>
        </a>
    </div>

    <footer>© 2026 Palazzo Essenza Restaurante • Alta Cucina Italiana</footer>
    
    <script>
        const hamburger = document.getElementById('hamburger');
        const navLinks = document.getElementById('nav-links');
        
        hamburger.addEventListener('click', (e) => { 
            e.stopPropagation();
            hamburger.classList.toggle('active'); 
            navLinks.classList.toggle('active'); 
        });

        document.querySelectorAll('.nav-links a').forEach(link => { 
            link.addEventListener('click', () => { 
                hamburger.classList.remove('active'); 
                navLinks.classList.remove('active'); 
            }); 
        });

        function toggleMenu(e) {
            e.stopPropagation();
            document.getElementById("dropdownMenu").classList.toggle("active");
        }

        window.addEventListener("click", function (e) {
            if (navLinks.classList.contains('active') && !navLinks.contains(e.target) && !hamburger.contains(e.target)) {
                hamburger.classList.remove('active');
                navLinks.classList.remove('active');
            }

            const menu = document.getElementById("dropdownMenu");
            const btn = document.querySelector(".profile-btn");
            if (btn && menu && menu.classList.contains("active") && !menu.contains(e.target)) {
                menu.classList.remove("active");
            }
        });

        const themeSwitch = document.getElementById('theme-checkbox');
        const body = document.body;
        const savedTheme = localStorage.getItem('palazzo_theme');

        if (savedTheme === 'dark') {
            body.classList.add('dark-theme');
            themeSwitch.checked = true; 
        } else {
            body.classList.remove('dark-theme');
            themeSwitch.checked = false;
        }

        themeSwitch.addEventListener('change', () => {
            if (themeSwitch.checked) {
                body.classList.add('dark-theme');
                localStorage.setItem('palazzo_theme', 'dark');
            } else {
                body.classList.remove('dark-theme');
                localStorage.setItem('palazzo_theme', 'light');
            }
        });
    </script>
</body>
</html>