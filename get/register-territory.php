<?php

    ob_start();
    $countryclass="class='active'";
    $registerTerclass="class='active'";

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
        $region = $_POST['region'];

            $arrVal = array(
                          "name" => $cname,
                          "population" => $population,
                          "id_region" => $region,
                          "stat" => 1
                         );

          $nId = InsertRec("territory", $arrVal);

          /* Log de Actividad */
            $mensaje = "Se ha creado un territorio. Territorio: '".$cname."'";
            log_actividad(4, 1, $_SESSION['USER_ID'], $mensaje);
          /* Fin de log de actividad */

          if($nId > 0)
          {
              MySQLQuery("Insert into master_notes (id) values (0)");

              $mstId = mysql_insert_id();
              UpdateRec("territory", "id = ".$nId, array("id_ref_master_note" => $mstId));

              if($mstId > 0)
              {
                $notemsg = "New Territory ( ".$cname." ) registered by ".$_SESSION['USER_NAME'];
                $subj = "Territory ( ".$cname." )";
                create_log($mstId, $subj, $notemsg);
              }

              $message = '<div class="alert alert-success">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                      <strong>Territory created successfully</strong>
                    </div>';
          }
          else
          {


            $message = '<div class="alert alert-danger">
                  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <strong>Territory not created</strong>
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
                          <span class="h4">Register Territory</span>
                        </header>
                        <div class="panel-body">
                          <?php
                                if($message !="")
                                    echo $message;
                          ?>
                          <div class="form-group required">
                            <label class="col-lg-4 text-right control-label font-bold">Select Country</label>
                            <div class="col-lg-4">
                              <select class="chosen-select form-control" name="country" required="required" onChange="getOptionsData(this.value, 'regionbycountry', 'region');">
                                <option value="">----------------</option>
                                <?PHP
                                $arrKindMeetings = GetRecords("Select * from country where stat = 1");
                                foreach ($arrKindMeetings as $key => $value) {
                                  $kinId = $value['id'];
                                  $kinDesc = $value['name'];
                                  $selRoll = (isset($_GET['cid']) && $_GET['cid'] == $kinId) ? 'selected' : '';
                                ?>
                                <option value="<?php echo $kinId?>" <?php echo $selRoll;?>><?php echo $kinDesc?></option>
                                <?php
                            }
                                ?>
                              </select>
                            </div>
                          </div>
                          <div class="form-group required">
                            <label class="col-lg-4 text-right control-label font-bold">Select Region</label>
                            <div class="col-lg-4">
                              <select class="chosen-select form-control" id="region" name="region" required="required" >
                              <?php
                              if(isset($_GET['cid']) && isset($_GET['rid']))
                              {
                              ?>
                                <option value="">----------------</option>
                                <?PHP
                                $arrKindMeetings = GetRecords("Select * from region where stat = 1 and id_country = ".$_GET['cid']);
                                foreach ($arrKindMeetings as $key => $value) {
                                  $kinId = $value['id'];
                                  $kinDesc = $value['name'];
                                  $selRoll = (isset($_GET['rid']) && $_GET['rid'] == $kinId) ? 'selected' : '';
                                ?>
                                  <option value="<?php echo $kinId?>" <?php echo $selRoll?>><?php echo $kinDesc?></option>
                                <?php
                                 }
                                ?>
                              <?php
                              }
                              ?>
                              </select>
                            </div>
                          </div>
                          <div class="form-group required">
                            <label class="col-lg-4 text-right control-label font-bold">Name Territory</label>
                            <div class="col-lg-4">
                              <input type="text" class="form-control" placeholder="Name Territory" name="name" data-required="true">
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-lg-4 text-right control-label font-bold">Population</label>
                            <div class="col-lg-4">
                              <input type="text" class="form-control" placeholder="Amount of Population" name="population" >
                            </div>
                          </div>

                        </div>
                        <footer class="panel-footer text-right bg-light lter">
                          <button type="submit" name="submitUser" class="btn btn-primary btn-s-xs">Register Territory</button>
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
