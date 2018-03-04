<?php

    ob_start();
    $activityclass="class='active'";
    $activitytelemarketign="class='active'";
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


       $bizname="";
       if(isset($_POST['bizname']) && $_POST['bizname'] != "")
        {
          $where.=" and campaign.user_assigned =  ".$_POST['bizname'];
          $bizname = $_POST['bizname'];
        }
        if(isset($_POST['datefrom']) && $_POST['datefrom'] != "")
        {
          $where.=" and campaign.date_register >= '".$_POST['datefrom']."'";
          $datefrom = $_POST['datefrom'];
        }
        if(isset($_POST['dateto']) && $_POST['dateto'] != "")
        {
          $where.=" and campaign.date_register <= '".$_POST['dateto'].' 23:59:59'."'";
          $dateto = $_POST['dateto'];
        }



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
                    <span class="h4">Reporte de Telemarketing</span>
                </header>
                <div class="panel-body">
                  <form method="post" action="" novalidate>
                    <div class="row wrapper">
                      <div class="col-sm-2 m-b-xs">
                        <div class="input-group" style="width:100%;">
                          <select class="chosen-select form-control" name="bizname" required="required" style="width:100%;" >
                                    <option value="">Usuario</option>
                                    <?PHP
                                    $arrKindMeetings = GetRecords("Select * from users WHERE stat = 1 and id_roll_user = 5");
                                    foreach ($arrKindMeetings as $key => $value) {
                                      $kinId = $value['id'];
                                      $kinDesc = $value['Name']." ".$value['Last_name'];
                                      $selType = (!empty($bizname) && $bizname == $kinId) ? 'selected' : '';
                                    ?>
                                    <option value="<?php echo $kinId?>" <?php echo $selType?>><?php echo utf8_encode($kinDesc)?></option>
                                    <?php
                                  }
                                    ?>
                            </select>
                        </div>
                      </div>
                      <div class="col-sm-8 m-b-xs">
                        <label class="col-lg-2 control-label" style="text-align:right">Fecha inicial</label>
                        <div class="col-lg-2 m-b-xs">
                          <div class="input-group">
                            <input class="input-sm input-s datepicker-input form-control datepicker" id="datepicker" name="datefrom" size="16" readonly="" data-required="true" type="text" value="<?php if(isset($datefrom)){ echo $datefrom;}?>" data-date-format="yyyy-mm-dd" >
                          </div>
                        </div>
                        <label class="col-lg-2 control-label" style="text-align:right">Fecha Final</label>
                        <div class="col-lg-2 m-b-xs">
                          <div class="input-group">
                            <input class="input-sm input-s datepicker-input form-control datepicker" id="datepicker1" name="dateto" size="16" readonly="" data-required="true" type="text" value="<?php if(isset($dateto)){ echo $dateto;}?>" data-date-format="yyyy-mm-dd" >
                          </div>
                        </div>
                        <span class="input-group-btn padder "><button class="btn btn-sm btn-primary">Buscar</button></span>
                      </div>
                      <div class="col-sm-1 m-b-xs">
                        <div class="input-group">
                        </div>
                      </div>
                    </div>
                  </form>
                    <div class="table-responsive">
                        <table class="table table-striped b-t b-light" data-ride="datatables">
                          <thead>
                            <tr>
                              <th>Nombre de la Campaña</th>
                              <?PHP /*if($loggdUType == "Admin" || $loggdUType == "Admin Telemarketing") :  ?>
                              <th>Agente TM</th>
                              <?php endif;*/ ?>
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
                              <?PHP /*if($loggdUType == "Admin" || $loggdUType == "Admin Telemarketing") :  ?>
                              <td class="tbdata"><?php echo $value['Name'].' '.$value['Last_name']?></td>
                              <?php endif;*/ ?>
                              <td class="tbdata"> <?php echo $value['date_start']?> </td>
                              <td class="tbdata"> <?php echo $value['date_end']?> </td>
                              <td class="tbdata"> <?php echo $value['sospechoso'].' / '.$value['total_listado']?> </td>
                              <td class="tbdata"> <?php echo $value['verificado'].' / '.$value['total_listado']?> </td>
                              <td class="tbdata"> <?php echo $value['lead'].' / '.$value['total_listado']?> </td>
                              <td class="tbdata" style="width:205px;">
                                  <a href="list_susp_report.php?id_campaign=<?php echo $value['id_campaign']?>" title="Ver detalle de: <?php echo $value['name']?>" class="btn btn-sm btn-icon btn-warning"><i class="glyphicon glyphicon-signal"></i></a>
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
