<?php
require_once "./connexionDb.php";
global $conn;


// Récupère les 15 auteurs avec le plus d'article
$queryAuteursMustArticle = "SELECT name, nombre_art FROM projet_api_data._authors ORDER BY nombre_art DESC LIMIT 15";
$resultAuteursMustArticle= pg_query($conn,$queryAuteursMustArticle);
$dataAuteursMustArticle = [];

while($row = pg_fetch_assoc($resultAuteursMustArticle)){
    array_push($dataAuteursMustArticle,$row);
}

// Récupère les 15 auteurs avec le plus haut rang
$queryAuteursMustRanking = "SELECT name, rank FROM projet_api_data._authors ORDER BY rank DESC LIMIT 15";
$resultAuteursMustRanking= pg_query($conn,$queryAuteursMustRanking);
$dataAuteursMustRanking = [];

while($row = pg_fetch_assoc($resultAuteursMustRanking)){
    array_push($dataAuteursMustRanking,$row);
}

//On récupere articles d'auteurs solo
$queryAuteurSolo = "SELECT c.rank, COUNT(*) AS nb_total
FROM projet_api_data._authors a
JOIN (
    SELECT aa.iddblp
    FROM projet_api_data._author_article aa
    GROUP BY aa.iddblp
    HAVING COUNT(aa.pid) = 1
) AS solo_articles ON solo_articles.iddblp IN (
    SELECT aa2.iddblp
    FROM projet_api_data._author_article aa2
    WHERE aa2.pid = a.pid
)
JOIN projet_api_data._author_article aa3 ON a.pid = aa3.pid AND solo_articles.iddblp = aa3.iddblp
JOIN projet_api_data._article art ON aa3.iddblp = art.iddblp
JOIN projet_api_data._conf c ON art.idcomptage = c.idcomptage
GROUP BY c.rank
ORDER BY c.rank ASC
";
$resultAuteurSolo = pg_query($conn, $queryAuteurSolo);
$dataAuteurSolo = [];
while($row = pg_fetch_assoc($resultAuteurSolo)){
    array_push($dataAuteurSolo, $row);
}


//On récupere articles d'auteurs en groupe
$queryAuteurGroupe = "SELECT c.rank, COUNT(*) AS nb_total
FROM projet_api_data._authors a
JOIN (
    SELECT aa.iddblp
    FROM projet_api_data._author_article aa
    GROUP BY aa.iddblp
    HAVING COUNT(aa.pid) > 1
) AS solo_articles ON solo_articles.iddblp IN (
    SELECT aa2.iddblp
    FROM projet_api_data._author_article aa2
    WHERE aa2.pid = a.pid
)
JOIN projet_api_data._author_article aa3 ON a.pid = aa3.pid AND solo_articles.iddblp = aa3.iddblp
JOIN projet_api_data._article art ON aa3.iddblp = art.iddblp
JOIN projet_api_data._conf c ON art.idcomptage = c.idcomptage
GROUP BY c.rank
ORDER BY c.rank ASC
";
$resultAuteurGroupe = pg_query($conn, $queryAuteurGroupe);
$dataAuteurGroupe = [];
while($row = pg_fetch_assoc($resultAuteurGroupe)){
    array_push($dataAuteurGroupe, $row);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notre Analyse</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 1em;
        }

        h1 {
            text-align: center;
            margin-top: 20px;
        }

        h2 {
            color: #333;
            font-size: 24px;
            margin-top: 30px;
        }

        p {
            color: #666;
            font-size: 18px;
            margin-bottom: 20px;
        }

        ul {
            list-style-type: none;
            padding: 0;
        }

        li {
            margin-bottom: 10px;
        }


        .button {
        background-color: #4CAF50; 
        border: none; 
        color: white; 
        padding: 15px 32px; 
        text-align: center; 
        text-decoration: none; 
        display: inline-block; 
        font-size: 16px; 
        margin: 4px 2px; 
        cursor: pointer; 
        border-radius: 8px; 
        transition-duration: 0.4s; 
        }


        .button:hover {
        background-color: #45a049; 
        color: white; 
        }

        #myChart_1{
            width: 100% !important;
            height: 100% !important;
        }
    </style>
