<?php

    ob_start();
    $propuestapendiente="class='active'";
    $editpendiente="class='active'";

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
       $join.=" inner join acces_category on acces_category.id_user = users.id
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

      if($loggdUType != "Admin" && $loggdUType != "Business Unit Manager")
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

      $arrUser = GetRecords("select
                              proposal.id,
                              opportunity.name as oportunidad,
                              opportunity.proposal_date,
                              business.name as empresa,
                              proposal.create_date,
                              proposal.amount,
                              users.Name,
                              users.Last_name,
                              opportunity.id as id_oportunidad
                              from
                              opportunity inner join contact on opportunity.id_contact = contact.id
                              			      inner join business on contact.id_business = business.id
                                          inner join proposal on opportunity.id = proposal.id_oppottunity
                                          inner join users on users.id = proposal.id_user
                                          $join
                             $where
                             and
                             proposal.status=1");

?>
	<section id="content">
          <section class="vbox">
            <section class="scrollable padder">
              <section class="panel panel-default">
                <header class="panel-heading">
                          <span class="h4">Lista de Propuestas Pendientes</span>
                </header>
                <div class="panel-body">
                    <?php /* ?><form method="post">
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
                    */ ?>
                    <div class="table-responsive">
                        <table class="table table-striped b-t b-light" data-ride="datatables">
                          <thead>
                            <tr>
                              <th>Nombre de la Oportunidad</th>
                              <th>Business Manager</th>
                              <th>Empresa</th>
                              <th>Monto</th>
                              <th>Fecha de Propuesta</th>
                              <th>Fecha de Entrega de Propuetsa</th>
                              <th>Acciones</th>
                            </tr>
                          </thead>
                          <tbody>
                          <?PHP
                            $i=1;
                            foreach ($arrUser as $key => $value) { ?>
                          <tr>
                              <td class="tbdata"> <?php echo $value['oportunidad']?> </td>
                              <td class="tbdata"> <?php echo $value['Name'].' '.$value['Last_name']?> </td>
                              <td class="tbdata"> <?php echo $value['empresa']?> </td>
                              <td class="tbdata"> <?php echo number_format($value['amount'],2);?> </td>
                              <td class="tbdata"> <?php echo $value['create_date']?> </td>
                              <td class="tbdata"> <?php echo $value['proposal_date']?></td>
                              <td class="tbdata">
                                <a href="opportunity-view.php?id=<?php echo $value['id_oportunidad']?>" title="Ver detalle de: <?php echo $value['oportunidad']?>" class="btn btn-sm btn-icon btn-info"><i class="fa fa-edit"></i></a>
                                <a href="ver_edit_propuesta.php?id=<?php echo $value['id']?>" title="Ver Propueta" class="btn btn-sm btn-icon btn-success"><i class="glyphicon glyphicon-ok"></i></a>
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
