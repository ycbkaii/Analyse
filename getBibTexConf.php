<?php
session_start();
/**
 * @version 1.0
 * @author Yanis Chiouar, Baron Noah
 * @api dblp.org
 */
require_once 'connexionDb.php';

if(isset($_GET['bibtex'])){

    
    // $_GET['bibtex'] = json_decode($_GET['bibtex'], true);
    $_GET['bibtex'] = $_SESSION['bibtex'];
    

    //On regarde dans la base de données que la conf existe
    $queryConf = "SELECT * FROM projet_api_data._conf WHERE _conf.idcomptage = ".$_GET['bibtex']['idcomptage'];
    $resultConf = pg_query($conn, $queryConf);
    $dataConf = pg_fetch_assoc($resultConf);

    //On prend les articles par rapport à la db
    $getArticles = "SELECT title FROM projet_api_data._article WHERE idcomptage = ".$_GET['bibtex']['idcomptage'];
    $resArticles = pg_query($conn, $getArticles);
    $dataArticles = [];
    while($row = pg_fetch_assoc($resArticles)){
        array_push($dataArticles, $row);
    }

    
    if($dataConf != false && $dataArticles != false){
        
        echo "<pre>";

        

        
        $base_travail = $_GET['bibtex'];
        foreach($base_travail as $hit){
            
            //ON vérifie que c'est bien un article
            if(isset($hit['info']['authors']['author'])){

                $auteurs = [];
                if(!isset($hit['info']['authors']['author']['@pid'])){
                    foreach($hit['info']['authors']['author'] as $auteur){
                        
                        array_push($auteurs,$auteur['text']);
                    }
                }else{
                    array_push($auteurs, $hit['info']['authors']['author']['text']);
                }


                echo "
                @article{".(isset($hit['info']['url']) ? $hit['info']['url'] : 'NULL')."
                author = {".implode(', ',$auteurs)."},
                title = {".(isset($hit['info']['title']) ? $hit['info']['title'] : 'NULL')."},
                doi = {".(isset($hit['info']['doi']) ? $hit['info']['doi'] : 'NULL')."},
                type = {".(isset($hit['info']['type']) ? $hit['info']['type'] : 'NULL')."},
                year = {".(isset($hit['info']['year']) ? $hit['info']['year'] : 'NULL')."},
                pages = {".(isset($hit['info']['pages']) ? $hit['info']['pages'] : 'NULL')."},
                ee = {".(isset($hit['info']['ee']) ? $hit['info']['ee'] : 'NULL')."},
                url = {".(isset($hit['info']['url']) ? $hit['info']['url'] : 'NULL')."},
                volume = {".(isset($hit['info']['volume']) ? $hit['info']['volume'] : 'NULL')."},
                booktitle = {".$dataConf['title']."}
                }
                
                ";
        }
        }
            
            
        
        
        echo "</pre>";
        
        
    }else{
        echo "Il n'y a pas d'articles associés à cette conférence";
    }

    


    
}



?>