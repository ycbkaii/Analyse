//TODO faire en csv la bdd

# Guide d'utilisation

Ce guide vous aidera à mettre en place et à utiliser le projet PHP sur votre environnement local.

## Étape 1 : Clonage du projet

1. Ouvrez un terminal et exécutez la commande suivante pour cloner le projet depuis le dépôt Git :
   ```bash
   git clone https://github.com/ycbkaii/Analyse.git
   ```

## Étape 2 : Lancement de la base de données

1. Assurez-vous d'avoir un serveur PostgreSQL installé et en cours d'exécution sur votre machine.
2. Lancez votre serveur PostgreSQL sur `Windows`:

```bash
   pg_ctl -D "C:\Program Files\PostgreSQL\votre_version\data" start
```

   Assurez-vous de remplacer `C:\Program Files\PostgreSQL\votre_version\data` par le chemin correspondant à votre dossier data dans PostgreSQL.

## Étape 3 : Lancement du serveur PHP

1. Assurez-vous d'avoir PHP installé sur votre machine.

2. Accédez au répertoire du projet cloné :

   ```bash
   cd Analyse
   ```
3. Dans le répertoire, lancez le serveur PHP en utilisant la commande suivante :

   ```bash
   php -S localhost:8000
   ```

   Cette commande démarre un serveur de développement sur le port 8000.

## Étape 4 : Initialisation et peuplement de la Base de données

### Initialisation :

1. Pour initialiser la base de données Ouvrez votre navigateur Web et accédez à l'URL suivante :

   ```
   http://localhost:8000/initDb.php
   ```

   si il ne s'affiche rien c'est que tout s'est bien déroulé, cette page va en php créer les tables de la bdd.

---

### Peuplement :

1. Importez les données nécessaires à partir du fichier CSV dans la base de données. Utilisez la commande SQL suivante dans votre interface PostgreSQL :

   ```sql
   \COPY projet_api_data._conf FROM 'chemin/absolu/vers/votre/fichier/CORE.csv' CSV HEADER;
   \COPY projet_api_data._article FROM 'chemin/absolu/vers/votre/fichier/article.csv' CSV HEADER;
   \COPY projet_api_data._authors FROM 'chemin/absolu/vers/votre/fichier/authors.csv' CSV HEADER;
   \COPY projet_api_data.author_article FROM 'chemin/absolu/vers/votre/fichier/author_article.csv' CSV HEADER;
   ```
   Assurez-vous de remplacer `chemin/absolu/vers/votre/fichier/...` par le chemin absolu correspondant à l'emplacement de vos fichiers CSV sur votre serveur.
