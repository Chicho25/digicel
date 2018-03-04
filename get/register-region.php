<?php

    ob_start();
    $countryclass="class='active'";
    $registerRegclass="class='active'";

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
        $cname = $_POST['name'];
        $population = $_POST['population'];
        $country = $_POST['country'];

            $arrVal = array(
                          "name" => $cname,
                          "population" => $population,
                          "id_country" => $country,
                          "stat" => 1
                         );

          $nId = InsertRec("region", $arrVal);

          /* Log de Actividad */
            $mensaje = "Se ha creado una región. Región: '".$cname."'";
            log_actividad(4, 1, $_SESSION['USER_ID'], $mensaje);
          /* Fin de log de actividad */

          if($nId > 0)
          {
              MySQLQuery("Insert into master_notes (id) values (0)");

              $mstId = mysql_insert_id();
              UpdateRec("region", "id = ".$nId, array("id_ref_master_note" => $mstId));

              if($mstId > 0)
              {
                $notemsg = "New Region ( ".$cname." ) registered by ".$_SESSION['USER_NAME'];
                $subj = "Region ( ".$cname." )";
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
                    <strong>Registro no Realizado</strong>
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
                          <span class="h4">Registrar Región</span>
                        </header>
                        <div class="panel-body">
                          <?php
                                if($message !="")
                                    echo $message;
                          ?>
                          <div class="form-group required">
                            <label class="col-lg-4 text-right control-label font-bold">Seleccionar País</label>
                            <div class="col-lg-4">
                              <select class="chosen-select form-control" name="country" required="required" onChange="mostrar(this.value);">
                                <option value="">----------------</option>
                                <?PHP
                                $arrKindMeetings = GetRecords("Select * from country where stat = 1");
                                foreach ($arrKindMeetings as $key => $value) {
                                  $kinId = $value['id'];
                                  $kinDesc = $value['name'];

                                  $selRoll = (isset($_GET['cid']) && $_GET['cid'] == $kinId) ? 'selected' : '';
                                ?>
                                <option value="<?php echo $kinId?>" <?PHP echo $selRoll?>><?php echo $kinDesc?></option>
                                <?php
                            }
                                ?>
                              </select>
                            </div>
                          </div>
                          <div class="form-group required">
                            <label class="col-lg-4 text-right control-label font-bold">Nombre de la región</label>
                            <div class="col-lg-4">
                              <input type="text" class="form-control" placeholder="Name Region" name="name" data-required="true">
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-lg-4 text-right control-label font-bold">Población</label>
                            <div class="col-lg-4">
                              <input type="text" class="form-control" placeholder="Población" name="population" >
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
