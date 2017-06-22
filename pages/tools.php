<?php
	/***************************************************************
	* tools page file, included by start if selected (also ?p=30). *
	* dependencies: msgs as $_msg (config);                        *
	*               session as $_session (login);                  *
	*               pgsql as $_pgobj (config).                     *
	****************************************************************/

	// ----- Checking Dependecies ----- //
	if(!isset($_msg)) die("Error: Messages Class not Initialized!");
	if(!isset($_session)) $_msg->err("Class Session not set!");
	if(!isset($_pgobj)) $_msg->err("Class PgSQL not set!");
?>
					<div class="row">
						<div class="x_panel">
							<div class="x_title">
								<h2><?= $_msg->lang("Tools") ." &raquo; ". $_msg->lang("Home"); ?></h2>
								<div class="clearfix"></div>
							</div>
							<div class="x_content">
								<div class="x_content text-center">
									<div class="col-xl-3 col-lg-4 col-md-5 col-sm-6 col-ms-9 col-xs-12" style="margin: auto; float: none;">
										<?= $_msg->inf("Under Construction!"); ?>
									</div>
								</div>
							</div>
						</div>
					</div>
