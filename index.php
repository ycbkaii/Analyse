<?php

/**
 * @version 1.0
 * @author Yanis Chiouar, Baron Noah
 * @api dblp.org
 */
require_once "./connexionDb.php";
global $conn;

//On mentionne qu'on est dans la section de recherche d'études
$mention = 'Études';

if(isset($_GET['title'])){
    try{
    //On récupere le titre
    $titre = $_GET['title'];

    $api = "https://dblp.org/search/publ/api?q=".urlencode($titre)."&format=json";

    

    //On récupere avec l'API la recherche en JSON
    $f = file_get_contents($api);
    $data = json_decode($f,true);

    //On récupere le tableau des résultats (hits)
    $dataHits = (isset($data['result']['hits']['hit']) ? $data['result']['hits']['hit'] : []);
    }catch(Exception $e){
        echo "oups Recharge la page :/";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Données</title>
</head>
<style>
    /* Styles */
.styled-table {
    width: 100%;
    border-collapse: collapse;
    border: 1px solid #ddd;
}

.styled-table th,
.styled-table td {
    padding: 8px;
    border: 1px solid #ddd;
}

.styled-table th {
    background-color: #f2f2f2;
    font-weight: bold;
    text-align: center;
}

.styled-table tbody tr:nth-child(even) {
    background-color: #f2f2f2;
}

.styled-table tbody tr:hover {
    background-color: #ddd;
}








.barre-recherche {
  display: inline-block;
  position: relative;
  margin-bottom: 20px;
}


.barre-recherche input[type="text"] {
  padding: 8px 12px;
  border: 1px solid #ccc;
  border-radius: 4px;
  font-size: 16px;
  width: 250px;
  transition: width 0.4s ease-in-out;
}


.barre-recherche button {
  padding: 8px 12px;
  background-color: #007bff;
  color: #fff;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  transition: background-color 0.4s ease-in-out;
}


.barre-recherche button:hover {
  background-color: #0056b3;
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


</style>

<body>
<form action="index.php" method="get" class='barre-recherche'>
    <input type="text" name="title" placeholder="Rechercher un titre ici">
    <button type='submit'>Rechercher</button>
</form>

<form action="conf.php">
    <button class='button' type='button' style='color:blue;'><?=$mention?></button>
    <button class='button' type='submit'>Conférences</button>
    <button class='button' type='button' onclick="window.location.href='notreAnalyse.php'">Notre Analyse</button>
</form>
<?php
if(isset($titre)){

?>
    <table class='styled-table'>
        <thead>
            <th>Iddblp</th>
            <th>Type</th>
            <th>Doi</th>
            <th>Title</th>
            <th>Auteurs</th>
            <th>Venue</th>
            <th>Year</th>
            <th>Pages</th>
            <th>ee</th>
            <th>Url</th>
            <th>VOLUME</th>
            <th>NUMERO</th>
            <th>BibTex</th>
        </thead>
        <tbody>
            
            <?php
            $idConf = 'NULL';
            foreach($dataHits as $hit){
                
                // //On fait un script pour vérifier que l'id de l'article  n'est pas déjà dans la BDD
                // $queryVerif = "SELECT iddblp FROM  projet_api_data._article WHERE iddblp = ".$hit['@id'];
                // $resultVerif = pg_query($conn, $queryVerif);
                // $dataVerif = pg_fetch_assoc($resultVerif);
                // if(!$dataVerif){

                //     if(isset($hit['info']['volume']) && !is_numeric($hit['info']['volume'])){
                //         $hit['info']['volume'] = "NULL";
                //     }

                //     //On regarde si un article fait partie d'une conférence
                //     $queryConf = "SELECT idcomptage FROM projet_api_data._conf WHERE POSITION(UPPER(title) IN UPPER('".$hit['info']['title']."')) > 0";
                //     $resConf = pg_query($conn, $queryConf);
                //     $data = pg_fetch_assoc($resConf);
                //     if($data != false){
                //         $idConf = $data['idcomptage'];
                        
                //     }
                    
                //     //On fait une insertion dans la BDD
                //     $insertArticle = "INSERT INTO projet_api_data._article VALUES 
                //     (".(isset($hit['@id']) ? $hit['@id'] : 'NULL').",'".(isset($hit['info']['type']) ? $hit['info']['type'] : 'NULL')."','".(isset($hit['info']['doi']) ? $hit['info']['doi'] : 'NULL')."','".(isset($hit['info']['title']) ? $hit['info']['title'] : 'NULL')."','".(isset($hit['info']['venue']) ? json_encode($hit['info']['venue']) : 'NULL')."',".(isset($hit['info']['year']) ? $hit['info']['year'] : 'NULL').",'".(isset($hit['info']['pages']) ? $hit['info']['pages'] : 'NULL')."','".(isset($hit['info']['ee']) ? $hit['info']['ee'] : 'NULL')."','".(isset($hit['info']['url']) ? $hit['info']['url'] : 'NULL')."',".(isset($hit['info']['number']) ? $hit['info']['number'] : 'NULL').",".(isset($hit['info']['volume']) ? $hit['info']['volume'] : 'NULL').",".$idConf.")";
                //     $resultInsertArt = pg_query($conn, $insertArticle);


                // }
                
            if(isset($hit['info']['authors']['author']['@pid']) || isset($hit['info']['authors']['author'])){
            ?>
            <tr>
                <td><?=$hit['@id']?></td>
                <td><?=(isset($hit['info']['type']) ? $hit['info']['type'] : 'NULL')?></td>
                <td><?=(isset($hit['info']['doi']) ? $hit['info']['doi'] : 'NULL')?></td>
                <td><?=(isset($hit['info']['title']) ? $hit['info']['title'] : 'NULL')?></td>
                <td>
                <?php
                    if(!isset($hit['info']['authors']['author']['@pid'])){
                        foreach($hit['info']['authors']['author'] as $auteur){
                            
                              echo "<a href=\"https://dblp.org/pid/{$auteur['@pid']}.bib\"> {$auteur['text']}, </a>";
                        }
                    }else{
                        echo $hit['info']['authors']['author']['text'];
                    }
                    
                ?>
                </td>
                <td><?=(isset($hit['info']['venue']) && !is_array($hit['info']['venue']) ? $hit['info']['venue'] : 'NULL')?></td>
                <td><?=(isset($hit['info']['year']) ? $hit['info']['year'] : 'NULL')?></td>
                <td><?=(isset($hit['info']['pages']) ? $hit['info']['pages'] : 'NULL')?></td>
                <td><?=(isset($hit['info']['ee']) ? $hit['info']['ee'] : 'NULL')?></td>
                <td><?=(isset($hit['info']['url']) ? $hit['info']['url'] : 'NULL')?></td>
                <?php
                if(!isset($hit['info']['volume'])){?>

                <td>Non renseigné</td>
                <?php
                    
                }else{?>
                    <td><?=$hit['info']['volume']?></td>
                <?php
                }
                ?>

                <?php
                if(!isset($hit['info']['number'])){?>

                <td>Non renseigné</td>
                <?php
                    
                }else{?>
                    <td><?=$hit['info']['number']?></td>
                <?php
                }
                ?>
                
                
            <td>
            <form action="getBibTex.php" method='get'>
                <button class='button' type='submit' name='bibtex' value="<?=$hit['info']['title']?>">Obtenir</button>
            </form>
            </td>
            </tr>

            <?php
            }
            }
            ?>
            
        </tbody>
        
    </table>

<?php
}
?>

</body>
</html>