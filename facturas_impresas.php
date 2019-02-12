<?php
    ob_start();
    session_start();
    $fpiclass="class='active'";
    $rfacimpreclass="class='active'";

    if(!isset($_SESSION['USER_ID']))
     {
          header("Location: index.php");
          exit;
     }

     include("include/config.php");
     include("include/defs.php");
     include("header.php");

     if (isset($_POST['eliminar_lista'])) {
       $array_factura = array("STAT" => 3,
                              "FECHA_REGISTRO"=>date("m-d-Y H:i:s"));
       UpdateRec("FACTURAS", "N_PEDIDO = '".$_POST['n_pedido_list']."'", $array_factura);

       $message = '<div class="alert alert-danger">
                   <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                     <strong>Pedido Regresado a facturas por imprimir</strong>
                   </div>';
     }

     if (isset($_POST['reimprimir_documento'])) {

       $ac = "reimprimir";
       $td = "0";
       $nd = $_POST['n_documento'];

       $tipo_documento = $td;
       $numero_documento = $nd;
       $exe = 'C:\xampp\htdocs\Debug\reprint\ConsoleApp1.exe';
       $accion = $ac;
       $parametros = $tipo_documento.";".$numero_documento;
       $dir = $exe ." ".$accion." ".$parametros;
        exec($dir, $output, $return);
       if ($return  == 0	)
       {
       	//echo "error al ejecutar la accion.</br>";
       }else{
       	//echo "Numero de secuencia fiscal: ".$return;
       }

     }

     $where = "where (1=1)";

     if(isset($_POST['n_pedido']) && $_POST['n_pedido'] != "")
     {
        $where.=" and  FACTURAS.N_PEDIDO LIKE '%".$_POST['n_pedido']."%'";
        $name = $_POST['n_pedido'];
     }
     if(isset($_POST['nombre_comerciante']) && $_POST['nombre_comerciante'] != "")
     {
        $where.=" and FACTURAS.DEALER_NAME LIKE '%".$_POST['nombre_comerciante']."%'";
        $lname = $_POST['nombre_comerciante'];
     }
     if(isset($_POST['ruc']) && $_POST['ruc'] != "")
     {
        $where.=" and FACTURAS.RUC LIKE '%".$_POST['ruc']."%'";
        $user = $_POST['ruc'];
     }

      $arrUser = GetRecords("Select
                              TOP 100
                            	FACTURAS.ID,
                            	FACTURAS.N_PEDIDO,
                            	FACTURAS.DEALER_NAME,
                            	FACTURAS.RUC,
                            	FACTURAS.METODO_PAGO,
                            	FACTURAS.STAT,
                              FACTURAS.FACTURA_REFERENCIA,
                            	(SELECT SUM(PRECIO_UNITARIO) AS SUMA_PRECIO FROM DETALLE_FACTURA WHERE DETALLE_FACTURA.N_PEDIDO = FACTURAS.N_PEDIDO) AS SUMA_PRECIO,
                            	(SELECT SUM(CANTIDAD) AS CANTIDAD FROM DETALLE_FACTURA WHERE DETALLE_FACTURA.N_PEDIDO = FACTURAS.N_PEDIDO) AS SUMA_CANTIDAD
                            	from FACTURAS
                               $where
                               AND
                               FACTURAS.STAT = 4
                               ORDER BY ID DESC"); ?>
	<section id="content">
          <section class="vbox">
            <section class="scrollable padder">
              <section class="panel panel-default">
                <?php
                      if($message !="")
                          echo $message;
                ?>
                <header class="panel-heading">
                    <span class="h4">Lista de Facturas impresas</span>
                </header>
                <div class="panel-body">
                    <form method="post">
                      <div class="row wrapper">
                        <div class="col-sm-2 m-b-xs">
                          <div class="input-group">
                            <input type="text" class="input-s input-sm form-control" value="<?php if(isset($name)){ echo $name;}?>" name="n_pedido" placeholder="Numero de Pedido">
                          </div>
                        </div>
                        <div class="col-sm-2 m-b-xs">
                          <div class="input-group">
                            <input type="text" class="input-s input-sm form-control" value="<?php if(isset($lname)){ echo $lname;}?>" name="nombre_comerciante" placeholder="Nombre de Comerciante">
                          </div>
                        </div>
                        <div class="col-sm-2 m-b-xs">
                          <div class="input-group">
                            <input type="text" class="input-s input-sm form-control" value="<?php if(isset($user)){ echo $user;}?>" name="ruc" placeholder="Ruc">
                          </div>
                        </div>
                        <div class="col-sm-3 m-b-xs">
                          <div class="input-group">
                            <span class="input-group-btn padder "><button class="btn btn-sm btn-default">Buscar</button></span>
                          </div>
                        </div>
                      </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-striped b-t b-light" data-ride="datatables">
                          <thead>
                            <tr>
                              <th>ID</th>
                              <th># Pedido</th>
                              <th># Factura</th>
                              <th>Razon Social</th>
                              <th>RUC</th>
                              <th>Metodo de Pago</th>
                              <th>Cantidad</th>
                              <th>Precio Unitario</th>
                              <th>Regresar</th>
                              <th>Ver Detalle</th>
                              <th>ReImpri</th>
                            </tr>
                          </thead>
                          <tbody>
                          <?PHP
                            foreach ($arrUser as $key => $value) {
                            ?>
                          <tr>
                              <td class="tbdata"> <?php echo $value['ID']?> </td>
                              <td class="tbdata"> <?php echo $value['N_PEDIDO']?> </td>
                              <td class="tbdata"> <?php echo $value['FACTURA_REFERENCIA']?> </td>
                              <td class="tbdata"> <?php echo utf8_encode($value['DEALER_NAME'])?> </td>
                              <td class="tbdata"> <?php echo $value['RUC']?> </td>
                              <td class="tbdata"> <?php echo $value['METODO_PAGO']?> </td>
                              <td class="tbdata"> <?php echo $value['SUMA_CANTIDAD']?> </td>
                              <td class="tbdata"> <?php echo $value['SUMA_PRECIO']?> </td>
                              <td>
                                  <?php //if ($value['FACTURA_REFERENCIA'] == ''): ?>
                                    <a href="modal_regresar_factura.php?N_PEDIDO=<?php echo $value['N_PEDIDO']?>" title="Regresar" data-toggle="ajaxModal" class="btn btn-sm btn-icon btn-primary"><i class="glyphicon glyphicon-chevron-left"></i></a>
                                  <?php //else : ?>
                                  <?php //endif; ?>
                              </td>
                              <td>
                                  <a href="modal_ver_detalle_factura.php?N_PEDIDO=<?php echo $value['N_PEDIDO']?>" title="Detalle de Productos" data-toggle="ajaxModal" class="btn btn-sm btn-icon btn-primary"><i class="glyphicon glyphicon-eye-open"></i></a>
                              </td>
                              <td>
                                <?php if ($value['FACTURA_REFERENCIA'] == ''): ?>
                                <?php else : ?>
                                  <a href="modal_reimprimir_documento.php?N_DOCUMENTO=<?php echo $value['FACTURA_REFERENCIA']?>" data-toggle="ajaxModal" title="Reimprimir documento" class="btn btn-sm btn-icon btn-primary" target="_blank"><i class="glyphicon glyphicon-print"></i></a>
                                <?php endif; ?>
                              </td>
                          </tr>
                          <?php
                            }
                          ?>
                          </tbody>
                        </table>
                    </div>
                </div>
              </section>
            </section>
        </section>
    </section>
    <script type="text/javascript">
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#img').show().attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
  </script>
<?php
	include("footer.php");
?>
