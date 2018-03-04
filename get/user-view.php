<?php

    //ob_start();
    $userclass="class='active'";
    $userlistclass="class='active'";

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
    if(isset($_GET['id']))
     {
        $arrUser = GetRecords("SELECT users.*, country.name as country,
                                    region.name as region, territory.name as territory
                             from users
                             left join location_user on location_user.id_user = users.id
                             left join territory on territory.id = location_user.id_territory
                             left join region on region.id = territory.id_region
                             left join country on country.id = region.id_country

                             WHERE users.id = ".$_GET['id']."
                             ");

     }
?>
	<section id="content">
          <section class="vbox">
            <section class="scrollable padder">

              <div class="row">
                <div class="col-sm-12">
                	<form class="form-horizontal" data-validate="parsley" action="edit-user.php?id=<?php echo $_GET['id']?>" method="post">
                      <section class="panel panel-default">
                        <header class="panel-heading">
                          <span class="h4">Ver usuario</span>
                        </header>
                        <div class="panel-body">

                          <div class="form-group">
                            <div class="col-lg-12 col-lg-offset-3 ">
                              <div style="width:204px;
                                                            height:154px;
                                                            background-color: #cccccc;
                                                            border: solid 2px gray;
                                                            margin: 5px;">
                                                    <img id="img" src="<?php echo $arrUser[0]['Image']?>" style='width:200px; height:150px;' " border="0" alt="your image" />
                                                </div>
                              </div>
                          </div>
                          <div class="form-group">
                            <label class="col-lg-4 text-right  font-bold">Nombre</label>
                            <div class="col-lg-4">
                              <?php echo $arrUser[0]['Name'] ?>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-lg-4 text-right  font-bold">Apellido</label>
                            <div class="col-lg-4">
                              <?php echo $arrUser[0]['Last_name'] ?>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-lg-4 text-right  font-bold">Usuario</label>
                            <div class="col-lg-4">
                              <?php echo $arrUser[0]['user'] ?>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-lg-4 text-right  font-bold">Email</label>
                            <div class="col-lg-4">
                              <?php echo $arrUser[0]['Email'] ?>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-lg-4 text-right  font-bold">País</label>
                            <div class="col-lg-4">
                              <label><?php echo $arrUser[0]['country'] ?></label>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-lg-4 text-right  font-bold">Region</label>
                            <div class="col-lg-4">
                              <label><?php echo $arrUser[0]['region'] ?></label>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-lg-4 text-right  font-bold">Territorio</label>
                            <div class="col-lg-4">
                              <label><?php echo $arrUser[0]['territory'] ?></label>
                            </div>
                          </div>
                        </div>
                        <?php if($loggdUType == "Admin") { ?>
                        <footer class="panel-footer text-right bg-light lter">
                          <button type="submit" name="editUser" class="btn btn-primary btn-s-xs">Editar Usuario</button>
                        </footer>
                        <?php } ?>
                      </section>
                    </form>
                    <?php
                      $userrefid = $arrUser[0]['id_ref_master_note'];
                      $userimage = $arrUser[0]['Image'];
                      include("notes-attached.php");
                    ?>
                  </div>
              </div>
            </section>
        </section>
    </section>
<?php
	include("footer.php");
?>
