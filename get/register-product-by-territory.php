<?php

    ob_start();
    $productclass="class='active'";
    $registerProdTerrclass="class='active'";

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
          $territory = $_POST['territory'];
          $product = $_POST['product'];
          $price = $_POST['price'];
          $cost = $_POST['cost'];
          $percentage = $_POST['percentage'];
          $arrVal = array(
                        "id_territory" => $territory,
                        "id_product" => $product,
                        "cost" => $cost,
                        "percentage" => $percentage,
                        "price" => $price,
                        "stat" => 1
                       );

          $nCountPT = RecCount("product_by_territory", "id_territory = ".$territory." and id_product = ".$product);
          if($nCountPT > 0)
          {
              $message = '<div class="alert alert-danger">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                      <strong>Producto ya esta registrado para este territorio</strong>
                    </div>';
          }
          else
          {
            $nId = InsertRec("product_by_territory", $arrVal);

            /* Log de Actividad */
              $mensaje = "Se ha creado un producto por territorio. Producto por Territorio: '".$territory."' '".$product."'";
              log_actividad(6, 1, $_SESSION['USER_ID'], $mensaje);
            /* Fin de log de actividad */

            if($nId > 0)
            {
                MySQLQuery("Insert into master_notes (id) values (0)");

                $mstId = mysql_insert_id();
                UpdateRec("product_by_territory", "id = ".$nId, array("id_ref_master_note" => $mstId));
                $getProdName = GetRecord("product", "id=".$product);
                $getTerrName = GetRecord("territory", "id=".$territory);
                if($mstId > 0)
                {
                  $notemsg = "Product (".$getProdName['name'].") Registered by Territory ( ".$getTerrName['name']." ) registered by ".$_SESSION['USER_NAME'];
                  $subj = "Product (".$getProdName['name'].") Registered by Territory ( ".$getTerrName['name']." )";
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
     }
?>
	<section id="content">
          <section class="vbox">
            <section class="scrollable padder">

              <div class="row">
                <div class="col-sm-12">
                	<form class="form-horizontal" data-validate="parsley" method="post"   enctype="multipart/form-data" novalidate>
                      <section class="panel panel-default">
                        <header class="panel-heading">
                        </header>
                        <span class="h4">Registrar Productos por territorio</span>
                        <div class="panel-body">
                          <?php
                                if($message !="")
                                    echo $message;
                          ?>
                          <div class="form-group required">
                              <label class="col-lg-4 text-right control-label"><b>País</b></label>
                              <div class="col-lg-4">
                                  <select class="chosen-select form-control" name="country" data-required="true"  onChange="getOptionsData(this.value, 'regionbycountry', 'region');">
                                    <option value="">------------</option>
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
                              <label class="col-lg-4 text-right control-label"><b>Region</b></label>
                              <div class="col-lg-4">
                                  <select class="chosen-select form-control" name="region" id="region" data-required="true"  onChange="getOptionsData(this.value, 'territorybyregion', 'territory');">
                                  </select>
                              </div>
                          </div>
                          <div class="form-group required">
                              <label class="col-lg-4 text-right control-label"><b>Territorio</b></label>
                              <div class="col-lg-4">
                                  <select class="chosen-select form-control" name="territory" id="territory" data-required="true"  >
                                  </select>
                              </div>
                          </div>
                          <!-- <div class="form-group required">
                            <label class="col-lg-4 text-right control-label font-bold">Select Territory</label>
                            <div class="col-lg-4">
                              <select class="chosen-select form-control" name="territory" required="required" >
                                <option value="">----------------</option>
                                <?PHP
                                $arrKindMeetings = GetRecords("Select * from territory where stat = 1");
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
                          </div> -->
                          <div class="form-group required">
                            <label class="col-lg-4 text-right control-label font-bold">Seleccionar Productos</label>
                            <div class="col-lg-4">
                              <select class="chosen-select form-control" name="product" data-required="true" >
                                <option value="">----------------</option>
                                <?PHP
                                $arrKindMeetings = GetRecords("Select * from product where stat = 1");
                                foreach ($arrKindMeetings as $key => $value) {
                                  $kinId = $value['id'];
                                  $kinDesc = $value['code']." / ".$value['name'];

                                  $selRoll = (isset($_GET['pid']) && $_GET['pid'] == $kinId) ? 'selected' : '';
                                ?>
                                <option value="<?php echo $kinId?>" <?PHP echo $selRoll?>><?php echo $kinDesc?></option>
                                <?php
                            }
                                ?>
                              </select>
                            </div>
                          </div>
                          <div class="form-group required">
                            <label class="col-lg-4 text-right control-label font-bold">Precio</label>
                            <div class="col-lg-4">
                              <input type="text" class="form-control" placeholder="Precio" name="price" data-required="true">
                            </div>
                          </div>
                          <div class="form-group required">
                            <label class="col-lg-4 text-right control-label font-bold">Costo</label>
                            <div class="col-lg-4">
                              <input type="text" class="form-control" placeholder="Costo" name="cost" data-required="true">
                            </div>
                          </div>
                          <div class="form-group required">
                            <label class="col-lg-4 text-right control-label font-bold">Porcentaje</label>
                            <div class="col-lg-4">
                              <input type="text" class="form-control" placeholder="Porcentaje" name="percentage" data-required="true">
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
