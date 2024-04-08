<?php

/**
 * @version 1.0
 * @author Yanis Chiouar, Baron Noah
 * @api dblp.org
 */
/*
require_once "./connexionDb.php";
global $conn;

//Script de création de BDD pour stocker les articles
$createDb = "
DROP SCHEMA IF EXISTS projet_api_data CASCADE; 
CREATE SCHEMA projet_api_data;
SET SCHEMA 'projet_api_data';


CREATE TABLE _conf(
    idComptage INT PRIMARY KEY,
    title VARCHAR,
    acronym VARCHAR,
    source VARCHAR,
    rank VARCHAR,
    note VARCHAR,
    primary_for INT DEFAULT NULL
);

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
    volume INT,
    idComptage INT DEFAULT NULL,
    FOREIGN KEY (idComptage) REFERENCES _conf(idComptage)
);

CREATE TABLE _authors(
    pid VARCHAR PRIMARY KEY,
    name VARCHAR,
    --affiliation_name VARCHAR,
    rank INT, -- Valeur calculée, sans doute un trigger
    nombre_art INT DEFAULT 0
);

CREATE TABLE _author_article(
    pid VARCHAR,
    iddblp INT,
    PRIMARY KEY (pid, iddblp),
    FOREIGN KEY (pid) REFERENCES _authors(pid),
    FOREIGN KEY (iddblp) REFERENCES _article(iddblp)
);



-- A faire dans le terminal psql
--\COPY projet_api_data._conf FROM '/home/bkaii/Documents/COURS_PORTATIFS/S4/Analyse/TD1/RecupData/CORE.csv' csv header;




-- fonctione pour calculer points des rangs des articles et update directement (l'annee precedent la notre) : 

CREATE OR REPLACE FUNCTION update_author_rank() RETURNS TRIGGER AS $$
BEGIN
    UPDATE projet_api_data._authors AS a
    SET rank = subquery.author_score
    FROM (
        SELECT aa.pid, SUM(
            CASE
                WHEN c.rank = 'A*' THEN 11
                WHEN c.rank = 'A+' THEN 11
                WHEN c.rank = 'A' THEN 8
                WHEN c.rank = 'B*' THEN 6
                WHEN c.rank = 'B+' THEN 6
                WHEN c.rank = 'B' THEN 4
                WHEN c.rank = 'C*' THEN 3
                WHEN c.rank = 'C+' THEN 3
                WHEN c.rank = 'C' THEN 2
                ELSE 1
            END
        ) AS author_score
        FROM projet_api_data._author_article AS aa
        INNER JOIN projet_api_data._article AS ar ON aa.iddblp = ar.iddblp
        INNER JOIN projet_api_data._conf AS c ON ar.idComptage = c.idComptage
        GROUP BY aa.pid
    ) AS subquery
    WHERE a.pid = subquery.pid
    AND a.pid = NEW.pid;
    
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER update_author_rank_trigger
AFTER INSERT ON projet_api_data._author_article
FOR EACH ROW
EXECUTE FUNCTION update_author_rank();




";

//TODO Faire copy des confs dans la table _conf et avec l'api dblp prendre tous les articles de conferences

$result = pg_query($conn, $createDb);


if(!$result){
    die("Erreur lors de la création de la base de données, vérifiez si la base de données PG est co : ".pg_last_error());
}

*/

?>