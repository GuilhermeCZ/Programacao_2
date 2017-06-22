<?php
	if(isset($_POST['ajax']) && isset($_POST['id']) && isset($_POST['conf']) && isset($_POST['data'])) {
		require("../config.php");
		require("../login.php");

		$id = intval($_POST['id']);
		$mac = addslashes($_POST['conf']);
		if($id) $dta = unserialize($_POST['data']);
		else $dta = array('', 'ssh', 22, '', '');
?>
										<div class="settings-block col-md-4 col-sm-4 col-ms-6 col-xs-12">
											<label class="control-label col-md-4 col-sm-4 col-xs-4" for="name_<?= $id; ?>">
												<?= $_msg->lang("Name"); ?>
											</label>
											<div class="col-md-8 col-sm-8 col-xs-8">
												<input type="text" name="name_<?= $id; ?>" id="name_<?= $id; ?>" value="<?= $dta[0]; ?>" class="form-control"/>
											</div>
											<label class="control-label col-md-4 col-sm-4 col-xs-4" for="conf_<?= $id; ?>">
												<?= $_msg->lang("MAC"); ?>
											</label>
											<div class="col-md-8 col-sm-8 col-xs-8">
												<input type="text" name="conf_<?= $id; ?>" id="conf_<?= $id; ?>" value="<?= $mac; ?>" class="form-control"/>
											</div>
											<label class="control-label col-md-4 col-sm-4 col-xs-4" for="srvc_<?= $id; ?>">
												<?= $_msg->lang("Service"); ?>
											</label>
											<div class="col-md-8 col-sm-8 col-xs-8" style="margin-bottom: 8px;">
												<select name="srvc_<?= $id; ?>" id="srvc_<?= $id; ?>" class="form-control selectpicker">
													<option value="<?= $dta[1]; ?>"><?= $dta[1]; ?></option>
												</select>
											</div>
											<label class="control-label col-md-4 col-sm-4 col-xs-4" for="port_<?= $id; ?>">
												<?= $_msg->lang("Port"); ?>
											</label>
											<div class="col-md-8 col-sm-8 col-xs-8">
												<input type="text" name="port_<?= $id; ?>" id="port_<?= $id; ?>" value="<?= $dta[2]; ?>" class="form-control"/>
											</div>
											<label class="control-label col-md-4 col-sm-4 col-xs-4" for="usnm_<?= $id; ?>">
												<?= $_msg->lang("Username"); ?>
											</label>
											<div class="col-md-8 col-sm-8 col-xs-8">
												<input type="text" name="usnm_<?= $id; ?>" id="usnm_<?= $id; ?>" value="<?= $dta[3]; ?>" class="form-control"/>
											</div>
											<label class="control-label col-md-4 col-sm-4 col-xs-4" for="pass_<?= $id; ?>">
												<?= $_msg->lang("Password"); ?>
											</label>
											<div class="col-md-8 col-sm-8 col-xs-8">
												<input type="password" name="pass_<?= $id; ?>" id="pass_<?= $id; ?>" value="<?= $dta[4]; ?>" class="form-control"/>
											</div>
<?php if($id) { ?>
											<button class="btn btn-danger pull-right"><?= $_msg->lang("Delete"); ?></button>
											<button class="btn btn-info pull-right"><?= $_msg->lang("Save"); ?></button>
<?php } else { ?>
											<button class="btn btn-primary pull-right"><?= $_msg->lang("Add"); ?></button>
<?php } ?>
										</div>
<?php
	}
?>
