<?php
	if(isset($_POST['ajax']) && isset($_POST['id']) && isset($_POST['conf']) && isset($_POST['data'])) {
		require("../config.php");
		require("../login.php");

		$id = intval($_POST['id']);
		$name = addslashes($_POST['conf']);
		if($id) $dta = unserialize($_POST['data']);
		else $dta = array('titl' => '', 'mask' => '', 'vrfy' => '');
?>
										<div class="settings-block col-md-4 col-sm-4 col-ms-6 col-xs-12">
											<label class="control-label col-md-4 col-sm-4 col-xs-4" for="conf_<?= $id; ?>">
												<?= $_msg->lang("Name"); ?>
											</label>
											<div class="col-md-8 col-sm-8 col-xs-8">
												<input type="text" name="conf_<?= $id; ?>" id="conf_<?= $id; ?>" value="<?= $name; ?>" class="form-control"/>
											</div>
											<label class="control-label col-md-4 col-sm-4 col-xs-4" for="titl_<?= $id; ?>">
												<?= $_msg->lang("Title"); ?>
											</label>
											<div class="col-md-8 col-sm-8 col-xs-8">
												<input type="text" name="titl_<?= $id; ?>" id="titl_<?= $id; ?>" value="<?= $dta['titl']; ?>" class="form-control"/>
											</div>
											<label class="control-label col-md-4 col-sm-4 col-xs-4" for="mask_<?= $id; ?>">
												<?= $_msg->lang("Mask"); ?>
											</label>
											<div class="col-md-8 col-sm-8 col-xs-8">
												<input type="text" name="mask_<?= $id; ?>" id="mask_<?= $id; ?>" value="<?= $dta['mask']; ?>" class="form-control"/>
											</div>
											<label class="control-label col-md-4 col-sm-4 col-xs-4" for="vrfy_<?= $id; ?>">
												<?= $_msg->lang("Validation"); ?>
											</label>
											<div class="col-md-8 col-sm-8 col-xs-8">
												<input type="text" name="vrfy_<?= $id; ?>" id="vrfy_<?= $id; ?>" value="<?= $dta['vrfy']; ?>" class="form-control"/>
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
