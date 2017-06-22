<?php
	if(isset($_POST['ajax']) && isset($_POST['skip'])) {
		require("../config.php");
		require("../login.php");

		$skip = $_POST['skip'];
	// --- Get Connection Information If Groupname is "tech"
		if($skip == 1 && $_session->groupname == 'tech') $_msg->err("You do not have permission to see this page!");
		elseif($skip == 1) $sign = array('maca'=>0,'sign'=>0,'txrt'=>0,'rxrt'=>0);
		else {
			$_pgobj->query("SELECT connection FROM at_session WHERE id = $_session->id");
			if($_pgobj->rows == 1) $sign = ($_pgobj->result[0]['connection']) ? (unserialize($_pgobj->result[0]['connection'])) : (array('maca'=>0,'sign'=>0,'txrt'=>0,'rxrt'=>0));
			else $_msg->err("You do not have permission to see this page!");
		}
	// --- Set MAC and Conn "value"
		$mcvl = ($sign['maca']) ? ($sign['maca']) : ('');
		$cnvl = json_encode(array('sign'=>$sign['sign'], 'rxrt'=>$sign['rxrt'], 'txrt'=>$sign['txrt']));
?>
								<form class="form-horizontal" name="reg" action="./?p=2" method="post" onsubmit="return form_submit(this);" enctype="application/x-www-form-urlencoded">
									<div class="wizard_horizontal">
										<ul class="wizard_steps">
											<li>
												<a href="#1" onclick="return form_goto(this);" class="selected">
													<span class="step_no">1</span>
													<span class="step_descr"><?= $_msg->lang("Main"); ?></span>
												</a>
											</li><li>
												<a href="#2" onclick="return form_goto(this);" class="disabled">
													<span class="step_no">2</span>
													<span class="step_descr"><?= $_msg->lang("Details"); ?></span>
												</a>
											</li><li>
												<a href="#3" onclick="return form_goto(this);" class="disabled">
													<span class="step_no">3</span>
													<span class="step_descr"><?= $_msg->lang("Confirmation"); ?></span>
												</a>
											</li>
										</ul>
										<div id="pnsc"></div>
									<!-- Step One -->
										<div id="1" style="display: block;">
											<div class="form-group">
												<label class="control-label col-md-3 col-sm-3 col-ms-3 col-xs-12" for="plan">
													<?= $_msg->lang("Plan"); ?>
												</label>
												<div class="col-md-6 col-sm-6 col-ms-6 col-xs-12">
													<select name="plan" id="plan" class="form-control selectpicker" onchange="$('#chk_plan').val(this.value);">
<?php	$_pgobj->query("SELECT groupname FROM radgroupcheck WHERE groupname NOT IN ( 'tech', 'admn', 'full' ) ORDER BY id");
		for($i=0; $i<$_pgobj->rows; $i++) {
			$opt = $_pgobj->fetch_array($i);
			echo str_repeat("\t", 16) ."<option value=\"$opt[groupname]\">$opt[groupname]</option>\n";
		} if(!$i) echo str_repeat("\t", 16) ."<option value=\"0\">".$_msg->lang("No Plan")."</option>\n"; ?>
													</select>
												</div>
											</div><div class="form-group">
												<label class="control-label col-md-3 col-sm-3 col-ms-3 col-xs-12" for="maca">
													<?= $_msg->lang("MAC"); ?>
												</label>
												<div class="col-md-6 col-sm-6 col-ms-6 col-xs-12">
													<input type="text" name="maca" id="maca" data-error="<?= $_msg->lang('Invalid').' '.$_msg->lang('MAC').'!'; ?>" value="<?= $mcvl; ?>" data-inputmask="'mask': '**:**:**:**:**:**'" onblur='$("#chk_maca").val(this.value);' class="form-control col-md-7 col-xs-12"/>
												</div>
											</div><div class="form-group">
												<label class="control-label col-md-3 col-sm-3 col-ms-3 col-xs-12" for="conn">
													<?= $_msg->lang("Connection"); ?>
												</label>
												<div class="col-md-6 col-sm-6 col-ms-6 col-xs-12">
													<input type="text" name="conn" id="conn" value='<?= $cnvl; ?>' disabled="disabled" class="form-control col-md-7 col-xs-12" >
												</div>
											</div><div class="form-group">
												<label class="control-label col-md-3 col-sm-3 col-ms-3 col-xs-12" for="name">
													<?= $_msg->lang("Full Name"); ?>
												</label>
												<div class="col-md-6 col-sm-6 col-ms-6 col-xs-12">
													<input type="text" name="name" id="name" data-error="<?= $_msg->lang('Invalid').' '.$_msg->lang('Full Name').'!'; ?>" onblur='$("#usnm").val(form_tip(this.value));' class="form-control col-md-7 col-xs-12"/>
												</div>
											</div><div class="form-group">
												<label class="control-label col-md-3 col-sm-3 col-ms-3 col-xs-12" for="pass">
													<?= $_msg->lang("Password"); ?>
												</label>
												<div class="col-md-6 col-sm-6 col-ms-6 col-xs-12">
													<div class="input-group">
														<input type="password" name="pass" id="pass" data-error="<?= $_msg->lang('Invalid').' '.$_msg->lang('Password').'!'; ?>" aria-describedby="btnad" class="form-control col-md-7 col-xs-12"/>
														<span id="btnad" class="input-group-addon" style="cursor: pointer;" title="<?= $_msg->lang('show / hide'); ?>"><i class="fa fa-eye"></i></span>
													</div>
												</div>
											</div><div class="col-md-9 col-sm-9 col-ms-9 col-xs-12">
												<button type="button" onclick="form_goto(this);" class="btn btn-primary pull-right"><?= $_msg->lang("Next"); ?></button>
											</div>
										</div>
								<!-- Step Two -->
										<div id="2" style="display: none;">
<?php	$rqcsfd = array();
		foreach($_settings->form_field as $t1 => $t2) {
			if($t2['mask']!='') {
				$mask = "data-inputmask=\"'mask': '". $t2['mask'] ."'\"";
			} else $mask = "";
			if($t2['vrfy']!='none') {
				$rqrd = 'data-verify="'. $t2['vrfy'] .'" ';
				$rqrd.= 'data-error="'. $_msg->lang("Invalid") .' '. $_msg->lang($t2['titl']) .'!" ';
				$rqrd.= "onblur=\"\$('#chk_$t1').val(this.value);\"";
				$rqcsfd[$t1] = $t2;
			} else $rqrd = ""; ?>
											<div class="form-group">
												<label class="control-label col-md-3 col-sm-3 col-ms-3 col-xs-12" for="<?= $t1; ?>">
													<?= $_msg->lang($t2['titl']); ?>
												</label>
												<div class="col-md-6 col-sm-6 col-ms-6 col-xs-12">
													<input type="text" name="<?= $t1; ?>" id="<?= $t1; ?>" <?= $rqrd.' '.$mask; ?> class="form-control col-md-7 col-xs-12"/>
												</div>
											</div>
<?php	} ?>
											<div class="col-md-9 col-sm-9 col-ms-9 col-xs-12">
												<button type="button" onclick="form_goto(this);" class="btn btn-primary pull-right"><?= $_msg->lang("Next"); ?></button>
												<div class="col-md-1 pull-right">&#160;</div>
												<button type="button" onclick="form_goto(this);" class="btn btn-info pull-right"><?= $_msg->lang("Previous"); ?></button>
											</div>
										</div>
										<div id="modal_confirm" class="modal fade" role="dialog">
											<div class='modal-dialog'>
												<div class='modal-content'>
													<div class='modal-header'>
														<button class="close" data-dismiss="modal" style="padding: 5px;">&times;</button>
														<strong style="padding-right: 20px;"><?= $_msg->lang('Details'); ?></strong>
													</div>
													<div class="modal-body">
														<p class="fa"><?= $_msg->lang("There are empty inputs in the form."); ?></p>
														<p class="fa"><?= $_msg->lang("These inputs are optional."); ?></p>
													</div>
													<div class="modal-footer">
														<button type="button" class="btn btn-default" data-dismiss="modal"><?= $_msg->lang("Fill them"); ?></button>
														<button type="button" class="btn btn-default" data-dismiss="modal" onclick="form_wizard(2, 3);"><?= $_msg->lang("Leave them empty"); ?></button>
													</div>
												</div>
											</div>
										</div>
								<!-- Step Three -->
										<div id="3" style="display: none;">
											<div class="form-group">
												<label class="control-label col-md-3 col-sm-3 col-ms-3 col-xs-12" for="chk_plan">
													<?= $_msg->lang("Plan"); ?>
												</label>
												<div class="col-md-6 col-sm-6 col-ms-6 col-xs-12">
													<input type="text" name="chk_plan" id="chk_plan" disabled="disabled" class="form-control col-md-7 col-xs-12"/>
												</div>
											</div>
											<div class="form-group">
												<label class="control-label col-md-3 col-sm-3 col-ms-3 col-xs-12" for="usnm">
													<?= $_msg->lang("Username"); ?>
												</label>
												<div class="col-md-6 col-sm-6 col-ms-6 col-xs-12">
													<input type="text" name="usnm" id="usnm" class="form-control col-md-7 col-xs-12"/>
												</div>
											</div>
											<div class="form-group">
												<label class="control-label col-md-3 col-sm-3 col-ms-3 col-xs-12" for="chk_maca">
													<?= $_msg->lang("MAC"); ?>
												</label>
												<div class="col-md-6 col-sm-6 col-ms-6 col-xs-12">
													<input type="text" name="chk_maca" id="chk_maca" disabled="disabled" class="form-control col-md-7 col-xs-12"/>
												</div>
											</div>
<?php	foreach($rqcsfd as $t1 => $t2) { ?>
											<div class="form-group">
												<label class="control-label col-md-3 col-sm-3 col-ms-3 col-xs-12" for="chk_<?= $t1; ?>">
													<?= $_msg->lang($t2['titl']); ?>
												</label>
												<div class="col-md-6 col-sm-6 col-ms-6 col-xs-12">
													<input type="text" name="chk_<?= $t1; ?>" id="chk_<?= $t1; ?>" disabled="disabled" class="form-control col-md-7 col-xs-12"/>
												</div>
											</div>
<?php	} ?>
											<div class="form-group">
												<div class="col-md-3 col-sm-3 col-ms-3 col-xs-2 text-right">
													<input  type="checkbox" name="cnfr" id="cnfr" data-error="<?= $_msg->lang('Check the confirmation box!'); ?>" class="checkbox pull-right"/>
													<span class="custom-checkbox"></span>
												</div>
												<label class="control-label col-md-6 col-sm-6 col-ms-6 col-xs-10" style="text-align: left;" for="cnfr">
													<?= $_msg->lang("Confirm the above data!"); ?>
												</label>
											</div>
											<div class="col-md-9 col-sm-9 col-ms-9 col-xs-12">
												<input type="submit" class="btn btn-success pull-right" value="<?= $_msg->lang('Finish'); ?>"/>
												<div class="col-md-1 pull-right">&#160;</div>
												<button type="button" onclick="form_goto(this);" class="btn btn-info pull-right"><?= $_msg->lang("Previous"); ?></button>
											</div>
										</div>
									</div>
								</form>
								<script src="<?= $_path->js; ?>/bootstrap-select.min.js"></script>
								<script src="<?= $_path->js; ?>/jquery.inputmask.bundle.min.js"></script>
								<script type="text/javascript">
<?php		if($_session->groupname=='tech') { ?>
									$("#maca").attr('disabled', 'disabled');
									$("#usnm").attr('disabled', 'disabled');
<?php		} ?>
									$("#btnad").on("click", function() {
										$('#pass').focus();
										$(this).find('i').toggleClass('fa-eye-slash').toggleClass('fa-eye');
										$('#pass').attr('type', ($('#pass').attr('type')=='text')?('password'):('text'));
									});
									$("#chk_plan").val($('#plan').val());
									$("#chk_maca").val($('#maca').val());
									$(".selectpicker").selectpicker();
									$(".form-horizontal input[data-inputmask]").inputmask();
								</script>
<?php	} else header("Location: /"); ?>
