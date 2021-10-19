<?php
    require_once "db.php";
    require_once "page.php";
    require_once "elastic.php";

    if(isSet($_GET['recherche']))  {$recherche = $_GET['recherche'];}
    else {                          $recherche = "";}

    $recherche = str_replace(" ", "+", $recherche);
    $recherche = UTF8_decode($recherche);

    $method = "GET";
    $command = "es_geo/_search?q={$recherche}";
    $reponse = elasticQuery($command, "", $method);
    $json = json_decode($reponse);

    $html = "<main><h1>Résultat de votre recherche sur {$_GET['recherche']}</h1>";
    foreach($json->hits->hits as $hit) {
        if(isSet($hit->_source->description))   {            
            $description = substr($hit->_source->description, 0, 100) . '...';
        }
        $html.= <<<EOD
            <a href="{$hit->_source->url}">
                <h3>{$hit->_source->nom}</h3>
            </a>
            <p>{$description}</p>
         EOD;
    }
    $html.= "</main>";

    echo new Page($html);
?>