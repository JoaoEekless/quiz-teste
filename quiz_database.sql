-- Banco de dados para o sistema de quiz
DROP DATABASE IF EXISTS quiz;
CREATE DATABASE quiz;
USE quiz;

-- Tabela de usuários
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nm_nome VARCHAR(100) NOT NULL,
    ds_email VARCHAR(100) NOT NULL UNIQUE,
    ds_password VARCHAR(255) NOT NULL,
    dt_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de temas
CREATE TABLE temas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL,
    descricao TEXT
);

-- Tabela de quizzes
CREATE TABLE quizzes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(200) NOT NULL,
    tema_id INT NOT NULL,
    FOREIGN KEY (tema_id) REFERENCES temas(id)
);

-- Tabela de perguntas
CREATE TABLE perguntas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    quiz_id INT NOT NULL,
    pergunta TEXT NOT NULL,
    alternativa_a VARCHAR(255) NOT NULL,
    alternativa_b VARCHAR(255) NOT NULL,
    alternativa_c VARCHAR(255) NOT NULL,
    resposta_correta CHAR(1) NOT NULL,
    pontos INT DEFAULT 10,
    FOREIGN KEY (quiz_id) REFERENCES quizzes(id)
);

-- Tabela de resultados
CREATE TABLE resultados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    quiz_id INT NOT NULL,
    pontuacao INT NOT NULL,
    data_quiz TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    FOREIGN KEY (quiz_id) REFERENCES quizzes(id)
);

-- Inserindo temas
INSERT INTO temas (nome, descricao) VALUES 
('Matemática', 'Questões básicas de matemática'),
('Geografia', 'Conhecimentos sobre países e capitais'),
('História', 'Eventos históricos importantes'),
('Conhecimentos Gerais', 'Questões diversas');

-- MATEMATICA - Quiz 1
INSERT INTO quizzes (titulo, tema_id) VALUES ('Quiz Básico de Matemática', 1);
INSERT INTO perguntas (quiz_id, pergunta, alternativa_a, alternativa_b, alternativa_c, resposta_correta, pontos) VALUES 
(1, 'Quanto é 1 + 1?', '3', '4', '2', 'c', 5),
(1, 'Quanto é 5 + 3?', '6', '8', '9', 'b', 8),
(1, 'Quanto é 10 + 10?', '20', '10', '15', 'a', 10),
(1, 'Quanto é 20 + 20?', '30', '40', '50', 'b', 6),
(1, 'Quanto é 50 + 75?', '125', '100', '150', 'a', 7),
(1, 'Quanto é 78 + 30?', '108', '100', '150', 'a', 4),
(1, 'Quanto é 70 ÷ 5?', '56', '15', '14', 'c', 3),
(1, 'Quanto é 100 - 50?', '60', '50', '40', 'b', 5);

-- GEOGRAFIA - Quiz 2
INSERT INTO quizzes (titulo, tema_id) VALUES ('Quiz de Geografia Mundial', 2);
INSERT INTO perguntas (quiz_id, pergunta, alternativa_a, alternativa_b, alternativa_c, resposta_correta, pontos) VALUES 
(2, 'Qual é a capital do Brasil?', 'São Paulo', 'Rio de Janeiro', 'Brasília', 'c', 8),
(2, 'Qual é o maior país do mundo?', 'China', 'Rússia', 'Estados Unidos', 'b', 10),
(2, 'Em que continente fica o Egito?', 'Ásia', 'África', 'Europa', 'b', 7),
(2, 'Qual é o rio mais longo do mundo?', 'Amazonas', 'Nilo', 'Mississippi', 'b', 9),
(2, 'Quantos continentes existem?', '5', '6', '7', 'c', 6),
(2, 'Qual é a capital da França?', 'Londres', 'Paris', 'Roma', 'b', 5),
(2, 'Qual país tem formato de bota?', 'Espanha', 'Itália', 'Grécia', 'b', 8),
(2, 'Qual é o menor país do mundo?', 'Mônaco', 'Vaticano', 'San Marino', 'b', 10);

