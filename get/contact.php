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

      $where = "where (1=1)";
      if($loggdUType != "Admin")
      {
        $where.=" and contact.id_user_register = ".$_SESSION['USER_ID'];
      }
      $bname = "";
      $email = "";
      $cname = "";
      if(isset($_POST['bname']) && $_POST['bname'] != "")
      {
        $where.=" and  business.name LIKE '%".$_POST['bname']."%'";
        $bname = $_POST['bname'];
      }
      if(isset($_POST['email']) && $_POST['email'] != "")
      {
        $where.=" and  contact.Email LIKE '%".$_POST['email']."%'";
        $email = $_POST['email'];
      }
      if(isset($_POST['cname']) && $_POST['cname'] != "")
      {
        $where.=" and  contact.name LIKE '%".$_POST['cname']."%'";
        $cname = $_POST['cname'];
      }

      $arrUser = GetRecords("SELECT contact.*, business.Name as bizname from contact
                             inner join business on business.id  = contact.id_business
                             $where
                             order by name");

?>
	<section id="content">
          <section class="vbox">
            <section class="scrollable padder">
              <section class="panel panel-default">
                <header class="panel-heading">
                          <span class="h4">Lista de contactos</span>
                </header>
                <div class="panel-body">
                    <form method="post">
                      <div class="row wrapper">
                        <div class="col-sm-2 m-b-xs">
                          <div class="input-group">
                            <input type="text" class="input-s input-sm form-control" value="<?php echo $cname?>" name="cname" placeholder="Nombre del contacto">
                          </div>
                        </div>
                        <div class="col-sm-2 m-b-xs">
                          <div class="input-group">
                            <input type="text" class="input-s input-sm form-control" value="<?php echo $bname?>" name="bname" placeholder="Nombre de la empresa">
                          </div>
                        </div>
                        <div class="col-sm-3 m-b-xs">
                          <div class="input-group">
                            <input type="text" class="input-s input-sm form-control" value="<?php echo $email?>" name="email" placeholder="Email">
                            <span class="input-group-btn padder "><button class="btn btn-sm btn-default">Buscar</button></span>
                          </div>
                        </div>
                      </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-striped b-t b-light" data-ride="datatables">
                          <thead>
                            <tr>
                              <th>Nombre del contacto</th>
                              <th>Empresa</th>
                              <th>Teléfono</th>
                              <th>Email</th>
                              <th>Status</th>
                              <th>Acción</th>
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
                              <td class="tbdata"> <?php echo $value['bizname']?> </td>
                              <td class="tbdata"> <?php echo $value['Phone']?> </td>
                              <td class="tbdata"> <?php echo $value['Email']?> </td>
                              <td class="tbdata"> <?php echo $status?> </td>
                              <td> <button type="button" onclick="window.location='contact-view.php?id=<?php echo $value['id']?>';" class="btn green btn-info">Ver detalle</button>
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
