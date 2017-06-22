<?php
	if(isset($_POST['ajax']) && isset($_POST['type'])) {
		require("../config.php");
		include("../login.php");
		$pstt = $_POST['type'];
		if($_session->groupname!='full' && $pstt=='admn') $_msg->err("Unauthorized Query!");

		$usid = (isset($_POST['usid'])) ? (intval($_POST['usid'])) : (FALSE);
		$ord = ($pstt=='eqip') ? ("amt.eqid") : ("nomecmpl");
		// ----- Query Equipments ----- //
		if($pstt=='eqip') {
			if($usid) $qry = "SELECT DISTINCT ON ($ord) amt.eqid, amt.name, aeq.*";
			else $qry = "SELECT DISTINCT ON ($ord) amt.eqid AS id, amt.date, amt.name, aeq.plac, aeq.brnd, aeq.ipad";
			$qry.= " FROM at_equip aeq LEFT JOIN at_monitor amt ON amt.eqid = aeq.id";
			if($usid) $qry.= " WHERE amt.eqid = $usid";
			$qry.= " ORDER BY $ord ASC, amt.date DESC";
		} else {
		// ----- Query Users ----- //
			if($pstt!='inet') {
				$qry = "SELECT DISTINCT ON ($ord) rug.username, rug.groupname";
				$qry.= ', substring(lower(aud.data) from \':"name";s:[0-9]+:"([^"]+)";\') AS nomecmpl';
				$qry.= ", fa.framedipaddress AS ipad, aud.id, aud.date, ac.passtype";
				if($usid) $qry.= ", ac.password AS ctps, aud.data";
				$qry.= ", NULL AS mac FROM radusergroup rug, at_check ac, at_userdata aud, at_fipacct fa";
				$qry.= " WHERE ( rug.groupname = '$pstt' AND ac.username = rug.username";
				$qry.= " AND aud.username = rug.username AND fa.username = rug.username";
				if($usid) $qry.= " AND aud.id = $usid";
				$qry.= " ) ORDER BY $ord ASC";
			} else {
			// ----- Query Clients ----- //
				if(!$usid) {
					$qry = "SELECT DISTINCT ON ($ord) rug.username, rug.groupname";
					$qry.= ', substring(lower(aud.data) from \':"name";s:[0-9]+:"([^"]+)";\') AS nomecmpl';
					$qry.= ", fa.framedipaddress AS ipad, aud.id, aud.date, ac.passtype, ac.macaddr AS mac";
					$qry.= " FROM radusergroup rug, at_check ac, at_userdata aud, at_fipacct fa";
					$qry.= " WHERE ( rug.groupname NOT IN ('full', 'admn', 'tech') AND ac.username = rug.username";
					$qry.= " AND aud.username = rug.username AND fa.username = rug.username )";
					if(isset($_POST['search'])) {
						$search_string = pg_escape_string($_POST['search']);
						$qry.= " AND ( rug.groupname ILIKE '%". $search_string ."%'";
						$qry.= ' OR substring(lower(aud.data) from \':"name";s:[0-9]+:"([^"]+)";\') ILIKE \'%'. $search_string .'%\'';
						$qry.= " OR text(aud.date) ILIKE '%". $search_string ."%'";
						$qry.= " OR text(ac.macaddr) ILIKE '%". $search_string ."%'";
						$qry.= " OR CASE WHEN fa.framedipaddress IS NULL THEN '". $_msg->lang("disconnected") ."' ILIKE '%". $search_string;
						$qry.= "%' ELSE '". $_msg->lang("connected") ."' ILIKE '%". $search_string ."%' END )";
					} $qry.= " ORDER BY $ord ASC";
				} else {
				// ----- Query One Client ----- //
					$qry = "SELECT DISTINCT ON ($ord) rug.username, rug.groupname";
					$qry.= ', substring(lower(aud.data) from \':"name";s:[0-9]+:"([^"]+)";\') AS nomecmpl';
					$qry.= ", fa.framedipaddress AS ipad, aud.id, aud.date, ac.passtype, ac.password AS ctps, aud.data";
					$qry.= ", ac.macaddr AS mac, awt.name AS whois";
					$qry.= " FROM radusergroup rug, at_check ac, at_userdata aud, at_fipacct fa, at_techs awt";
					$qry.= " WHERE ( rug.groupname NOT IN ('full', 'admn', 'tech') AND ac.username = rug.username AND aud.higher_id = awt.id";
					$qry.= " AND aud.username = rug.username AND fa.username = rug.username AND aud.id = $usid ) ORDER BY $ord ASC";
				}
			}
		}
// ----- Starting query loop ----- //
		if($_pgobj->query($qry)) {
			if(!$usid) include("$_path->ajax/lst_view.php");
			else include("$_path->ajax/lst_details.php");
		} else $_msg->err("Invalid Query!");
	} else header("Location: /");
?>
