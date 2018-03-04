<?php
  ob_start();
  session_start();
  $hideLeft = true;
  include("include/config.php");
  include("include/defs.php");
  $loggdUType = current_user_type();

  include("header.php");

  if(!isset($_SESSION['USER_ID']))
     {
          header("Location: index.php");
          exit;
     }
 ?>

 <section id="content">
          <section class="vbox">
            <section class="scrollable padder">
              <div class="row">
                <div class="col-sm-12">
                    Welcome<br /><br /><br /><br /><br /><br />
                </div>
              </div>
            </section>
          </section>
  </section>

<?php include("footer.php"); ?>
