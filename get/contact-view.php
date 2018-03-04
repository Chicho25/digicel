<?php

    ob_start();
    $businessclass="class='active'";
    $editContclass="class='active'";

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
        $arrUser = GetRecords("SELECT contact.*, business.Name as bizname
                             from contact
                              inner join business on business.id  = contact.id_business
                             WHERE contact.id = ".$_GET['id']."
                             ");

     }
?>
	<section id="content">
          <section class="vbox">
            <section class="scrollable padder">

              <div class="row">
                <div class="col-sm-12">
                	<form class="form-horizontal" data-validate="parsley" action="edit-contact.php?id=<?php echo $_GET['id']?>" method="post">
                      <section class="panel panel-default">
                        <header class="panel-heading">
                          <span class="h4">Ver Contacto</span>
                        </header>
                        <div class="panel-body">

                          <div class="form-group">
                            <div class="col-lg-12 col-lg-offset-3 ">
                              <div style="width:200px;
                                          height:200px;
                                          background-color: #cccccc;
                                          border: solid 2px gray;
                                          margin: 5px;">
                                <img id="img" src="<?php echo $arrUser[0]['Image']?>" style='width:200px; height:200px;' border="0" alt="your image" />
                              </div>
                            </div>
                          </div>
                          
                          <div class="form-group">
                            <label class="col-lg-4 text-right  font-bold">Empresa</label>
                            <div class="col-lg-4">
                              <?php echo $arrUser[0]['bizname'] ?>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-lg-4 text-right  font-bold">Nombre del contacto</label>
                            <div class="col-lg-4">
                              <?php echo $arrUser[0]['Name'] ?>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-lg-4 text-right  font-bold">Telefono</label>
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
                          <button type="submit" name="editUser" class="btn btn-primary btn-s-xs">Editar Contacto</button>
                        </footer>
                      </section>
                    </form>
                    <?php
                      $userrefid = $arrUser[0]['id_ref_master_note'];
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
