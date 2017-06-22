<?php
	/******************************************************************
	* settings page file, included by start if selected (also ?p=40). *
	* dependencies: msgs as $_msg (config);                           *
	*               session as $_session (login);                     *
	*               pgsql as $_pgobj (config).                        *
	*******************************************************************/

	// ----- Checking Dependecies ----- //
	if(!isset($_msg)) die("Error: Messages Class not Initialized!");
	if(!isset($_session)) $_msg->err("Class Session not set!");
	if(!isset($_pgobj)) $_msg->err("Class PgSQL not set!");
?>
					<div class="x_panel" style="min-height: 200px;">
						<div class="x_title">
							<h2><?= $_msg->lang("Settings"); ?></h2>
							<div class="clearfix"></div>
						</div>
						<div class="x_content">
							<div id="pnsc" style="top: -12px;"></div>
							<div role="tabpanel" data-example-id="togglable-tabs">
								<ul class="nav nav-tabs bar_tabs" role="tablist">
									<li role="presentation" class="active">
										<a href="#tab_system" role="tab" data-toggle="tab" aria-expanded="true"><?= $_msg->lang("System"); ?></a>
									</li>
									<li role="presentation">
										<a href="#tab_installation" role="tab" data-toggle="tab" aria-expanded="false"><?= $_msg->lang("Installation"); ?></a>
									</li>
<?php
	if($_session->groupname != 'tech') {
?>
									<li role="presentation">
										<a href="#tab_formfields" role="tab" data-toggle="tab" aria-expanded="false"><?= $_msg->lang("Form Fields"); ?></a>
									</li>
<?php
	}
?>
								</ul>
								<div class="tab-content">
									<div role="tabpanel" id="tab_system" class="tab-pane fade active in">
<?php	// System Settings Tab Pane
	foreach($_settings->config as $cnf => $vlu) {
?>
										<label class="control-label col-md-3 col-sm-3 col-ms-4 col-xs-6" for="<?= $cnf; ?>">
											<?= $_msg->lang("$cnf"); ?>
										</label>
										<div class="col-md-3 col-sm-3 col-ms-6 col-xs-6">
											<input type="text" name="<?= $cnf; ?>" id="<?= $cnf; ?>" value="<?= $vlu; ?>" class="form-control col-md-7 col-xs-12"/>
										</div>
<?php
	}
?>
										<div class="col-md-6 col-sm-6 col-ms-10 col-xs-12">
											<button class="btn btn-info pull-right"><?= $_msg->lang("Save"); ?></button>
										</div>
									</div>
									<div role="tabpanel" id="tab_installation" class="tab-pane fade">
<?php // Installation Settings Tab Pane
	$_pgobj->query("SELECT id, conf, data FROM at_settings WHERE type = 'device' ORDER BY seqn");
	for ($i=0; $i<$_pgobj->rows; $i++)
		echo str_repeat("\t", 11) ."<input type='hidden' name='drac' value='". json_encode($_pgobj->result[$i]) ."'/>\n";
?>
									</div>
<?php
	if($_session->groupname != 'tech') {
?>
									<div role="tabpanel" id="tab_formfields" class="tab-pane fade">
<?php // Form Fields' Settings Tab Pane
		$_pgobj->query("SELECT id, conf, data FROM at_settings WHERE type = 'form_field' ORDER BY seqn");
		for ($i=0; $i<$_pgobj->rows; $i++)
			echo str_repeat("\t", 11) ."<input type='hidden' name='mnic' value='". json_encode($_pgobj->result[$i]) ."'/>\n";
?>
									</div>
									<script type="text/javascript">
										$(function () {
											ffcs = $("#tab_formfields input[name='mnic']");
											set_mnicGet(0);
										});
									</script>
<?php
	}
?>
								</div>
							</div>
							<script src="<?= $_path->js; ?>/bootstrap-select.min.js"></script>
							<script type="text/javascript">
								$(function () {
								// Prepare document loading animation
								//	$(document).ajaxStart(function (){ $('.fa-spin').css("visibility", 'visible');})
								//						.ajaxStop(function (){ $('.fa-spin').css("visibility", 'hidden'); });
									drcs = $("#tab_installation input[name='drac']");
									set_dracGet(0);
								});
							</script>
						</div>
					</div>
