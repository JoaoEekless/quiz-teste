<?php
session_start();
require_once 'conexao.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.html");
    exit();
}

// Busca informações do usuário
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$_SESSION['usuario_id']]);
$usuario = $stmt->fetch();

// Busca estatísticas do usuário
$stmt = $pdo->prepare("
    SELECT 
        COUNT(*) as total_quizzes,
        IFNULL(SUM(pontuacao), 0) as pontos_totais
    FROM resultados 
    WHERE usuario_id = ?
");
$stmt->execute([$_SESSION['usuario_id']]);
$stats = $stmt->fetch();

// Busca todos os temas
$stmt = $pdo->prepare("
    SELECT 
        t.*,
        COUNT(q.id) as total_quizzes
    FROM temas t
    LEFT JOIN quizzes q ON t.id = q.tema_id
    GROUP BY t.id
    ORDER BY t.nome
");
$stmt->execute();
$temas = $stmt->fetchAll();

// Busca últimos resultados
$stmt = $pdo->prepare("
    SELECT 
        q.titulo,
        r.pontuacao,
        r.data_quiz
    FROM resultados r
    JOIN quizzes q ON r.quiz_id = q.id
    WHERE r.usuario_id = ?
    ORDER BY r.data_quiz DESC
    LIMIT 5
");
$stmt->execute([$_SESSION['usuario_id']]);
$ultimos_resultados = $stmt->fetchAll();
?>



<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quizify - Dashboard</title>
    <link rel ="stylesheet" href="css/dashboard.css">
    <link rel="icon" type="image/svg+xml" href="img/cerebro1.svg">
</head>
<body>

    <!-- Cabeçalho da página -->
    <header class="header">
        <div class="header-content">
            <a href="dashboard.php" class="logo"> Quizify</a>
            <div class="user-menu">
                <span>Olá, <?= htmlspecialchars($usuario['nm_nome']) ?>!</span>
                <a href="logout.php" class="logout-btn">Sair</a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="container">
        <!-- O bem vindo -->
        <div class="welcome-box">
            <h1>Bem-vindo ao Quizify!</h1>
            <p>Teste seus conhecimentos e divirta-se aprendendo</p>
        </div>

        <!-- Sessão dos status do jogador -->
        <div class="stats">
            <div class="stat-card">
                <div class="stat-number"> 
                    <?= $stats['pontos_totais'] ?> </div>
                <div class="stat-label">Pontos Totais</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"> 
                    <?= $stats['total_quizzes'] ?> </div>
                <div class="stat-label">Quizzes Realizados</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"> 
                    <?= count($temas) ?> </div>
                <div class="stat-label">Temas Disponíveis</div>
            </div>
        </div>


        <!-- Sessão dos temas -->
        <h2 class="section-title">Escolha um Tema</h2>
        <div class="temas-grid">
            <?php foreach ($temas as $tema): ?>
            <div class="tema-card" onclick="location.href='quizzes.php?tema=<?= $tema['id'] ?>'">
                <h3><?= htmlspecialchars($tema['nome']) ?></h3>
                <p><?= htmlspecialchars($tema['descricao']) ?></p>
                <p style="font-size: 12px; color: #999;"><?= $tema['total_quizzes'] ?> quiz(zes) disponível(eis)</p>
                <a href="quizzes.php?tema=<?= $tema['id'] ?>" class="btn-quiz">Jogar</a>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Resultados Recentes -->
        <h2 class="section-title">Resultados Recentes</h2>
        <div class="recent-results">
            <?php if (!empty($ultimos_resultados)): ?>
                <?php foreach ($ultimos_resultados as $resultado): ?>
                <div class="result-item">
                    <div class="result-info">
                        <h4><?= htmlspecialchars($resultado['titulo']) ?></h4>
                        <p><?= date('d/m/Y H:i', strtotime($resultado['data_quiz'])) ?></p>
                    </div>
                    <div class="result-score"><?= $resultado['pontuacao'] ?> pts</div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-results">Nenhum quiz realizado ainda. Que tal começar agora?</div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>