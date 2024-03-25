<?php
session_start();
/**
 * @version 1.0
 * @author Yanis Chiouar, Baron Noah
 * @api dblp.org
 */
require_once "./connexionDb.php";
global $conn;

function xmlToArray($xml) {
    $array = [];
    foreach ($xml->children() as $child) {
        if ($child->count() > 0) {
            $array[$child->getName()][] = xmlToArray($child);
        } else {
            $value = (string) $child;
            if (array_key_exists($child->getName(), $array)) {
                if (!is_array($array[$child->getName()]) || !array_key_exists(0, $array[$child->getName()])) {
                    $array[$child->getName()] = [$array[$child->getName()]];
                }
                $array[$child->getName()][] = $value;
            } else {
                $array[$child->getName()] = $value;
            }
        }
    }
    return $array;
}

function xml2JsonByPid($pid){
    
    $url = "https://dblp.org/pid/$pid.xml";

    $xml_content = file_get_contents($url);
    try {
        $xml = new SimpleXMLElement($xml_content);
    } catch (\Throwable $th) {
        echo "Oups impossible de charger les acteurs => $th";
    }

    // Convertir le XML en tableau associatif récursif
    

    // $xmlArray = xmlToArray($xml);
    // $json = json_encode($xmlArray, JSON_PRETTY_PRINT);

    return $xml;

}





