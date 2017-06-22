<?php
	if(isset($_POST['ajax']) && isset($_POST['type'])) {
	//$_msg->wrn($qry);
// ---- Loop Fill Table Rows ----- //
		$all_rows = array();
		for($i=0; $i<$_pgobj->rows; $i++) {
			$ats = $_pgobj->fetch_array($i);
			if($pstt!='eqip') $dsbld = ($ats['passtype']=='User-Password') ? (TRUE) : (FALSE);
			else {
				if($ats['name']==NULL) $ats['name'] = $_msg->lang("unknown");
				if($ats['date']==NULL) $dsbld = TRUE;
				else $dsbld = ((time() - strtotime($ats['date']))>1000) ? (TRUE) : (FALSE);
			}
// ----- Display columns ----- //
			$td_name = '<a href="javascript:void(0);" onclick="list_details('. $ats['id'];
			$td_name.= ');" title="'. $_msg->lang('Show Details') .'" ';
			$td_name.= ($dsbld) ? ('class="dsbld">') : ('>');
			$td_name.= ($pstt=='eqip') ? ($ats['name']) : (ucwords(strtolower($ats['nomecmpl'])));
			$td_name.= '</a>';

			$td_pldt = ($pstt=='eqip') ? ($ats['plac']) : (date("Y-m-d", strtotime($ats['date'])));
			$td_brmc = ($pstt=='eqip') ? ($ats['brnd']) : (strtoupper($ats['mac']));
			$td_ipgu = ($pstt=='eqip') ? ($ats['ipad']) : (($pstt=='inet')?($ats['groupname']):($ats['username']));
			if(!filter_var($ats['ipad'], FILTER_VALIDATE_IP) === false) {
				$td_stts = "<a href=\"http://$ats[ipad]/\" title=\"". $_msg->lang("Access Device");
				$td_stts.= "\" target=\"_blank\">". $_msg->lang("connected") ."</a>";
			} else $td_stts = '<i style="color: red;">'. $_msg->lang("disconnected") .'</i>';
			$all_rows[] = array($td_name, $td_pldt, $td_brmc, $td_ipgu, $td_stts);
		} print(json_encode($all_rows));
	}
?>
