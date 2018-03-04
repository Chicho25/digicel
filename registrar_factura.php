<?php
    ob_start();
    session_start();
    $fpiclass="class='active'";
    $fpiregtclass="class='active'";

    if(!isset($_SESSION['USER_ID']))
     {
          header("Location: logout.php");
          exit;
     }

     include("include/config.php");
     include("include/defs.php");
     include("header.php");
     $message="";

    if(isset($_POST['submitFactura']))
     {
        $n_pepdido = $_POST['n_pepdido'];
        $razon_social = $_POST['razon_social'];
        $ruc = $_POST['ruc'];
        $direccion = $_POST['direccion'];
        $m_pago = $_POST['m_pago'];
        $monto_pago = $_POST['monto_pago'];

        $ifFacExist = RecCount("FACTURAS", "N_PEDIDO = '".$n_pepdido."'");

        if($ifUserExist > 0)
        {
          $message = '<div class="alert alert-danger">
                      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                        <strong>El numero de Pedido ya existe</strong>
                      </div>';
        }
        else
        {
            $arrVal = array(
                          "N_PEDIDO" => $n_pepdido,
                          "DEALER_NAME" => $razon_social,
                          "RUC" => $ruc,
                          "METODO_PAGO" => $m_pago,
                          "STAT" => 3,
                          "FECHA_REGISTRO" =>date("m-d-Y H:i:s"),
                          "ID_USER_REGISTER" => $_SESSION['USER_ID'],
                          "TIPO_DOCUMENTO" => 0,
                          "SECUENCIA_FISCAL" => 0,
                          "MONTO_PAGO" => $monto_pago
                         );

          $nId = InsertRec("FACTURAS", $arrVal);

          if (isset($_POST["SKU"])){

                $numero=$_POST["SKU"];
                $count = count($numero);
                $n_factura = "";
                for ($i = 0; $i < $count; $i++) {
                     $arrayInsert = array("N_PEDIDO" => $n_pepdido,
                                          "SKU" => $_POST["SKU"][$i],
                                          "PRECIO_UNITARIO" => $_POST["price_detail"][$i],
                                          "DESCRIPCION" => $_POST["Description"][$i],
                                          "CANTIDAD" => $_POST["cantidad"][$i],
                                          "STAT" => 1);

                     InsertRec("DETALLE_FACTURA", $arrayInsert);
              }
              $message = '<div class="alert alert-success">
                          <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                            <strong>Factura Agregada</strong>
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
                	<form class="form-horizontal" data-parsley-validate="" method="post" enctype="multipart/form-data">
                      <section class="panel panel-default">
                        <header class="panel-heading">
                          <span class="h4">Registro de Factura</span>
                          <?php

                                    if (isset($_POST['r_n_pedido'])) {

                                    }
                           ?>
                        </header>
                        <div class="panel-body">
                          <?php
                                if($message !="")
                                    echo $message;
                          ?>

                          <div class="form-group required">
                            <label class="col-lg-4 text-right control-label font-bold">Numero de Pedido</label>
                            <div class="col-lg-4">
                              <input type="text" id="n_pedido" onkeyup="cargar()" class="form-control" value="<?php if(isset($_POST['r_n_pedido'])){ echo $_POST['r_n_pedido'];} ?>" placeholder="Numero de Pedido" name="n_pepdido" data-required="true" required>
                            </div>
                          </div>
                          <div class="form-group required">
                            <label class="col-lg-4 text-right control-label font-bold">Razon Social</label>
                            <div class="col-lg-4">
                              <input type="text" id="razon_social" onkeyup="cargar()" value="<?php if(isset($_POST['r_razon_social'])){ echo $_POST['r_razon_social'];} ?>" class="form-control" placeholder="Nombre de usuario" name="razon_social" data-required="true" required>
                            </div>
                          </div>
                          <div class="form-group required">
                            <label class="col-lg-4 text-right control-label font-bold">RUC</label>
                            <div class="col-lg-4">
                              <input type="text" id="ruc" onkeyup="cargar()" value="<?php if(isset($_POST['r_ruc'])){ echo $_POST['r_ruc'];} ?>" class="form-control" placeholder="Nombre" name="ruc" data-required="true" required>
                            </div>
                          </div><?php /* ?>
                          <div class="form-group required">
                            <label class="col-lg-4 text-right control-label font-bold">Direccion</label>
                            <div class="col-lg-4">
                              <textarea class="form-control" id="direccion" onkeyup="cargar()" value="<?php if(isset($_POST['r_direccion'])){ echo $_POST['r_direccion'];} ?>" placeholder="Direccion" name="direccion" data-required="true" required></textarea>
                            </div>
                          </div> */ ?>
                          <div class="form-group required">
                              <label class="col-lg-4 text-right control-label font-bold">Metodo de Pago</label>
                              <div class="col-lg-4">
                                  <input type="text" readonly name="m_pago" value="EFECTIVO" class="form-control">
                                  <?php /* ?>
                                  <select class="chosen-select form-control" id="m_pago" onChange="imprimirValor()" name="usertype" required="">
                                    <option value="">Seleccionar</option>
                                    <?PHP
                                    $arrKindMeetings = GetRecords("Select * from METODO_PAGO where stat = 1");
                                    foreach ($arrKindMeetings as $key => $value) {
                                      $kinId = $value['N_DESC'];
                                      $kinDesc = $value['DESCRIPCION'];
                                      if(isset($_POST['r_m_pago']) && $_POST['r_m_pago'] == $kinId){ $selected = "selected"; }
                                    ?>
                                    <option value="<?php echo $kinId?>" <?php if(isset($selected)){ echo $selected;} ?>><?php echo $kinDesc?></option>
                                    <?php } ?>
                                  </select>
                                  */ ?>
                              </div>
                          </div>
                          <div class="form-group required">
                              <label class="col-lg-4 text-right control-label font-bold">Monto Pago</label>
                              <div class="col-lg-4">
                                  <input type="text" id="monto_pago" onkeyup="imprimirValor()" name="monto_pago" value="<?php if(isset($_POST['r_pago'])){ echo $_POST['r_pago'];} ?>" class="form-control">
                              </div>
                          </div>
                          <?php

                          if (isset($_POST["check"])){

                                $numero=$_POST["check"];
                                $count = count($numero);
                                $n_factura = "";
                                for ($i = 0; $i < $count; $i++) {
                                    $n_factura .= " ,'".$_POST["check"][$i]."'";
                                }
                                $id_product = substr($n_factura, 2);

                                $id_product = str_replace(' ', '', $id_product);

                                $arrProdc = GetRecords("select * from [PRODUCT_CATALOGUE].[dbo].[vw_Sku_Catalogue] where SKU IN ($id_product)");?>

                                <table class="table table-striped">
                                  <thead>
                                    <tr>
                                      <th>SKU</th>
                                      <th>Model</th>
                                      <th>Brand</th>
                                      <th>Price Pre</th>
                                      <th>Cantidad</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                  <?PHP foreach ($arrProdc as $key => $value) { ?>
                                  <tr>
                                    <td class="tbdata"> <?php echo $value['SKU']?> </td>
                                    <td class="tbdata"> <?php echo $value['Model']?> </td>
                                    <td class="tbdata"> <?php echo $value['Description']?> </td>
                                    <td class="tbdata"> <input type="number" name="price_detail[]" value="<?php echo $value['Price_Pre']?>" class="form-control"> </td>
                                    <td class="tbdata"> <input type="number" name="cantidad[]" class="form-control"> </td>
                                  </tr>
                                  <input type="hidden" name="SKU[]" value="<?php echo $value['SKU']?>">
                                  <input type="hidden" name="Price_Pre[]" value="<?php echo $value['Price_Pre']?>">
                                  <input type="hidden" name="Description[]" value="<?php echo $value['Description']?>">
                                  <?php } ?>
                                  </tbody>
                                </table>

                            <?php }else{ ?>

                          <div class="form-group">
                              <label class="col-lg-4 text-right control-label font-bold"></label>
                              <div class="col-lg-4">
                              <button type="button" class="btn btn-info" data-toggle="modal" data-target="#myModal">
                               <span class="glyphicon glyphicon-plus"></span> Agregar productos
                              </button>
                            </div>
                          </div>
                        <?php } ?>
                        </div>

                        <footer class="panel-footer text-right bg-light lter">
                          <button type="submit" name="submitFactura" class="btn btn-primary btn-s-xs">Registrar</button>
                        </footer>
                      </section>
                    </form>
                  </div>
              </div>
              <!-- Modal -->
              <script type="text/javascript">
              function cargar(){
                document.getElementById("r_n_pedido").value = document.getElementById("n_pedido").value;
                document.getElementById("r_razon_social").value = document.getElementById("razon_social").value;
                document.getElementById("r_ruc").value = document.getElementById("ruc").value;
                document.getElementById("r_direccion").value = document.getElementById("direccion").value;

              }
              function imprimirValor(){
                  document.getElementById("r_pago").value = document.getElementById("monto_pago").value;
                }
              </script>
              <div class="modal fade bs-example-modal-lg" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                <div class="modal-dialog modal-lg" role="document">
                  <div class="modal-content">
                    <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                      <h4 class="modal-title" id="myModalLabel">Buscar productos</h4>
                    </div>
                    <?php

                    $arrUser = GetRecords("SELECT [SKU]
                                                  ,[Color]
                                                  ,[ID_Color]
                                                  ,[Master_SKU]
                                                  ,[ID_Master_Sku]
                                                  ,[ID_Model]
                                                  ,[Model]
                                                  ,[ID_Brands]
                                                  ,[Brand]
                                                  ,[Description]
                                                  ,[Category_Sku]
                                                  ,[ID_Cat_Sku]
                                                  ,[Origin_Sku]
                                                  ,[Dummy_Sku]
                                                  ,[Tier]
                                                  ,[Message]
                                                  ,[First_Cost]
                                                  ,[Last_Cost]
                                                  ,[Price_Pre]
                                                  ,[Denominacion]
                                                  ,[ID_Denominacion]
                                                  ,[Ventas_Minimas]
                                                  ,[Status_Name]
                                                  ,[ID_Status_Sku]
                                                  ,[Corporativo]
                                              FROM [PRODUCT_CATALOGUE].[dbo].[vw_Sku_Catalogue]");

                     ?>
                    <form class="" action="" method="post">
                    <div class="modal-body">
                      <div class="table-responsive">

                          <table class="table table-striped b-t b-light" data-ride="datatables">
                            <thead>
                              <tr>
                                <th>SKU</th>
                                <th>Model</th>
                                <th>Brand</th>
                                <th>Price Pre</th>
                                <th>Seleccionar</th>
                              </tr>
                            </thead>
                            <tbody>
                            <?PHP
                              foreach ($arrUser as $key => $value) {
                              ?>
                            <tr>
                                <td class="tbdata"> <?php echo $value['SKU']?> </td>
                                <td class="tbdata"> <?php echo $value['Model']?> </td>
                                <td class="tbdata"> <?php echo $value['Description']?> </td>
                                <td class="tbdata"> <?php echo $value['Price_Pre']?> </td>
                                <td class="tbdata"> <input type="checkbox" name="check[]" value="<?php echo $value['SKU']?>"> </td>
                            </tr>
                            <?php
                              }
                            ?>
                            </tbody>
                          </table>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                      <button type="submit" class="btn btn-primary">Guardar</button>
                      <input type="hidden" id="r_n_pedido" name="r_n_pedido">
                      <input type="hidden" id="r_razon_social" name="r_razon_social">
                      <input type="hidden" id="r_ruc" name="r_ruc">
                      <input type="hidden" id="r_direccion" name="r_direccion">
                      <input type="hidden" id="r_pago" name="r_pago">
                    </div>
                    </form>
                  </div>
                </div>

              </div>
            </section>
        </section>
    </section>
    <?php
    	include("footer.php");
    ?>
