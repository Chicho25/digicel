<?php
    ob_start();
    $opportunityclass="class='active'";
    $noasigOpporclass="class='active'";
    include("include/config.php");
    include("include/defs.php");
    $loggdUType = current_user_type();
    $loggdURegion = current_user_region();
    include("header.php");
    $message="";

    if(!isset($_SESSION['USER_ID']))
     {
          header("Location: index.php");
          exit;
     }

     if(isset($_POST['submitcheckoportunity'])){

       $getrefid = GetRecord("campaign", "id = ".$_POST['id_campaign']);

       $lead = $getrefid['lead'];

       $arrVal = array(
                       "lead" => $lead+1
                      );

       UpdateRec("campaign", "id = ".$_POST['id_campaign'], $arrVal);

     }

     if(isset($_POST['submitcheckoportunity'])){

         $arrVal = array(
                       "verified_opportunity" => 1,
                       "id_user_updated" => $_SESSION['USER_ID'],
                       "result" => 6
                      );

         UpdateRec("suspects", "id = ".$_POST['id_suspect'], $arrVal);

         $verificarEmpresa = GetRecords("Select name from campaign where id = '".$_POST['id_campaign']."'");
         $name_campaign = $verificarEmpresa[0]['name'];

         /* Log de Actividad */
         $mensaje = "Se ha aprobado una Oportunidad de la campaña '".$name_campaign."'";
         log_actividad(9, 12, $_SESSION['USER_ID'], $mensaje);
         /* Fin de log de actividad */

         $message .= '<div class="alert alert-success">
                     <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                       <strong>Se aprobó la Oportunidad</strong>
                     </div>';

     }

     if(isset($_POST['submitUsuarioOpp'])){

         $arrVal = array(
                       "assign_opp_user" => $_POST['id_usuario'],
                       "id_user_updated" => $_SESSION['USER_ID'],
                       "stat" => 2
                      );

         UpdateRec("suspects", "id = ".$_POST['id_suspect'], $arrVal);

         $ObtenerRegistrosSospe = GetRecords("Select * from suspects where id = '".$_POST['id_suspect']."'");

         $business = $ObtenerRegistrosSospe[0]['business'];
         $phone_business = $ObtenerRegistrosSospe[0]['phone_office'];
         $email_business = $ObtenerRegistrosSospe[0]['email'];
         $domain = $ObtenerRegistrosSospe[0]['domain'];
         $contact_name = $ObtenerRegistrosSospe[0]['customer_client'];
         $contact_position = $ObtenerRegistrosSospe[0]['position'];
         $contact_email = $ObtenerRegistrosSospe[0]['email_contact'];
         $contact_phone = $ObtenerRegistrosSospe[0]['cell_phone'];


         if(!is_numeric($business)){

             $name = $business;
             $phone = $phone_business;
             $email = $email_business;
             $url = $domain;

             $arrVal = array(
                           "Name" => $name,
                           "Phone" => $phone,
                           "Email" => $email,
                           "Url" => $url,
                           "create_date" => date("Y-m-d H:i:s"),
                           "id_user_register" => $_SESSION['USER_ID'],
                           "stat" => 1
                          );

               $business = InsertRec("business", $arrVal);

               /* Log de Actividad */
                 $mensaje = "Se ha creado una empresa al momento de asignar una oportunidad proveniente de una campaña. Empresa: '".$name."'";
                 log_actividad(5, 1, $_SESSION['USER_ID'], $mensaje);
               /* Fin de log de actividad */

               if($business > 0)
               {

                   MySQLQuery("Insert into master_notes (id) values (0)");

                   $mstId = mysql_insert_id();
                   UpdateRec("business", "id = ".$business, array("id_ref_master_note" => $mstId));

                   if($mstId > 0)
                   {
                     $notemsg = "Nueva Compañia ".$name." Registrada por ".$_SESSION['USER_NAME'];
                     $subj = "Business ( ".$name." )";
                     create_log($mstId, $subj, $notemsg);
                   }

                   $message .= '<div class="alert alert-success">
                               <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                 <strong>Se ha registrado una empresa</strong>
                               </div>';
               }

             }


                 $name = $contact_name;
                 $busid = $business;
                 $phone = $contact_phone;
                 $email = $contact_email;
                 $position = $contact_position;

                 $arrVal = array(
                               "Name" => $name,
                               "id_business" => $busid,
                               "Phone" => $phone,
                               "Email" => $email,
                               "position" => $position,
                               "create_date" => date("Y-m-d H:i:s"),
                               "id_user_register" => $_SESSION['USER_ID'],
                               "stat" => 1
                              );

                   $nId = InsertRec("contact", $arrVal);

                   /* Log de Actividad */
                     $mensaje = "Se ha creado un contacto al momento de asignar una oportunidad proveniente de una campaña. Contacto: '".$name."'";
                     log_actividad(5, 1, $_SESSION['USER_ID'], $mensaje);
                   /* Fin de log de actividad */

                   if($nId > 0)
                   {

                       MySQLQuery("Insert into master_notes (id) values (0)");

                       $mstId = mysql_insert_id();
                       UpdateRec("contact", "id = ".$nId, array("id_ref_master_note" => $mstId));

                       if($mstId > 0)
                       {
                         $notemsg = "Nuevo contacto ".$name." Registrado Por ".$_SESSION['USER_NAME'];
                         $subj = "Contact ( ".$name." )";
                         create_log($mstId, $subj, $notemsg);
                       }

                       $message .= '<div class="alert alert-success">
                                   <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                     <strong>Contacto registrado</strong>
                                   </div>';
                   }

                   $verificarEmpresa = GetRecords("Select name from campaign where id = '".$_POST['id_campaign']."'");
                   $name_campaign = $verificarEmpresa[0]['name'];

                   $verificarEmpresa = GetRecords("Select Name, Last_name, Email from users where id = '".$_POST['id_usuario']."'");
                   $name_user = $verificarEmpresa[0]['Name'].' '.$verificarEmpresa[0]['Last_name'];
                   $email_user = $verificarEmpresa[0]['Email'];

                   $arrVal = array(
                                 "name" => $name_campaign,
                                 "id_contact" => $nId,
                                 "description" => "Esta Oportunidad se Genero de la campaña '".$name_campaign."'",
                                 "nextstep_date" => date("Y-m-d H:i:s"),
                                 "estimated_amount" => 0,
                                 "source" => 3,
                                 "nextstep" => 1,
                                 "id_user_register" => $_POST['id_usuario'],
                                 "created_date" => date("Y-m-d H:i:s"),
                                 "stat" => 1,
                                 "id_stage" => 1
                                );

                     $OppId = InsertRec("opportunity", $arrVal);

                     /* Log de Actividad */
                       $mensaje = "Se ha creado una oportunidad proveniente de una campaña.  Oportunidad: '".$name_campaign."'";
                       log_actividad(7, 1, $_SESSION['USER_ID'], $mensaje);
                     /* Fin de log de actividad */

                     if($OppId > 0)
                     {

                         MySQLQuery("Insert into master_notes (id) values (0)");

                         $mstId = mysql_insert_id();
                         UpdateRec("opportunity", "id = ".$OppId, array("id_ref_master_note" => $mstId));

                         if($mstId > 0)
                         {
                           $notemsg = "Nueva Oportunidad ".$name_campaign." Registrada Por ".$_SESSION['USER_NAME'];
                           $subj = "opportunity ( ".$name_campaign." ) registered";
                           create_log($mstId, $subj, $notemsg);
                         }

                         $message .= '<div class="alert alert-success">
                                     <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                       <strong>Oportunidad registrada</strong>
                                     </div>';
                     }
                     else
                     {
                       echo "<script>alert('Ocurrio un problema');window.location='register-opportunity-step1.php';</script>";
                     }

         /* Log de Actividad */
         $mensaje = "Se ha asignado una Oportunidad a un Bussiness Manager: '".$name_user."' de la campaña '".$name_campaign."'";
         log_actividad(9, 12, $_SESSION['USER_ID'], $mensaje);
         /* Fin de log de actividad */

         $message .= '<div class="alert alert-success">
                     <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                       <strong>Se asigno la Oportunidad</strong>
                     </div>';

           /* Email */

           $actual_link = "http://".$_SERVER['HTTP_HOST']."//opportunity.php";

           $admin_email = "smccape@gmail.com";
           $admin_email = "dev@dchain.com";
           $subject = "dChain GET: Nueva Oportunidad";
           $headers = "MIME-Version: 1.0\\r\ ";
           $headers .= "Content-type: text/html; charset=iso-8859-1\\r\ ";
           $headers .= "From: ".$admin_email;
           $comment = " Hola ".$name_user.",\n\n Se te ha asignado una nueva oportunidad '".$name_campaign."', Ingrese al sistema para mas detalles " . $actual_link . " ";
           /*$send = mail($email_user, "$subject", $comment, $headers);*/

           include("mailjet/src/Mailjet/php-mailjet-v3-simple.class.php");

           $apiKey = '16ecb7873995588027a5cef50f59b719';
           $secretKey = '06e6276f1fe3249498c103b601869f58';

           $mj = new Mailjet($apiKey, $secretKey);
           if (isset($_POST['submitUsuarioOpp'])) {

               function sendEmail($email, $subject, $comment, $admin_email) {
                   // Create a new Object
                   $mj = new Mailjet();
                   $params = array(
                       "method" => "POST",
                       "from" => "{$admin_email}",
                       "to" => "{$email}",
                       "subject" => "{$subject}",
                       "text" => "{$comment}"
                   );
                   $result = $mj->sendEmail($params);
                   if ($mj->_response_code == 200) {
                       //echo "success - email sent";
                       print '<script type="text/javascript">';
                       print 'alert("email successfully sent!")';
                       print '</script>';
                   } elseif ($mj->_response_code == 400) {
                       //echo "error - " . $mj->_response_code;
                       print '<script type="text/javascript">';
                       print 'alert("Bad Request! One or more arguments are missing or maybe mispelling.")';
                       print '</script>';
                   } elseif ($mj->_response_code == 401) {
                       //echo "error - " . $mj->_response_code;
                       print '<script type="text/javascript">';
                       print 'alert("Unauthorized! You have specified an incorrect ApiKey or username/password couple.")';
                       print '</script>';
                   } elseif ($mj->_response_code == 404) {
                       //echo "error - " . $mj->_response_code;
                       print '<script type="text/javascript">';
                       print 'alert("Not Found! The method your are trying to reach don\'t exists.")';
                       print '</script>';
                   } elseif ($mj->_response_code == 405) {
                       //echo "error - " . $mj->_response_code;
                       print '<script type="text/javascript">';
                       print 'alert("Method Not Allowed! You made a POST request instead of GET, or the reverse.")';
                       print '</script>';
                   } else {
                       print '<script type="text/javascript">';
                       print 'alert(" Internal Server Error! Status returned when an unknow error occurs")';
                       print '</script>';
                   }

                   return $result;
               }

               sendEmail($email_user, $subject, $comment, $admin_email);

     }

   }

      /*$where = "where (1=1)";
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

      }*/

      $arrUser = GetRecords("select
                             suspects.id as id_suspect,
                             suspects.business,
                             suspects.id_user_updated,
                             suspects.id_ref_master_note,
                             suspects.date_create,
                             suspects.verified_opportunity,
                             campaign.id as id_campaign,
                             campaign.name as name_campaign,
                             suspects.result,
                             suspects.date_lead,
                             users.Name,
                             users.Last_name
                             from campaign inner join suspects on suspects.id_campaign = campaign.id
                                           inner join users on users.id = suspects.id_user_updated

                             where
                             suspects.result in(1, 6)
                             and
                             suspects.stat=1");

?>
<section id="content">
      <section class="vbox">
        <section class="scrollable padder">
          <section class="panel panel-default">
            <?php
                  if($message !="")
                      echo $message;
            ?>
            <header class="panel-heading">
                <span class="h4">Oportunidades sin asignar</span>
            </header>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-striped b-t b-light" data-ride="datatables">
                      <thead>
                        <tr>
                          <th>Generado por</th>
                          <th>Nombre de la Oportunidad</th>
                          <th>Empresa</th>
                          <th>Fecha Creación</th>
                          <th>Acciones</th>
                        </tr>
                      </thead>
                      <tbody>
                      <?PHP
                        $i=0;
                        foreach ($arrUser as $key => $value) {
                          $i++; ?>
                      <tr>
                          <td class="tbdata"> <?php echo $value['Name'].' '.$value['Last_name']?> </td>
                          <td class="tbdata"> <?php echo $value['name_campaign']?> </td>
                          <td class="tbdata">
                          <?php if(is_numeric($value['business'])){
                            $arrbusiness = GetRecords("Select * from business WHERE id = '".$value['business']."'");
                            $name_business = $arrbusiness[0]["Name"];
                            echo $name_business;
                          }else{
                            echo $value['business'];
                          }?> </td>
                          <td class="tbdata"> <?php echo $value['date_lead']?> </td>
                          <td class="tbdata" style="width:205px;">
                            <a href="detail_suspect.php?id_suspect=<?php echo $value['id_suspect']?>&id_campaign=<?php echo $value['id_campaign']?>" title="Ver detalle de: " class="btn btn-sm btn-icon btn-info"><i class="fa fa-edit"></i></a>
                            <?PHP if($loggdUType == "Business Unit Manager" || $loggdUType == "Admin" && $value['verified_opportunity']==1 ) :  ?>
                            <a href="modal-usuario-assign-opp.php?id_suspect=<?php echo $value['id_suspect']?>&master_note=<?php echo $value['id_ref_master_note']?>&id_campaign=<?php echo $value['id_campaign']?>" title="Asignar un usuario" data-toggle="ajaxModal" class="btn btn-sm btn-icon btn-danger"><i class="i i-user2"></i></a>
                            <?php endif; ?>
                            <a href="modal-oppotunity-check.php?id_suspect=<?php echo $value['id_suspect']?>&master_note=<?php echo $value['id_ref_master_note']?>&id_campaign=<?php echo $value['id_campaign']?>" title="Aprobar una Oportunidad" data-toggle="ajaxModal" class="btn btn-sm btn-icon <?php if($value['verified_opportunity']==0){ ?>btn-success"<?php }else{ ?> btn" style="background-color:gray;" <?php } ?>><i class="fa fa-check"></i></a>
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

<?php
	include("footer.php");
?>
