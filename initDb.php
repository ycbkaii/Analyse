<?php

/**
 * @version 1.0
 * @author Yanis Chiouar, Baron Noah
 * @api dblp.org
 */

require_once "./connexionDb.php";
global $conn;

//Script de création de BDD pour stocker les articles
$createDb = "
DROP SCHEMA IF EXISTS projet_api_data CASCADE; 
CREATE SCHEMA projet_api_data;
SET SCHEMA 'projet_api_data';




CREATE TABLE _article(
    iddblp INT PRIMARY KEY,
    type VARCHAR,
    doi VARCHAR,
    title VARCHAR,
    venue VARCHAR,
    annee INT,
    pages VARCHAR,
    ee VARCHAR,
    url_dblp VARCHAR,
    numero INT,
    volume INT
);


CREATE TABLE _conf(
    
);





";

//TODO FAIRE LA BDD REVUE, ARTICLE, CONFERENCES (faire un autre input que pour les confs)

$result = pg_query($conn, $createDb);

if(!$result){
    die("Erreur lors de la création de la base de données, vérifiez si la base de données PG est co : ".pg_last_error());
}



?>