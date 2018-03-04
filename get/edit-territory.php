<?php

    ob_start();
    $countryclass="class='active'";
    $editTerclass="class='active'";

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

    if(isset($_POST['submitUser']) && $_REQUEST['id'] > 0)
     {
        $cname = $_POST['name'];
        $population = $_POST['population'];
        $country = $_POST['country'];
        $region = $_POST['region'];
        $stval = (isset($_POST['status'])) ? 1 : 0;
        $arrVal = array(
                      "name" => $cname,
                      "population" => $population,
                      "stat" => $stval
                     );

        UpdateRec("territory", "id=".$_REQUEST['id'], $arrVal);

        /* Log de Actividad */
          $mensaje = "Se ha actualizado un territorio. Territorio: '".$cname."' Fecha:".date("Y-m-d H:i:s");
          log_actividad(4, 2, $_SESSION['USER_ID'], $mensaje);
        /* Fin de log de actividad */

        $nId=$_REQUEST['id'];

        $getRefId = GetRecord("territory", "id = ".$_REQUEST['id']);
        $mstId = $getRefId['id_ref_master_note'];
        if($mstId > 0)
        {
          $notemsg = "Territory ".$cname." updated by ".$_SESSION['USER_NAME'];
          $subj = "Territory ( ".$cname." )";
          create_log($mstId, $subj, $notemsg);
        }

        $message = '<div class="alert alert-success">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                      <strong>Registro Realizado</strong>
                    </div>';

        header("location:territory-view.php?id=".$_REQUEST['id']);
     }

     $arrUser = GetRecord("territory", "id = ".$_REQUEST['id']);
     $arrRegion = GetRecord("region", "id = ".$arrUser['id_region']);
     $nCountryId = $arrRegion['id_country'];
     $status = ($arrUser['stat'] == 1) ? 'checked' : '';
?>
	<section id="content">
          <section class="vbox">
            <section class="scrollable padder">

              <div class="row">
                <div class="col-sm-12">
                	<form class="form-horizontal" data-validate="parsley" method="post"   enctype="multipart/form-data">
                      <input type="hidden" value="<?php echo $arrUser['id']?>" name="id">
                      <section class="panel panel-default">
                        <header class="panel-heading">
                          <span class="h4">Editar Territorio</span>
                        </header>
                        <div class="panel-body">
                          <?php
                                if($message !="")
                                    echo $message;
                          ?>
                          <div class="form-group required">
                            <label class="col-lg-4 text-right control-label font-bold">Nombre del país</label>
                            <div class="col-lg-4">
                              <select class="form-control" name="country" required="required" onChange="getOptionsData(this.value, 'regionbycountry', 'region');" disabled="disabled">
                                <option value="">----------------</option>
                                <?PHP
                                $arrKindMeetings = GetRecords("Select * from country where stat = 1");
                                foreach ($arrKindMeetings as $key => $value) {
                                  $kinId = $value['id'];
                                  $kinDesc = $value['name'];
                                  $selRoll = (isset($nCountryId) && $nCountryId == $kinId) ? 'selected' : '';
                                ?>
                                <option value="<?php echo $kinId?>" <?php echo $selRoll?>><?php echo $kinDesc?></option>
                                <?php
                            }
                                ?>
                              </select>
                            </div>
                          </div>
                          <div class="form-group required">
                            <label class="col-lg-4 text-right control-label font-bold">Nombre de la región </label>
                            <div class="col-lg-4">
                              <select class="form-control" id="region" name="region" required="required" disabled="disabled">
                                <option value="">----------------</option>
                                <?PHP
                                $arrKindMeetings = GetRecords("Select * from region where stat = 1 and id_country = ".$nCountryId);
                                foreach ($arrKindMeetings as $key => $value) {
                                  $kinId = $value['id'];
                                  $kinDesc = $value['name'];
                                  $selRoll = (isset($arrUser['id_region']) && $arrUser['id_region'] == $kinId) ? 'selected' : '';
                                ?>
                                <option value="<?php echo $kinId?>" <?php echo $selRoll?>><?php echo $kinDesc?></option>
                                <?php
                            }
                                ?>
                              </select>
                            </div>
                          </div>
                          <div class="form-group required">
                            <label class="col-lg-4 text-right control-label font-bold">Nombre del territorio</label>
                            <div class="col-lg-4">
                              <input type="text" class="form-control" placeholder="Name Region" value="<?php echo $arrUser['name']?>" name="name" data-required="true">
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-lg-4 text-right control-label font-bold">Población</label>
                            <div class="col-lg-4">
                              <input type="text" class="form-control" placeholder="Amount of Population" value="<?php echo $arrUser['population']?>" name="population" >
                            </div>
                          </div>
                          <div class="form-group required">
                            <label class="col-lg-4 font-bold control-label">Activo/Desactivo</label>
                            <div class="col-lg-4">
                              <label class="switch">
                                <input type="checkbox" name="status" <?php echo $status?>>
                                <span></span>
                              </label>
                            </div>
                          </div>
                        </div>
                        <footer class="panel-footer text-right bg-light lter">
                          <button type="submit" name="submitUser" class="btn btn-primary btn-s-xs">Actualizar</button>
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
