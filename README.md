# <p align="center"><img src="logo.png" alt="Palazzo Essenza Logo" width="180"></p>

<h1 align="center">Palazzo Essenza</h1>

<p align="center">
  <b>Ecossistema Digital Gastronômico de Alta Cucina Italiana</b><br>
  Plataforma Web Full-Stack com Sistema de Pedidos, Reservas, Painel Administrativo e Aplicativo Móvel Integrado.
</p>

<p align="center">
  <img src="https://img.shields.io/badge/PHP-8.x-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8">
  <img src="https://img.shields.io/badge/MySQL-Database-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
  <img src="https://img.shields.io/badge/JavaScript-ES6+-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black" alt="JavaScript">
  <img src="https://img.shields.io/badge/CSS3-Glassmorphism-1572B6?style=for-the-badge&logo=css3&logoColor=white" alt="CSS3">
  <img src="https://img.shields.io/badge/Android-Kotlin-3DDC84?style=for-the-badge&logo=android&logoColor=white" alt="Android">
  <img src="https://img.shields.io/badge/WhatsApp_API-Green_API-25D366?style=for-the-badge&logo=whatsapp&logoColor=white" alt="WhatsApp">
</p>

---

## 📌 Sumário

1. [Sobre o Projeto](#-sobre-o-projeto)
2. [Principais Funcionalidades](#-principais-funcionalidades)
3. [Design System & Experiência Visual (UX/UI)](#-design-system--experiência-visual-uxui)
4. [Estrutura do Repositório](#-estrutura-do-repositório)
5. [Arquitetura & Engenharia de Software](#-arquitetura--engenharia-de-software)
6. [Modelo do Banco de Dados](#-modelo-do-banco-de-dados)
7. [Integrações e APIs Externas](#-integrações-e-apis-externas)
8. [Como Executar o Projeto Localmente](#-como-executar-o-projeto-localmente)
9. [Segurança e Boas Práticas](#-segurança-e-boas-práticas)
10. [Licença e Autoria](#-licença-e-autoria)

---

## 🍽️ Sobre o Projeto

O **Palazzo Essenza** é um ecossistema digital desenvolvido para proporcionar uma experiência gastronômica premium inspirada na autêntica tradição italiana (*Alta Cucina Italiana*). A solução conecta clientes e a cozinha através de uma arquitetura omnichannel integrada:

- **Plataforma Web Completa:** Desenvolvida em **PHP 8+** e **MySQL**, com interface moderna em **HTML5**, **CSS3 (Glassmorphism)** e **JavaScript Vanilla**, permitindo cardápio digital interativo, personalização de massas artesanais, carrinho com checkout inteligente e gestão de reservas.
- **Aplicativo Móvel Android:** Desenvolvido em **Kotlin** e **Jetpack Compose** utilizando uma *Arquitetura Híbrida Centrada no Servidor (Server-Centric Hybrid)*, garantindo sincronização instantânea de dados, splash screen nativa com animação luminosa e tratamento de conectividade offline.
- **Painel Administrativo:** Interface em tempo real para controle da cozinha e despacho de pedidos, disparando mensagens automáticas para o cliente via WhatsApp e E-mail a cada mudança de status.

---

## ✨ Principais Funcionalidades

### 🍝 Cardápio Digital & "Monte sua Massa" (`cardapio.php` / `cardapio.css`)
- **Navegação Categorizada:** Entradas Clássicas, Pratos Principais, Sobremesas, Sucos e Bebidas Especiais.
- **Customização de Pratos:** Seção interativa onde o cliente escolhe tipos de massa (Espaguete, Penne, Fusilli), molhos artesanais e adicionais (parmesão, bacon crocante, cogumelos frescos, frango em cubos, etc.), com cálculo dinâmico de valor.
- **Modal de Seleção de Bebidas:** Seleção rápida e responsiva de opções adicionais (como sabores e tamanhos de sucos naturais).

### 🛒 Carrinho Dinâmico & Checkout Inteligente
- **Blindagem Contra Scroll Vazado:** Drawer lateral flutuante com `overscroll-behavior: none` e `-webkit-overflow-scrolling: touch`, garantindo rolagem 100% contínua e sem travamento em dispositivos móveis.
- **Preenchimento Automático de Endereço (ViaCEP):** Ao digitar o CEP na modalidade delivery, o sistema preenche logradouro, bairro, cidade e estado em tempo real.
- **Cartão de Crédito 3D Interativo:** 
  - Animação em perspectiva 3D que gira o cartão (*card flip*) ao preencher o código de segurança (CVV).
  - Reconhecimento dinâmico da bandeira (*Visa, Mastercard, Amex, Elo, Hipercard*) via Regex.
  - Validação matemática de integridade através do **Algoritmo de Luhn (Módulo 10)** antes do envio.
- **Suporte a Múltiplos Pagamentos:** Pix, Cartão de Crédito, Cartão de Débito e Dinheiro (com cálculo automático de troco).

### 📅 Reserva de Mesas (`reserva.php` / `reserva.css`)
- Sistema inteligente de agendamento validando:
  - Limite de até 10 pessoas por mesa.
  - Restrição de reservas apenas para o mês corrente.
  - Verificação de horário de funcionamento do restaurante (18:00 às 00:30).
- Confirmação automática via WhatsApp com os dados da reserva.

### 📦 Rastreamento de Pedidos (`meus_pedidos.php` / `meus_pedidos.css`)
- Listagem detalhada dos pedidos do cliente com identificador único (`#ID`).
- **Badges de Status Dinâmicos:**
  - 🟡 **Preparando:** Pedido recebido e em preparo pelo chef.
  - 🔵 **Enviado:** Saiu para rota de entrega.
  - 🟢 **Finalizado:** Pedido entregue ou retirado com sucesso.
- Histórico de itens, observações, troco e opção de cancelamento de reservas com aviso imediato à administração.

### 👨‍🍳 Painel Administrativo (`admin.php` / `admin.css`)
- Visão geral de todos os pedidos ativos e reservas pendentes.
- Atualização em um clique do status do pedido (`Preparando` ➡️ `Enviado` ➡️ `Finalizado`).
- Despacho automático de notificações no WhatsApp do cliente e e-mail transacional a cada atualização.
- Função de impressão de comanda de pedidos para a cozinha (`imprimir_pedido.php`).

---

## 🎨 Design System & Experiência Visual (UX/UI)

O design foi construído seguindo diretrizes de sofisticação, combinando tons clássicos de ouro e champanhe com a profundidade do tema escuro:

| Elemento | Tema Claro (*Light*) | Tema Escuro (*Dark*) |
| :--- | :--- | :--- |
| **Cor Primária / Destaque** | `#8b6932` (Ouro Envelhecido) | `#e6c97a` / `#c6a75e` (Dourado Nobre) |
| **Fundo Principal** | `#ffffff` / Gradiente translúcido | `#121212` (Preto Profundo) |
| **Tipografia Títulos** | *Playfair Display* (Serifada elegante) | *Playfair Display* (Serifada elegante) |
| **Tipografia Corpo** | *Poppins* (Moderna e legível) | *Poppins* (Moderna e legível) |
| **Superfícies & Cards** | Glassmorphism (`backdrop-filter: blur(15px)`) | Vidro escurecido com bordas sutis |

- **Persistência de Tema:** A preferência de tema é gravada no `localStorage` sob a chave `palazzo_theme`, garantindo consistência em todas as páginas e navegações.
- **Botão de Download Interativo (`.btn-download-app`):** Efeito de hover suave onde o ícone de download expande elegantemente sobre toda a extensão do botão.
- **Botões Flutuantes (FABs):** Acesso rápido aos canais oficiais de atendimento (WhatsApp e E-mail).

---

## 📂 Estrutura do Repositório

```plaintext
palazzo/
├── logo.png                # Identidade visual / Logo oficial em alta resolução
├── logoP.ico               # Favicon oficial do restaurante
│
├── index.php               # Página inicial / Landing Page de boas-vindas
├── index.css               # Estilos da Home, Hero Section, Nav e Botão de App
│
├── cardapio.php            # Cardápio completo, montagem de massas e checkout
├── cardapio.css            # Estilos do cardápio, modal de sucos e carrinho de compras
│
├── login.php               # Interface de autenticação de usuários
├── login.css               # Estilos em glassmorphism da tela de login
├── telacadastro.php        # Formulário de criação de conta
├── telacadastro.css        # Estilos da tela de cadastro de novos clientes
│
├── meus_pedidos.php        # Histórico de pedidos e acompanhamento em tempo real
├── meus_pedidos.css        # Estilos dos cards de pedidos e badges de status
│
├── reserva.php             # Agendamento e reserva de mesas
├── reserva.css             # Estilos do formulário de reservas
│
├── perfil.php              # Edição de perfil do usuário e upload de avatar
├── admin.php               # Painel gerencial e despacho de pedidos
├── admin.css               # Estilos do dashboard administrativo
│
├── conexao.php             # Conexão com banco de dados MySQL via PDO
├── finalizar_pedido.php    # Processamento de compras, transações e disparo de APIs
├── confirmar_entrega.php   # Confirmação do recebimento pelo cliente
├── imprimir_pedido.php     # Layout para impressão térmica de comanda
│
├── processa_login.php      # Lógica de login e validação BCRYPT
├── processa_cadastro.php   # Cadastro, hash de senha e geração de tokens 2FA
├── ativar_conta.php        # Validação do código de ativação em duas etapas
├── recuperar_senha.php     # Solicitação de recuperação de senha
├── nova_senha.php          # Redefinição de senha com token
│
└── src/                    # Biblioteca PHPMailer para envio de e-mails transacionais
    ├── PHPMailer.php
    ├── SMTP.php
    └── Exception.php
```

---

## 🏛️ Arquitetura & Engenharia de Software

```mermaid
graph TD
    UserWeb[🌐 Cliente Web - Navegador] -->|Requisições HTTP/HTTPS| WebServer[🖥️ Servidor Apache / PHP 8+]
    UserApp[📱 Cliente Móvel - App Android] -->|WebView Integrado| WebServer

    WebServer -->|PDO Prepared Statements| DB[(🗄️ MySQL Database)]
    WebServer -->|JSON API / cURL| GreenAPI[💬 Green API / WhatsApp]
    WebServer -->|JSON API / cURL| MailService[📧 Google Script / PHPMailer]
    WebServer -->|REST GET| ViaCEP[📍 ViaCEP API]

    Admin[👨‍🍳 Painel Admin] -->|Atualiza Status do Pedido| WebServer
    WebServer -->|Disparo Automático de Alertas| GreenAPI
    GreenAPI -->|Mensagem no WhatsApp| UserWeb
```

### 📱 Aplicativo Android Híbrido (*Server-Centric*)
- **Tecnologias:** Kotlin, Jetpack Compose, Material Design 3.
- **Splash Screen com Efeito Breathing:** Transição visual suave com animação luminosa de pulso antes de carregar a tela principal.
- **Tratamento de Queda de Conexão:** Módulo `isNetworkAvailable` que exibe tela offline nativa amigável com botão de "Tentar Novamente".
- **Intercepção de Esquemas Externos (`shouldOverrideUrlLoading`):** Suporte nativo para abrir discador (`tel:`), WhatsApp (`whatsapp://`) ou cliente de e-mail (`mailto:`).
- **Gestão de Histórico (`BackHandler`):** O botão voltar físico/gesto do Android navega pelas páginas internas da aplicação antes de sair.

---

## 🗄️ Modelo do Banco de Dados

O banco de dados relacional é estruturado em tabelas otimizadas com suporte a integridade referencial:

- **`usuarios`**: Controle de usuários, contendo `nome`, `email`, `senha` (hash BCRYPT), `telefone`, `foto_perfil` (MEDIUMBLOB), `codigo_ativacao` (2FA) e flag `is_admin`.
- **`categorias`**: Separação dos pratos (`Entradas Clássicas`, `Pratos Principais`, `Sobremesas`, `Bebidas`).
- **`produtos`**: Pratos e bebidas cadastrados com nome, descrição, valor e status de disponibilidade.
- **`pedidos`**: Registro mestre de compras com `usuario_id`, `total`, `status` (`preparando`, `enviado`, `finalizado`), `troco_para` e data de criação.
- **`itens_pedido`**: Composição detalhada de cada prato, incluindo montagens especiais e ingredientes adicionais escolhidos.
- **`reservas`**: Dados de agendamento de mesa, horário, quantidade de pessoas, observações e status.

---

## 🔌 Integrações e APIs Externas

1. **WhatsApp Notifier (Green API / Periskope):**
   - Disparo automático de mensagens a cada alteração no ciclo do pedido:
     - 👨‍🍳 *Pedido Confirmado e Sendo Preparado*
     - 🛵 *Saiu para Entrega (com instrução de confirmação)*
     - ✅ *Pedido Finalizado com Sucesso*
2. **Google Apps Script / PHPMailer:**
   - Envio de comprovantes em HTML estilizado com resumo da compra e avisos de cancelamento de reservas para os administradores.
3. **ViaCEP API:**
   - Consulta assíncrona de endereços brasileiros via CEP sem necessidade de recarregar a página.

---

## 🚀 Como Executar o Projeto Localmente

### Pré-requisitos
- **PHP 8.0** ou superior instalado
- **MySQL / MariaDB**
- Servidor Web local (**XAMPP**, **WampServer**, **Laragon** ou servidor embutido do PHP)
- Extensões PHP habilitadas: `pdo_mysql`, `curl`, `mbstring`, `openssl`

### Passo a Passo

1. **Clonar o Repositório:**
   ```bash
   git clone https://github.com/seu-usuario/palazzo-essenza.git
   cd palazzo-essenza
   ```

2. **Configurar o Banco de Dados:**
   - Crie uma base de dados no seu gerenciador MySQL (ex: `palazzo_db`).
   - Importe o script SQL disponível com as tabelas e dados iniciais.
   - Ajuste as credenciais no arquivo `conexao.php`:
     ```php
     $host = 'localhost';
     $dbname = 'palazzo_db';
     $user = 'root';
     $pass = '';
     ```

3. **Iniciar o Servidor:**
   - Se estiver usando o **XAMPP**, mova a pasta do projeto para `htdocs` e acesse no navegador:
     ```plaintext
     http://localhost/palazzo-essenza/
     ```
   - Ou utilize o servidor embutido do próprio PHP:
     ```bash
     php -S localhost:8000
     ```
     E acesse: `http://localhost:8000`

---

## 🛡️ Segurança e Boas Práticas

- **Prevenção contra SQL Injection:** 100% das operações de banco utilizam o driver PDO com *Prepared Statements* e parâmetros vinculados.
- **Criptografia Forte de Senhas:** Nenhuma senha em texto plano. Utilização de `password_hash()` com algoritmo `PASSWORD_DEFAULT` (BCRYPT) e verificação com `password_verify()` protegendo contra *timing attacks*.
- **Controle de Sessão e Rotas Protegidas:** Verificação de autenticação no início de páginas restritas (`$_SESSION['usuario_id']` e `$_SESSION['is_admin']`).
- **Timeouts Controlados:** Chamadas cURL com timeout fixado em 5 segundos para impedir que lentidões em APIs externas congelem a aplicação.

---

## 👥 Licença e Autoria

Desenvolvido com carinho e dedicação para proporcionar o melhor da tecnologia e gastronomia italiana.

<p align="center">
  <b>Buon Appetito! 🍷🍝</b><br>
  <i>Palazzo Essenza © Todos os direitos reservados.</i>
</p>
