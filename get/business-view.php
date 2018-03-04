<?php

    ob_start();
    $businessclass="class='active'";
    $editBusnclass="class='active'";

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
        $arrUser = GetRecords("SELECT *
                             from business

                             WHERE id = ".$_GET['id']."
                             ");

     }
?>
	<section id="content">
          <section class="vbox">
            <section class="scrollable padder">

              <div class="row">
                <div class="col-sm-12">
                	<form class="form-horizontal" data-validate="parsley" action="edit-business.php?id=<?php echo $_GET['id']?>" method="post">
                      <section class="panel panel-default">
                        <header class="panel-heading">
                          <span class="h4">Ver Empresa</span>
                        </header>
                        <div class="panel-body">

                          <div class="form-group">
                            <div class="col-lg-12 col-lg-offset-3 ">
                              <div style="width:200px;
                                                            height:200px;
                                                            background-color: #cccccc;
                                                            border: solid 2px gray;
                                                            margin: 5px;">
                                                    <img id="img" src="<?php echo $arrUser[0]['Logo']?>" style='width:200px; height:200px;' border="0" alt="your image" />
                                                </div>
                              </div>
                          </div>
                          <div class="form-group">
                            <label class="col-lg-4 text-right  font-bold">Nombre de la Empresa</label>
                            <div class="col-lg-4">
                              <?php echo $arrUser[0]['Name'] ?>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-lg-4 text-right  font-bold">Ruc</label>
                            <div class="col-lg-4">
                              <?php echo $arrUser[0]['Rut'] ?>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-lg-4 text-right  font-bold">Teléfono</label>
                            <div class="col-lg-4">
                              <?php echo $arrUser[0]['Phone'] ?>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-lg-4 text-right  font-bold">Email</label>
                            <div class="col-lg-4">
                              <?php echo $arrUser[0]['Email'] ?>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-lg-4 text-right  font-bold">Descipción</label>
                            <div class="col-lg-4">
                              <label><?php echo $arrUser[0]['Description'] ?></label>
                            </div>
                          </div>

                        </div>
                        <footer class="panel-footer text-right bg-light lter">
                          <button type="submit" name="editUser" class="btn btn-primary btn-s-xs">Editar Empresa</button>
                        </footer>
                      </section>
                    </form>
                    <?php
                      $userrefid = $arrUser[0]['id_ref_master_note'];
                      include("notes-attached.php");
                      $where="";
                      if($loggdUType != "Admin")
                      {
                        $where.=" and id_user_register = ".$_SESSION['USER_ID'];
                      }
                    $arrTerritory = GetRecords("SELECT * from contact where id_business = ".$_GET['id']." $where
                             order by Name");
                    ?>

                      <section class="panel panel-default">
                          <header class="panel-heading">
                            <span class="h4">Contactos que pertenecen a esta empresa</span>
                            <span><a href="register-contact.php?bid=<?php echo $_GET['id']?>"  class="btn btn-sm btn-primary">Agregar</a></span>
                          </header>
                          <div class="panel-body">
                            <div class="table-responsive">
                              <table class="table table-striped b-t b-light" data-ride="datatables">
                                <thead>
                                  <tr>
                                    <th>NOMBRE DEL CONTACTO</th>
                                    <th>STATUS</th>
                                    <th>ACCIÓN</th>
                                  </tr>
                                </thead>
                                <tbody>
                                <?PHP
                                $i=1;
                                foreach ($arrTerritory as $key => $value) {

                                  $status = ($value['stat'] == 1) ? 'Active' : 'In Active';
                                ?>
                              <tr>
                                  <td class="tbdata"> <?php echo $value['Name']?> </td>
                                  <td class="tbdata"> <?php echo $status?> </td>
                                  <td> <button type="button" onclick="window.location='contact-view.php?id=<?php echo $value['id']?>';" class="btn green btn-info">Ver detalle</button>
                                    <button type="button" onclick="window.location='edit-contact.php?id=<?php echo $value['id']?>';" class="btn green btn-info">Editar</button>
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
