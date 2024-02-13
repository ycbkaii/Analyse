<?php
/**
 * @version 1.0
 * @author Yanis Chiouar, Baron Noah
 * @api dblp.org
 */
require_once 'connexionDb.php';
//On check si on recoit bien la requete
if(isset($_GET['bibtex'])){
    

    //On prend la data de l'article
    $hit = $_GET['bibtex'];

    //On fait une requete SQL pour récuperer le nom de l'article pour faire un bibtex
    $query = "SELECT title FROM projet_api_data._article WHERE iddblp = ".$hit;
    $result = pg_query($conn, $query);
    $data = pg_fetch_assoc($result);
    


    
        
    $bibTex = file_get_contents("https://dblp.org/search/publ/api?q=".urlencode($data['title'])."&format=bib");
    echo "<pre>".$bibTex."</pre>";
    

    
    
}
?>