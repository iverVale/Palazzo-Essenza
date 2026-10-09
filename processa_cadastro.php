<?php
// Mostra erros na tela para facilitar a descoberta de problemas (se houver)
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require 'conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $telefone = trim($_POST['telefone']);
    $senha = $_POST['senha'];

    $is_admin_logado = (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1);
    $is_email_admin = (strpos(strtolower($email), 'admin') !== false);

    if (!$is_admin_logado && !$is_email_admin) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            header("Location: telacadastro.php?erro=email_invalido");
            exit;
        }
        $dominio = substr(strrchr($email, "@"), 1);
      
        if (!checkdnsrr($dominio, "MX")) {
            header("Location: telacadastro.php?erro=email_falso");
            exit;
        }
    }

    try {
        $stmtCheck = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmtCheck->execute([$email]);
        if ($stmtCheck->rowCount() > 0) {
            header("Location: telacadastro.php?erro=email_existe");
            exit;
        }
   
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
        
        $codigo_email = sprintf("%06d", mt_rand(1, 999999)); 
        $codigo_whatsapp = sprintf("%06d", mt_rand(1, 999999)); 
        
        $status_inicial = ($is_admin_logado || $is_email_admin) ? 'ativo' : 'pendente';

        // SALVA APENAS O CÓDIGO DO E-MAIL NO BANCO (Para caber no VARCHAR original)
        $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, telefone, senha, codigo_ativacao, status_conta) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nome, $email, $telefone, $senha_hash, $codigo_email, $status_inicial]);
    
        if ($status_inicial === 'ativo') {
            header("Location: login.php?cadastro=sucesso");
            exit;
        }

        // ==========================================
        // 1. ENVIO VIA E-MAIL
        // ==========================================
        $assunto_email = "Código de Ativação - Palazzo Essenza";
        $corpo_email = "
        <div style='font-family: Arial, sans-serif; text-align: center; padding: 20px;'>
            <h2 style='color: #8b6932;'>Bem-vindo(a) ao Palazzo Essenza!</h2>
            <p>Olá, $nome! Falta pouco para concluir seu cadastro.</p>
            <p>Seu código de ativação por <b>E-MAIL</b> é:</p>
            <h1 style='font-size: 40px; letter-spacing: 5px; color: #333;'>$codigo_email</h1>
            <p>Copie este código e cole na tela do site junto com o código do WhatsApp para ativar sua conta.</p>
        </div>";
    
        $url_google_script = "https://script.google.com/macros/s/AKfycbwr73-Fbps91Zejn4auUVFKt1_t_EEvkBlJKQwWlYCCQ-OZ3MT5F_T_3BkrhkZiPBqKtQ/exec";

        $dados_email = json_encode([
            "para" => $email,
            "assunto" => $assunto_email,
            "corpo" => $corpo_email
        ]);

        $curl_email = curl_init();
        curl_setopt_array($curl_email, array(
            CURLOPT_URL => $url_google_script,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $dados_email,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
            CURLOPT_TIMEOUT => 5
        ));
        curl_exec($curl_email);
        curl_close($curl_email);
        
        // ==========================================
        // 2. ENVIO VIA WHATSAPP (GREEN API)
        // ==========================================
        $mensagem_wa = "🍝 *Palazzo Essenza* 🍝\n\n";
        $mensagem_wa .= "Olá, *" . explode(' ', $nome)[0] . "*! Falta pouco para concluir seu cadastro.\n\n";
        $mensagem_wa .= "Seu código de ativação do *WhatsApp* é:\n";
        $mensagem_wa .= "👉 *$codigo_whatsapp* 👈\n\n";
        $mensagem_wa .= "Digite este código junto com o código enviado por e-mail na tela do site para ativar sua conta.";

        $telefone_limpo = preg_replace('/[^0-9]/', '', $telefone);
        if (substr($telefone_limpo, 0, 2) !== '55') {
            $telefone_limpo = '55' . $telefone_limpo;
        }
        $chat_id = $telefone_limpo . "@c.us";

        $idInstance = "710722704429"; 
        $apiTokenInstance = "d53ad8de9a0d49b39ccb9a99715ec2e0de9adb576c994935b7"; 
        $url_greenapi = "https://7107.api.greenapi.com/waInstance" . $idInstance . "/sendMessage/" . $apiTokenInstance;

        $dados_greenapi = ["chatId" => $chat_id, "message" => $mensagem_wa];

        $curl_greenapi = curl_init();
        curl_setopt_array($curl_greenapi, array(
            CURLOPT_URL => $url_greenapi,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 5,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($dados_greenapi),
            CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
        ));
        curl_exec($curl_greenapi);
        curl_close($curl_greenapi);

        // ==========================================
        // REDIRECIONAMENTO FINAL
        // ==========================================
        $_SESSION['email_ativacao'] = $email;
        $_SESSION['wa_ativacao'] = $codigo_whatsapp; // SALVA O CÓDIGO DO WA NA MEMÓRIA DA SESSÃO
        header("Location: ativar_conta.php");
        exit;

    } catch (PDOException $e) {
        die("Erro ao cadastrar: " . $e->getMessage());
    }
} else {
    header("Location: telacadastro.php");
    exit;
}
?>