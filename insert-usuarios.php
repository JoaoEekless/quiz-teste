<?php
require_once "conexao.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {  
        $nome = $_POST["nm_nome"] ?? '';
        $email = $_POST["ds_email"] ?? '';
        $senha = $_POST["ds_password"] ?? '';
        
        // Validações básicas
        if (empty($nome)) {
            throw new Exception("Nome não pode ser vazio.");
        }
        
        if (empty($email)) {
            throw new Exception("E-mail não pode ser vazio.");
        }
        
        if (empty($senha)) {
            throw new Exception("Senha não pode ser vazia.");
        }
        
        if (strlen($senha) < 6) {
            throw new Exception("Senha deve ter pelo menos 6 caracteres.");
        }

        // Verifica se email já existe
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE ds_email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            throw new Exception("Este e-mail já está cadastrado.");
        }

        // Hash da senha
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

        // Inserção no banco de dados
        $sql = "INSERT INTO usuarios (nm_nome, ds_email, ds_password) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nome, $email, $senha_hash]);

      header("Location: dashboard.php");
        exit();
        
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) { // Duplicate entry
            echo "❌ Este e-mail já está cadastrado.";
        } else {
            echo "❌ Erro ao cadastrar: " . $e->getMessage();
        }
    } catch (Exception $e) {
        echo "❌ Erro: " . $e->getMessage();
    }
} else {
    echo "❌ Erro no envio do formulário.";
}
?>