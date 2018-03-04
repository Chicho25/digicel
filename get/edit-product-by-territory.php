<?php

    ob_start();
    $productclass="class='active'";
    $editProdTerrclass="class='active'";

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
          $stval = (isset($_POST['status'])) ? 1 : 0;
          $arrVal = array(
                        "id_territory" => $territory,
                        "id_product" => $product,
                        "price" => $price,
                        "cost" => $cost,
                        "percentage" => $percentage,
                        "stat" => $stval
                       );

          $nCountPT = RecCount("product_by_territory", "id_territory = ".$territory." and id_product = ".$product." and id <> ".$_REQUEST['id']);
          if($nCountPT > 0)
          {
              $message = '<div class="alert alert-danger">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                      <strong>Already registered product with this Territory</strong>
                    </div>';
          }
          else
          {

            UpdateRec("product_by_territory", "id=".$_REQUEST['id'], $arrVal);

            /* Log de Actividad */
              $mensaje = "Se ha actualizado un producto por territorio. Producto por Territorio: '".$territory."' '".$product."' Fecha:".date("Y-m-d H:i:s");
              log_actividad(6, 2, $_SESSION['USER_ID'], $mensaje);
            /* Fin de log de actividad */

            $nId=$_REQUEST['id'];
            if($nId > 0)
            {
                $getrefid = GetRecord("product_by_territory", "id = ".$_REQUEST['id']);
                $mstId = $getrefid['id_ref_master_note'];
                $getProdName = GetRecord("product", "id=".$product);
                $getTerrName = GetRecord("territory", "id=".$territory);
                if($mstId > 0)
                {
                  $notemsg = "Product (".$getProdName['name'].") by Territory ( ".$getTerrName['name']." ) updated by ".$_SESSION['USER_NAME'];
                  $subj = "Product (".$getProdName['name'].")  by Territory ( ".$getTerrName['name']." )";
                  create_log($mstId, $subj, $notemsg);
                }

                $message = '<div class="alert alert-success">
                      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                        <strong>Product Registered By Territory updated successfully</strong>
                      </div>';

                header("location:product-territory-view.php?id=".$_REQUEST['id']);
            }
            else
            {


              $message = '<div class="alert alert-danger">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                      <strong>Product Registered By Territory  not updated</strong>
                    </div>';
            }


        }
     }
     $arrUser = GetRecord("product_by_territory", "id = ".$_REQUEST['id']);
     $arrTer = GetRecord("territory", "id = ".$arrUser['id_territory']);
     $arrReg = GetRecord("region", "id = ".$arrTer['id_region']);
     $status = ($arrUser['stat'] == 1) ? 'checked' : '';

