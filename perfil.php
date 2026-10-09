<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require 'conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$id_usuario = $_SESSION['usuario_id'];
$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $telefone = trim($_POST['telefone']);
    $nova_senha = $_POST['nova_senha'];
    
    $stmtFoto = $pdo->prepare("SELECT foto_perfil FROM usuarios WHERE id = ?");
    $stmtFoto->execute([$id_usuario]);
    $dados_foto_binario = $stmtFoto->fetchColumn();

    if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] == 0) {
        $extensao = strtolower(pathinfo($_FILES['foto_perfil']['name'], PATHINFO_EXTENSION));
        $extensoes_permitidas = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (in_array($extensao, $extensoes_permitidas)) {
            $dados_foto_binario = file_get_contents($_FILES['foto_perfil']['tmp_name']);
        } else {
            $mensagem = "<div class='mensagem-erro'>Formato de imagem inválido. Use JPG ou PNG.</div>";
        }
    }

    try {
        if (!empty($nova_senha)) {
            $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
            $stmtUpdate = $pdo->prepare("UPDATE usuarios SET nome=?, email=?, telefone=?, senha=?, foto_perfil=? WHERE id=?");
            $stmtUpdate->execute([$nome, $email, $telefone, $senha_hash, $dados_foto_binario, $id_usuario]);
        } else {
            $stmtUpdate = $pdo->prepare("UPDATE usuarios SET nome=?, email=?, telefone=?, foto_perfil=? WHERE id=?");
            $stmtUpdate->execute([$nome, $email, $telefone, $dados_foto_binario, $id_usuario]);
        }

        $_SESSION['usuario_nome'] = $nome;
        $_SESSION['usuario_email'] = $email;
        $_SESSION['usuario_telefone'] = $telefone;

        if (!empty($dados_foto_binario)) {
            $_SESSION['usuario_foto'] = 'data:image/jpeg;base64,' . base64_encode($dados_foto_binario);
        }

        if (empty($mensagem)) {
            $mensagem = "<div class='mensagem-sucesso'>Perfil atualizado com sucesso!</div>";
        }

    } catch (PDOException $e) {
        $mensagem = "<div class='mensagem-erro'>Erro ao atualizar: " . $e->getMessage() . "</div>";
    }
}

$stmt = $pdo->prepare("SELECT nome, email, telefone, foto_perfil FROM usuarios WHERE id = ?");
$stmt->execute([$id_usuario]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!empty($usuario['foto_perfil'])) {
    $foto_exibicao = 'data:image/jpeg;base64,' . base64_encode($usuario['foto_perfil']);
} else {
    $foto_exibicao = 'https://via.placeholder.com/150?text=Sem+Foto';
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="shortcut icon" href="logoP.ico" type="image/x-icon">
<title>Palazzo Essenza | Meu Perfil</title>
<link rel="stylesheet" href="reserva.css"> 
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
<style>
    .perfil-foto-container { text-align: center; margin-bottom: 20px; }
    .perfil-foto { width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 3px solid var(--accent-color, #8b6932); margin-bottom: 10px; }
    .file-input { display: block; margin: 0 auto; font-family: 'Poppins', sans-serif; font-size: 14px; }
    .aviso-senha { font-size: 12px; color: #666; margin-top: -10px; margin-bottom: 15px; display: block; }
</style>
</head>
<body>
<div class="container">
    <div class="header">
        <div class="logo">Palazzo Essenza</div>
        <div class="subtitle">Meu Perfil</div>
    </div>
    
    <?= $mensagem ?>
    
    <form action="perfil.php" method="POST" enctype="multipart/form-data">
        <div class="perfil-foto-container">
            <img src="<?= $foto_exibicao ?>" alt="Foto de Perfil" class="perfil-foto">
            <input type="file" name="foto_perfil" class="file-input" accept="image/png, image/jpeg">
        </div>

        <div class="input-group">
            <label>Nome Completo</label>
            <input type="text" name="nome" value="<?= htmlspecialchars($usuario['nome']) ?>" required>
        </div>
        
        <div class="row">
            <div class="input-group">
                <label>E-mail</label>
                <input type="email" name="email" value="<?= htmlspecialchars($usuario['email']) ?>" required>
            </div>
            <div class="input-group">
                <label>Telefone (WhatsApp)</label>
                <input type="text" name="telefone" value="<?= htmlspecialchars($usuario['telefone']) ?>" required>
            </div>
        </div>
        
        <div class="input-group">
            <label>Nova Senha</label>
            <input type="password" name="nova_senha" placeholder="••••••••">
            <span class="aviso-senha"><br>Deixe em branco se não quiser alterar a senha.</span>
        </div>
        
        <button type="submit" class="btn-submit">Salvar Alterações</button>
    </form>
    
    <a href="index.php" class="voltar">← Voltar para a Tela Inicial</a>
</div>
<script>
    const body = document.body;
    const savedTheme = localStorage.getItem('palazzo_theme');
    if (savedTheme === 'dark') { body.classList.add('dark-theme'); } 
    else { body.classList.remove('dark-theme'); }
</script>
</body>
</html>