<?php
	if(isset($_POST['ajax']) && isset($_POST['type'])) {
// ---- Loop Fill Table Rows ----- //
//$_msg->wrn($qry);
		$details_result = $_pgobj->result;
		// --- Filling plans array
		$_pgobj->query("SELECT DISTINCT ON (groupname) groupname FROM radgroupcheck WHERE groupname NOT IN ('full','admn','tech')");
		$plans_array = array();
		for ($i=0; $i<$_pgobj->rows; $i++) $plans_array[] = $_pgobj->result[$i]['groupname'];
		// --- Filling Technician Array
		$_pgobj->query("SELECT * FROM at_techs");
		$techs_array = array();
		for ($i=0; $i<$_pgobj->rows; $i++) $techs_array[$_pgobj->result[$i]['id']] = $_msg->lang($_pgobj->result[$i]['name']);
		// --- Display user Details
		for($i=0; $i<count($details_result); $i++) {
			$arr = $details_result[$i];
			for($n=0; $n<count($arr); $n++) unset($arr[$n]);
			$ats = array();
			foreach($arr as $ftc => $ftv) if($ftc!='data') $ats[$ftc] = $ftv;
			if($pstt!='eqip') {
				$ats = array_merge($ats, unserialize($arr['data']));
				if($pstt=='inet') {
					if($ats['whois']=='unknown') $ats['whois'] = $_msg->lang("unknown");
				}
			} elseif($ats['name']==NULL) $ats['name'] = $_msg->lang("unknown");
// ----- Prepare to fill the modal ----- //
			if($pstt=='eqip') {
				$vv = array("name"		=> array($_msg->lang("Name"), $ats['name']),
								"brnd"		=> array($_msg->lang("Brand"), $ats['brnd']),
								"date"		=> array($_msg->lang("Register Date"), date("d/m/Y", strtotime($ats['date']))),
								"deta"		=> array($_msg->lang("Details"), $ats['deta']),
								"type"		=> array($_msg->lang("Type"), $ats['type']),
								"grup"		=> array($_msg->lang("Group"), $ats['grup']),
								"maca"		=> array($_msg->lang("MAC"), strtoupper($ats['maca'])),
								"ipad"		=> array($_msg->lang("IP Address"), $ats['ipad']),
								"plac"		=> array($_msg->lang("Place"), $ats['plac']),
								"srvc"		=> array($_msg->lang("Service"), $ats['srvc']),
								"port"		=> array($_msg->lang("Port"), $ats['port']),
								"usnm"		=> array($_msg->lang("Username"), $ats['usnm']),
								"pass"		=> array($_msg->lang("Password"), $ats['pass']));
			} elseif($pstt=='inet') {
				$vv = array("name"		=> array($_msg->lang("Full Name"), ucwords(strtolower($ats['name']))),
								"groupname"	=> array($_msg->lang("Plan"), $ats['groupname']),
								"date"		=> array($_msg->lang("Register Date"), date("d/m/Y", strtotime($ats['date']))),
								"whois"		=> array($_msg->lang("Technician"), ucwords(strtolower($ats['whois']))),
								"mac"			=> array($_msg->lang("MAC"), strtoupper($ats['mac'])),
								"ipad"		=> array($_msg->lang("IP Address"), $ats['ipad']),
								"username"	=> array($_msg->lang("Username"), $ats['username']),
								"ctps"		=> array($_msg->lang("Password"), $ats['ctps']));
			} else {
				$vv = array("name"		=> array($_msg->lang("Full Name"), ucwords(strtolower($ats['name']))),
								"ipad"		=> array($_msg->lang("IP Address"), $ats['ipad']),
								"date"		=> array($_msg->lang("Register Date"), date("d/m/Y", strtotime($ats['date']))),
								"username"	=> array($_msg->lang("Username"), $ats['username']),
								"ctps"		=> array($_msg->lang("Password"), $ats['ctps']));
			}
// ----- Fill the remaining fields with dynamic data from DB ----- //
			if($pstt!='eqip') {
				foreach($_settings->form_field as $t1 => $t2) {
					if(strstr($t1, "mail")) $vv[$t1] = array($_msg->lang($t2['titl']), $ats[$t1]);
					else $vv[$t1] = array($_msg->lang($t2['titl']), ucwords(strtolower($ats[$t1])));
				}
			} foreach($vv as $cc => $ii) {
?>
				<div class="col-md-6 col-sm-6 col-xs-12 form-group">
					<div class="col-md-12 col-sm-12 col-xs-12 form-group"><strong><?= $ii[0]; ?>:</strong></div>
					<div class="col-md-12 col-sm-12 col-xs-12 form-group">
<?php			if($cc == 'ctps') {
					$hidden_password = str_repeat("●", strlen($ii[1])); ?>
						<input type="password" name="<?= $cc; ?>" value="<?= $ii[1]; ?>" class="form-control form-edit" />
						<span class="ellipsis fadeIn animated"><?= (strlen($hidden_password)) ? ($hidden_password) : ('●●'); ?></span>
<?php			} elseif($cc == 'whois') { ?>
						<select class="form-control selectpicker form-edit">
<?php				foreach($techs_array as $tech_id => $tech_name) {
						$is_selected = ($tech_name == $ii[1]) ? ('selected="true"') : (''); ?>
							<option value="<?= $tech_id; ?>" <?= $is_selected; ?> ><?= $tech_name; ?></option>
<?php				} ?>
						</select>
						<span class="ellipsis fadeIn animated"><?= (strlen($ii[1])) ? ($ii[1]) : ('--'); ?></span>
<?php			} elseif($cc == 'groupname') { ?>
						<select class="form-control selectpicker form-edit">
<?php				foreach($plans_array as $plans_name) {
						$is_selected = ($plans_name == $ii[1]) ? ('selected="true"') : (''); ?>
							<option value="<?= $plans_name; ?>"<?= $is_selected; ?> ><?= $plans_name; ?></option>
<?php				} ?>
						</select>
						<span class="ellipsis fadeIn animated"><?= (strlen($ii[1])) ? ($ii[1]) : ('--'); ?></span>
<?php			} else { ?>
						<input type="text" name="<?= $cc; ?>" value="<?= $ii[1]; ?>" class="form-control form-edit" />
						<span class="ellipsis fadeIn animated"><?= (strlen($ii[1])) ? ($ii[1]) : ('--'); ?></span>
<?php			} ?>
					</div>
				</div>
<?php		} ?>
				<div class="clearfix"></div>
<?php	}
	} ?>
