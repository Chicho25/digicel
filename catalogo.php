<?php
    ob_start();
    session_start();
    $catalogoclass="class='active'";
    $catalogolistclass="class='active'";

    if(!isset($_SESSION['USER_ID']))
     {
          header("Location: index.php");
          exit;
     }

    include("include/config.php");
    include("include/defs.php");

    include("header.php");
    $where = "where (1=1)";

     if(isset($_POST['name_fitro']) && $_POST['name_fitro'] != "")
     {
        $where.=" and  users.Name LIKE '%".$_POST['name_fitro']."%'";
        $name = $_POST['name_fitro'];
     }
     if(isset($_POST['lname_fitro']) && $_POST['lname_fitro'] != "")
     {
        $where.=" and  users.Last_name LIKE '%".$_POST['lname_fitro']."%'";
        $lname = $_POST['lname_fitro'];
     }
     if(isset($_POST['user_fitro']) && $_POST['user_fitro'] != "")
     {
        $where.=" and  users.user_name LIKE '%".$_POST['user_fitro']."%'";
        $user = $_POST['user_fitro'];
     }

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
                                FROM [PRODUCT_CATALOGUE].[dbo].[vw_Sku_Catalogue]
                             $where"); ?>
	<section id="content">
          <section class="vbox">
            <section class="scrollable padder">
              <section class="panel panel-default">
                <?php
                      if($message !="")
                          echo $message;
                ?>
                <header class="panel-heading">
                          <span class="h4">Lista de Usuarios</span>
                          <h1>otra cosa</h1>
                </header>
                <div class="panel-body">
                    <form method="post">
                      <div class="row wrapper">
                        <div class="col-sm-2 m-b-xs">
                          <div class="input-group">
                            <input type="text" class="input-s input-sm form-control" value="<?php if(isset($name)){ echo $name;}?>" name="name_fitro" placeholder="Nombre">
                          </div>
                        </div>
                        <div class="col-sm-2 m-b-xs">
                          <div class="input-group">
                            <input type="text" class="input-s input-sm form-control" value="<?php if(isset($lname)){ echo $lname;}?>" name="lname_fitro" placeholder="Apellido">
                          </div>
                        </div>
                        <div class="col-sm-2 m-b-xs">
                          <div class="input-group">
                            <input type="text" class="input-s input-sm form-control" value="<?php if(isset($user)){ echo $user;}?>" name="user_fitro" placeholder="Usuario">
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
                              <th>SKU</th>
                              <th>Color</th>
                              <th>Master SKU</th>
                              <th>Model</th>
                              <th>Brand</th>
                              <th>First Cost</th>
                              <th>Last Cost</th>
                              <th>Price Pre</th>
                              <th>Ver Detalle</th>
                            </tr>
                          </thead>
                          <tbody>
                          <?PHP
                            foreach ($arrUser as $key => $value) {
                            ?>
                          <tr>
                              <td class="tbdata"> <?php echo $value['SKU']?> </td>
                              <td class="tbdata"> <?php echo $value['Color']?> </td>
                              <td class="tbdata"> <?php echo $value['Master_SKU']?> </td>
                              <td class="tbdata"> <?php echo $value['Model']?> </td>
                              <td class="tbdata"> <?php echo $value['Brand']?> </td>
                              <td class="tbdata"> <?php echo $value['First_Cost']?> </td>
                              <td class="tbdata"> <?php echo $value['Last_Cost']?> </td>
                              <td class="tbdata"> <?php echo $value['Price_Pre']?> </td>
                              <td>
                                <a href="modal_ver_detalle_catalogo.php?sku=<?php echo $value['SKU']?>" title="Detalle de catalgo" data-toggle="ajaxModal" class="btn btn-sm btn-icon btn-primary"><i class="glyphicon glyphicon-eye-open"></i></a>
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
