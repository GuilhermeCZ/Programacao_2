<?php
	/**************************************************************
	* plans page file, included by start if selected (also ?p=7). *
	* dependencies: msgs as $_msg (config); session &$ (login);    *
	* pgsql as $_pgobj (config).                                        *
	*******************************************************************/

	// ----- Checking Dependecies ----- //
	if(!isset($_msg)) die("Error: Messages Class not Initialized!");
	if(!isset($_session)) $_msg->err("Class Session not set!");
	if(!isset($_pgobj)) $_msg->err("Class PgSQL not set!");
?>
					<div class="row">
						<div class="x_panel">
							<div class="x_title">
								<h2><?= $_msg->lang("Manage Plans"); ?></h2>
								<button class="btn btn-primary pull-right strong" onclick="plans_add();"><?= $_msg->lang("Add"); ?></button>
								<div class="clearfix"></div>
							</div>
							<div class="x_content">
<?php
	if($_session->groupname == 'tech') {
		echo str_repeat("\t", 10);
		$_msg->wrn("You do not have permission to see this page!");
	} else {
		$qery = "SELECT * FROM radgroupreply WHERE (attribute ILIKE '%rate-limit%' OR attribute ILIKE '%data-rate%')";
		$qery.= " AND groupname NOT IN ( 'full', 'admn', 'tech' ) ORDER BY to_number(groupname, '99S')";
		$_pgobj->query($qery);
		while($plan = $_pgobj->fetch_array()) {
			$jsar = array();
			$jsar['id'] = $plan['id'];
			$jsar['plan'] = $plan['groupname'];
			$jsar['type'] = $plan['attribute'];
			$temp = substr($plan['value'], 0, strpos($plan['value'], ' '));
			$jsar['upld'] = round(intval(substr($temp, 0, strpos($temp, '/'))) / (1024*1024), 1);
			$jsar['down'] = round(intval(substr($temp, strpos($temp, '/') + 1)) / (1024*1024), 1);
			$temp = substr($plan['value'], strrpos($plan['value'], ' '));
			$mndw = round(intval(substr($temp, strpos($temp, '/') + 1)) / (1024*1024), 1);
			$jsar['minm'] = round(($mndw * 100) / $jsar['down']);
			echo str_repeat("\t", 12) ."<input type='hidden' name='plan' value='". json_encode($jsar) ."'/>\n";
		}
?>
								<div id="modal_plnadd" class="modal fade" role="dialog"></div>
								<script src="<?= $_path->js; ?>/bootstrap-select.min.js"></script>
								<script type="text/javascript">
									$(function () {
									// Prepare document loading animation
									//	$(document).ajaxStart(function (){ $('.fa-spin').css("visibility", 'visible');})
									//						.ajaxStop(function (){ $('.fa-spin').css("visibility", 'hidden'); });
										plns = $(".x_content input[name='plan']");
										plans_get(0);
									});
								</script>
<?php
	}
/*	<th>Nome</th>
		<th>Taxa RX/TX</th>
		<th>Burst RX/TX</th>
		<th>Média RX/TX</th>
		<th>Tempo Burst</th>
		<th>Mínimo RX/TX</th> */
?>
							</div>
						</div>
					</div>
