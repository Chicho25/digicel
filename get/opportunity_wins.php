<?php

    ob_start();
    $opportunityclass="class='active'";
    $winsOpporclass="class='active'";

    include("include/config.php");
    include("include/defs.php");
    $loggdUType = current_user_type();
    $loggdURegion = current_user_region();

    include("header.php");

/* Actualizar proximo paso */

if(isset($_POST['submitUsuario'])){

  $update_next_step = MySQLQuery("update opportunity set id_user_register = '".$_POST['id_usuario']."'
                                                         where
                                                         id = '".$_POST['id_opportunity']."'");

}

if(isset($_POST['submitNextstep'])){

  $update_next_step = MySQLQuery("update opportunity set nextstep_date = '".$_POST['nextstepdate']."',
                                                         nextstep = '".$_POST['nextstep']."'
                                                         where
                                                         id = '".$_POST['id']."'");

}

if(isset($_POST['etapaNote'])){

  $update_next_step = MySQLQuery("update opportunity set source = '".$_POST['etapa']."'
                                                         where
                                                         id = '".$_POST['id_opportunity']."'");

}

/* Inser de la nota Oportunidad */

    	$message="";

    	if(isset($_POST['submitNote']))
    	{

    		    $notetype = $_POST['notetype'];
            $note = $_POST['note'];
            $noteid = $_POST['noteid'];
            $userrefid = $_POST['master_note'];
            $subject = $_POST['subject'];

            if($noteid != "")
            {
            	$arrVal = array(
                              "note_type" => $notetype,
                              "note" => $note,
                              "note_subject" => $subject,
                              "note_ref_id" => $userrefid,
                              "create_date" => date("Y-m-d H:i:s")
                             );
            	$nId = InsertRec("note_detail", $arrVal);
            }
            else
            {
            	$nId = $noteid;
            	$arrVal = array(
                              "note_type" => $notetype,
                              "note" => $note,
                              "note_subject" => $subject
                             );

            	UpdateRec("note_detail", "id = ".$nId, $arrVal);
            }

            if($nId == 0)
            {
            	$message = 'La nota no fue creada!';
            }
            else
            {
            	if(isset($_FILES['attached']) && $_FILES['attached']['tmp_name'] != "")
    	          {
    	              $target_dir = "notes/";
    	              $target_file = $target_dir . basename($_FILES["attached"]["name"]);
    	              $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
    	              $filename = $target_dir . $nId.".".$imageFileType;
    	              $filenameThumb = $target_dir . $nId."_thumb.".$imageFileType;
    	              if (move_uploaded_file($_FILES["attached"]["tmp_name"], $filename))
    	              {
    	                  makeThumbnailsWithGivenWidthHeight($target_dir, $imageFileType, $nId, 100, 100);

    	                  UpdateRec("note_detail", "id = ".$nId, array("attached" => $filenameThumb, "filename" => basename($_FILES["attached"]["name"])));
    	              }
    	          }
            	$message = 'Nota creada con éxito!';
            }

            //echo '<script>window.location="'.$_SERVER['REQUEST_URI'].'"</script>';
    	}

/* Fin de la nota oportunidad */

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

      if($loggdUType != "Admin")
      {
        $where.=" and opportunity.id_user_register = ".$_SESSION['USER_ID'];
      }
      else
      {
       $bizname="";
       $stage="";
       $contactname="";
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

      }
      $arrUser = GetRecords("SELECT
                              opportunity.*,
                              contact.Name as contactname,
                              business.Name as bizname,
                              stage.Name_stage  as stagename,
                              next_step.detail as name_next_step,
                              concat(users.Name,' ',users.Last_name) as nombre_vendedor
                              from opportunity
                              inner join contact on contact.id  = opportunity.id_contact
                              inner join business on business.id  = contact.id_business
                              inner join stage on stage.id  = opportunity.id_stage
                              inner join next_step on next_step.id = opportunity.nextstep
                              inner join users on users.id = opportunity.id_user_register
                              $join
                              $where and opportunity.stat in (3)
                              order by opportunity.name");

?>
	<section id="content">
          <section class="vbox">
            <section class="scrollable padder">
              <section class="panel panel-default">
                <header class="panel-heading">
                  <?php if(isset($update_next_step) || isset($nId)){ ?>

                  <div class="alert alert-success">
                      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                      <strong>Realizado con éxito</strong>
                  </div>

                  <?php } ?>
                          <span class="h4">Oportunidades Cerradas(Won)</span>
                </header>
                <div class="panel-body">

                    <div class="table-responsive">
                        <table class="table table-striped b-t b-light" data-ride="datatables">
                          <thead>
                            <tr>
                              <!--<th>Nombre del Contacto</th>-->
                              <th>Nombre de la Oportunidad</th>
                              <?PHP if($loggdUType == "Admin") :  ?>
                              <th>Business Manager</th>
                              <?php endif; ?>
                              <th>Empresa</th>
                              <th>Monto </th>
                              <th>Proximo Paso</th>
                              <th>Fecha de Próximo Paso</th>
                              <!--<th>Status</th>-->
                              <th>Etapa</th>
                              <th>Acciones</th>
                            </tr>
                          </thead>
                          <tbody>
                          <?PHP
                            $i=1;
                            foreach ($arrUser as $key => $value) {

                              if($value['stat'] == 3){ $status ='Won';}
                                elseif($value['stat']==4){ $status ='Lost';}
                                  elseif($value['stat']==5){ $status ='Cancelled';}
                                    elseif($value['stat']==6){ $status ='Finished';}
                            ?>
                          <tr>
                              <?php /* ?><td class="tbdata"> <a href='opportunity-view.php?id=<?php echo $value['id']?>'><?php echo $value['contactname']?></a> </td><?php */ ?>
                              <td class="tbdata"><b><?php echo $value['name']?></b></td>
                              <?PHP if($loggdUType == "Admin") :  ?>
                              <td class="tbdata"><?php echo $value['nombre_vendedor']?></td>
                              <?php endif; ?>
                              <td class="tbdata"> <?php echo $value['bizname']?> </td>
                              <td class="tbdata"> <?php echo $value['estimated_amount']?> </td>
                              <td class="tbdata"> <?php /*echo date("M d, Y", strtotime($value['created_date'])) */?> <?php echo utf8_encode($value['name_next_step'])?> </td>
                              <td class="tbdata"> <?php echo $value['nextstep_date']?> </td>
                              <?php /* ?><td class="tbdata"> <?php echo $status?> </td> */ ?>
                              <td class="tbdata"> <?php echo utf8_encode($value['stagename'])?> </td>

                              <td class="tbdata" style="width:205px;">
                                  <a href="opportunity-view.php?id=<?php echo $value['id']?>" class="btn btn-sm btn-icon btn-info"><i class="fa fa-edit"></i></a>
                                  <?php /* ?><a href="modal-notes.php?id=<?php echo $value['id']?>&master_note=<?php echo $value['id_ref_master_note']?>" data-toggle="ajaxModal" class="btn btn-sm btn-icon btn-primary"><i class="i i-chat"></i></a>
                                  <a href="modal-next-step.php?next_step=<?php echo $value['id']?>&next_step_id=<?php echo $value['nextstep']?>&fecha=<?php echo $value['nextstep_date']?>" data-toggle="ajaxModal" class="btn btn-sm btn-icon btn-success"><i class="glyphicon glyphicon-calendar"></i></a>
                                  <a href="modal-notes.php?call=1&id=<?php echo $value['id']?>&master_note=<?php echo $value['id_ref_master_note']?>" data-toggle="ajaxModal" data-toggle="ajaxModal" class="btn btn-sm btn-icon btn-warning"><i class="fa fa-phone"></i></a>
                                  <a href="modal-etapa.php?etapa=<?php echo $value['id']?>&source=<?php echo $value['source']; ?>" data-toggle="ajaxModal" class="btn btn-sm btn-icon btn-default"><i class="glyphicon glyphicon-tasks"></i></a>
                                  <?PHP if($loggdUType == "Admin") :  ?>
                                  <a href="modal-usuario.php?usuario=<?php echo $value['id_user_register']?>&oportunidad_id=<?php echo $value['id']?>" data-toggle="ajaxModal" class="btn btn-sm btn-icon btn-danger"><i class="i i-user2"></i></a>
                                  <?php endif; ?> */ ?>
                              </td>
                              <!-- <td> <button type="button" onclick="window.location='opportunity-view.php?id=<?php echo $value['id']?>';" class="btn green btn-info">See Detail</button>
                              </td> -->
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
