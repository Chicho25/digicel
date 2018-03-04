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

      $where = "where (1=1)";

      /* Para los usuarios Business Unit Manager */
      $join = "";
       if($_SESSION['USER_ROLE']=="Business Unit Manager"){
       $join.=" inner join users on users.id = business.id_user_register
                inner join acces_category on acces_category.id_user = users.id
                inner join category_business on category_business.id = acces_category.id_category";
       $access = optener_category_roll_by_id($_SESSION['USER_ID']);
       $in = "";
       $nr = 1;
       foreach($access as $key => $value){
                if($nr>1){
                $in .= $value['id_category'].", ";
                }else{
                  $in .= $value['id_category'];
                }
                $nr++;
              }
       $where.= " and acces_category.id_category in($in)";
       }
       /* Fin Business Unit Manager */

      if($loggdUType != "Admin" && $_SESSION['USER_ROLE']!="Business Unit Manager")
      {
        $where.=" and id_user_register = ".$_SESSION['USER_ID'];
      }

      $bname = "";
      $email = "";
      $rut = "";
      if(isset($_POST['bname']) && $_POST['bname'] != "")
      {
        $where.=" and  business.name LIKE '%".$_POST['bname']."%'";
        $bname = $_POST['bname'];
      }
      if(isset($_POST['email']) && $_POST['email'] != "")
      {
        $where.=" and  business.Email LIKE '%".$_POST['email']."%'";
        $email = $_POST['email'];
      }
      if(isset($_POST['rut']) && $_POST['rut'] != "")
      {
        $where.=" and  business.Rut LIKE '%".$_POST['rut']."%'";
        $rut = $_POST['rut'];
      }

      $arrUser = GetRecords("SELECT business.* from business $join
                              $where
                             order by name");

?>
	<section id="content">
          <section class="vbox">
            <section class="scrollable padder">
              <section class="panel panel-default">
                <header class="panel-heading">
                          <span class="h4">Lista de Empresas</span>
                </header>
                <div class="panel-body">
                    <form method="post">
                      <div class="row wrapper">
                        <div class="col-sm-2 m-b-xs">
                          <div class="input-group">
                            <input type="text" class="input-s input-sm form-control" value="<?php echo $bname?>" name="bname" placeholder="Nombre de la empresa">
                          </div>
                        </div>
                        <div class="col-sm-2 m-b-xs">
                          <div class="input-group">
                            <input type="text" class="input-s input-sm form-control" value="<?php echo $email?>" name="email" placeholder="Email">
                          </div>
                        </div>
                        <div class="col-sm-3 m-b-xs">
                          <div class="input-group">
                            <input type="text" class="input-s input-sm form-control" value="<?php echo $rut?>" name="rut" placeholder="Ruc">
                            <span class="input-group-btn padder "><button class="btn btn-sm btn-default">Search</button></span>
                          </div>
                        </div>
                      </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-striped b-t b-light" data-ride="datatables">
                          <thead>
                            <tr>
                              <th>Nombre de la empresa</th>
                              <th>RUC</th>
                              <th>Teléfono</th>
                              <th>EMAIL</th>
                              <th>STATUS</th>
                              <th>ACCIÓN</th>
                            </tr>
                          </thead>
                          <tbody>
                          <?PHP
                            $i=1;
                            foreach ($arrUser as $key => $value) {

                              $status = ($value['stat'] == 1) ? 'Active' : 'In Active';
                            ?>
                          <tr>
                              <td class="tbdata"> <?php echo $value['Name']?> </td>
                              <td class="tbdata"> <?php echo $value['Rut']?> </td>
                              <td class="tbdata"> <?php echo $value['Phone']?> </td>
                              <td class="tbdata"> <?php echo $value['Email']?> </td>
                              <td class="tbdata"> <?php echo $status?> </td>
                              <td> <button type="button" onclick="window.location='business-view.php?id=<?php echo $value['id']?>';" class="btn green btn-info">Ver detalles</button>
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
            </section>
        </section>
    </section>

<?php
	include("footer.php");
?>
