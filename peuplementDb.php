<?php
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


//On prend toutes les conf
$queryConf_ = "SELECT title, idcomptage FROM projet_api_data._conf";
$resultConf_ = pg_query($conn, $queryConf_);

while($row = pg_fetch_assoc($resultConf_)){

    

    echo $row['title'];



    //On mentionne qu'on est dans la section de recherche de conférence
    $mention = 'Conférences';
    

        $titre = $row['title'];

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
    
    
    sleep(5);

    //TODO Mettre peuplement dans le SCRIPT conf.php quand la personne recherche la conf, insert les autheurs et leur pid et les associations entre autheurs et article si ils n'éxiste pas déjà

    // Pour recup les article des authors avec le pid : https://dblp.org/pid/$pid.bib -> se télécharge en bib

    // recup le xml au format json avec cette commande ( normalement ): json_encode($xml);

    //TODO faire le traitement en local sur les données stocké en bdd (important jour de review)

}