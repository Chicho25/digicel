<!-- .aside -->
        <aside class="bg-black aside-md hidden-print hidden-xs" id="nav">
          <section class="vbox">
            <section class="w-f scrollable">
              <div class="slim-scroll" data-height="auto" data-disable-fade-out="true" data-distance="0" data-size="10px" data-railOpacity="0.2">
                <div class="clearfix wrapper dk nav-user hidden-xs">
                  <div class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                      <span class="thumb avatar pull-left m-r">
                        <?php
                          $getuserDetail = GetRecord("users", "id =".$_SESSION['USER_ID']);

                          if(isset($getuserDetail['Image']) && $getuserDetail['Image'] != '')
                            $uimage = $getuserDetail['Image'];
                          else
                            $uimage = "images/p0.jpg";
                        ?>
                        <img src="<?php echo $uimage;?>" class="dker" alt="...">
                        <i class="on md b-black"></i>
                      </span>
                      <span class="hidden-nav-xs clear">
                        <span class="block m-t-xs">
                          <strong class="font-bold text-lt"><?php echo $_SESSION['USER_NAME']?></strong>
                          <b class="caret"></b>
                        </span>
                        <span class="text-muted text-xs block"><?php echo $_SESSION['USER_ROLE']?></span>
                      </span>
                    </a>
                    <ul class="dropdown-menu animated fadeInRight m-t-xs">
                      <!-- <li>
                        <span class="arrow top hidden-nav-xs"></span>
                        <a href="#">Settings</a>
                      </li>
                      <li>
                        <a href="profile.html">Profile</a>
                      </li>
                      <li>
                        <a href="docs.html">Help</a>
                      </li>
                      <li class="divider"></li> -->
                      <li>
                        <a href="logout.php"  >Salir</a>
                      </li>
                    </ul>
                  </div>
                </div>
                <!-- nav -->
                <nav class="nav-primary hidden-xs">
                  <div class="text-muted text-sm hidden-nav-xs padder m-t-sm m-b-sm">Inicio </div>
                  <ul class="nav nav-main" data-ride="collapse">
                    <?php if($loggdUType == "Region Master" || $loggdUType == "Admin" || $loggdUType == "Business Unit Manager") : ?>
                    <li <?php if(isset($userclass)) echo $userclass;?>>
                      <a href="#table" class="auto">
                        <span class="pull-right text-muted">
                          <i class="i i-circle-sm-o text"></i>
                          <i class="i i-circle-sm text-active"></i>
                        </span>
                        <i class="i i-dot"></i>
                        <span>Usuarios</span>
                      </a>
                      <ul class="nav dker">
                        <li <?php if(isset($registerclass)) echo $registerclass;?>>
                          <a href="register.php"><i class="i i-dot"></i>
                            <span>Registrar Usuarios</span>
                          </a>
                        </li>
                        <li <?php if(isset($userlistclass)) echo $userlistclass;?>>
                          <a href="users.php"><i class="i i-dot"></i>
                            <span>Ver Usuarios</span>
                          </a>
                        </li>
                      </ul>
                    </li>
                    <?php if($loggdUType != "Business Unit Manager") : ?>
                    <li <?php if(isset($countryclass)) echo $countryclass;?>>
                      <a href="#table" class="auto">
                        <span class="pull-right text-muted">
                          <i class="i i-circle-sm-o text"></i>
                          <i class="i i-circle-sm text-active"></i>
                        </span>
                        <i class="i i-dot"></i>
                        <span>Localidad</span>
                      </a>
                      <ul class="nav dker">
                        <li <?php if(isset($registerCntclass)) echo $registerCntclass;?>>
                          <a href="register-country.php"><i class="i i-dot"></i>
                            <span>Registrar País</span>
                          </a>
                        </li>
                        <li <?php if(isset($editCntclass)) echo $editCntclass;?>>
                          <a href="country.php"><i class="i i-dot"></i>
                            <span>Ver País</span>
                          </a>
                        </li>
                      </ul>
                    </li>
                    <?php endif;?>
                    <?php endif;?>
                    <?php if($loggdUType == "Admin" || $loggdUType == "Admin Telemarketing" || $loggdUType == "Business Manager" || $loggdUType == "Region Master" || $loggdUType == "Business Unit Manager") : ?>
                    <li <?php if(isset($businessclass)) echo $businessclass;?>>
                      <a href="#table" class="auto">
                        <span class="pull-right text-muted">
                          <i class="i i-circle-sm-o text"></i>
                          <i class="i i-circle-sm text-active"></i>
                        </span>
                        <i class="i i-dot"></i>
                        <span>Empresa</span>
                      </a>
                      <ul class="nav dker">
                        <li <?php if(isset($registerBusnclass)) echo $registerBusnclass;?>>
                          <a href="register-business.php"><i class="i i-dot"></i>
                            <span>Registrar Empresa</span>
                          </a>
                        </li>
                        <li <?php if(isset($editBusnclass)) echo $editBusnclass;?>>
                          <a href="business.php"><i class="i i-dot"></i>
                            <span>Ver Empresa</span>
                          </a>
                        </li>
                        <li <?php if(isset($registerContclass)) echo $registerContclass;?>>
                          <a href="register-contact.php"><i class="i i-dot"></i>
                            <span>Registrar Contacto</span>
                          </a>
                        </li>
                        <li <?php if(isset($editContclass)) echo $editContclass;?>>
                          <a href="contact.php"><i class="i i-dot"></i>
                            <span>Ver Contacto</span>
                          </a>
                        </li>
                      </ul>
                    </li>
                    <?php endif; ?>
                    <?php if($loggdUType == "Admin" || $loggdUType == "Business Manager" || $loggdUType == "Region Master" || $loggdUType == "Business Unit Manager") : ?>
                    <li <?php if(isset($productclass)) echo $productclass;?>>
                      <a href="#table" class="auto">
                        <span class="pull-right text-muted">
                          <i class="i i-circle-sm-o text"></i>
                          <i class="i i-circle-sm text-active"></i>
                        </span>
                        <i class="i i-dot"></i>
                        <span>Producto</span>
                      </a>
                      <ul class="nav dker">
                        <?php if($loggdUType == "Admin") : ?>
                        <li <?php if(isset($registerCateclass)) echo $registerCateclass;?>>
                          <a href="register-category.php"><i class="i i-dot"></i>
                            <span>Registrar Categoria</span>
                          </a>
                        </li>
                        <?php endif;?>
                        <?php if($loggdUType != "Business Manager") : ?>
                        <li <?php if(isset($editCateclass)) echo $editCateclass;?>>
                          <a href="category.php"><i class="i i-dot"></i>
                            <span>Ver Categoria</span>
                          </a>
                        </li>
                        <?php endif;?>
                        <?php if($loggdUType == "Admin") : ?>
                        <li <?php if(isset($registerProdclass)) echo $registerProdclass;?>>
                          <a href="register-product.php"><i class="i i-dot"></i>
                            <span>Registrar Producto</span>
                          </a>
                        </li>
                        <?php endif;?>
                        <li <?php if(isset($editProdclass)) echo $editProdclass;?>>
                          <a href="product.php"><i class="i i-dot"></i>
                            <span>Ver Producto</span>
                          </a>
                        </li>
                        <?php if($loggdUType != "Business Manager") : ?>
                        <?php if($loggdUType == "Admin") : ?>
                        <li <?php if(isset($registerProdTerrclass)) echo $registerProdTerrclass;?>>
                          <a href="register-product-by-territory.php"><i class="i i-dot"></i>
                            <span>Registrar Producto por Territorio</span>
                          </a>
                        </li>
                        <?php endif;?>
                        <li <?php if(isset($editProdTerrclass)) echo $editProdTerrclass;?>>
                          <a href="product-territory.php"><i class="i i-dot"></i>
                            <span>Ver Producto por Territorio</span>
                          </a>
                        </li>
                        <?php endif;?>
                      </ul>
                    </li>
                    <?php endif;?>
                    <?php if($loggdUType == "Admin" || $loggdUType == "Admin Telemarketing" || $loggdUType == "Business Manager" || $loggdUType == "Region Master" || $loggdUType == "Business Unit Manager") : ?>
                    <li <?php if(isset($opportunityclass)) echo $opportunityclass;?>>
                      <a href="#table" class="auto">
                        <span class="pull-right text-muted">
                          <i class="i i-circle-sm-o text"></i>
                          <i class="i i-circle-sm text-active"></i>
                        </span>
                        <i class="i i-dot"></i>
                        <span>Oportunidad</span>
                      </a>
                      <ul class="nav dker">
                        <li <?php if(isset($registerOpporclass)) echo $registerOpporclass;?>>
                          <a href="register-opportunity-step1.php"><i class="i i-dot"></i>
                            <span>Registrar Oportunidad</span>
                          </a>
                        </li>
                        <li <?php if(isset($editOpporclass)) echo $editOpporclass;?>>
                          <a href="opportunity.php"><i class="i i-dot"></i>
                            <span>Lista de Oportunidades</span>
                          </a>
                        </li>
                        <li <?php if(isset($closedOpporclass)) echo $closedOpporclass;?>>
                          <a href="opportunity_closed.php">
                            <i class="i i-dot"></i>
                            <span>Oportunidades Cerradas</span>
                          </a>
                        </li>
                        <li <?php if(isset($winsOpporclass)) echo $winsOpporclass;?>>
                          <a href="opportunity_wins.php">
                            <i class="i i-dot"></i>
                            <span>Oportunidades Ganadas</span>
                          </a>
                        </li>
                        <?php if($loggdUType != "Business Manager") : ?>
                        <li <?php if(isset($noasigOpporclass)) echo $noasigOpporclass;?>>
                          <a href="list_oppprtunities_to_assign.php">
                            <i class="i i-dot"></i>
                            <span>Oportunidades sin asignar</span>
                          </a>
                        </li>
                        <?php endif; ?>
                      </ul>
                    </li>
                    <?php endif; ?>
                    <!-- Agregado -->
                    <?php if($loggdUType == "Admin" || $loggdUType == "Admin Telemarketing" || $loggdUType == "Telemarketing" || $loggdUType == "Region Master" || $loggdUType == "Business Unit Manager") : ?>
                      <li <?php if(isset($campanatyclass)) echo $campanatyclass;?>>
                        <a href="#table" class="auto">
                          <span class="pull-right text-muted">
                            <i class="i i-circle-sm-o text"></i>
                            <i class="i i-circle-sm text-active"></i>
                          </span>
                          <i class="i i-dot"></i>
                          <span>Campaña</span>
                        </a>
                        <ul class="nav dker">
                          <?php if($loggdUType == "Admin" || $loggdUType == "Admin Telemarketing" || $loggdUType == "Region Master" || $loggdUType == "Business Unit Manager") : ?>
                          <li <?php if(isset($registerCampanaclass)) echo $registerCampanaclass;?>>
                            <a href="register_campaign.php"><i class="i i-dot"></i>
                              <span>Crear Campaña</span>
                            </a>
                          </li>
                          <?php endif;?>
                          <li <?php if(isset($ViewCampaignclass)) echo $ViewCampaignclass;?>>
                            <a href="campaign_view.php"><i class="i i-dot"></i>
                              <span>Ver Campañas</span>
                            </a>
                          </li>
                        </ul>
                      </li>
                    <?php endif;?>
                    <?php if($loggdUType == "Admin" || $loggdUType == "Admin Telemarketing" || $loggdUType == "Business Unit Manager") : ?>
                    <li <?php if(isset($activityclass)) echo $activityclass; ?>>
                      <a href="#table" class="auto">
                        <span class="pull-right text-muted">
                          <i class="i i-circle-sm-o text"></i>
                          <i class="i i-circle-sm text-active"></i>
                        </span>
                        <i class="i i-dot"></i>
                        <span>Reportes</span>
                      </a>
                      <ul class="nav dker">
                        <?php if($loggdUType == "Admin" || $loggdUType == "Business Unit Manager"): ?>
                        <li <?php if(isset($activitylistclass)) echo $activitylistclass; ?>>
                          <a href="activity.php"><i class="i i-dot"></i>
                            <span>Actividad</span>
                          </a>
                        </li>
                        <?php endif; ?>
                        <li <?php if(isset($activitytelemarketign)) echo $activitytelemarketign; ?>>
                          <a href="report_telemarketign.php"><i class="i i-dot"></i>
                            <span>Telemarketing</span>
                          </a>
                        </li>
                      </ul>
                    </li>
                  <?php endif; ?>
                  <?php if($loggdUType == "Admin" || $loggdUType == "Region Master" || $loggdUType == "Business Unit Manager"): ?>
                 <li <?php if(isset($propuestapendiente)) echo $propuestapendiente;?>>
                   <a href="#table" class="auto">
                     <span class="pull-right text-muted">
                       <i class="i i-circle-sm-o text"></i>
                       <i class="i i-circle-sm text-active"></i>
                     </span>
                     <i class="i i-dot"></i>
                     <span>Propuestas</span>
                   </a>
                   <ul class="nav dker">
                     <li <?php if(isset($editpendiente)) echo $editpendiente; ?>>
                       <a href="propuestas_pendientes.php"><i class="i i-dot"></i>
                         <span>Propuestas Pendientes</span>
                       </a>
                     </li>
                     <li <?php if(isset($editaprobada)) echo $editaprobada; ?>>
                       <a href="propuestas_aprobadas.php"><i class="i i-dot"></i>
                         <span>Propuestas Enviadas</span>
                       </a>
                     </li>
                     <li <?php if(isset($editrechazada)) echo $editrechazada; ?>>
                       <a href="propuestas_rechazadas.php"><i class="i i-dot"></i>
                         <span>Propuestas Rechazadas</span>
                       </a>
                     </li>
                   </ul>
              </li>
              <?php endif; ?>
              </ul>
            </nav>
          </div>
        </section>
        <footer class="footer hidden-xs no-padder text-center-nav-xs">
          <a href="modal.lockme.html" data-toggle="ajaxModal" class="btn btn-icon icon-muted btn-inactive pull-right m-l-xs m-r-xs hidden-nav-xs">
            <i class="i i-logout"></i>
          </a>
          <a href="#nav" data-toggle="class:nav-xs" class="btn btn-icon icon-muted btn-inactive m-l-xs m-r-xs">
            <i class="i i-circleleft text"></i>
            <i class="i i-circleright text-active"></i>
          </a>
        </footer>
      </section>
    </aside>
