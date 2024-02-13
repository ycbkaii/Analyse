DROP SCHEMA IF EXISTS projet_api_data CASCADE; 
CREATE SCHEMA projet_api_data;
SET SCHEMA 'projet_api_data';

-- CREATE TABLE _ville(
--     city VARCHAR,
--     city_ascii VARCHAR, --PRIMARY KEY ?
--     state_id VARCHAR,
--     state_name VARCHAR,
--     county_fips VARCHAR,
--     county_name VARCHAR,
--     lat FLOAT,
--     lng FLOAT,
--     population INTEGER,
--     density FLOAT, -- OR INTEGER ?
--     source VARCHAR, -- TODO REVOIR LE TYPE
--     millitary BOOLEAN,
--     incorporated BOOLEAN,
--     timezone VARCHAR, -- voir Tz_database
--     ranking INTEGER,
--     zips VARCHAR,
--     id INTEGER
    
-- );


CREATE TABLE pays_continent(
    continent VARCHAR,
    country VARCHAR,
    PRIMARY KEY (continent, country)
);

CREATE TABLE _ville(
    city VARCHAR,
    city_ascii VARCHAR, --PRIMARY KEY ?
    lat FLOAT,
    lng FLOAT,
    country VARCHAR,
    iso2 VARCHAR,
    iso3 VARCHAR,
    admin_name VARCHAR,
    capital VARCHAR,
    population VARCHAR,
    id INTEGER PRIMARY KEY,
    FOREIGN KEY (country) REFERENCES pays_continent(country)
    
);



-- PEUPLEMENT :
copy projet_api_data._ville FROM 'C:\Users\salim\Downloads\simplemaps_uscities_basicv1.78\uscities.csv' csv header;
copy projet_api_data.pays_continent FROM 'C:\Users\salim\Downloads\Countries-Continents.csv' csv header;


-- MISE EN PLACE DE LA COHÉRENCE DES DONNÉES ENTRE LES DEUX TABLES
--TODO Faire les updates sur les pays des villes

    -- Korea, South -> 
    UPDATE projet_api_data.pays_continent SET country = 'North Korea' WHERE country = 'Korea, North';
    UPDATE projet_api_data.pays_continent SET country = 'South Korea' WHERE country = 'Korea, South';
    UPDATE projet_api_data.pays_continent SET country = 'United States' WHERE country = 'US';
    UPDATE projet_api_data.pays_continent SET country = 'Congo (Brazzaville)' WHERE country = 'Congo';
    UPDATE projet_api_data.pays_continent SET country = 'Congo (Kinshasa)' WHERE country = 'Congo, Democratic Republic of';
    


-- VUE

CREATE OR REPLACE VIEW ville_pays_continent AS
SELECT * FROM projet_api_data._ville NATURAL JOIN projet_api_data.pays_continent
;


-- SCRIPT : 

-- 1 : 

    SELECT COUNT(city), country FROM ville_pays_continent GROUP BY (country);

-- 2 : 
    SELECT COUNT(country), continent FROM ville_pays_continent GROUP BY(continent);

-- 3 : 
    SELECT COUNT(city), continent FROM ville_pays_continent GROUP BY (continent);

-- 4 :

    SELECT MAX(COUNT(city)), country FROM  ville_pays_continent GROUP BY (country) LIMIT 1;

-- 5 : 

    SELECT AVG(city), continent FROM ville_pays_continent GROUP BY (continent);

-- 6 : 
    SELECT MAX(COUNT(country)), continent FROM ville_pays_continent GROUP BY(continent) LIMIT 1;


/*
Au niveau des problèmes :

Les noms des pays n'étaient pas noté de la même manière entre les tables différentes CSV (exemple : US dans pays_continent et United States dans _ville)
 
*/