</head>
<body>
<form action="conf.php">
    <button class='button' type='button' onclick="window.location.href='index.php'">Études</button>
    <button class='button' type='submit'>Conférences</button>
    <button class='button' type='button' onclick="window.location.href='notreAnalyse.php'" style='color:blue;'>Notre Analyse</button>
</form>
<div style='width:70%; margin:0 auto;'>
    <h1>Analyse de l'Influence des Groupes de Chercheurs sur l'Acceptation des Sujets de Conférence</h1>

    <h2>Introduction :</h2>
    <p>Dans cette analyse, nous nous pencherons sur l'impact de la présence d'un groupe de chercheurs sur l'acceptation des sujets présentés lors de conférences bien classées. Notre objectif est d'évaluer si les groupes de chercheurs ont tendance à bénéficier d'un accès accru à des conférences de rang supérieur, comparativement aux chercheurs travaillant de manière individuelle.</p>

    <h2>Contexte :</h2>
    <p>Les conférences académiques jouent un rôle crucial dans la diffusion des nouvelles idées et des avancées dans divers domaines de la recherche. Cependant, l'acceptation des sujets présentés lors de ces conférences peut varier en fonction de plusieurs facteurs, notamment la qualité de la recherche et la réputation des chercheurs.</p>

    <h2>Hypothèse :</h2>
    <p>Nous supposons que la présence d'un groupe de chercheurs pourrait influencer de manière significative l'acceptation des sujets de conférence. Les groupes de chercheurs peuvent potentiellement bénéficier d'un réseau plus étendu, de ressources partagées et d'une diversité d'expertise, ce qui pourrait leur donner un avantage dans l'obtention d'acceptations à des conférences bien classées.</p>

    <h2>Méthodologie :</h2>
    <p>Nous utiliserons des données recueillies auprès de plusieurs conférences académiques bien classées dans différents domaines de recherche. Ces données comprendront des informations sur les sujets présentés, les chercheurs impliqués (en tant qu'individus ou groupes), ainsi que les décisions d'acceptation ou de rejet des sujets.</p>
    <p>Nous utiliserons des techniques d'analyse statistique pour examiner les tendances et les corrélations entre la présence de groupes de chercheurs et l'acceptation des sujets de conférence. Des tests de significativité seront effectués pour déterminer si les différences observées sont statistiquement pertinentes.</p>

    <h2>Discussion et Implications :</h2>
    <p>Les résultats de cette analyse pourraient avoir des implications importantes pour la façon dont les chercheurs collaborent et présentent leur travail dans le milieu académique. Si la présence de groupes de chercheurs est effectivement associée à un taux d'acceptation plus élevé, cela pourrait encourager davantage de collaborations et de regroupements au sein de la communauté scientifique. En revanche, si aucune corrélation significative n'est observée, cela soulèverait des questions sur l'équité et l'accessibilité des conférences académiques pour les chercheurs travaillant de manière individuelle.</p>

    <h2>Conclusion :</h2>
    <p>Cette analyse vise à éclairer le débat sur l'influence des groupes de chercheurs sur l'acceptation des sujets de conférence. En examinant les données recueillies, nous espérons mieux comprendre les dynamiques qui sous-tendent le processus d'acceptation des sujets de recherche et contribuer ainsi à une discussion plus approfondie sur les pratiques académiques et la collaboration scientifique.</p>

    <h2 id="technologies-utilis%C3%A9es">Technologies utilisées</h2>
    <ul>
    <li>PostGreSQL (SGBD)</li>
    <li>PHP (Analyse de données et affichage)</li>
    <li>API : dblp.org, portal.core.edu.au</li>
    <li>JavaScript (Analyse de données)</li>
    </ul>
</div>

<!-- Les 15 auteurs qui ont le plus d'articles -->
<h3>Nombre d'articles par auteur</h3> 
<canvas id="myChart_1"></canvas>

<h3>15 meilleurs auteurs</h3>
<canvas id="myChart_2"></canvas>

<h3>Comparaison entre le nombre d'articles produits en groupe d'auteurs et le nombre d'articles produits par un seul auteur dans une conférence de haut niveau</h3>
<canvas id="myChart"></canvas>


    <script>
        // Données à afficher sur le graphe
        var data = {
            labels: [<?php 
            foreach($dataAuteursMustArticle as $auteur){
                echo '"'.$auteur['name'].'", ';
            }
            
            
            ?>],
            datasets: [{
                label: "Nombre d'articles par auteur",
                data: [
                    <?php
                    foreach($dataAuteursMustArticle as $auteur){
                        echo '"'.$auteur['nombre_art'].'", ';
                    }
                    ?>
                ],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)'
                ],
                borderWidth: 1
            }]
        };

        // Options du graphe
        var options = {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        };

        // Création du graphe
        var ctx = document.getElementById('myChart_1').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: data,
            options: options
        });
    </script>

