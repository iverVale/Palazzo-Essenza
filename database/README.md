# 🗄️ Palazzo Essenza - Banco de Dados

Este diretório contém os scripts de criação de tabelas e carga inicial de dados para o sistema **Palazzo Essenza**.

---

## 📁 Arquivos

- **`palazzo_db.sql`**: Dump SQL contendo a estrutura de tabelas, índices e dados pré-cadastrados (categorias, produtos, usuários de teste e histórico).

---

## 🛠️ Como Importar no MySQL / MariaDB

### Opção 1: Via phpMyAdmin (XAMPP / cPanel / InfinityFree)
1. Abra o **phpMyAdmin** no seu navegador (`http://localhost/phpmyadmin`).
2. Crie um novo banco de dados com codificação `utf8mb4_unicode_ci` (exemplo: `palazzo_db`).
3. Selecione o banco de dados recém-criado na lista lateral.
4. Clique na aba superior **"Importar"** (*Import*).
5. Clique em **"Escolher arquivo"** e selecione o arquivo `palazzo_db.sql`.
6. Role até o fim e clique no botão **"Executar"** (*Go*).

---

### Opção 2: Via Linha de Comando (Terminal / MySQL CLI)
Execute os comandos abaixo no seu terminal:

```bash
# 1. Acessar o cliente MySQL e criar a base de dados
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS palazzo_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 2. Importar o arquivo SQL diretamente
mysql -u root -p palazzo_db < palazzo_db.sql
```

---

## 📋 Estrutura das Tabelas

| Tabela | Descrição |
| :--- | :--- |
| **`usuarios`** | Dados cadastrais dos clientes e administradores, incluindo senhas criptografadas (BCRYPT), telefone e foto de perfil. |
| **`categorias`** | Categorias de pratos: Entradas Clássicas, Pratos Principais, Sobremesas e Bebidas. |
| **`produtos`** | Itens do cardápio com preços, descrições detalhadas e disponibilidade. |
| **`pedidos`** | Histórico de compras com valores totais, status (`preparando`, `enviado`, `finalizado`), troco e datas. |
| **`itens_pedido`** | Composição detalhada de cada item do pedido, incluindo ingredientes e montagens de massas personalizadas. |
| **`reservas`** | Agendamento de mesas com data, hora, número de convidados, método de pagamento e observações. |
