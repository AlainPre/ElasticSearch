<?php
    require_once("db.php");
    require_once("page.php");
    require_once("elastic.php");

    // Supprimer l'index :

    $command = "es_geo";
    $method = "DELETE";

    echo elasticQuery($command, "", $method);
    echo "<br/><br/>";

    // Créer un index avec un analyseur français :

    $command = "es_geo";
    $method = "PUT";
    $doc = <<<EOD
    {
        "settings": {
            "analysis": {
                "analyzer": {
                    "default": {
                        "type": "french"
                    }
                }
            }
        }
    }
    EOD;

    echo elasticQuery($command, $doc, $method);
    echo "<br/><br/>";
  
    // Indexer les régions:

    $q = $db->prepare("SELECT no_region, nom, description FROM es_regions");
    $q->execute();
    while($data = $q->fetchObject()) {
        $description = str_replace('"', '\"', $data->description);
        $description = str_replace("\r", " ", $description);
        $description = str_replace("\n", " ", $description);
        $doc = <<<EOD
        {
            "nom": "{$data->nom}",
            "description": "{$description}",
            "url": "region.php?no_region={$data->no_region}"
        }
        EOD;
        $method = "POST";
        $command = "es_geo/_doc/R{$data->no_region}";
       echo elasticQuery($command, $doc, $method);
       echo '<br/><br/>';
    }

    // Indexer les départements:

    $q = $db->prepare("SELECT no_dept, nom, html FROM es_departements");
    $q->execute();
    while($data = $q->fetchObject()) {
        $description = strip_tags($data->html);
        $description = str_replace('"', '\"', $description);
        $description = str_replace("\r\n", " ", $description);
        $doc = <<<EOD
        {
            "nom": "{$data->nom}",
            "description": "{$description}",
            "url": "departement.php?no_dept={$data->no_dept}"
        }
        EOD;
        $method = "POST";
        $command = "es_geo/_doc/D{$data->no_dept}";
       echo elasticQuery($command, $doc, $method);
       echo '<br/><br/>';
    }
?>