<script>
        // Données à afficher sur le graphe
        var data = {
            labels: [<?php 
            foreach($dataAuteursMustRanking as $auteur){
                echo '"'.$auteur['name'].'", ';
            }
            
            
            ?>],
            datasets: [{
                label: "Rank",
                data: [
                    <?php
                    foreach($dataAuteursMustRanking as $auteur){
                        echo '"'.$auteur['rank'].'", ';
                    }
                    ?>
                ],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)'
                ],
                borderWidth: 1
            }]
        };

        // Options du graphe
        var options = {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        };

        // Création du graphe
        var ctx = document.getElementById('myChart_2').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: data,
            options: options
        });
    </script>


<script>
        // Données à afficher sur le graphe
        var data = {
            labels: ["A*","A","B","C"],
            datasets: [
                {
                    label: "Nombre d'Articles par Auteurs en Groupe",
                    data: [
                        <?php
                            $tabRes = [0,0,0,0];
                            foreach($dataAuteurGroupe as $auteur){
                                if($auteur['rank'] == 'A*'){
                                    $tabRes[0] = $auteur['nb_total'];
                                }
                                if($auteur['rank'] == 'A'){
                                    $tabRes[1] = $auteur['nb_total'];
                                }
                                if($auteur['rank'] == 'B'){
                                    $tabRes[2] = $auteur['nb_total'];
                                }
                                if($auteur['rank'] == 'C'){
                                    $tabRes[3] = $auteur['nb_total'];
                                }
                            }

                            echo '"'.$tabRes[0].'", "'.$tabRes[1].'", "'.$tabRes[2].'", "'.$tabRes[3].'" ';
                        
                            
                        

                        ?>


                    ],
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1,
                    yAxisID: 'y-axis-1'
                },
                {
                    label: "Nombre d'Articles par Auteurs Solo",
                    data: [
                        <?php
                            $tabRes = [0,0,0,0];
                            foreach($dataAuteurSolo as $auteur){
                                if($auteur['rank'] == 'A*'){
                                    $tabRes[0] = $auteur['nb_total'];
                                }
                                if($auteur['rank'] == 'A'){
                                    $tabRes[1] = $auteur['nb_total'];
                                }
                                if($auteur['rank'] == 'B'){
                                    $tabRes[2] = $auteur['nb_total'];
                                }
                                if($auteur['rank'] == 'C'){
                                    $tabRes[3] = $auteur['nb_total'];
                                }
                            }

                            echo '"'.$tabRes[0].'", "'.$tabRes[1].'", "'.$tabRes[2].'", "'.$tabRes[3].'" ';
                        
                            
                        

                        ?>
                    ],
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1,
                    yAxisID: 'y-axis-1'
                }
                
            ]
        };

        // Options du graphe
        var options = {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        };

        // Création du graphe
        var ctx = document.getElementById('myChart').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: data,
            options: options
        });
    </script>
    
</body>
</html>