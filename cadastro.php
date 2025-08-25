<?php session_start(); ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link rel="stylesheet" href="css/registro.css" />
  <link rel="icon" type="image/svg+xml" href="img/cerebro1.svg" />
  <title>Cadastro - Quizify</title>
</head>

<body>
  <div class="main-login">
    <div class="brand">Quizify</div>

    <h1>Crie sua conta</h1>
    <p class="subtitle">Vamos começar sua jornada no quiz!</p>

    <!-- MENSAGEM -->
    <?php
    if (isset($_SESSION['mensagem'])) {
      echo $_SESSION['mensagem'];
      unset($_SESSION['mensagem']);
    }
    ?>

    <form class="card-login" action="insert-usuarios.php" method="post">
      <div class="textfield">
        <label for="nm_nome">Nome:</label>
        <input type="text" id="nm_nome" name="nm_nome" placeholder="Nome para seu user" required />
      </div>

      <div class="textfield">
        <label for="ds_email">E-mail:</label>
        <input type="email" id="ds_email" name="ds_email" placeholder="email@exemplo.com" required />
      </div>

      <div class="textfield">
        <label for="ds_password">Senha:</label>
        <input type="password" id="ds_password" name="ds_password" placeholder="••••••••••" required />
      </div>

      <button class="btn-login" type="submit">Cadastrar</button>

      <div class="signup-link">
        Já tem uma conta? <a href="login.html">Faça login</a>
      </div>
    </form>
  </div>
</body>
</html>