<?php
	if(isset($_POST['ajax']) && isset($_POST['id']) && isset($_POST['minm']) &&
		isset($_POST['plan']) && isset($_POST['type']) &&
		isset($_POST['upld']) && isset($_POST['down'])) {
		require("../config.php");
		require("../login.php");

		$id = intval($_POST['id']);
		$plan = addslashes($_POST['plan']);
		$type = addslashes($_POST['type']);
		$txup = floatval($_POST['upld']);
		$txdw = floatval($_POST['down']);
		$minm = intval($_POST['minm']);
		$avrt = array('Ascend-Data-Rate', 'Mikrotik-Rate-Limit');

		if (!$id) { ?>
									<div class='modal-dialog'>
										<div class='modal-content'>
											<div class='modal-header'>
												<button class="close" data-dismiss="modal">&times;</button>
												<span style="font-size: 21px; padding-right: 20px;"><?= $_msg->lang('Add') .' '. $_msg->lang('Plan'); ?></span>
											</div>
											<div class="modal-body">
												<div class="col-lg-3 col-md-2 col-sm-2 col-ms-1 col-xs-1 hide-xxs">&#160;</div>
												<div class="settings-block col-lg-6 col-md-8 col-sm-8 col-ms-10 col-xs-12">
<?php	} else { ?>
								<div class="settings-block col-lg-4 col-md-6 col-sm-6 col-ms-9 col-xs-12">
									<button class="btn btn-default delete"><i class="fa fa-trash"></i></button>
<?php	} ?>
									<label class="control-label col-xs-4" for="plan_<?= $id; ?>">
										<?= $_msg->lang("Plan"); ?>
									</label>
									<div class="col-xs-8">
										<input type="text" name="plan_<?= $id; ?>" id="plan_<?= $id; ?>" value="<?= $plan; ?>" class="form-control strong"/>
									</div>
									<label class="control-label col-xs-4" for="type_<?= $id; ?>">
										<?= $_msg->lang("Type"); ?>
									</label>
									<div class="col-xs-8" style="margin-bottom: 8px;">
										<select name="type_<?= $id; ?>" id="type_<?= $id; ?>" class="form-control selectpicker">
<?php	foreach ($avrt as $rate) {
				if($rate != $type) echo str_repeat("\t", 13) ."<option value=\"$rate\">$rate</option>\n";
				else echo str_repeat("\t", 13) ."<option value=\"$rate\" selected>$rate</option>\n";
			} ?>
										</select>
									</div>
									<label class="control-label col-xs-4" for="txdw_<?= $id; ?>">
										<?= $_msg->lang("Download"); ?>
									</label>
									<div class="col-xs-8">
										<div class="input-group">
											<input type="text" name="txdw_<?= $id; ?>" id="txdw_<?= $id; ?>" value="<?= $txdw; ?>" class="form-control" aria-describedby="dwsfx"/>
											<span id="dwsfx" class="input-group-addon">Mbps</span>
										</div>
									</div>
									<label class="control-label col-xs-4" for="txup_<?= $id; ?>">
										<?= $_msg->lang("Upload"); ?>
									</label>
									<div class="col-xs-8">
										<div class="input-group">
											<input type="text" name="txup_<?= $id; ?>" id="txup_<?= $id; ?>" value="<?= $txup; ?>" class="form-control" aria-describedby="upsfx"/>
											<span id="upsfx" class="input-group-addon">Mbps</span>
										</div>
									</div>
									<label class="control-label col-xs-4" for="minm_<?= $id; ?>">
										<?= $_msg->lang("Minimum"); ?>
									</label>
									<div class="col-xs-8">
										<div class="input-group">
											<input type="text" name="minm_<?= $id; ?>" id="minm_<?= $id; ?>" value="<?= $minm; ?>" class="form-control" aria-describedby="mnsfx"/>
											<span id="mnsfx" class="input-group-addon"> % </span>
										</div>
									</div>
<?php 	if($id) { ?>
									<!-- <button class="btn btn-danger pull-right"><?= $_msg->lang("Delete"); ?></button>
									<button class="btn btn-info pull-right"><?= $_msg->lang("Save"); ?></button> -->
<?php 	} else { ?>
									<button class="btn btn-primary pull-right"><?= $_msg->lang("Add"); ?></button>
								</div>
								<div class="clearfix"></div>
										</div>
									</div>
<?php 	} ?>
								</div>
<?php	} ?>
