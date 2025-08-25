<?php
session_start();
include 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['login'] ?? '';
    $senha = $_POST['ds_password'] ?? '';

    if (empty($login) || empty($senha)) {
        echo "❌ Por favor, preencha todos os campos.";
        exit;
    }

    // Query que busca pelo nome ou email
    $query = "SELECT * FROM usuarios WHERE (nm_nome = :login OR ds_email = :login)";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':login', $login);
    $stmt->execute();

    $usuario = $stmt->fetch();

    if ($usuario && password_verify($senha, $usuario['ds_password'])) {
        // Login bem-sucedido
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario'] = $usuario['nm_nome'];
        header("Location: dashboard.php");
        exit();
    } else {
        echo "❌ Nome de usuário ou senha incorretos.";
    }
} else {
    // Se não é POST, redireciona para a página de login
    header("Location: login.html");
    exit();
}
?>