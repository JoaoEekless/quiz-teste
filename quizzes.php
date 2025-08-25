<?php
session_start();
require_once 'conexao.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.html");
    exit();
}

// Verifica se foi passado um tema
if (!isset($_GET['tema']) || !is_numeric($_GET['tema'])) {
    header("Location: dashboard.php");
    exit();
}

$tema_id = (int)$_GET['tema'];

// Busca informações do tema
$stmt = $pdo->prepare("SELECT * FROM temas WHERE id = ?");
$stmt->execute([$tema_id]);
$tema = $stmt->fetch();

if (!$tema) {
    header("Location: dashboard.php");
    exit();
}

// Busca quizzes do tema
$stmt = $pdo->prepare("
    SELECT 
        q.*,
        COUNT(p.id) as total_perguntas
    FROM quizzes q
    LEFT JOIN perguntas p ON q.id = p.quiz_id
    WHERE q.tema_id = ?
    GROUP BY q.id
    ORDER BY q.titulo
");
$stmt->execute([$tema_id]);
$quizzes = $stmt->fetchAll();

// Busca melhores pontuações do usuário
$stmt = $pdo->prepare("
    SELECT 
        quiz_id,
        MAX(pontuacao) as melhor_pontuacao
    FROM resultados 
    WHERE usuario_id = ?
    GROUP BY quiz_id
");
$stmt->execute([$_SESSION['usuario_id']]);
$melhores_pontuacoes = [];
while ($row = $stmt->fetch()) {
    $melhores_pontuacoes[$row['quiz_id']] = $row['melhor_pontuacao'];
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quizify - <?= htmlspecialchars($tema['nome']) ?></title>
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="icon" type="image/svg+xml" href="img/cerebro1.svg">
    <style>
        .quiz-item {
            background-color: white;
            padding: 20px;
            margin-bottom: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .quiz-info h3 {
            color: white;
            margin-bottom: 5px;
        }
        
        .quiz-info p {
            color: #666;
            font-size: 14px;
        }
      
        .quiz-actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        .best-score {
            background-color: #f8f9fa;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            color: #666;
        }
        
        .back-btn {
            background-color: #74b9ff;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            margin-bottom: 20px;
            display: inline-block;
        }
        
        .back-btn:hover {
            background-color: #0984e3;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-content">
            <a href="dashboard.php" class="logo"> Quizify</a>
            <div class="user-menu">
                <span>Tema: <?= htmlspecialchars($tema['nome']) ?></span>
                <a href="logout.php" class="logout-btn">Sair</a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="container">
        <a href="dashboard.php" class="back-btn">← Voltar ao Dashboard</a>
        
        <div class="welcome-box">
            <h1><?= htmlspecialchars($tema['nome']) ?></h1>
            <p><?= htmlspecialchars($tema['descricao']) ?></p>
        </div>

        <h2 class="section-title">Quizzes Disponíveis</h2>
        
        <?php if (!empty($quizzes)): ?>
            <?php foreach ($quizzes as $quiz): ?>
            <div class="quiz-item">
                <div class="quiz-info">
                    <h3><?= htmlspecialchars($quiz['titulo']) ?></h3>
                    <p><?= $quiz['total_perguntas'] ?> pergunta(s)</p>
                </div>
                <div class="quiz-actions">
                    <?php if (isset($melhores_pontuacoes[$quiz['id']])): ?>
                        <div class="best-score">
                            Melhor: <?= $melhores_pontuacoes[$quiz['id']] ?> pts
                        </div>
                    <?php endif; ?>
                    <a href="jogar-quiz.php?id=<?= $quiz['id'] ?>" class="btn-quiz">Jogar</a>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-results">Nenhum quiz disponível neste tema ainda.</div>
        <?php endif; ?>
    </div>
</body>
</html>