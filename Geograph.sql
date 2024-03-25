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
    city_ascii VARCHAR, 
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
    INSERT INTO projet_api_data.pays_continent VALUES ('Asie', 'Taiwan');
    INSERT INTO projet_api_data.pays_continent VALUES ('Asie', 'Hong Kong');
    UPDATE projet_api_data.pays_continent SET country = 'Myanmar' WHERE country = 'Burma (Myanmar)';
    INSERT INTO projet_api_data.pays_continent VALUES ('Africa', 'Côte d''Ivoire');
    UPDATE projet_api_data.pays_continent SET country = 'Burkina Faso' WHERE country = 'Burkina';
    INSERT INTO projet_api_data.pays_continent VALUES ('North America', 'Puerto Rico');
    INSERT INTO projet_api_data.pays_continent VALUES ('Europe', 'Czechia');
    INSERT INTO projet_api_data.pays_continent VALUES ('Europe', 'Gaza Strip');
    INSERT INTO projet_api_data.pays_continent VALUES ('Africa', 'The Gambia');
    INSERT INTO projet_api_data.pays_continent VALUES ('Africa', 'Reunion');
    UPDATE projet_api_data.pays_continent SET country = 'The Bahamas' WHERE country = 'Bahamas';
    INSERT INTO projet_api_data.pays_continent VALUES ('North America', 'Martinique');
    INSERT INTO projet_api_data.pays_continent VALUES ('Asie', 'Timor-Leste');
    INSERT INTO projet_api_data.pays_continent VALUES ('Europe', 'Gibraltar');
    INSERT INTO projet_api_data.pays_continent VALUES ('Oceania', 'New Caledonia');
    INSERT INTO projet_api_data.pays_continent VALUES ('Europe', 'Kosovo');
    INSERT INTO projet_api_data.pays_continent VALUES ('South America', 'Curaçao');
    INSERT INTO projet_api_data.pays_continent VALUES ('Oceania', 'French Polynesia');
    UPDATE projet_api_data.pays_continent SET country = 'Cabo Verde' WHERE country = 'Cape Verde';
    INSERT INTO projet_api_data.pays_continent VALUES ('South America', 'French Guiana');
    INSERT INTO projet_api_data.pays_continent VALUES ('South America', 'Aruba');
    INSERT INTO projet_api_data.pays_continent VALUES ('Europe', 'Jersey');
    INSERT INTO projet_api_data.pays_continent VALUES ('Europe', 'Mayotte');
    INSERT INTO projet_api_data.pays_continent VALUES ('Europe', 'Isle Of Man');
    INSERT INTO projet_api_data.pays_continent VALUES ('North America', 'Cayman Islands');
    INSERT INTO projet_api_data.pays_continent VALUES ('North America', 'Greenland');
    INSERT INTO projet_api_data.pays_continent VALUES ('Europe', 'Faroe Islands');
    INSERT INTO projet_api_data.pays_continent VALUES ('North America', 'British Virgin Islands');
    UPDATE projet_api_data.pays_continent SET country = 'American Samoa' WHERE country = 'Samoa';
    INSERT INTO projet_api_data.pays_continent VALUES ('Oceania', 'Samoa');
    INSERT INTO projet_api_data.pays_continent VALUES ('North America', 'Turks and Caicos Islands');
    INSERT INTO projet_api_data.pays_continent VALUES ('North America', 'Saint Martin');
    INSERT INTO projet_api_data.pays_continent VALUES ('Oceania', 'Federated States of Micronesia');
    INSERT INTO projet_api_data.pays_continent VALUES ('Oceania', 'Cook Islands');
    INSERT INTO projet_api_data.pays_continent VALUES ('North America', 'Sint Maarten');
    INSERT INTO projet_api_data.pays_continent VALUES ('Oceania', 'Northern Mariana Islands');
    INSERT INTO projet_api_data.pays_continent VALUES ('South America', 'Falkland Islands (Islas Malvinas)');
    INSERT INTO projet_api_data.pays_continent VALUES ('North America', 'Bermuda');
    INSERT INTO projet_api_data.pays_continent VALUES ('Oceania', 'Niue');
    INSERT INTO projet_api_data.pays_continent VALUES ('North America', 'Guadeloupe');
    INSERT INTO projet_api_data.pays_continent VALUES ('Oceania', 'Guam');
    INSERT INTO projet_api_data.pays_continent VALUES ('Africa', 'Saint Helena, Ascension, and Tristan da Cunh');
    INSERT INTO projet_api_data.pays_continent VALUES ('Africa', 'Saint Helena, Ascension, and Tristan da Cunha');
    INSERT INTO projet_api_data.pays_continent VALUES ('North America', 'Montserrat');
    INSERT INTO projet_api_data.pays_continent VALUES ('North America', 'Saint Barthelemy');
    INSERT INTO projet_api_data.pays_continent VALUES ('North America', 'Saint Pierre and Miquelon')
    INSERT INTO projet_api_data.pays_continent VALUES ('North America', 'Anguilla');
    INSERT INTO projet_api_data.pays_continent VALUES ('Oceania', 'Wallis and Futuna');
    INSERT INTO projet_api_data.pays_continent VALUES ('Oceania', 'Norfolk Island');
    INSERT INTO projet_api_data.pays_continent VALUES ('Europe', 'Svalbard');
    INSERT INTO projet_api_data.pays_continent VALUES ('Oceania', 'Pitcairn Islands');
    INSERT INTO projet_api_data.pays_continent VALUES ('Asie', 'Christmas Island');
    INSERT INTO projet_api_data.pays_continent VALUES ('Antarctica', 'South Georgia And South Sandwich Islands');
    INSERT INTO projet_api_data.pays_continent VALUES ('Asie', 'Macau');
    INSERT INTO projet_api_data.pays_continent VALUES ('Cisjordanie', 'West Bank');
    INSERT INTO projet_api_data.pays_continent VALUES ('North America', 'Bonaire, Sint Eustatius, and Saba');
    INSERT INTO projet_api_data.pays_continent VALUES ('North America', 'U.S. Virgin Islands');
    INSERT INTO projet_api_data.pays_continent VALUES ('Antarctica', 'South Georgia and South Sandwich Islands');




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