?>
	<section id="content">
          <section class="vbox">
            <section class="scrollable padder">

              <div class="row">
                <div class="col-sm-12">
                	<form class="form-horizontal" data-validate="parsley" method="post"   enctype="multipart/form-data" novalidate>
                      <input type="hidden" value="<?php echo $arrUser['id']?>" name="id">
                      <section class="panel panel-default">
                        <header class="panel-heading">
                          <span class="h4">Edit Product By Territory</span>
                        </header>
                        <div class="panel-body">
                          <?php
                                if($message !="")
                                    echo $message;
                          ?>
                          <div class="form-group required">
                              <label class="col-lg-4 text-right control-label"><b>Country</b></label>
                              <div class="col-lg-4">
                                  <select class="chosen-select form-control" name="country" data-required="true"  onChange="getOptionsData(this.value, 'regionbycountry', 'region');">
                                    <option value="">------------</option>
                                    <?PHP
                                        $arrKindMeetings = GetRecords("Select * from country where stat = 1");
                                        foreach ($arrKindMeetings as $key => $value) {
                                          $kinId = $value['id'];
                                          $kinDesc = $value['name'];
                                          $selRoll = (isset($arrReg['id_country']) && $arrReg['id_country'] == $kinId) ? 'selected' : '';
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
                                    <option value="">------------</option>
                                    <?PHP
                                    $arrKindMeetings = GetRecords("Select * from region where stat = 1 and id_country = ".$arrReg['id_country']);
                                    foreach ($arrKindMeetings as $key => $value) {
                                      $kinId = $value['id'];
                                      $kinDesc = $value['name'];
                                      $selRoll = (isset($arrReg['id']) && $arrReg['id'] == $kinId) ? 'selected' : '';
                                    ?>
                                      <option value="<?php echo $kinId?>" <?php echo $selRoll?>><?php echo $kinDesc?></option>
                                    <?php
                                     }
                                    ?>
                                  </select>
                              </div>
                          </div>
                          <div class="form-group required">
                              <label class="col-lg-4 text-right control-label"><b>Territory</b></label>
                              <div class="col-lg-4">
                                  <select class="chosen-select form-control" name="territory" id="territory" data-required="true"  >
                                    <option value="">------------</option>
                                    <?PHP
                                    $arrKindMeetings = GetRecords("Select * from territory where stat = 1 and id_region = ".$arrReg['id']);
                                    foreach ($arrKindMeetings as $key => $value) {
                                      $kinId = $value['id'];
                                      $kinDesc = $value['name'];
                                      $selRoll = (isset($arrUser['id_territory']) && $arrUser['id_territory'] == $kinId) ? 'selected' : '';
                                    ?>
                                      <option value="<?php echo $kinId?>" <?php echo $selRoll?>><?php echo $kinDesc?></option>
                                    <?php
                                     }
                                    ?>
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

                                  $selRoll = (isset($arrUser['id_territory']) && $arrUser['id_territory'] == $kinId) ? 'selected' : '';
                                ?>
                                <option value="<?php echo $kinId?>" <?PHP echo $selRoll?>><?php echo $kinDesc?></option>
                                <?php
                            }
                                ?>
                              </select>
                            </div>
                          </div> -->
                          <div class="form-group required">
                            <label class="col-lg-4 text-right control-label font-bold">Select Product</label>
                            <div class="col-lg-4">
                              <select class="chosen-select form-control" name="product" data-required="true" >
                                <option value="">----------------</option>
                                <?PHP
                                $arrKindMeetings = GetRecords("Select * from product where stat = 1");
                                foreach ($arrKindMeetings as $key => $value) {
                                  $kinId = $value['id'];
                                  $kinDesc = $value['name'];

                                  $selRoll = (isset($arrUser['id_product']) && $arrUser['id_product'] == $kinId) ? 'selected' : '';
                                ?>
                                <option value="<?php echo $kinId?>" <?PHP echo $selRoll?>><?php echo $kinDesc?></option>
                                <?php
                            }
                                ?>
                              </select>
                            </div>
                          </div>
                          <div class="form-group required">
                            <label class="col-lg-4 text-right control-label font-bold">Price</label>
                            <div class="col-lg-4">
                              <input type="text" class="form-control" placeholder="Price" value="<?php echo $arrUser['price'] ?>" name="price" data-required="true">
                            </div>
                          </div>
                          <div class="form-group required">
                            <label class="col-lg-4 text-right control-label font-bold">Cost</label>
                            <div class="col-lg-4">
                              <input type="text" class="form-control" placeholder="Cost" value="<?php echo $arrUser['cost'] ?>" name="cost" data-required="true">
                            </div>
                          </div>
                          <div class="form-group required">
                            <label class="col-lg-4 text-right control-label font-bold">Percentage</label>
                            <div class="col-lg-4">
                              <input type="text" class="form-control" placeholder="Percentage" value="<?php echo $arrUser['percentage'] ?>" name="percentage" data-required="true">
                            </div>
                          </div>
                          <div class="form-group required">
                            <label class="col-lg-4 font-bold control-label">Active/Deactive</label>
                            <div class="col-lg-4">
                              <label class="switch">
                                <input type="checkbox" name="status" <?php echo $status?>>
                                <span></span>
                              </label>
                            </div>
                          </div>
                        </div>
                        <footer class="panel-footer text-right bg-light lter">
                          <button type="submit" name="submitUser" class="btn btn-primary btn-s-xs">Edit Product</button>
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
