<?php
session_start();
require 'conexao.php'; 
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="shortcut icon" href="logoP.ico" type="image/x-icon">
<title>Palazzo Essenza | Cardápio</title>
<link rel="stylesheet" href="cardapio.css">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
<style>
    #carrinho-container {
        position: fixed; top: 0; right: 0; bottom: 0; width: 400px; max-width: 100%; height: 100%;
        background: var(--cart-bg); backdrop-filter: blur(15px); border-left: 1px solid var(--cart-border);
        padding: 25px; padding-bottom: 80px; display: flex; flex-direction: column; z-index: 1000;
        overflow-y: auto; overscroll-behavior: none; -webkit-overflow-scrolling: touch; touch-action: pan-y;
    }

    /* MODAL DINÂMICO DE OPÇÕES */
    .fundo-modal { position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0, 0, 0, 0.6); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center; }
    .conteudo-modal { background: var(--bg-color, #ffffff); color: var(--text-color, #333333); padding: 30px; border-radius: 12px; width: 90%; max-width: 350px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); transition: all 0.3s ease; }
    .conteudo-modal h3 { margin-top: 0; text-align: center; color: var(--accent-color, #8b6932); font-size: 18px; }
    .opcoes-modal { display: flex; flex-direction: column; gap: 15px; margin: 20px 0; }
    .opcoes-modal label { display: flex; align-items: center; gap: 10px; font-size: 16px; cursor: pointer; font-family: 'Poppins', sans-serif;}
    .acoes-modal { display: flex; justify-content: space-between; margin-top: 25px; }
    .btn-cancelar { padding: 10px 15px; background: transparent; border: 1px solid #ccc; border-radius: 6px; cursor: pointer; color: var(--text-color, #333); flex: 1; margin-right: 10px; font-family: 'Poppins', sans-serif;}
    .btn-confirmar { padding: 10px 15px; background: var(--accent-color, #8b6932); color: white; border: none; border-radius: 6px; cursor: pointer; flex: 1; font-family: 'Poppins', sans-serif; font-weight: bold;}
    body.dark-theme .conteudo-modal { background: rgba(20, 20, 20, 0.75); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); color: #ffffff; border: 1px solid rgba(255, 255, 255, 0.1); }
    body.dark-theme .conteudo-modal h3 { color: #ffffff; }
    body.dark-theme .btn-cancelar { color: #ffffff; border-color: rgba(255, 255, 255, 0.3); }

    /* CARTÃO DE CRÉDITO ANIMADO EM 3D */
    .card-wrapper { perspective: 1000px; margin: 15px 0; display: none; }
    .card-inner { position: relative; width: 100%; height: 180px; transition: transform 0.6s cubic-bezier(0.4, 0.2, 0.2, 1); transform-style: preserve-3d; }
    .card-inner.is-flipped { transform: rotateY(180deg); }
    .card-front, .card-back { position: absolute; width: 100%; height: 100%; backface-visibility: hidden; border-radius: 15px; padding: 20px; color: white; box-sizing: border-box; }
    .card-front { background: linear-gradient(135deg, #2c2c2c 0%, #1a1a1a 100%); box-shadow: 0 4px 15px rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.1); }
    .card-back { background: linear-gradient(135deg, #1a1a1a 0%, #2c2c2c 100%); transform: rotateY(180deg); box-shadow: 0 4px 15px rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.1); padding: 0; }
    .card-logo { text-align: right; font-family: 'Playfair Display', serif; font-style: italic; font-weight: bold; font-size: 18px; color: #c6a75e; }
    .card-chip { width: 40px; height: 30px; background: linear-gradient(135deg, #e6c97a, #b89845); border-radius: 5px; margin-top: 10px; margin-bottom: 20px; box-shadow: inset 0 0 5px rgba(0,0,0,0.2);}
    .visual-num { font-size: 18px; letter-spacing: 2.5px; margin-bottom: 15px; font-family: monospace; text-shadow: 1px 1px 2px rgba(0,0,0,0.5); }
    .card-details { display: flex; justify-content: space-between; font-size: 12px; text-transform: uppercase; font-family: 'Poppins', sans-serif; letter-spacing: 1px; }
    .tarja-magnetica { width: 100%; height: 40px; background: #000; margin-top: 25px; }
    .cvv-box-container { padding: 0 20px; margin-top: 15px; text-align: right; }
    .cvv-box { background: #fff; color: #000; padding: 5px 10px; display: inline-block; width: 60px; text-align: center; font-style: italic; font-family: monospace; font-size: 14px; border-radius: 4px; }
    .card-form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 10px; }
</style>
</head>
<body>

<div class="top-controls">
    <button class="btn-back" onclick="voltarPagina()">
        <span class="arrow">←</span>
        <span class="text">Voltar</span>
    </button>
    
    <div style="display: flex; align-items: center;">
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
        <?php endif; ?>

        <div class="theme-switch">
                <input type="checkbox" id="theme-checkbox" />
                <label for="theme-checkbox">
                    <div></div>
                    <span><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path fill-rule="evenodd" d="M9.528 1.718a.75.75 0 01.162.819A8.97 8.97 0 009 6a9 9 0 009 9 8.97 8.97 0 003.463-.69.75.75 0 01.981.98 10.503 10.503 0 01-9.694 6.46c-5.799 0-10.5-4.701-10.5-10.5 0-4.368 2.667-8.112 6.46-9.694a.75.75 0 01.818.162z" clip-rule="evenodd"></path></svg></span>
                    <span><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.25a.75.75 0 01.75.75v2.25a.75.75 0 01-1.5 0V3a.75.75 0 01.75-.75zM7.5 12a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0zM18.894 6.166a.75.75 0 00-1.06-1.06l-1.591 1.59a.75.75 0 101.06 1.061l1.591-1.59zM21.75 12a.75.75 0 01-.75.75h-2.25a.75.75 0 010-1.5H21a.75.75 0 01.75.75zM17.834 18.894a.75.75 0 001.06-1.06l-1.59-1.591a.75.75 0 10-1.061 1.06l1.59 1.591zM12 18a.75.75 0 01.75.75V21a.75.75 0 01-1.5 0v-2.25A.75.75 0 0112 18zM7.758 17.303a.75.75 0 00-1.061-1.06l-1.591 1.59a.75.75 0 001.06 1.061l1.591-1.59zM6 12a.75.75 0 01-.75.75H3a.75.75 0 010-1.5h2.25A.75.75 0 016 12zM6.697 7.757a.75.75 0 001.06-1.06l-1.59-1.591a.75.75 0 00-1.061 1.06l1.59 1.591z"></path></svg></span>
                </label>
        </div>
    </div>
</div>

<header>
    <div class="logo">Palazzo Essenza</div>
    <div class="subtitle">Ristorante • Alta Cucina Italiana</div>
</header>

<div class="menu-container">
    <?php
    try {
        $stmtCategorias = $pdo->query("SELECT * FROM categorias ORDER BY id");
        $categorias = $stmtCategorias->fetchAll(PDO::FETCH_ASSOC);

        foreach ($categorias as $categoria) {
            $nomeCategoria = mb_strtolower(trim($categoria['nome']), 'UTF-8');
            if(strpos($nomeCategoria, 'bebida') !== false || strpos($nomeCategoria, 'drinks') !== false){
                continue;
            }

            $stmtProdutos = $pdo->prepare("SELECT * FROM produtos WHERE categoria_id = ? AND disponivel = 1");
            $stmtProdutos->execute([$categoria['id']]);
            $produtosDaCategoria = $stmtProdutos->fetchAll(PDO::FETCH_ASSOC);
            
            if (count($produtosDaCategoria) > 0) {
                echo "<div class='menu-section'>";
                echo "<h2>" . htmlspecialchars($categoria['nome']) . "</h2>";
                
                foreach ($produtosDaCategoria as $produto) {
                    $nomeProduto = htmlspecialchars($produto['nome']);
                    $descProduto = htmlspecialchars($produto['descricao']);
                    $precoFormatado = number_format($produto['preco'], 2, ',', '.');
                    $precoJS = number_format($produto['preco'], 2, '.', ''); 
                    
                    if (stripos($nomeProduto, 'Suco') !== false) {
                        $acaoBotao = "abrirModalOpcoes('{$nomeProduto}', {$precoJS}, 'Escolha o sabor do Suco', ['Laranja', 'Limão', 'Maracujá', 'Abacaxi'])";
                    } elseif (stripos($nomeProduto, 'Refrigerante') !== false) {
                        $acaoBotao = "abrirModalOpcoes('{$nomeProduto}', {$precoJS}, 'Escolha o Refrigerante', ['Coca-Cola', 'Guaraná', 'Sprite'])";
                    } elseif (stripos($nomeProduto, 'Chá Gelado') !== false) {
                        $acaoBotao = "abrirModalOpcoes('{$nomeProduto}', {$precoJS}, 'Escolha o sabor do Chá', ['Limão', 'Pêssego'])";
                    } elseif (stripos($nomeProduto, 'Cerveja Long Neck') !== false) {
                        $acaoBotao = "abrirModalOpcoes('{$nomeProduto}', {$precoJS}, 'Escolha a Cerveja', ['Heineken', 'Stella Artois'])";
                    } else {
                        $acaoBotao = "adicionarPratoPronto('{$nomeProduto}', {$precoJS})";
                    }
                    
                    echo "
                    <div class='menu-item'>
                        <div class='menu-item-info'>
                            <h3>{$nomeProduto}</h3>
                            <p>{$descProduto}</p>
                            <button class='btn-add' onclick=\"{$acaoBotao}\">+ Adicionar</button>
                        </div>
                        <div class='price'>R$ {$precoFormatado}</div>
                    </div>";
                }
                echo "</div>"; 
            }
        }
    } catch (PDOException $e) { echo "<p>Erro ao carregar o cardápio: " . $e->getMessage() . "</p>"; }
    ?>

    <div class="menu-section">
        <h2>Entradas</h2>
        <?php
        $entradas = [
            ['nome'=>'Bruschetta al Pomodoro', 'descricao'=>'Fatias de pão italiano com tomate fresco, manjericão e azeite', 'preco'=>15.00],
            ['nome'=>'Carpaccio di Manzo', 'descricao'=>'Finas fatias de carne bovina com rúcula e parmesão', 'preco'=>25.00],
            ['nome'=>'Mozzarella in Carrozza', 'descricao'=>'Queijo mozzarella empanado, servido com molho de tomate', 'preco'=>18.00],
            ['nome'=>'Sopa Minestrone', 'descricao'=>'Tradicional sopa italiana de legumes e massa curta', 'preco'=>12.00]
        ];
        foreach($entradas as $item){
            $precoJS = number_format($item['preco'],2,'.','');
            $precoFormat = number_format($item['preco'],2,',','.');
            echo "
            <div class='menu-item'>
                <div class='menu-item-info'>
                    <h3>{$item['nome']}</h3>
                    <p>{$item['descricao']}</p>
                    <button class='btn-add' onclick=\"adicionarPratoPronto('{$item['nome']}', {$precoJS})\">+ Adicionar</button>
                </div>
                <div class='price'>R$ {$precoFormat}</div>
            </div>";
        }
        ?>
    </div>

<div class="menu-section">
    <h2>Bebidas sem Álcool</h2>
    <?php
    $bebidasSemAlcool = [
        ['nome'=>'Água Mineral (500ml)', 'descricao'=>'Água mineral sem gás', 'preco'=>5.00],
        ['nome'=>'Água com Gás (500ml)', 'descricao'=>'Água mineral gaseificada', 'preco'=>6.00],
        ['nome'=>'Refrigerante (350ml)', 'descricao'=>'Coca-Cola, Guaraná ou Sprite', 'preco'=>7.00],
        ['nome'=>'Suco Natural', 'descricao'=>'Laranja, limão, maracujá ou abacaxi', 'preco'=>9.00],
        ['nome'=>'Chá Gelado', 'descricao'=>'Limão ou pêssego', 'preco'=>8.00],
        ['nome'=>'Mocktail Italiano', 'descricao'=>'Drink sem álcool com frutas cítricas', 'preco'=>14.00]
    ];
    foreach($bebidasSemAlcool as $item){
        $precoJS = number_format($item['preco'],2,'.','');
        $precoFormat = number_format($item['preco'],2,',','.');
        
        if (stripos($item['nome'], 'Suco') !== false) {
            $acaoBotao = "abrirModalOpcoes('{$item['nome']}', {$precoJS}, 'Escolha o sabor do Suco', ['Laranja', 'Limão', 'Maracujá', 'Abacaxi'])";
        } elseif (stripos($item['nome'], 'Refrigerante') !== false) {
            $acaoBotao = "abrirModalOpcoes('{$item['nome']}', {$precoJS}, 'Escolha o Refrigerante', ['Coca-Cola', 'Guaraná', 'Sprite'])";
        } elseif (stripos($item['nome'], 'Chá Gelado') !== false) {
            $acaoBotao = "abrirModalOpcoes('{$item['nome']}', {$precoJS}, 'Escolha o sabor do Chá', ['Limão', 'Pêssego'])";
        } else {
            $acaoBotao = "adicionarPratoPronto('{$item['nome']}', {$precoJS})";
        }

        echo "
        <div class='menu-item'>
            <div class='menu-item-info'>
                <h3>{$item['nome']}</h3>
                <p>{$item['descricao']}</p>
                <button class='btn-add' onclick=\"{$acaoBotao}\">+ Adicionar</button>
            </div>
            <div class='price'>R$ {$precoFormat}</div>
        </div>";
    }
    ?>
</div>

<div class="menu-section">
    <h2>Bebidas com Álcool</h2>
    <?php
    $bebidasComAlcool = [
        ['nome'=>'Vinho Tinto (Taça)', 'descricao'=>'Vinho tinto suave italiano', 'preco'=>18.00],
        ['nome'=>'Vinho Branco (Taça)', 'descricao'=>'Vinho branco leve e refrescante', 'preco'=>18.00],
        ['nome'=>'Aperol Spritz', 'descricao'=>'Drink italiano clássico com espumante', 'preco'=>24.00],
        ['nome'=>'Negroni', 'descricao'=>'Gin, Campari e vermute rosso', 'preco'=>26.00],
        ['nome'=>'Cerveja Long Neck', 'descricao'=>'Heineken ou Stella Artois', 'preco'=>12.00],
        ['nome'=>'Espumante Italiano', 'descricao'=>'Taça de espumante brut', 'preco'=>22.00]
    ];
    foreach($bebidasComAlcool as $item){
        $precoJS = number_format($item['preco'],2,'.','');
        $precoFormat = number_format($item['preco'],2,',','.');
        
        if (stripos($item['nome'], 'Cerveja Long Neck') !== false) {
            $acaoBotao = "abrirModalOpcoes('{$item['nome']}', {$precoJS}, 'Escolha a Cerveja', ['Heineken', 'Stella Artois'])";
        } else {
            $acaoBotao = "adicionarPratoPronto('{$item['nome']}', {$precoJS})";
        }

        echo "
        <div class='menu-item'>
            <div class='menu-item-info'>
                <h3>{$item['nome']}</h3>
                <p>{$item['descricao']}</p>
                <button class='btn-add' onclick=\"{$acaoBotao}\">+ Adicionar</button>
            </div>
            <div class='price'>R$ {$precoFormat}</div>
        </div>";
    }
    ?>
</div>
    
    <div class="menu-section">
        <h2>Sobremesas</h2>
        <?php
        $sobremesas = [
            ['nome'=>'Tiramisu Classico', 'descricao'=>'Tradicional sobremesa italiana com café e mascarpone', 'preco'=>18.00],
            ['nome'=>'Panna Cotta ai Frutti di Bosco', 'descricao'=>'Creme italiano com calda de frutas vermelhas', 'preco'=>16.00],
            ['nome'=>'Gelato al Cioccolato', 'descricao'=>'Sorvete artesanal de chocolate', 'preco'=>12.00],
            ['nome'=>'Cannoli Siciliani', 'descricao'=>'Massa crocante recheada com creme de ricota e chocolate', 'preco'=>15.00]
        ];
        foreach($sobremesas as $item){
            $precoJS = number_format($item['preco'],2,'.','');
            $precoFormat = number_format($item['preco'],2,',','.');
            echo "
            <div class='menu-item'>
                <div class='menu-item-info'>
                    <h3>{$item['nome']}</h3>
                    <p>{$item['descricao']}</p>
                    <button class='btn-add' onclick=\"adicionarPratoPronto('{$item['nome']}', {$precoJS})\">+ Adicionar</button>
                </div>
                <div class='price'>R$ {$precoFormat}</div>
            </div>";
        }
        ?>
    </div>

    <div class="menu-section">
        <h2>Crie sua Obra-Prima</h2>
        <div class="custom-form">
            <div class="custom-group">
                <h3>1. Escolha sua Massa Base (Obrigatório)</h3>
                <div class="options-grid">
                    <label class="option-label"><div><input type="radio" name="massa_base" value="Ravioli" data-preco="25.00"> Ravioli</div><span class="option-price">R$ 25,00</span></label>
                    <label class="option-label"><div><input type="radio" name="massa_base" value="Espaguete" data-preco="20.00"> Espaguete</div><span class="option-price">R$ 20,00</span></label>
                    <label class="option-label"><div><input type="radio" name="massa_base" value="Penne" data-preco="22.00"> Penne</div><span class="option-price">R$ 22,00</span></label>
                    <label class="option-label"><div><input type="radio" name="massa_base" value="Macaroni" data-preco="20.00"> Macaroni</div><span class="option-price">R$ 20,00</span></label>
                    <label class="option-label"><div><input type="radio" name="massa_base" value="Fusili" data-preco="22.00"> Fusili</div><span class="option-price">R$ 22,00</span></label>
                    <label class="option-label"><div><input type="radio" name="massa_base" value="Gravata" data-preco="20.00"> Gravata</div><span class="option-price">R$ 20,00</span></label>
                </div>
            </div>
             <div class="custom-group">
            <h3>2. Escolha seu Molho</h3>
            <div class="options-grid">
                <label class="option-label"><div><input type="radio" name="molho" value="Pomodoro" data-preco="5.00"> Molho Pomodoro</div><span class="option-price">+ R$ 5,00</span></label>
                <label class="option-label"><div><input type="radio" name="molho" value="Alfredo" data-preco="6.00"> Molho Alfredo</div><span class="option-price">+ R$ 6,00</span></label>
                <label class="option-label"><div><input type="radio" name="molho" value="Pesto" data-preco="6.50"> Molho Pesto</div><span class="option-price">+ R$ 6,50</span></label>
                <label class="option-label"><div><input type="radio" name="molho" value="Bolonhesa" data-preco="7.00"> Molho Bolonhesa</div><span class="option-price">+ R$ 7,00</span></label>
                <label class="option-label"><div><input type="radio" name="molho" value="Quatro Queijos" data-preco="7.50"> Molho Quatro Queijos</div><span class="option-price">+ R$ 7,50</span></label>
            </div>
        </div>
            <div class="custom-group">
                <h3>3. Escolha seus Acompanhamentos</h3>
                <div class="options-grid">
                    <label class="option-label"><div><input type="checkbox" name="acompanhamentos" value="Bacon" data-preco="8.00"> Bacon Artesanal</div><span class="option-price">+ R$ 8,00</span></label>
                    <label class="option-label"><div><input type="checkbox" name="acompanhamentos" value="Cogumelos" data-preco="6.00"> Cogumelos Frescos</div><span class="option-price">+ R$ 6,00</span></label>
                    <label class="option-label"><div><input type="checkbox" name="acompanhamentos" value="Queijo Ralado" data-preco="4.00"> Parmesão Curado</div><span class="option-price">+ R$ 4,00</span></label>
                    <label class="option-label"><div><input type="checkbox" name="acompanhamentos" value="Milho" data-preco="3.00"> Milho Doce</div><span class="option-price">+ R$ 3,00</span></label>
                    <label class="option-label"><div><input type="checkbox" name="acompanhamentos" value="Brócolis" data-preco="5.00"> Brócolis Tostado</div><span class="option-price">+ R$ 5,00</span></label>
                    <label class="option-label"><div><input type="checkbox" name="acompanhamentos" value="Frango" data-preco="10.00"> Frango Grelhado</div><span class="option-price">+ R$ 10,00</span></label>
                </div>
            </div>
            <button class="btn-build" onclick="adicionarMassaPersonalizada()">Adicionar Obra-Prima ao Carrinho</button>
        </div>
    </div>
</div>

<footer>©️ 2026 Palazzo Essenza Restaurante • Todos os direitos reservados</footer>

<!-- MODAL GENÉRICO PARA OPÇÕES (SUCOS, CERVEJAS, REFRIS, ETC) -->
<div id="modal-opcoes" class="fundo-modal" style="display: none;">
    <div class="conteudo-modal">
        <h3 id="modal-titulo">Escolha sua Opção</h3>
        <div class="opcoes-modal" id="modal-lista-opcoes"></div>
        <div class="acoes-modal">
            <button class="btn-cancelar" onclick="fecharModalOpcoes()">Cancelar</button>
            <button class="btn-confirmar" onclick="confirmarOpcao()">Adicionar</button>
        </div>
    </div>
</div>

<div id="carrinho-container" style="display: none;">
    <div id="carrinho-header">
        <h3>Seu Pedido</h3>
        <button onclick="fecharCarrinho()">×</button>
    </div>
    
    <div id="itens-carrinho"></div>
    
    <div class="carrinho-footer">
        <p><span>Total:</span> <span>R$ <span id="valor-total">0.00</span></span></p>
        
        <div class="checkout-group">
            <label>Como deseja receber?</label>
            <select id="tipo-entrega" class="checkout-input" onchange="toggleEndereco()">
                <option value="mesa">Consumir no Local / Retirar</option>
                <option value="delivery">Entrega (Delivery)</option>
            </select>
        </div>

        <div id="box-endereco" style="display: none; margin-bottom: 12px;">
            <input type="text" id="end-cep" class="checkout-input" placeholder="CEP (Apenas números)" maxlength="9" style="margin-bottom: 8px;">
            <input type="text" id="end-rua" class="checkout-input" placeholder="Rua / Avenida" style="margin-bottom: 8px;">
            <div class="endereco-row" style="margin-bottom: 8px;">
                <input type="text" id="end-num" class="checkout-input" placeholder="Número" style="width: 35%;">
                <input type="text" id="end-bairro" class="checkout-input" placeholder="Bairro" style="width: 65%;">
            </div>
            <div class="endereco-row">
                <input type="text" id="end-cidade" class="checkout-input" placeholder="Cidade" style="width: 75%;" readonly>
                <input type="text" id="end-uf" class="checkout-input" placeholder="UF" style="width: 25%;" readonly>
            </div>
            <input type="text" id="end-comp" class="checkout-input" placeholder="Complemento (Opcional)" style="margin-top: 8px;">
        </div>

        <div class="checkout-group">
            <label>Forma de Pagamento</label>
            <select id="forma-pagamento" class="checkout-input" onchange="togglePagamento()">
                <option value="pix">Pix</option>
                <option value="cartao_online">Cartão de Crédito (Pagar Agora pelo Site)</option>
                <option value="cartao_credito">Cartão de Crédito (Na Maquininha)</option>
                <option value="cartao_debito">Cartão de Débito (Na Maquininha)</option>
                <option value="dinheiro">Dinheiro</option>
            </select>
        </div>

        <div id="box-troco" class="checkout-group" style="display: none;">
            <label>Precisa de troco para quanto?</label>
            <input type="number" id="troco" class="checkout-input" placeholder="Ex: 50.00">
        </div>

        <div id="box-cartao" class="card-wrapper">
            <div class="card-inner" id="card-inner">
                <div class="card-front">
                    <div class="card-logo">PalazzoCard</div>
                    <div class="card-chip"></div>
                    <div class="visual-num" id="vis-num">#### #### #### ####</div>
                    <div class="card-details">
                        <div>
                            <div style="font-size: 8px; color: #aaa;">Titular</div>
                            <div id="vis-nome">NOME DO TITULAR</div>
                        </div>
                        <div>
                            <div style="font-size: 8px; color: #aaa;">Validade</div>
                            <div id="vis-val">MM/AA</div>
                        </div>
                    </div>
                </div>
                <div class="card-back">
                    <div class="tarja-magnetica"></div>
                    <div class="cvv-box-container">
                        <div style="font-size: 10px; margin-bottom: 2px;">CVV</div>
                        <div class="cvv-box" id="vis-cvv"></div>
                    </div>
                </div>
            </div>
            
            <input type="text" id="input-num" class="checkout-input" placeholder="Número do Cartão" maxlength="19" style="margin-top: 15px;" autocomplete="off">
            <input type="text" id="input-nome" class="checkout-input" placeholder="Nome impresso no cartão" style="margin-top: 10px; text-transform: uppercase;" autocomplete="off">
            <div class="card-form-grid">
                <input type="text" id="input-val" class="checkout-input" placeholder="MM/AA" maxlength="5" autocomplete="off">
                <input type="text" id="input-cvv" class="checkout-input" placeholder="CVV" maxlength="4" autocomplete="off">
            </div>
        </div>
        <div class="checkout-group" style="margin-top: 15px;">
            <label>Observações do Pedido</label>
            <textarea id="obs-pedido" class="checkout-input" rows="2" placeholder="Ex: Tirar cebola, campainha quebrada, etc..."></textarea>
        </div>

        <button id="btn-finalizar" onclick="enviarPedidoPHP()">Finalizar Pedido</button>
    </div>
</div>

<button id="btn-abrir-carrinho" onclick="abrirCarrinho()">🛒 Ver Carrinho (<span id="qtd-itens">0</span>)</button>

<script>
    const usuarioLogado = <?php echo isset($_SESSION['usuario_id']) ? 'true' : 'false'; ?>;
    let scrollPosition = 0; 

    function voltarPagina() { window.location.href = "index.php"; }
    function toggleMenu() { document.getElementById("dropdownMenu").classList.toggle("active"); }

    window.addEventListener("click", function(e) {
        const menu = document.getElementById("dropdownMenu");
        const btn = document.querySelector(".profile-btn");
        if (btn && menu && !btn.contains(e.target) && !menu.contains(e.target)) {
            menu.classList.remove("active");
        }
    });

    const themeSwitch = document.getElementById('theme-checkbox');
    const body = document.body;
    const savedTheme = localStorage.getItem('palazzo_theme');

    if (savedTheme === 'dark') { body.classList.add('dark-theme'); themeSwitch.checked = true; } 
    else { body.classList.remove('dark-theme'); themeSwitch.checked = false; }

    themeSwitch.addEventListener('change', () => {
        if (themeSwitch.checked) { body.classList.add('dark-theme'); localStorage.setItem('palazzo_theme', 'dark'); } 
        else { body.classList.remove('dark-theme'); localStorage.setItem('palazzo_theme', 'light'); }
    });

    let carrinho = []; let total = 0;
    
    // VARIÁVEIS DE CONTROLE DO MODAL DE OPÇÕES
    let itemAtualNome = "";
    let itemAtualPreco = 0;

    function abrirModalOpcoes(nomeItem, preco, titulo, opcoes) {
        if (!usuarioLogado) {
            alert("Precisas de iniciar sessão ou criar conta para fazer um pedido!");
            window.location.href = "login.php";
            return;
        }

        itemAtualNome = nomeItem;
        itemAtualPreco = preco;

        document.getElementById('modal-titulo').innerText = titulo;
        const listaDiv = document.getElementById('modal-lista-opcoes');
        listaDiv.innerHTML = '';

        opcoes.forEach((opcao, index) => {
            const isChecked = index === 0 ? 'checked' : '';
            listaDiv.innerHTML += `
                <label>
                    <input type="radio" name="opcao_dinamica" value="${opcao}" ${isChecked}> ${opcao}
                </label>
            `;
        });

        document.getElementById('modal-opcoes').style.display = 'flex';
        document.body.classList.add('carrinho-aberto'); 
    }

    function fecharModalOpcoes() {
        document.getElementById('modal-opcoes').style.display = 'none';
        document.body.classList.remove('carrinho-aberto'); 
    }

    function confirmarOpcao() {
        const radioSelecionado = document.querySelector('input[name="opcao_dinamica"]:checked');
        if (!radioSelecionado) return;
        
        const opcaoEscolhida = radioSelecionado.value;
        const nomeFinal = `${itemAtualNome} (${opcaoEscolhida})`;
        adicionarPratoPronto(nomeFinal, itemAtualPreco);
        fecharModalOpcoes();
    }
    
    function notificarItemAdicionado() {
        const btnCarrinho = document.getElementById('btn-abrir-carrinho');
        btnCarrinho.classList.remove('btn-animado');
        void btnCarrinho.offsetWidth; 
        btnCarrinho.classList.add('btn-animado');
    }

    function adicionarPratoPronto(nome, preco) {
        if (!usuarioLogado) {
            alert("Precisas de iniciar sessão ou criar conta para fazer um pedido!");
            window.location.href = "login.php"; 
            return; 
        }
        carrinho.push({ nome: nome, preco: preco, detalhes: "" });
        total += preco; 
        atualizarInterface(); 
        notificarItemAdicionado(); 
    }

    function adicionarMassaPersonalizada() {
        if (!usuarioLogado) {
            alert("Precisas de iniciar sessão ou criar conta para montar o teu prato!");
            window.location.href = "login.php"; 
            return; 
        }
        const massaSelecionada = document.querySelector('input[name="massa_base"]:checked');
        if (!massaSelecionada) return alert("Escolha um tipo de massa base.");
        
        const nomeMassa = massaSelecionada.value; 
        let precoTotalItem = parseFloat(massaSelecionada.dataset.preco);
        let detalhesAcompanhamentos = [];
        
        const molhoSelecionado = document.querySelector('input[name="molho"]:checked');
        if (molhoSelecionado) {
            detalhesAcompanhamentos.push("Molho " + molhoSelecionado.value);
            precoTotalItem += parseFloat(molhoSelecionado.dataset.preco);
        }

        const acompanhamentosSelecionados = document.querySelectorAll('input[name="acompanhamentos"]:checked');
        acompanhamentosSelecionados.forEach((checkbox) => { 
            detalhesAcompanhamentos.push(checkbox.value); 
            precoTotalItem += parseFloat(checkbox.dataset.preco); 
        });
        
        const textoDetalhes = detalhesAcompanhamentos.length > 0 ? "Com: " + detalhesAcompanhamentos.join(", ") : "Sem acompanhamentos extras";
        
        carrinho.push({ nome: "Massa (" + nomeMassa + ")", preco: precoTotalItem, detalhes: textoDetalhes });
        total += precoTotalItem; 
        atualizarInterface(); 
        notificarItemAdicionado(); 
        
        document.querySelectorAll('input[name="massa_base"]').forEach(r => r.checked = false);
        document.querySelectorAll('input[name="molho"]').forEach(r => r.checked = false); 
        document.querySelectorAll('input[name="acompanhamentos"]').forEach(c => c.checked = false);
    }

    function atualizarInterface() {
        const divItens = document.getElementById('itens-carrinho');
        divItens.innerHTML = '';
        carrinho.forEach((item, index) => {
            divItens.innerHTML += `
            <div class="item-no-carrinho">
                <button class="btn-remover-item" onclick="removerDoCarrinho(${index})">×</button>
                <div class="item-conteudo">
                    <div class="item-header">
                        <span><strong>${item.nome}</strong></span>
                        <span>R$ ${item.preco.toFixed(2)}</span>
                    </div>
                    ${item.detalhes !== "" ? `<div class="item-detalhes">${item.detalhes}</div>` : ""}
                </div>
            </div>`;
        });
        document.getElementById('valor-total').innerText = total.toFixed(2);
        document.getElementById('qtd-itens').innerText = carrinho.length;
    }

    function removerDoCarrinho(index) {
        total -= carrinho[index].preco; 
        carrinho.splice(index, 1); 
        if (total < 0 || carrinho.length === 0) total = 0;
        atualizarInterface(); 
        document.getElementById('qtd-itens').innerText = carrinho.length;
    }

    function abrirCarrinho() { 
        if (!usuarioLogado) { alert("Inicia sessão para acederes ao teu carrinho!"); window.location.href = "login.php"; return; }
        document.getElementById('carrinho-container').style.display = 'flex';
        document.body.classList.add('carrinho-aberto');
    }

    function fecharCarrinho() { 
        document.getElementById('carrinho-container').style.display = 'none';
        document.body.classList.remove('carrinho-aberto');
    }

    function toggleEndereco() {
        const tipo = document.getElementById('tipo-entrega').value;
        document.getElementById('box-endereco').style.display = (tipo === 'delivery') ? 'block' : 'none';
    }

    function togglePagamento() {
        const forma = document.getElementById('forma-pagamento').value;
        document.getElementById('box-troco').style.display = (forma === 'dinheiro') ? 'block' : 'none';
        document.getElementById('box-cartao').style.display = (forma === 'cartao_online') ? 'block' : 'none';
    }

    // MAGIA DO CARTÃO: IDENTIFICA A BANDEIRA
    document.getElementById('input-num').addEventListener('input', function(e) {
        let val = e.target.value.replace(/\D/g, ''); 
        
        let bandeira = 'PalazzoCard';
        if (val.match(/^4/)) { bandeira = 'VISA'; } 
        else if (val.match(/^(5[1-5]|2[2-7])/)) { bandeira = 'MASTERCARD'; } 
        else if (val.match(/^3[47]/)) { bandeira = 'AMEX'; } 
        else if (val.match(/^(4011|4312|4389|4514|4576|5041|5066|5090|6277|6362|6363|6504|6505|6509|6516|6550)/)) { bandeira = 'ELO'; } 
        else if (val.match(/^606282|^3841/)) { bandeira = 'HIPERCARD'; }

        document.querySelector('.card-logo').innerText = bandeira;

        let valFormatado = val.replace(/(\d{4})/g, '$1 ').trim();
        e.target.value = valFormatado;
        document.getElementById('vis-num').innerText = valFormatado || '#### #### #### ####';
    });

    document.getElementById('input-nome').addEventListener('input', function(e) {
        document.getElementById('vis-nome').innerText = e.target.value.toUpperCase() || 'NOME DO TITULAR';
    });

    document.getElementById('input-val').addEventListener('input', function(e) {
        let val = e.target.value.replace(/\D/g, '');
        if (val.length > 2) val = val.substring(0,2) + '/' + val.substring(2,4);
        e.target.value = val;
        document.getElementById('vis-val').innerText = val || 'MM/AA';
    });

    document.getElementById('input-cvv').addEventListener('input', function(e) {
        let val = e.target.value.replace(/\D/g, '');
        e.target.value = val;
        document.getElementById('vis-cvv').innerText = val;
    });

    document.getElementById('input-cvv').addEventListener('focus', () => {
        document.getElementById('card-inner').classList.add('is-flipped');
    });
    document.getElementById('input-cvv').addEventListener('blur', () => {
        document.getElementById('card-inner').classList.remove('is-flipped');
    });

    // ALGORITMO DE LUHN
    function validarCartaoLuhn(numero) {
        let soma = 0;
        let deveDobrar = false;
        
        for (let i = numero.length - 1; i >= 0; i--) {
            let digito = parseInt(numero.charAt(i));
            if (deveDobrar) {
                digito *= 2;
                if (digito > 9) digito -= 9;
            }
            soma += digito;
            deveDobrar = !deveDobrar;
        }
        return (soma % 10) == 0;
    }

    // BUSCA DE CEP VIA API
    function realizarBuscaCep() {
        let inputCep = document.getElementById('end-cep');
        let cep = inputCep.value.replace(/\D/g, ''); 
        if (cep !== "") {
            let validacep = /^[0-9]{8}$/;
            if(validacep.test(cep)) {
                document.getElementById('end-rua').value = "...";
                document.getElementById('end-bairro').value = "...";
                document.getElementById('end-cidade').value = "...";
                document.getElementById('end-uf').value = "...";

                fetch(`https://viacep.com.br/ws/${cep}/json/`)
                    .then(response => response.json())
                    .then(dados => {
                        if (!("erro" in dados)) {
                            document.getElementById('end-rua').value = dados.logradouro;
                            document.getElementById('end-bairro').value = dados.bairro;
                            document.getElementById('end-cidade').value = dados.localidade;
                            document.getElementById('end-uf').value = dados.uf;
                            document.getElementById('end-num').focus(); 
                        } else {
                            alert("CEP não encontrado."); limparCamposCep();
                        }
                    }).catch(() => { alert("Erro ao buscar o CEP."); limparCamposCep(); });
            } else { alert("Formato de CEP inválido."); }
        }
    }

    document.getElementById('end-cep').addEventListener('blur', realizarBuscaCep);
    document.getElementById('end-cep').addEventListener('keydown', function(event) {
        if (event.key === 'Enter') { event.preventDefault(); realizarBuscaCep(); }
    });

    function limparCamposCep() {
        document.getElementById('end-rua').value = ""; document.getElementById('end-bairro').value = "";
        document.getElementById('end-cidade').value = ""; document.getElementById('end-uf').value = "";
    }

    // FINALIZAR PEDIDO
    function enviarPedidoPHP() {
        if(carrinho.length === 0) return alert("O teu carrinho está vazio!");
        
        const tipoEntrega = document.getElementById('tipo-entrega').value;
        const cep = document.getElementById('end-cep').value;
        const rua = document.getElementById('end-rua').value;
        const num = document.getElementById('end-num').value;
        const bairro = document.getElementById('end-bairro').value;
        const cidade = document.getElementById('end-cidade').value;
        const uf = document.getElementById('end-uf').value;
        const comp = document.getElementById('end-comp').value;
        const formaPagamento = document.getElementById('forma-pagamento').value;
        const troco = document.getElementById('troco').value;
        let obs = document.getElementById('obs-pedido').value;

        if (tipoEntrega === 'delivery' && (!cep || !rua || !num || !bairro || !cidade)) {
            return alert("Por favor, preencha o CEP e o endereço completo para a entrega.");
        }

        if (formaPagamento === 'cartao_online') {
            const cNum = document.getElementById('input-num').value.replace(/\s/g, ''); 
            const cNome = document.getElementById('input-nome').value;
            const cVal = document.getElementById('input-val').value;
            const cCvv = document.getElementById('input-cvv').value;

            if(cNum.length < 13 || cNome.trim() === '' || cVal.length < 5 || cCvv.length < 3) {
                return alert("Por favor, preencha todos os dados do cartão de crédito.");
            }

            if (!validarCartaoLuhn(cNum)) {
                return alert("Oops! O número deste cartão de crédito é inválido. Verifique os dígitos.");
            }
        
            obs = `[PAGO ONLINE - FINAL ${cNum.slice(-4)}] ` + obs;
        }

        const enderecoCompleto = tipoEntrega === 'delivery' ? `${rua}, ${num} - ${bairro}, ${cidade}/${uf} (CEP: ${cep}) ${comp ? ' | Comp: '+comp : ''}` : 'Consumir no Local / Retirada';

        const btnFinalizar = document.getElementById('btn-finalizar');
        btnFinalizar.innerText = "Processando..."; btnFinalizar.disabled = true;

        const dadosPedido = { 
            itens: carrinho, 
            total: total, 
            tipo_entrega: tipoEntrega,
            endereco: enderecoCompleto,
            forma_pagamento: formaPagamento === 'cartao_online' ? 'cartao_credito' : formaPagamento,
            troco_para: (formaPagamento === 'dinheiro' && troco) ? parseFloat(troco) : 0,
            observacoes: obs 
        };

        fetch('finalizar_pedido.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(dadosPedido) })
        .then(response => response.json())
        .then(data => {
            if(data.sucesso) { 
                alert(`Sucesso! Pedido #${data.pedido} recebido.`); 
                carrinho = []; total = 0; 
                
                document.querySelectorAll('.checkout-input').forEach(input => {
                    if(input.tagName === 'INPUT' || input.tagName === 'TEXTAREA') input.value = '';
                });
                
                document.querySelector('.card-logo').innerText = 'PalazzoCard';
                document.getElementById('vis-num').innerText = '#### #### #### ####';
                document.getElementById('vis-nome').innerText = 'NOME DO TITULAR';
                document.getElementById('vis-val').innerText = 'MM/AA';
                document.getElementById('vis-cvv').innerText = '';

                atualizarInterface(); 
                fecharCarrinho(); 
            } 
            else { alert("Erro: " + data.erro); }
        })
        .catch(error => alert("Erro ao finalizar o pedido. Verifica a ligação ao servidor local."))
        .finally(() => { btnFinalizar.innerText = "Finalizar Pedido"; btnFinalizar.disabled = false; });
    }
</script>
</body>
</html>