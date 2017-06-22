<?php
	/******************************************************************
	* reg_cstmr page file, included by start if selected (also ?p=2)  *
	* dependencies: session &$ (login); pgsql as $_pgobj (config);    *
	* msgs as $_msg (config); paths as $_path (config).                 *
	*******************************************************************/

	// ----- Checking Dependecies ----- //
	if(!isset($_msg)) die("Error: Messages Class not Initialized!");
	if(!isset($_session)) $_msg->err("Class Session not set!");
	if(!isset($_pgobj)) $_msg->err("Class PgSQL not set!");
	if(!isset($_path)) $_msg->err("Class Path not set!");

	if($_session->groupname != 'tech') {
		$check = FALSE;
		$_pgobj->query("SELECT * FROM at_fipacct WHERE username = '$_session->username'");
		if($_pgobj->rows == 1)
			if($_pgobj->result[0]['framedipaddress'] != NULL) $check = $_session->groupname;
	} else $check = $_session->groupname;
?>
					<div class="row">
						<div class="x_panel">
							<div class="x_title">
								<h2><?= $_msg->lang("Register") ." &raquo; ". $_msg->lang("Customers"); ?></h2>
								<i class="fa fa-refresh fa-spin fa-3x fa-fw pull-right" style="visibility: hidden;"></i>
								<div class="clearfix"></div>
							</div>
							<div class="x_content">
<?php
	if(isset($_POST['cnfr'])) {
		//verify the POST array against $settings->mnic and the DB
 /*   ( [plan] => 1M-radio
        [maca] => 32:13:21:32:13:21
        [conn] => {"sign":0,"rxrt":0,"txrt":0}
        [name] => Liliane Rosa Lanhi
        [pass] => 432f432534
        [usnm] => liliane.lanhi@10.0.2.13 ) */
?>
								<textarea><? print_r($_POST); ?></textarea>
							</div>
<?php	} else { ?>
								<div style="text-align: center;"><?= $_msg->lang("Please Wait ..."); ?></div>
							</div>
							<script type="text/javascript" >
								$(function () {
								// Prepare document loading animation
									$(document).ajaxStart(function (){ $('.fa-spin').css("visibility", 'visible'); })
													.ajaxStop(function (){ $('.fa-spin').css("visibility", 'hidden'); });
								// Load Registration Form
<?php		if($check) { ?>
									$.ajax({	url: '<?= $_path->ajax; ?>/reg_info.php',
												type: 'POST',
												data: 'ajax=1',
												success: function (htrs) {
													if (htrs=='SKIP_CHECK' && '<?= $_session->groupname; ?>' != 'tech') form_load(1);
													else if (htrs=='PASS_CHECK') form_load(0);
													else $('.x_content').html(htrs);
												}
											});
<?php		} else { ?>
									form_load(1);
<?php		} ?>
								});
							</script>
<?php	} ?>
						</div>
					</div>
