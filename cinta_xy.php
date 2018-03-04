<?php
    ob_start();
    session_start();
    $fpiclass="class='active'";
    $cxtclass="class='active'";

    if(!isset($_SESSION['USER_ID']))
     {
          header("Location: logout.php");
          exit;
     }

     // Metodo Factura fiscal 
     if(isset($_POST['cinta'])){
       include("print.php");
       if ($_POST['cinta'] == "x") {
           $metodo = "printX";
       }else{
           $metodo = "printZ";
       }
       imprimir(0, "",$metodo);
     }

     include("include/config.php");
     include("include/defs.php");
     include("header.php");
     $message="";

      /*$message = '<div class="alert alert-success">
                  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <strong>Usuario Creado Con Exito!</strong>
                  </div>';*/ ?>
	<section id="content">
          <section class="vbox">
            <section class="scrollable padder">
              <div class="row">
                <div class="col-sm-12">
                	<form class="form-horizontal" data-parsley-validate="" method="post" enctype="multipart/form-data">
                      <section class="panel panel-default">
                        <header class="panel-heading">
                          <span class="h4">Cinta X & Z</span>
                        </header>
                        <div class="panel-body">
                            <div class="form-group required">
                                <label class="col-lg-4 text-right control-label font-bold">Seleccionar Cinta</label>
                                <div class="col-lg-4">
                                    <select class="chosen-select form-control" id="select" name="cinta" required="">
                                      <option value="">Seleccionar</option>
                                      <option value="x">Cinta X</option>
                                      <option value="z">Cinta Z</option>
                                    </select>
                                </div>
                            </div>
                          </div>
                        </div>
                        <footer class="panel-footer text-right bg-light lter">
                          <button type="submit" name="submitUser" class="btn btn-primary btn-s-xs">Imprimir</button>
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
