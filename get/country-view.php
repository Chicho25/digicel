<?php

    ob_start();
    $countryclass="class='active'";
    $editCntclass="class='active'";

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
        $arrUser = GetRecords("select * from country where id = ".$_GET['id']);

     }
?>
	<section id="content">
          <section class="vbox">
            <section class="scrollable padder">

              <div class="row">
                <div class="col-sm-12">
                	<form class="form-horizontal" data-validate="parsley" action="edit-country.php?id=<?php echo $_GET['id']?>" method="post">
                      <section class="panel panel-default">
                        <header class="panel-heading">
                          <span class="h4">Ver País</span>
                        </header>
                        <div class="panel-body">


                          <div class="form-group">
                            <label class="col-lg-4 text-right font-bold">Nombre del País</label>
                            <div class="col-lg-4">
                              <?php echo $arrUser[0]['name'] ?>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-lg-4 text-right font-bold">Población</label>
                            <div class="col-lg-4">
                              <?php echo $arrUser[0]['population'] ?>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-lg-4 text-right  font-bold">Capital</label>
                            <div class="col-lg-4">
                              <label><?php echo $arrUser[0]['capital'] ?></label>
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
                          <button type="submit" name="editUser" class="btn btn-primary btn-s-xs">Editar País</button>
                        </footer>
                        <?php } ?>
                      </section>
                    </form>
                    <?php
                      $userrefid = $arrUser[0]['id_ref_master_note'];
                      //$userimage = $arrUser[0]['Image'];
                      include("notes-attached.php");

                      $where = "(1=1)";
                      if($loggdUType == "Region Master")
                      {
                        $where.=" and id = ".$loggdURegion;
                      }
                      $arrRegion = GetRecords("SELECT * from region where $where AND id_country = ".$_GET['id']."
                             order by name");
                    ?>

                      <section class="panel panel-default">
                          <header class="panel-heading">
                            <span class="h4">Region que pertenecen a este País</span>
                            <?php if($loggdUType == "Admin") { ?>
                            <span><a href="register-region.php?cid=<?php echo $_GET['id']?>"  class="btn btn-sm btn-primary">Agregar</a></span>
                            <?php } ?>
                          </header>
                          <div class="panel-body">
                            <div class="table-responsive">
                              <table class="table table-striped b-t b-light" data-ride="datatables">
                                <thead>
                                  <tr>
                                    <th>Nombre de la Región</th>
                                    <th>Estatus</th>
                                    <th>Acción</th>
                                  </tr>
                                </thead>
                                <tbody>
                                <?PHP
                                $i=1;
                                foreach ($arrRegion as $key => $value) {

                                  $status = ($value['stat'] == 1) ? 'Activo' : 'Inactivo';
                                ?>
                              <tr>
                                  <td class="tbdata"> <?php echo $value['name']?> </td>
                                  <td class="tbdata"> <?php echo $status?> </td>
                                  <td> <button type="button" onclick="window.location='region-view.php?id=<?php echo $value['id']?>';" class="btn green btn-info">Ver detalles</button>
                                  <?php if($loggdUType == "Admin") { ?>
                                    <button type="button" onclick="window.location='edit-region.php?id=<?php echo $value['id']?>';" class="btn green btn-info">Editar</button>
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
