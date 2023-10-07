<?php  

echo (" tes---teee dentro de empresa.php ");
die();

if($api === 'empresa'){

    if( $method === "GET"){

      var_dump(" estamos no metodo get------ ");
     /* $db = DB::connect();
      $res = $db->prepare("SELECT * FROM empresas ");
      $res->execute();
      $obj = $res->fetchAll(PDO::FETCH_ASSOC);

      var_dump($obj)*/
    }
}