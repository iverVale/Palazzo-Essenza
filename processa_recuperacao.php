<?php
session_start();
require 'conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $metodo = $_POST['metodo_envio']; 

    try {
        $stmt = $pdo->prepare("SELECT id, nome, telefone, status_conta FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && $usuario['status_conta'] === 'ativo') {
            
            $codigo_recuperacao = sprintf("%06d", mt_rand(1, 999999));
            
            $stmtUpdate = $pdo->prepare("UPDATE usuarios SET codigo_ativacao = ? WHERE email = ?");
            $stmtUpdate->execute([$codigo_recuperacao, $email]);

            $nome_primeiro = explode(' ', $usuario['nome'])[0];

            if ($metodo === 'email') {
                $assunto = "Recuperação de Senha - Palazzo Essenza";
                $corpo = "
                <div style='font-family: Arial, sans-serif; text-align: center; padding: 20px;'>
                    <h2 style='color: #8b6932;'>Recuperação de Senha</h2>
                    <p>Olá, $nome_primeiro!</p>
                    <p>Seu código para redefinir a senha é:</p>
                    <h1 style='font-size: 40px; letter-spacing: 5px; color: #333;'>$codigo_recuperacao</h1>
                    <p>Se você não solicitou isso, ignore este e-mail.</p>
                </div>";
            
                $url_google_script = "https://script.google.com/macros/s/AKfycbwr73-Fbps91Zejn4auUVFKt1_t_EEvkBlJKQwWlYCCQ-OZ3MT5F_T_3BkrhkZiPBqKtQ/exec";
                $dados_email = json_encode(["para" => $email, "assunto" => $assunto, "corpo" => $corpo]);
        
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => $url_google_script,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => $dados_email,
                    CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
                    CURLOPT_TIMEOUT => 5
                ));
                curl_exec($curl);
                curl_close($curl);
                
            } else if ($metodo === 'whatsapp') {
                $mensagem_wa = "🔑 *Palazzo Essenza - Recuperação de Senha* 🔑\n\n";
                $mensagem_wa .= "Olá, *$nome_primeiro*!\n";
                $mensagem_wa .= "Seu código para redefinir a senha é:\n";
                $mensagem_wa .= "👉 *$codigo_recuperacao* 👈\n\n";
                $mensagem_wa .= "Se você não solicitou isso, ignore esta mensagem.";
        
                $telefone_limpo = preg_replace('/[^0-9]/', '', $usuario['telefone']);
                if (substr($telefone_limpo, 0, 2) !== '55') { $telefone_limpo = '55' . $telefone_limpo; }
                $chat_id = $telefone_limpo . "@c.us";
        
                $idInstance = "710722704429"; 
                $apiTokenInstance = "d53ad8de9a0d49b39ccb9a99715ec2e0de9adb576c994935b7"; 
                $url_greenapi = "https://7107.api.greenapi.com/waInstance" . $idInstance . "/sendMessage/" . $apiTokenInstance;
        
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => $url_greenapi,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => json_encode(["chatId" => $chat_id, "message" => $mensagem_wa]),
                    CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
                    CURLOPT_TIMEOUT => 5
                ));
                curl_exec($curl);
                curl_close($curl);
            }

            $_SESSION['email_recuperacao'] = $email;
            header("Location: nova_senha.php");
            exit;

        } else {
            header("Location: recuperar_senha.php?erro=nao_encontrado");
            exit;
        }

    } catch (PDOException $e) {
        die("Erro: " . $e->getMessage());
    }
} else {
    header("Location: recuperar_senha.php");
    exit;
}
?>