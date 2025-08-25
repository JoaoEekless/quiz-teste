<?php
session_start();
require_once 'conexao.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.html");
    exit();
}

// Busca ranking geral (soma de pontos por usuário)
$stmt = $pdo->prepare("
    SELECT 
        u.nm_nome,
        SUM(r.pontuacao) as pontos_totais,
        COUNT(r.id) as total_quizzes,
        MAX(r.data_quiz) as ultimo_quiz
    FROM usuarios u
    LEFT JOIN resultados r ON u.id = r.usuario_id
    GROUP BY u.id, u.nm_nome
    HAVING pontos_totais > 0
    ORDER BY pontos_totais DESC
    LIMIT 20
");
$stmt->execute();
$ranking = $stmt->fetchAll();

// Busca posição do usuário atual
$stmt = $pdo->prepare("
    SELECT 
        COUNT(*) + 1 as posicao
    FROM (
        SELECT 
            SUM(r.pontuacao) as pontos_totais
        FROM usuarios u
        LEFT JOIN resultados r ON u.id = r.usuario_id
        WHERE u.id != ?
        GROUP BY u.id
        HAVING pontos_totais > (
            SELECT IFNULL(SUM(pontuacao), 0) 
            FROM resultados 
            WHERE usuario_id = ?
        )
    ) as ranking_acima
");
$stmt->execute([$_SESSION['usuario_id'], $_SESSION['usuario_id']]);
$minha_posicao = $stmt->fetchColumn();

// Busca pontos do usuário atual
$stmt = $pdo->prepare("SELECT IFNULL(SUM(pontuacao), 0) as meus_pontos FROM resultados WHERE usuario_id = ?");
$stmt->execute([$_SESSION['usuario_id']]);
$meus_pontos = $stmt->fetchColumn();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quizify - Ranking</title>
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="icon" type="image/svg+xml" href="img/cerebro1.svg">
    <style>
        .ranking-table {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .ranking-table table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .ranking-table th {
            background-color: #6c5ce7;
            color: white;
            padding: 15px;
            text-align: left;
            font-weight: 600;
        }
        
        .ranking-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
        }
        
        .ranking-table tr:hover {
            background-color: #f8f9fa;
        }
        
        .posicao {
            font-weight: bold;
            color: #6c5ce7;
            width: 60px;
            text-align: center;
        }
        
        .medal {
            font-size: 20px;
        }
        
        .minha-posicao {
            background-color: #fff3cd;
            border: 2px solid #ffeaa7;
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
        
        .minha-info {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            text-align: center;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="dashboard.php" class="logo"> Quizify</a>
            <div class="user-menu">
                <span>Ranking Geral</span>
                <a href="logout.php" class="logout-btn">Sair</a>
            </div>
        </div>
    </header>

    <div class="container">
        <a href="dashboard.php" class="back-btn">← Voltar ao Dashboard</a>
        
        <div class="minha-info">
            <h2>Sua Posição</h2>
            <p>Você está em <strong><?= $minha_posicao ?>º lugar</strong> com <strong><?= $meus_pontos ?> pontos</strong></p>
        </div>

        <h2 class="section-title">Ranking dos Jogadores</h2>
        
        <div class="ranking-table">
            <table>
                <thead>
                    <tr>
                        <th>Posição</th>
                        <th>Jogador</th>
                        <th>Pontos Totais</th>
                        <th>Quizzes Realizados</th>
                        <th>Último Quiz</th>
                    </tr>
                </thead>

            </table>
        </div>
    </div>
</body>
</html>