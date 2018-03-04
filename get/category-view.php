<?php

    ob_start();
    $productclass="class='active'";
    $editCateclass="class='active'";

    include("include/config.php");
    include("include/defs.php");
    $loggdUType = current_user_type();
    $loggdURegion = current_user_region();

    include("header.php");

    if(!isset($_SESSION['USER_ID']) || $loggdUType == "Business Manager")
     {
          header("Location: index.php");
          exit;
     }
     $message="";
    if(isset($_GET['id']))
     {
        $arrUser = GetRecords("select * from category where id = ".$_GET['id']);

     }
?>
	<section id="content">
          <section class="vbox">
            <section class="scrollable padder">

              <div class="row">
                <div class="col-sm-12">
                	<form class="form-horizontal" data-validate="parsley" action="edit-category.php?id=<?php echo $_GET['id']?>" method="post">
                      <section class="panel panel-default">
                        <header class="panel-heading">
                          <span class="h4">Ver Categoria</span>
                        </header>
                        <div class="panel-body">


                          <div class="form-group">
                            <label class="col-lg-4 text-right font-bold">Nombre de la categoria</label>
                            <div class="col-lg-4">
                              <?php echo $arrUser[0]['name'] ?>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-lg-4 text-right font-bold">Descripción</label>
                            <div class="col-lg-4">
                              <?php echo $arrUser[0]['description'] ?>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-lg-4 text-right  font-bold">Estatus</label>
                            <div class="col-lg-4">
                              <label><?php echo ($arrUser[0]['stat'] == 1) ? 'Activa' : 'Inactiva' ?></label>
                            </div>
                          </div>
                        </div>
                        <?php if($loggdUType == "Admin") { ?>
                        <footer class="panel-footer text-right bg-light lter">
                          <button type="submit" name="editUser" class="btn btn-primary btn-s-xs">Editar Categoria</button>
                        </footer>
                        <?php } ?>
                      </section>
                    </form>
                    <?php
                      $userrefid = $arrUser[0]['id_ref_master_note'];
                      //$userimage = $arrUser[0]['Image'];
                      include("notes-attached.php");

                      $arrRegion = GetRecords("SELECT product.name as pname, product.stat, product.code,  product.training, product.id as pid from product
                                               where product.id_category = ".$_GET['id']."
                             order by name");
                    ?>

                      <section class="panel panel-default">
                          <header class="panel-heading">
                            <span class="h4">Productos que pertenecen a esta categoria</span>
                            <span><a href="register-product.php?cid=<?php echo $_GET['id']?>"  class="btn btn-sm btn-primary">Registrar Productos</a></span>
                          </header>
                          <div class="panel-body">
                            <div class="table-responsive">
                              <table class="table table-striped b-t b-light" data-ride="datatables">
                                <thead>
                                  <tr>
                                    <th>PRODUCTOS</th>
                                    <th>CÓDIGO</th>
                                    <th>REQUIERE ENTRENAMIENTO</th>
                                    <th>ESTATUS</th>
                                    <th>ACCIÓN</th>
                                  </tr>
                                </thead>
                                <tbody>
                                <?PHP
                                $i=1;
                                foreach ($arrRegion as $key => $value) {

                                  $status = ($value['stat'] == 1) ? 'Activo' : 'Inactivo';
                                  $training = ($value['training'] == 1) ? 'Yes' : 'No';
                                ?>
                              <tr>
                                  <td class="tbdata"> <?php echo $value['pname']?> </td>
                                  <td class="tbdata"> <?php echo $value['code']?> </td>
                                  <td class="tbdata"> <?php echo $training?> </td>
                                  <td class="tbdata"> <?php echo $status?> </td>
                                  <td> <button type="button" onclick="window.location='product-view.php?id=<?php echo $value['pid']?>';" class="btn green btn-info">Ver Producto</button>
                                    <?php if($loggdUType == "Admin") { ?>
                                    <button type="button" onclick="window.location='edit-product.php?id=<?php echo $value['pid']?>';" class="btn green btn-info">Editar Producto</button>
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
