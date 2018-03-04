<?php

    ob_start();
    $productclass="class='active'";
    $registerCateclass="class='active'";

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
        $description = $_POST['description'];

            $arrVal = array(
                          "name" => $cname,
                          "description" => $description,
                          "stat" => 1
                         );

          $nId = InsertRec("category", $arrVal);

          /* Log de Actividad */
            $mensaje = "Se ha creado una categoria. Categoria: '".$cname."'";
            log_actividad(6, 1, $_SESSION['USER_ID'], $mensaje);
          /* Fin de log de actividad */

          if($nId > 0)
          {
              MySQLQuery("Insert into master_notes (id) values (0)");

              $mstId = mysql_insert_id();
              UpdateRec("category", "id = ".$nId, array("id_ref_master_note" => $mstId));

              if($mstId > 0)
              {
                $notemsg = "New Category (".$cname.") registered by ".$_SESSION['USER_NAME'];
                $subj = "Category ( ".$cname." )";
                create_log($mstId, $subj, $notemsg);
              }
              $message = '<div class="alert alert-success">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                      <strong>La categoria fue creada!</strong>
                    </div>';
          }
          else
          {


            $message = '<div class="alert alert-danger">
                  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <strong>La categoria no fue creada</strong>
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
                          <span class="h4">Registrar Categoria</span>
                        </header>
                        <div class="panel-body">
                          <?php
                                if($message !="")
                                    echo $message;
                          ?>
                          <div class="form-group required">
                            <label class="col-lg-4 text-right control-label font-bold">Nombre de la Categoria</label>
                            <div class="col-lg-4">
                              <input type="text" class="form-control" placeholder="Nombre de la Categoria" name="cname" data-required="true">
                            </div>
                          </div>
                          <div class="form-group required">
                            <label class="col-lg-4 text-right control-label font-bold">Descripción</label>
                            <div class="col-lg-4">
                              <textarea cols="40" class="form-control" rows="5" name="description" data-required="true" placeholder="Descripción"></textarea>
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
