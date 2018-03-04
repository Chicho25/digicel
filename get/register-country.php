<?php

    ob_start();
    $countryclass="class='active'";
    $registerCntclass="class='active'";

    include("include/config.php");
    include("include/defs.php");
    $loggdUType = current_user_type();
    $loggdURegion = current_user_region();

    include("header.php");

    if(!isset($_SESSION['USER_ID']) || $loggdUType != "Admin")
     {
          header("Location: index.php");
          exit;
     }
     $message="";

    if(isset($_POST['submitUser']))
     {
        $cname = $_POST['cname'];
        $population = $_POST['population'];
        $capital = $_POST['capital'];

            $arrVal = array(
                          "name" => $cname,
                          "population" => $population,
                          "capital" => $capital,
                          "stat" => 1
                         );

          $nId = InsertRec("country", $arrVal);

          /* Log de Actividad */
            $mensaje = "Se ha creado un país. País: '".$cname."'";
            log_actividad(4, 1, $_SESSION['USER_ID'], $mensaje);
          /* Fin de log de actividad */

          if($nId > 0)
          {
              MySQLQuery("Insert into master_notes (id) values (0)");

              $mstId = mysql_insert_id();
              UpdateRec("country", "id = ".$nId, array("id_ref_master_note" => $mstId));

              if($mstId > 0)
              {
                $notemsg = "Nuevo País (".$cname.") Registrado Por ".$_SESSION['USER_NAME'];
                $subj = "País ( ".$cname." )";
                create_log($mstId, $subj, $notemsg);
              }
              $message = '<div class="alert alert-success">
                          <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                            <strong>Registro Realizado</strong>
                          </div>';
          }
          else
          {


            $message = '<div class="alert alert-danger">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                          <strong>Registro no realizado</strong>
                        </div>';
          }



     }
?>
	<section id="content">
          <section class="vbox">
            <section class="scrollable padder">

              <div class="row">
                <div class="col-sm-12">
                	<form class="form-horizontal" data-validate="parsley" method="post"   enctype="multipart/form-data">
                      <section class="panel panel-default">
                        <header class="panel-heading">
                          <span class="h4">Registro de País</span>
                        </header>
                        <div class="panel-body">
                          <?php
                                if($message !="")
                                    echo $message;
                          ?>
                          <div class="form-group required">
                            <label class="col-lg-4 text-right control-label font-bold">Nombre de el País</label>
                            <div class="col-lg-4">
                              <input type="text" class="form-control" placeholder="Nombre de el País" name="cname" data-required="true">
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-lg-4 text-right control-label font-bold">Población</label>
                            <div class="col-lg-4">
                              <input type="text" class="form-control" placeholder="Cantidad de Población" name="population" >
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-lg-4 text-right control-label font-bold">Capital</label>
                            <div class="col-lg-4">
                              <input type="text" class="form-control" placeholder="Capital" name="capital">
                            </div>
                          </div>
                        </div>
                        <footer class="panel-footer text-right bg-light lter">
                          <button type="submit" name="submitUser" class="btn btn-primary btn-s-xs">Registrar</button>
                        </footer>
                      </section>
                    </form>
                  </div>
              </div>
            </section>
        </section>
    </section>

<?php
	include("footer.php");
?>
