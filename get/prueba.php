<?php
include("include/config.php");
include("include/defs.php");
 ?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title></title>
  </head>
  <body>
    <?php
      $sql_usuarios = GetRecords("SELECT * FROM users inner join type_user on users.id_roll_user = type_user.id_user
                                                      where users.id_roll_user in(5, 4, 3)");

      foreach ($sql_usuarios as $key => $value) {
        $arrVal = array(
                        "id_category" => 1,
                        "id_user" => $value['id'],
                        "stat" => 1
                       );
        $nId = InsertRec("acces_category", $arrVal);
        echo $nId.'<br>'; 
      }
     ?>
  </body>
</html>
