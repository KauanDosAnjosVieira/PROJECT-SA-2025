-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 29/10/2024 às 17:02
-- Versão do servidor: 8.3.0
-- Versão do PHP: 8.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: bd_imagem
--
CREATE DATABASE IF NOT EXISTS bd_imagem DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE bd_imagem;

-- --------------------------------------------------------

--
-- Estrutura para tabela funcionarios
--

DROP TABLE IF EXISTS funcionarios;
CREATE TABLE IF NOT EXISTS funcionarios (
  id int NOT NULL AUTO_INCREMENT,
  nome varchar(255) NOT NULL,
  telefone varchar(255) DEFAULT NULL,
  nome_foto varchar(255) DEFAULT NULL,
  tipo_foto varchar(255) DEFAULT NULL,
  foto longblob,
  PRIMARY KEY (id)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela imagens
--

DROP TABLE IF EXISTS imagens;
CREATE TABLE IF NOT EXISTS imagens (
  id int NOT NULL AUTO_INCREMENT,
  nome_imagem varchar(255) NOT NULL,
  tipo_imagem varchar(50) DEFAULT NULL,
  imagem longblob,
  PRIMARY KEY (id)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;