<?php

    ob_start();
    $campanatyclass="class='active'";
    $ViewCampaignclass="class='active'";
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

      $where = "where (1=1) and campaign.stat = 1";

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

      if($loggdUType != "Admin" && $loggdUType != "Admin Telemarketing" && $loggdUType != "Business Unit Manager")
      {
        $where.=" and campaign.user_assigned = ".$_SESSION['USER_ID'];
      }
      /*else
      {
       if(isset($_POST['bizname']) && $_POST['bizname'] != "")
        {
          $where.=" and  opportunity.id_user_register =  ".$_POST['bizname'];
          $bizname = $_POST['bizname'];
        }
        if(isset($_POST['contactname']) && $_POST['contactname'] != "")
        {
          $where.=" and  contact.Name LIKE '%".$_POST['contactname']."%'";
          $contactname = $_POST['contactname'];
        }
        if(isset($_POST['stage']) && $_POST['stage'] != "")
        {
          $where.=" and  stage.Name_stage LIKE '%".$_POST['stage']."%'";
          $stage = $_POST['stage'];
        }
        if(isset($_POST['datefrom']) && $_POST['datefrom'] != "")
        {
          $where.=" and  opportunity.closing_date >= '".$_POST['datefrom']."'";
          $datefrom = $_POST['datefrom'];
        }
        if(isset($_POST['dateto']) && $_POST['dateto'] != "")
        {
          $where.=" and  opportunity.closing_date <= '".$_POST['dateto']."'";
          $dateto = $_POST['dateto'];
        }

      }*/

      $arrUser = GetRecords("select
                              *,
                              campaign.id as id_campaign
                            from
                            campaign inner join users on users.id = campaign.id_user_register
                            $join
                            $where");

?>
	<section id="content">
          <section class="vbox">
            <section class="scrollable padder">
              <section class="panel panel-default">
                <header class="panel-heading">
                    <span class="h4">Lista de Campañas</span>
                </header>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-striped b-t b-light" data-ride="datatables">
                          <thead>
                            <tr>
                              <th>Nombre de la Campaña</th>
                              <?PHP if($loggdUType == "Admin" || $loggdUType == "Admin Telemarketing") :  ?>
                              <th>Agente TM</th>
                              <?php endif; ?>
                              <th>Fecha Inicial</th>
                              <th>Fecha Final</th>
                              <th>Sospechosos</th>
                              <th>Verificados</th>
                              <th>Leads</th>
                              <th>Acción</th>
                            </tr>
                          </thead>
                          <tbody>
                          <?PHP
                            $i=1;
                            foreach ($arrUser as $key => $value) {
                              $status = ($value['stat'] == 1) ? 'Active' : 'In Active'; ?>
                          <tr>
                              <td class="tbdata"><b><?php echo $value['name']?></b></td>
                              <?PHP if($loggdUType == "Admin" || $loggdUType == "Admin Telemarketing") :  ?>
                              <td class="tbdata"><?php echo $value['Name'].' '.$value['Last_name']?></td>
                              <?php endif; ?>
                              <td class="tbdata"> <?php echo date('Y-m-d', strtotime($value['date_start']));?> </td>
                              <td class="tbdata"> <?php echo date('Y-m-d', strtotime($value['date_end']));?> </td>
                              <td class="tbdata"> <?php echo $value['sospechoso'].' / '.$value['total_listado']?> </td>
                              <td class="tbdata"> <?php echo $value['verificado'].' / '.$value['total_listado']?> </td>
                              <td class="tbdata"> <?php echo $value['lead'].' / '.$value['total_listado']?> </td>
                              <td class="tbdata" style="width:205px;">
                                  <a href="list_susp.php?id_campaign=<?php echo $value['id_campaign']?>" title="Ver detalle de: <?php echo $value['name']?>" class="btn btn-sm btn-icon btn-info"><i class="fa fa-edit"></i></a>
                                  <?PHP if($loggdUType == "Admin" || $loggdUType == "Admin Telemarketing") :  ?>
                                  <a href="download.php?file_campaign=<?php echo $value['list_suspects']?>" title="Descargar Archivo" style="background-color:green; color: white;" class="btn btn-sm btn-icon"><i class="i i-download"></i></a>
                                  <?php endif; ?>
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