-- HISTORIA - Quiz 3
INSERT INTO quizzes (titulo, tema_id) VALUES ('Quiz de História Geral', 3);
INSERT INTO perguntas (quiz_id, pergunta, alternativa_a, alternativa_b, alternativa_c, resposta_correta, pontos) VALUES 
(3, 'Em que ano o Brasil foi descoberto?', '1500', '1498', '1502', 'a', 7),
(3, 'Quem foi o primeiro presidente do Brasil?', 'Getúlio Vargas', 'Deodoro da Fonseca', 'Juscelino Kubitschek', 'b', 9),
(3, 'Em que ano terminou a Segunda Guerra?', '1944', '1945', '1946', 'b', 8),
(3, 'Qual civilização construiu as pirâmides?', 'Romanos', 'Gregos', 'Egípcios', 'c', 6),
(3, 'Em que século viveu Leonardo da Vinci?', 'Século XIV', 'Século XV', 'Século XVI', 'b', 10),
(3, 'Quem descobriu o Brasil?', 'Cristóvão Colombo', 'Pedro Álvares Cabral', 'Vasco da Gama', 'b', 5),
(3, 'Em que ano foi a Independência do Brasil?', '1820', '1822', '1824', 'b', 7),
(3, 'Qual foi a primeira capital do Brasil?', 'Rio de Janeiro', 'Salvador', 'São Paulo', 'b', 8);

-- CONHECIMENTOS GERAIS - Quiz 4
INSERT INTO quizzes (titulo, tema_id) VALUES ('Quiz de Conhecimentos Gerais', 4);
INSERT INTO perguntas (quiz_id, pergunta, alternativa_a, alternativa_b, alternativa_c, resposta_correta, pontos) VALUES 
(4, 'Quantos dias tem um ano bissexto?', '365', '366', '367', 'b', 6),
(4, 'Qual planeta mais próximo do Sol?', 'Vênus', 'Mercúrio', 'Terra', 'b', 8),
(4, 'Quantas cores tem o arco-íris?', '6', '7', '8', 'b', 7),
(4, 'Qual é o maior mamífero do mundo?', 'Elefante', 'Baleia-azul', 'Girafa', 'b', 9),
(4, 'Em que estação as folhas caem?', 'Inverno', 'Outono', 'Primavera', 'b', 5),
(4, 'Quantos ossos tem o corpo humano?', '206', '208', '210', 'a', 10),
(4, 'Qual é o maior órgão do corpo?', 'Fígado', 'Pulmão', 'Pele', 'c', 8),
(4, 'Quantos minutos tem uma hora?', '50', '60', '70', 'b', 4);

-- MATEMATICA AVANÇADA - Quiz 5
INSERT INTO quizzes (titulo, tema_id) VALUES ('Quiz Avançado de Matemática', 1);
INSERT INTO perguntas (quiz_id, pergunta, alternativa_a, alternativa_b, alternativa_c, resposta_correta, pontos) VALUES 
(5, 'Quanto é 15 × 8?', '120', '125', '130', 'a', 8),
(5, 'Qual é a raiz quadrada de 64?', '6', '8', '10', 'b', 10),
(5, 'Quanto é 144 ÷ 12?', '11', '12', '13', 'b', 7),
(5, 'Quanto é 25% de 200?', '50', '75', '100', 'a', 9),
(5, 'Qual é o resultado de 2³?', '6', '8', '9', 'b', 10),
(5, 'Quanto é 0,5 + 0,25?', '0,75', '0,80', '0,85', 'a', 6),
(5, 'Soma dos ângulos de um triângulo?', '180°', '360°', '90°', 'a', 12),
(5, 'Quanto é 7 × 9?', '63', '65', '72', 'a', 5);

-- GEOGRAFIA DO BRASIL - Quiz 6
INSERT INTO quizzes (titulo, tema_id) VALUES ('Quiz de Geografia do Brasil', 2);
INSERT INTO perguntas (quiz_id, pergunta, alternativa_a, alternativa_b, alternativa_c, resposta_correta, pontos) VALUES 
(6, 'Qual é o maior estado do Brasil?', 'Amazonas', 'Bahia', 'Minas Gerais', 'a', 8),
(6, 'Região mais populosa do Brasil?', 'Norte', 'Sudeste', 'Sul', 'b', 7),
(6, 'Em que estado fica Joinville?', 'Paraná', 'Santa Catarina', 'Rio Grande do Sul', 'b', 6),
(6, 'Rio que corta São Paulo?', 'Rio Tietê', 'Rio Pinheiros', 'Rio Tamanduateí', 'a', 9),
(6, 'Quantos estados tem o Brasil?', '25', '26', '27', 'b', 5),
(6, 'Qual é a maior cidade do Brasil?', 'Rio de Janeiro', 'São Paulo', 'Brasília', 'b', 6),
(6, 'Em que região fica o Pantanal?', 'Norte', 'Centro-Oeste', 'Sul', 'b', 10),
(6, 'Estado que faz fronteira com mais países?', 'Amazonas', 'Acre', 'Roraima', 'a', 12);