//On mentionne qu'on est dans la section de recherche de conférence
$mention = 'Conférences';
if(isset($_GET['title'])){

    $titre = $_GET['title'];

    //On récupére les conférences
    $getConf = "SELECT * FROM projet_api_data._conf WHERE title LIKE '".$titre."'";
    $resultConf = pg_query($conn, $getConf);
    $data =pg_fetch_assoc($resultConf);


    //Récuperation des articles pour les insert dans la BDD
    $apiArticles = "https://dblp.org/search/publ/api?q=".urlencode($titre)."&format=json";


    //On récupere avec l'API la recherche en JSON
    $f = file_get_contents($apiArticles);
    $dataApiArticles = json_decode($f,true);

    //On récupere le tableau des résultats (hits)
    $dataHits = (isset($dataApiArticles['result']['hits']['hit']) ? $dataApiArticles['result']['hits']['hit'] : []);
    


    
    
    foreach($dataHits as $hit){
        if(isset($hit['info']['authors']['author']['@pid']) || isset($hit['info']['authors']['author'])){        
        //On fait un script pour vérifier que l'id de l'article  n'est pas déjà dans la BDD
        $queryVerif = "SELECT iddblp FROM  projet_api_data._article WHERE iddblp = ".$hit['@id'];
        $resultVerif = pg_query($conn, $queryVerif);
        $dataVerif = pg_fetch_assoc($resultVerif);

        
        if($data != false && ($dataVerif == false || $dataVerif == null)){
            if(isset($hit['info']['volume']) && !is_numeric($hit['info']['volume'])){
                $hit['info']['volume'] = "NULL";
            }

            //On fait une insertion dans la BDD
            $insertArticle = "INSERT INTO projet_api_data._article VALUES 
            (".(isset($hit['@id']) ? $hit['@id'] : 'NULL').",'".(isset($hit['info']['type']) ? $hit['info']['type'] : 'NULL')."','".(isset($hit['info']['doi']) ? $hit['info']['doi'] : 'NULL')."','".(isset($hit['info']['title']) ? pg_escape_string($conn,$hit['info']['title']) : 'NULL')."','".(isset($hit['info']['venue']) ? json_encode($hit['info']['venue']) : 'NULL')."',".(isset($hit['info']['year']) ? $hit['info']['year'] : 'NULL').",'".(isset($hit['info']['pages']) ? $hit['info']['pages'] : 'NULL')."','".(isset($hit['info']['ee']) ? $hit['info']['ee'] : 'NULL')."','".(isset($hit['info']['url']) ? $hit['info']['url'] : 'NULL')."',".(isset($hit['info']['number']) ? $hit['info']['number'] : 'NULL').",".(isset($hit['info']['volume']) ? $hit['info']['volume'] : 'NULL').", '".$data['idcomptage']."')";
            $resultInsertArt = pg_query($conn, $insertArticle);


            //On vérifie que l'auteur n'existe pas déjà
            $auteurs = [];
            if(!isset($hit['info']['authors']['author']['@pid'])){
                foreach($hit['info']['authors']['author'] as $auteur){
                    array_push($auteurs,$auteur['@pid']);
                }
            }else{
                array_push($auteurs, $hit['info']['authors']['author']['@pid']);
            }


            //On parcourt les différents auteurs
            foreach($auteurs as $a){

                //On verifie que les auteurs sont pas dans la BDD
                $queryCheckAuteurs = "SELECT * FROM projet_api_data._authors WHERE pid = '$a'";
                $resultCheckAuteurs = pg_query($conn,$queryCheckAuteurs);
                $dataCheckAuteurs = pg_fetch_assoc($resultCheckAuteurs);
                $larticle = $hit['@id'];
                if($dataCheckAuteurs != false){
                    //On verifie que l'article ajouté n'est pas déjà associé à l'auteur
                    $queryCheckAuteursAssoc = "SELECT * FROM projet_api_data._author_article WHERE pid = '$a' AND iddblp = $larticle";
                    $resultCheckAuteursAssoc = pg_query($conn,$queryCheckAuteursAssoc);
                    $dataCheckAuteursAssoc = pg_fetch_assoc($resultCheckAuteursAssoc);

                    if($dataCheckAuteursAssoc){
                        echo "article et auteur déjà associés";
                    }else{
                        // l'auteur est dans la bdd mais n'est pas associé à l'article   
                        //On associe l'auteur à l'article
                        try{
                             $insertAuteurArticle = "INSERT INTO projet_api_data._author_article VALUES 
                             ('$a', $larticle)";
                             $resultInsertAutArt = pg_query($conn, $insertAuteurArticle);
                        }catch(Exception $e){
                            echo "Impossible d'associer les auteurs";
                        }
                    }

                }else {
                    // L'auteur n'est pas dans la bdd
                    try{
                        $auteurInfos = xml2JsonByPid($a);
                        $nomAuteur = $auteurInfos['name'];
                        $nbArticle = $auteurInfos['n'];
                        //On ajoute l'auteur dans la BDD
                        $insertAuteur = "INSERT INTO projet_api_data._authors (pid, name, nombre_art) VALUES 
                             ('$a', '".pg_escape_string($conn, $nomAuteur)."', $nbArticle)";
                             $resultInsertAut = pg_query($conn, $insertAuteur);
                    }catch(Exception $e){
                        echo "oups erreur du chargement des auteurs";
                    }

                    try{
                        //On associe l'auteur à l'article
                        $insertAuteurArticle2 = "INSERT INTO projet_api_data._author_article VALUES 
                        ('$a', $larticle)";
                        $resultInsertAutArt = pg_query($conn, $insertAuteurArticle2);
                   }catch(Exception $e){
                       echo "Impossible d'associer les auteurs";
                   }
                }
            }
            



        }

    }


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
    
<form action="conf.php" method="get" class='barre-recherche'>
    <input type="text" name="title" placeholder="Rechercher un titre de conférence">
    <button type='submit'>Rechercher</button>
</form>

<form action="index.php" style='margin-bottom: 10px;'>
    <button class='button' type='submit'>Études</button>
    <button class='button' type='button' style='color:blue;'><?=$mention?></button>
    <button class='button' type='button' onclick="window.location.href='notreAnalyse.php'">Notre Analyse</button>
</form>

<?php
if(isset($titre) && $data != false){

?>
    <table class='styled-table'>
        <thead>
            <th>Id conference</th>
            <th>Title</th>
            <th>Acronym</th>
            <th>Source</th>
            <th>Rank</th>
            <th>note</th>
            <th>Primary For</th>
            <th>BibTex</th>
        </thead>
        <tbody>

            <tr>
                <td><?=(isset($data['idcomptage']) ? $data['idcomptage'] : 'NULL')?></td>
                <td><?=(isset($data['title']) ? $data['title'] : 'NULL')?></td>
                <td><?=(isset($data['acronym']) ? $data['acronym'] : 'NULL')?></td>
                <td><?=(isset($data['source']) ? $data['source'] : 'NULL')?></td>
                <td><?=(isset($data['rank']) ? $data['rank'] : 'NULL')?></td>
                <td><?=(isset($data['note']) ? $data['note'] : 'NULL')?></td>
                <td><?=(isset($data['primary_for']) ? $data['primary_for'] : 'NULL')?></td>

            <td>
            <?php
            $dataHits['idcomptage'] = $data['idcomptage'];
            $_SESSION['bibtex'] = $dataHits;
            ?>
            <form action="getBibTexConf.php" method='get' style='text-align: center;'>
                <button  class='button' type='submit' name='bibtex' value='null'>Obtenir</button>
            </form>
            </td>
            </tr>
            <?php
            }
            ?>
            
            
        </tbody>
        
    </table>

</body>
</html>