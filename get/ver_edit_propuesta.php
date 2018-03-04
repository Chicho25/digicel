<?php

    ob_start();
    $propuestapendiente="class='active'";
    $editpendiente="class='active'";

    include("include/config.php");
    include("include/defs.php");
    $loggdUType = current_user_type();
    $loggdURegion = current_user_region();

    include("header.php");

    /* Editar el documento */

      $editar = 0;
    if(isset($_POST['editar'])){
      $editar = 1;
    }

    if(isset($_POST['guardar'])){

      $guardada = MySQLQuery("UPDATE
                             proposal
                             SET
                              message = '".$_POST['mensaje']."',
                              email_cc = '".$_POST['user_email']."'
                             WHERE
                              id =".$_POST['id_propuesta']);

    }

    /* fin de la edicion del documento */

    if(!isset($_SESSION['USER_ID']))
     {
          header("Location: index.php");
          exit;
     }

     if(isset($_GET['id'])){
     $id_propuesta = $_GET['id'];
     }

     $arrPro = GetRecords("select
                            opportunity.id as id_oportunidad,
                            proposal.id,
                            opportunity.name as oportunidad,
                            opportunity.proposal_date,
                            business.name as empresa,
                            proposal.create_date,
                            proposal.amount,
                            proposal.message,
                            proposal.email,
                            proposal.email_cc,
                            users.Name,
                            users.Last_name,
                            users.email as userEmail,
                            attached
                            from
                            opportunity inner join contact on opportunity.id_contact = contact.id
                            			      inner join business on contact.id_business = business.id
                                        inner join proposal on opportunity.id = proposal.id_oppottunity
                                        inner join users on users.id = proposal.id_user
                            WHERE
                            proposal.id =".$id_propuesta);

      if(isset($_POST['aprobada'])){

        $aprobada = MySQLQuery("UPDATE
                               proposal
                               SET
                                status = 2,
                                message = '".$_POST['mensaje']."'
                               WHERE
                               id =".$_POST['id_propuesta']);

/* Email */

$email_cliente = $_POST["email_cliente"];
$email_copia = $_POST["email_copia"];
$actual_link = "http://".$_SERVER['HTTP_HOST']."/opportunity.php";
$adjunto_link = "http://".$_SERVER['HTTP_HOST']."/download.php?file_code=".$_POST['file']."";
$admin_email = "dev@dchain.com";
$subject = "dChain GET: Propuesta Aprobada";
$subject_customers = "Propuesta Adjunta";
$headers = "MIME-Version: 1.0\\r\ ";
$headers .= "Content-type: text/html; charset=iso-8859-1\\r\ ";
$headers .= "From: ".$admin_email;
$comment = "Se aprobó la propuesta Ingrese al sistema para mas detalles ".$actual_link."
            Propuesta Adjunta: ".$adjunto_link."
            El mensaje Adjunto a la propuesta es: '".$_POST['mensaje']."' ";

$comment_customer = "Propuesta Adjunta: ".$adjunto_link."
                     El mensaje Adjunto: '".$_POST['mensaje']."' ";


include("mailjet/src/Mailjet/php-mailjet-v3-simple.class.php");

$apiKey = 'a65e4a94cfb69e119f2650cc394fd958';
$secretKey = '100dfed2fc46c031c6bc242e108e7f93';

$mj = new Mailjet($apiKey, $secretKey);
if (isset($_POST['id_propuesta'])) {

   function sendEmail($email, $subject, $comment, $admin_email, $cc) {
       // Create a new Object
       $mj = new Mailjet();
       $params = array(
           "method" => "POST",
           "from" => "{$admin_email}",
           "to" => "{$email}",
           "cc" => "{$cc}",
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

   if (isset($_POST['send_customer']) && $_POST['send_customer'] == 1) {
     sendEmail($email_cliente, $subject_customers, $comment_customer, $admin_email, $email_copia);
   }else{
     sendEmail($email_copia, $subject, $comment, $admin_email, $email_copia);
 }
}

/* Fin Email */

       /* Log de Actividad */
         $mensaje = "Se ha aprobado la propuesta para la oportunidad:  '".$_POST['nombre_oportunidad']."' Fecha:".date("Y-m-d H:i:s");
         $arrVal = array(
                         "id_module" => 8,
                         "id_action" => 2,
                         "description" => $mensaje,
                         "status" => 1,
                         "id_user" => $_SESSION['USER_ID']
                        );
             InsertRec("log_activity", $arrVal);
       /* Fin de log de actividad */

      }

     if(isset($_POST['id_propuesta'], $_POST['messaje'])){

       $insertar_registro = MySQLQuery("UPDATE
                                        proposal
                                        SET rejection_message = '".$_POST['messaje']."',
                                            status = 3
                                        WHERE
                                        id =".$_POST['id_propuesta']);

      /* Log de Actividad */
        $mensaje = "La propuesta se ha enviado a revisión Fecha:".date("Y-m-d H:i:s");
        $arrVal = array(
                        "id_module" => 8,
                        "id_action" => 2,
                        "description" => $mensaje,
                        "status" => 1,
                        "id_user" => $_SESSION['USER_ID']
                       );
            InsertRec("log_activity", $arrVal);
      /* Fin de log de actividad */
     }
?>
	<section id="content">

      <?php if(isset($insertar_registro) || isset($aprobada)){ ?>

      <div class="alert alert-success">
          <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
          <strong>Realizado con éxito</strong>
      </div>

      <?php } ?>

          <section class="vbox">
            <section class="scrollable padder">
              <section class="panel panel-default">
                <header class="panel-heading">
                          <span class="h4">Envio de Propuestas</span>
                </header>
                <div class="panel-body">
                  <form data-validate="parsley" action="" method="post">

                  <?php foreach ($arrPro as $key => $value) { ?>

                      <div class="panel-body">
                        <p class="text-muted" >
                          <i class="fa fa-file-pdf-o" style="font-size:30px; float:left; position:relative; padding-right:10px;"></i>
                          <?php $key = "Ma&&&#$%%!#$%&/()/&$##%%"; ?>
                          <p style="margin-top:-15px;"><a href="download.php?file_code=<?php echo md5($key.$value['attached']);?>">Propuesta PDF.</a><br>
                          <?php echo '<b>'.$value['Name'].' '.$value['Last_name'].'</b>'; ?>
                          </p>
                        </p>

                          <div class="text-right" style="position:absolute; right:50px; top:40px;">
                            <span style="font-size:30px;"><?php echo date("d",strtotime($value['create_date'])); ?></span><br>
                            <span class="badge bg-success"><?php echo date("M",strtotime($value['create_date'])); ?></span>
                          </div>

                          <div class="form-group pull-in clearfix">
                            <div class="col-sm-6">
                              <label>Cliente </label>
                              <input type="text" readonly value="<?php echo $value['empresa']; ?>" class="form-control" name="nombre_cliente" data-required="true">
                            </div>
                            <div class="col-sm-6">
                              <label>Nombre de la Oportunidad</label>
                              <input type="text" <?php if($editar==0){ echo 'readonly';} ?> value="<?php echo $value['oportunidad']; ?>" class="form-control" name="nombre_oportunidad" data-required="true">
                            </div>

                          <div class="col-sm-6">
                            <label>Email</label>
                            <input type="email" readonly value="<?php echo $value['email']; ?>" class="form-control" name="email" data-required="true">
                          </div>
                          <div class="col-sm-6">
                            <label>CC</label>
                            <input type="email" <?php if($editar==0){ echo 'readonly';} ?> value="<?php if($value['email_cc']!=""){ echo $value['email_cc']; }else{ echo $value['userEmail']; } ?>" class="form-control" name="user_email" data-required="true">
                          </div>

                        </div>
                        <div class="form-group">
                          <label>Mensaje</label>
                          <textarea <?php if($editar==0){ echo 'readonly';} ?> class="form-control" name="mensaje" rows="6" data-required="true" placeholder=""><?php echo $value['message']; ?></textarea>
                        </div>

                      </div>

                      <?php if(isset($aprobada) || isset($insertar_registro)){}else{ ?>
                      <?php  $mensaje=$value['message']; ?>
                      <footer class="panel-footer text-left bg-light lter">
                        <a href="modal-propuesta_rechazada.php?id=<?php echo $value['id']?>" title="Mensaje" data-toggle="ajaxModal" class="btn btn-s-md btn-danger btn-rounded">Volver a Revisar</a>
                        <!--<button type="submit" class="btn btn-s-md btn-danger btn-rounded"></button>-->
                        <a href="modal-aprobar-propuesta.php?id=<?php echo $value['id']?>&email_cliente=<?php echo $value['email']; ?>&email_copia=<?php if($value['email_cc']!=""){ echo $value['email_cc']; }else{ echo $value['userEmail']; } ?>&id_oportunidad=<?php echo $value['id_oportunidad']; ?>&file=<?php echo md5($key.$value['attached']);?>&menssajj=<?php echo str_replace(" ", "_", $mensaje); ?>" data-toggle="ajaxModal" class="btn btn-s-md btn-success btn-rounded">Enviar</a>
                        <a href="propuestas_pendientes.php" class="btn btn-s-md btn-default btn-rounded">Cancelar</a>
                        <button type="submit" <?php if($editar==0){ echo 'name="editar"';}else{ echo 'name="guardar"';} ?> class="btn btn-s-md btn-default btn-primary btn-rounded"><?php if($editar==0){ echo 'Editar';}else{ echo 'Guardar';} ?></button>
                      </footer>
                      <?php } ?>
                      <?php } ?>
                      <input type="hidden" name="id_propuesta" value="<?php echo $id_propuesta; ?>">
                  </form>
                </div>
              </section>
            </section>
        </section>
    </section>

<?php
	include("footer.php");
?>
