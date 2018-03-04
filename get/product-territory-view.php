<?php

    ob_start();
    $productclass="class='active'";
    $editProdTerrclass="class='active'";

    include("include/config.php");
    include("include/defs.php");
    $loggdUType = current_user_type();
    $loggdURegion = current_user_region();

    include("header.php");

    if(!isset($_SESSION['USER_ID']))
     {
          header("Location: index.php");
          exit;
     }
     $message="";
    if(isset($_GET['id']))
     {
        $arrUser = GetRecords("SELECT product_by_territory.*, product.name as pname, territory.name as tename from product_by_territory
                              inner join product on product.id =   product_by_territory.id_product
                              inner join territory on territory.id =   product_by_territory.id_territory
                               where product_by_territory.id = ".$_GET['id']);

     }
?>
	<section id="content">
          <section class="vbox">
            <section class="scrollable padder">

              <div class="row">
                <div class="col-sm-12">
                	<form class="form-horizontal" data-validate="parsley" action="edit-product-by-territory.php?id=<?php echo $_GET['id']?>" method="post">
                      <section class="panel panel-default">
                        <header class="panel-heading">
                          <span class="h4">Ver Productos por Territorio</span>
                        </header>
                        <div class="panel-body">


                          <div class="form-group">
                            <label class="col-lg-4 text-right font-bold">Productos</label>
                            <div class="col-lg-4">
                              <?php echo $arrUser[0]['pname'] ?>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-lg-4 text-right font-bold">Territorio</label>
                            <div class="col-lg-4">
                              <?php echo $arrUser[0]['tename'] ?>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-lg-4 text-right font-bold">Pricio</label>
                            <div class="col-lg-4">
                              <?php echo $arrUser[0]['price'] ?>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-lg-4 text-right font-bold">Costo</label>
                            <div class="col-lg-4">
                              <?php echo $arrUser[0]['cost'] ?>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-lg-4 text-right font-bold">Porcentaje</label>
                            <div class="col-lg-4">
                              <?php echo $arrUser[0]['percentage'] ?>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-lg-4 text-right  font-bold">Estatus</label>
                            <div class="col-lg-4">
                              <label><?php echo ($arrUser[0]['stat'] == 1) ? 'Activo' : 'Inactivo' ?></label>
                            </div>
                          </div>
                        </div>
                        <?php if($loggdUType == "Admin") { ?>
                        <footer class="panel-footer text-right bg-light lter">
                          <button type="submit" name="editUser" class="btn btn-primary btn-s-xs">Editar</button>
                        </footer>
                        <?php } ?>
                      </section>
                    </form>
                    <?php
                      $userrefid = $arrUser[0]['id_ref_master_note'];
                      //$userimage = $arrUser[0]['Image'];
                      include("notes-attached.php");

                       $arrRegion = GetRecords("SELECT product_by_territory.*,  product.name as pname from product_by_territory
                                               inner join product on product.id =   product_by_territory.id_product
                                               inner join territory on territory.id =   product_by_territory.id_territory
                                               where product_by_territory.id_territory = ".$arrUser[0]['id_territory']." and product_by_territory.id not in (".$_GET['id'].")
                             order by pname");
                    ?>
                    <!-- <section class="panel panel-default">
                          <header class="panel-heading">
                            <span class="h4">Products belong to this Territory</span>
                            <?php if($loggdUType == "Admin") { ?>
                            <span><a href="register-product-by-territory.php?cid=<?php echo $_GET['id']?>"  class="btn btn-sm btn-primary">Add</a></span>
                            <?php } ?>
                          </header>
                          <div class="panel-body">
                            <div class="table-responsive">
                              <table class="table table-striped b-t b-light">
                                <thead>
                                  <tr>
                                    <th>PRODUCT</th>
                                    <th>PRICE</th>
                                    <th>STATUS</th>
                                    <th>ACTION</th>
                                  </tr>
                                </thead>
                                <tbody>
                                <?PHP
                                $i=1;
                                foreach ($arrRegion as $key => $value) {

                                  $status = ($value['stat'] == 1) ? 'Activo' : 'Inactivo';
                                ?>
                              <tr>
                                  <td class="tbdata"> <?php echo $value['pname']?> </td>
                                  <td class="tbdata"> <?php echo $value['price']?> </td>
                                  <td class="tbdata"> <?php echo $status?> </td>
                                  <td> <button type="button" onclick="window.location='product-view.php?id=<?php echo $value['id_product']?>';" class="btn green btn-info">See Detail</button>
                                  <?php if($loggdUType == "Admin") { ?>
                                    <button type="button" onclick="window.location='edit-product.php?id=<?php echo $value['id_product']?>';" class="btn green btn-info">Edit Product By Territory</button>
                                    <?php } ?>
                                  </td>
                              </tr>
                              <?php
                                $i++;
                              }
                              ?>
                              </tbody>
                            </table>
                        </div>
                      </div>
                    </section>  -->
                  </div>
              </div>
            </section>
        </section>
    </section>
<?php
	include("footer.php");
?>
