<?php

    ob_start();
    $productclass="class='active'";
    $editProdclass="class='active'";

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
        $arrUser = GetRecords("select product.*, category.name as catname from product
                              inner join category on category.id  = product.id_category
                               where product.id = ".$_GET['id']);

     }
?>
	<section id="content">
          <section class="vbox">
            <section class="scrollable padder">

              <div class="row">
                <div class="col-sm-12">
                	<form class="form-horizontal" data-validate="parsley" action="edit-product.php?id=<?php echo $_GET['id']?>" method="post">
                      <section class="panel panel-default">
                        <header class="panel-heading">
                          <span class="h4">Ver Producto</span>
                        </header>
                        <div class="panel-body">

                          <div class="form-group">
                            <div class="col-lg-12 col-lg-offset-3 ">
                              <div style="width:204px;
                                                            height:154px;
                                                            background-color: #cccccc;
                                                            border: solid 2px gray;
                                                            margin: 5px;">
                                                    <img id="img" src="<?php echo $arrUser[0]['image']?>" style='width:200px; height:150px;' " border="0" alt="your image" />
                                                </div>
                              </div>
                          </div>
                          <div class="form-group">
                            <label class="col-lg-4 text-right font-bold">Nombre de la categoria</label>
                            <div class="col-lg-4">
                              <?php echo $arrUser[0]['catname'] ?>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-lg-4 text-right font-bold">Nombre del producto</label>
                            <div class="col-lg-4">
                              <?php echo $arrUser[0]['name'] ?>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-lg-4 text-right font-bold">Código</label>
                            <div class="col-lg-4">
                              <?php echo $arrUser[0]['code'] ?>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-lg-4 text-right font-bold">Requiere Entrenamiento?</label>
                            <div class="col-lg-4">
                              <?php echo $tr = ($arrUser[0]['training'] == 1) ? 'Si' : 'No'; ?>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-lg-4 text-right  font-bold">Estatus</label>
                            <div class="col-lg-4">
                              <label><?php echo ($arrUser[0]['stat'] == 1) ? 'Activo' : 'Inactive' ?></label>
                            </div>
                          </div>
                        </div>
                        <?php if($loggdUType == "Admin") { ?>
                        <footer class="panel-footer text-right bg-light lter">
                          <button type="submit" name="editUser" class="btn btn-primary btn-s-xs">Editar Producto</button>
                        </footer>
                        <?php } ?>
                      </section>
                    </form>
                    <?php
                      $userrefid = $arrUser[0]['id_ref_master_note'];
                      //$userimage = $arrUser[0]['Image'];
                      include("notes-attached.php");

                    $arrRegion = GetRecords("SELECT product_by_territory.*, territory.name as TName, region.name as RName, country.name as Country from product_by_territory
                                               inner join product on product.id =   product_by_territory.id_product
                                               inner join territory on territory.id =   product_by_territory.id_territory
                                               inner join region on region.id = territory.id_region
                                              inner join country on country.id = region.id_country
                                               where product_by_territory.id_product = ".$arrUser[0]['id']."
                             order by Country, RName, TName");
                    ?>
                    <section class="panel panel-default">
                          <header class="panel-heading">
                            <span class="h4">Territorios donde esta asignado este producto</span>
                            <?php if($loggdUType == "Admin") { ?>
                            <span><a href="register-product-by-territory.php"  class="btn btn-sm btn-primary">Agregar</a></span>
                            <?php } ?>
                          </header>
                          <div class="panel-body">
                            <div class="table-responsive">
                              <table class="table table-striped b-t b-light" data-ride="datatables">
                                <thead>
                                  <tr>
                                    <th>Localidad</th>
                                    <th>Precio</th>
                                    <th>Costo</th>
                                    <th>Porcentaje</th>
                                    <th>Estatus</th>
                                    <th>Acción</th>
                                  </tr>
                                </thead>
                                <tbody>
                                <?PHP
                                $i=1;
                                foreach ($arrRegion as $key => $value) {

                                  $status = ($value['stat'] == 1) ? 'Active' : 'In Active';
                                  $locAddress = $value['Country']." / ".$value['RName']." / ".$value['TName'];
                                ?>
                              <tr>
                                  <td class="tbdata"> <?php echo $locAddress?> </td>
                                  <td class="tbdata"> <?php echo $value['price']?> </td>
                                  <td class="tbdata"> <?php echo $value['cost']?> </td>
                                  <td class="tbdata"> <?php echo $value['percentage']?> </td>
                                  <td class="tbdata"> <?php echo $status?> </td>
                                  <td> <button type="button" onclick="window.location='product-territory-view.php?id=<?php echo $value['id']?>';" class="btn green btn-info">Ver detalles</button>
                                  <?php if($loggdUType == "Admin") { ?>
                                    <button type="button" onclick="window.location='edit-product-by-territory.php?id=<?php echo $value['id']?>';" class="btn green btn-info">Editar</button>
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
                    </section>

                  </div>
              </div>
            </section>
        </section>
    </section>
<?php
	include("footer.php");
?>
