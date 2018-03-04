<?php

    session_start();
    $businessclass="class='active'";
    $registerContclass="class='active'";

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
     $message="";

    if(isset($_POST['submitUser']))
     {
        $name = $_POST['name'];
        $busid = $_POST['busid'];
        $phone = $_POST['phone'];
        $email = $_POST['email'];
        $position = $_POST['position'];
        $description = $_POST['description'];
        $arrVal = array(
                      "Name" => $name,
                      "id_business" => $busid,
                      "Phone" => $phone,
                      "Email" => $email,
                      "position" => $position,
                      "Description" => $description,
                      "create_date" => date("Y-m-d H:i:s"),
                      "id_user_register" => $_SESSION['USER_ID'],
                      "stat" => 1
                     );

          $nId = InsertRec("contact", $arrVal);

          /* Log de Actividad */
            $mensaje = "Se ha creado un contacto. Contacto: '".$name."'";
            log_actividad(5, 1, $_SESSION['USER_ID'], $mensaje);
          /* Fin de log de actividad */

          if($nId > 0)
          {

              MySQLQuery("Insert into master_notes (id) values (0)");

              $mstId = mysql_insert_id();
              UpdateRec("contact", "id = ".$nId, array("id_ref_master_note" => $mstId));

              if($mstId > 0)
              {
                $notemsg = "New contact ".$name." registered by ".$_SESSION['USER_NAME'];
                $subj = "Contact ( ".$name." )";
                create_log($mstId, $subj, $notemsg);
              }

              if(isset($_FILES['logo']) && $_FILES['logo']['tmp_name'] != "")
              {
                  $target_dir = "contactimage/";
                  $target_file = $target_dir . basename($_FILES["logo"]["name"]);
                  $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
                  $filename = $target_dir . $nId.".".$imageFileType;
                  $filenameThumb = $target_dir . $nId."_thumb.".$imageFileType;
                  if (move_uploaded_file($_FILES["logo"]["tmp_name"], $filename))
                  {
                      makeThumbnailsWithGivenWidthHeight($target_dir, $imageFileType, $nId, 100, 100);

                      UpdateRec("contact", "id = ".$nId, array("Image" => $filenameThumb));
                  }
              }

              $message = '<div class="alert alert-success">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                      <strong>Contact registered successfully</strong>
                    </div>';
          }




     }
?>
	<section id="content">
          <section class="vbox">
            <section class="scrollable padder">

              <div class="row">
                <div class="col-sm-12">
                	<form class="form-horizontal" data-validate="parsley" method="post"   enctype="multipart/form-data" novalidate>
                      <section class="panel panel-default">
                        <header class="panel-heading">
                          <span class="h4">Register Contact</span>
                        </header>
                        <div class="panel-body">
                          <?php
                                if($message !="")
                                    echo $message;
                          ?>
                          <div class="form-group required">
                            <label class="col-lg-4 text-right control-label font-bold">Select Business</label>
                            <div class="col-lg-4">
                              <select class="chosen-select form-control"  name="busid" required="required">
                                <option value="">----------------</option>
                                <?PHP
                                $where = "";
                                 if($loggdUType != "Admin")
                                 {
                                   $where.=" and id_user_register=". $_SESSION['USER_ID'];
                                 }
                                $arrKindMeetings = GetRecords("Select * from business where stat = 1 $where");
                                foreach ($arrKindMeetings as $key => $value) {
                                  $kinId = $value['id'];
                                  $kinDesc = $value['Name'];
                                  $selRoll = (isset($_GET['bid']) && $_GET['bid'] == $kinId) ? 'selected' : '';
                                ?>
                                <option value="<?php echo $kinId?>" <?php echo $selRoll?>><?php echo $kinDesc?></option>
                                <?php
                            }
                                ?>
                              </select>
                            </div>
                          </div>
                          <div class="form-group required">
                            <label class="col-lg-4 text-right control-label font-bold">Name</label>
                            <div class="col-lg-4">
                              <input type="text" class="form-control" placeholder="Name" name="name" data-required="true">
                            </div>
                          </div>
                          <div class="form-group ">
                              <label class="col-lg-4 text-right control-label font-bold">Image</label>
                              <div class="col-lg-4">
                                  <input type="file" name="logo" class="form-control">
                                </div>
                          </div>
                          <div class="form-group required">
                            <label class="col-lg-4 text-right control-label font-bold">Phone</label>
                            <div class="col-lg-4">
                              <input type="text" class="form-control" placeholder="Phone" name="phone" data-required="true">
                            </div>
                          </div>
                          <div class="form-group ">
                            <label class="col-lg-4 text-right control-label font-bold">Email</label>
                            <div class="col-lg-4">
                              <input type="email" class="form-control" placeholder="Email" name="email">
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-lg-4 text-right control-label font-bold">Description</label>
                            <div class="col-lg-4">
                              <textarea rows="3" class="form-control" cols="44" name="description" placeholder="Description"></textarea>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-lg-4 text-right control-label font-bold">Position in the Business</label>
                            <div class="col-lg-4">
                              <input type="text" class="form-control" placeholder="Position in the Business" name="position">
                            </div>
                          </div>
                        </div>
                        <footer class="panel-footer text-right bg-light lter">
                          <button type="submit" name="submitUser" class="btn btn-primary btn-s-xs">Register Contact</button>
                        </footer>
                      </section>
                    </form>
                  </div>
              </div>
            </section>
        </section>
    </section>

<?php
	include("footer.php");
?>
