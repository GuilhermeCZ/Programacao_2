<?php
	/******************************************************************
	* reg_techn page file, included by start if selected (also ?p=3)  *
	* dependencies: session &$ (login); pgsql as $_pgobj (config);    *
	* msgs as $_msg (config); paths as $_path (config).                 *
	*******************************************************************/

	// ----- Checking Dependecies ----- //
	if(!isset($_msg)) die("Error: Messages Class not Initialized!");
	if(!isset($_session)) $_msg->err("Class Session not set!");
	if(!isset($_pgobj)) $_msg->err("Class PgSQL not set!");
	if(!isset($_path)) $_msg->err("Class Path not set!");
?>
					<div class="row">
						<div class="x_panel">
							<div class="x_title">
								<h2><?= $_msg->lang("Register") ." &raquo; ". $_msg->lang("Technicians"); ?></h2>
								<div class="clearfix"></div>
							</div>
							<div class="x_content">
<?php	if($_session->groupname == 'tech') $_msg->wrn("You do not have permission to see this page!"); else { ?>
								<i class="fa fa-warning"></i> <span>Empty</span>
<?php	} ?>
							</div>
						</div>
					</div>
