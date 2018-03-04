<?php

    ob_start();
    $countryclass="class='active'";
    $editTerclass="class='active'";

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
        $arrUser = GetRecords("select * from territory where id = ".$_GET['id']);

     }
?>
	<section id="content">
          <section class="vbox">
            <section class="scrollable padder">

              <div class="row">
                <div class="col-sm-12">
                	<form class="form-horizontal" data-validate="parsley" action="edit-territory.php?id=<?php echo $_GET['id']?>" method="post">
                      <section class="panel panel-default">
                        <header class="panel-heading">
                          <span class="h4">Ver Territorio</span>
                        </header>
                        <div class="panel-body">


                          <div class="form-group">
                            <label class="col-lg-4 text-right font-bold">Nombre del Territorio</label>
                            <div class="col-lg-4">
                              <?php echo $arrUser[0]['name'] ?>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-lg-4 text-right font-bold">Poblacion</label>
                            <div class="col-lg-4">
                              <?php echo $arrUser[0]['population'] ?>
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
                          <button type="submit" name="editUser" class="btn btn-primary btn-s-xs">Editar Territorio</button>
                        </footer>
                        <?php } ?>
                      </section>
                    </form>
                    <?php
                      $userrefid = $arrUser[0]['id_ref_master_note'];
                      //$userimage = $arrUser[0]['Image'];
                      include("notes-attached.php");
                    ?>
                  </div>
              </div>
            </section>
        </section>
    </section>
<?php
	include("footer.php");
?>
