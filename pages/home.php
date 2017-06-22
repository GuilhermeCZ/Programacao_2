<?php
	/************************************************************
	* home page file, included by start if no page is selected. *
	* dependencies: all classes in the config file;             *
	*               all classes in the login file.              *
	*************************************************************/

	// ----- Checking Dependecies ----- //
	if(!isset($_msg)) die("Error: Messages Class not Initialized!");
	if(!isset($_session)) $_msg->err("Class Session not set!");
	if(!isset($_pgobj)) $_msg->err("Class PgSQL not set!");
	if(!isset($_path)) $_msg->err("Class Path not set!");
	if(!isset($_settings)) $_msg->err("Class Settings not set!");

	// Get tile info total customers
	$_pgobj->query("SELECT COUNT(id) AS ttcs FROM radusergroup WHERE groupname NOT IN ('full', 'admn', 'tech')");
	$total_customers = $_pgobj->result[0]['ttcs'];
	$_pgobj->query("SELECT COUNT(id) AS mtcs FROM at_userdata WHERE date > DATE_TRUNC('month', now())");
	$month_customers = $_pgobj->result[0]['mtcs'];

	// Get tile info onLine / offLine
	$query = "SELECT COUNT(username) AS olcs FROM at_fipacct";
	$query.= " WHERE username NOT IN (SELECT username FROM radusergroup WHERE groupname IN ('full', 'admn', 'tech'))";
	$query.= " AND framedipaddress IS NOT NULL";
	$_pgobj->query($query);
	$online_customers = $_pgobj->result[0]['olcs'];
	$offline_customers = $total_customers - $online_customers;

	// Get chart info users per plan
	include("$_path->php/get_color.php"); // Function get_color($string, $alpha = 1);
	$plans = array();
	$customers_per_plan = array();
	$plan_custom_color = array();
	$query = "SELECT COUNT(groupname) AS uspp, groupname FROM radusergroup";
	$query.= " WHERE groupname NOT IN ('full', 'admn', 'tech') GROUP BY groupname ORDER BY to_number(groupname, '99S')";
	if($_pgobj->query($query)) {
		for($i=0; $i<$_pgobj->rows; $i++) {
			$temp = $_pgobj->fetch_array($i);
			$plans[] = $temp['groupname'];
			$customers_per_plan[] = $temp['uspp'];
			$plan_custom_color[] = get_color($temp['groupname'], 0.8);
		}
	}

	// Get tile info Mbits Sold
	if(count($plans)>1) {
		$sold_bits_download = array();
		$sold_bits_upload = array();
		for($i=0; $i<count($plans); $i++) {
			$query = "SELECT \"value\" FROM radgroupreply WHERE (attribute ILIKE '%rate-limit%' OR attribute ILIKE '%data-rate%')";
			$query.= " AND groupname = '". $plans[$i] ."' ORDER BY attribute LIMIT 1";
			if($_pgobj->query($query)) {
				$temp = substr($_pgobj->result[0]['value'], strpos($_pgobj->result[0]['value'], '/'));
				$sold_bits_download[] = intval(substr($temp, 1, strpos($temp, ' '))) * $customers_per_plan[$i];
				$sold_bits_upload[] = intval($_pgobj->result[0]['value']) * $customers_per_plan[$i];
			} else {
				$sold_bits_download[] = intval($plans[$i]) * $customers_per_plan[$i];
				$sold_bits_upload[] = intval($plans[$i]) * $customers_per_plan[$i] * 0.4;
			}
		}
	} else {
		$sold_bits_download = array(0);
		$sold_bits_upload = array(0);
	}
	if($sold_bits_download[0]<500000) $total_sold_mbits_download = round(array_sum($sold_bits_download));
	else $total_sold_mbits_download = round(array_sum($sold_bits_download) / (1024 * 1024));
	if($sold_bits_upload[0]<100000) $total_sold_mbits_upload = round(array_sum($sold_bits_upload));
	else $total_sold_mbits_upload = round(array_sum($sold_bits_upload) / (1024 * 1024));

	// Get tile info total of open Tickets
	$query = "WITH alm AS (SELECT DISTINCT ON (ticket_id) ticket_id, status FROM at_ticket_messages ORDER BY ticket_id, date DESC)";
	$query.= " SELECT COUNT (id) AS otic FROM at_tickets at, alm";
	$query.= " WHERE at.id = alm.ticket_id AND alm.status";
	$_pgobj->query($query);
	$total_open_tickets = $_pgobj->result[0]['otic'];
	// open tickets on the month
	$query = "WITH afm AS (SELECT DISTINCT ON (ticket_id) ticket_id, status, date FROM at_ticket_messages ORDER BY ticket_id, date ASC)";
	$query.= " SELECT COUNT (id) AS oticm FROM at_tickets at, afm";
	$query.= " WHERE at.id = afm.ticket_id AND afm.status AND afm.date > DATE_TRUNC('month', now())";
	$_pgobj->query($query);
	$month_open_tickets = $_pgobj->result[0]['oticm'];

	// Get Main Chart info form at_monitor
	$equipment_id = 1;
	$main_chart = $_settings->config['Main Chart'];
	$data_points = intval($_settings->config['Data Points']) + 1;
	$moment = array();
	$mbps_download = array();
	$mbps_upload = array();
	$query = "SELECT name, date, data FROM at_monitor";
	$query.= " WHERE eqid = $equipment_id ORDER BY date DESC LIMIT $data_points";
	if($_pgobj->query($query)) {
		for($i=($_pgobj->rows - 1); $i>=0; $i--) {
			$temp = $_pgobj->fetch_array($i);
			if(!@unserialize($temp['data'])) continue;
			$data = unserialize($temp['data']);
			if(!isset($previous_moment)) {
				if(!isset($data[$main_chart])) break;
				if(!isset($data[$main_chart][0][0])) break;
				$previous_moment = strtotime($temp['date']);
				$previous_download = $data[$main_chart][0][0];
				$previous_upload = $data[$main_chart][0][1];
				continue;
			} $actual_moment = strtotime($temp['date']);
			$actual_download = $data[$main_chart][0][0];
			$actual_upload = $data[$main_chart][0][1];
			if(!isset($diff_moment)) $diff_moment = ($actual_moment-$previous_moment);
			$moment[] = date("Y-m-d H:i", ($actual_moment-($diff_moment/2)));
		// --- Make it smart for many charts
		// $kpps_download[] = round(($actual_download-$previous_download)/1000/($actual_moment-$previous_moment), 2); // kpps
		// $kpps_upload[] = round(($actual_upload-$previous_upload)/1000/($actual_moment-$previous_moment), 2); // kpps
			$mbps_download[] = round((($actual_download-$previous_download)/(1024*1024)/($actual_moment-$previous_moment))*8); // Mbps
			$mbps_upload[] = round((($actual_upload-$previous_upload)/(1024*1024)/($actual_moment-$previous_moment))*8); // Mbps
			$previous_moment = $actual_moment;
			$previous_download = $actual_download;
			$previous_upload = $actual_upload;
		}
	}

	// Get chart info ranking
	$customers_download_utilisation = array();
	$customers_upload_utilisation = array();
	for($i=1; $i<15; $i++) { // Try last 14 months for movement
		$query = "SELECT rac.username,";
		$query.= " SUBSTRING(rgr.value FROM '^[0-9]+/([^ ]+)') AS dwpl,";
		$query.= " SUBSTRING(rgr.value FROM '^([^/]+)') AS uppl,";
		$query.= " EXTRACT( epoch FROM SUM(rac.acctstoptime - rac.acctstarttime)) AS secs,";
		$query.= " SUM(rac.acctinputoctets) AS down, SUM(rac.acctoutputoctets) AS upld";
		$query.= " FROM radacct rac, radusergroup rug, radgroupreply rgr";
		$query.= " WHERE rac.acctstoptime IS NOT NULL AND rac.username = rug.username";
		$query.= " AND rac.acctstarttime > (now() - INTERVAL '$i MONTH')";
		$query.= " AND rug.groupname NOT IN ('full', 'admn', 'tech')";
		$query.= " AND rug.groupname = rgr.groupname";
		$query.= " AND (rgr.attribute ILIKE '%rate-limit%' OR rgr.attribute ILIKE '%data-rate%')";
		$query.= " GROUP BY rac.username, rgr.value";

		$_pgobj->query($query);
		if($_pgobj->rows < 1) continue; // Skip month with no movement
		for($j=0; $j<$_pgobj->rows; $j++) {
			$temp = $_pgobj->fetch_array($j);
			$customers_download_utilisation[$temp['username']] = round((($temp['down'] * 8) / $temp['secs']) * 100 / $temp['dwpl'], 2);
			$customers_upload_utilisation[$temp['username']] = round((($temp['upld'] * 8) / $temp['secs']) * 100 / $temp['uppl'], 2);
		} break;
	}
	// Function to reverse sort keeping keys
	function cmp($a, $b) {
		if ($a == $b) return 0;
		return ($a > $b) ? (-1) : (1);
	}

	uasort($customers_download_utilisation, 'cmp');
	uasort($customers_upload_utilisation, 'cmp');
	$customers_download_utilisation = array_slice($customers_download_utilisation, 0, 4);
	$customers_upload_utilisation = array_slice($customers_upload_utilisation, 0, 4);
?>
					<div class="row tile_count">
						<div class="col-md-2 col-sm-4 col-ms-4 col-xs-6 tile_stats_count ellipsis">
							<span class="count_top"><i class="fa fa-users"></i> <?= $_msg->lang("Registrations"); ?></span>
							<div class="count"><a href="./?p=10"><?= $total_customers; ?></a></div>
							<span class="count_bottom">
								<a href="./?p=10#<?= date('Y-m'); ?>">
									<strong class="green"><?= $month_customers; ?></strong> <?= $_msg->lang("This Month") ."\n"; ?>
								</a>
							</span>
						</div>
						<div class="col-md-2 col-sm-4 col-ms-4 col-xs-6 tile_stats_count ellipsis">
							<span class="count_top"><i class="fa fa-check-circle-o"></i> <?= $_msg->lang("On Line"); ?></span>
							<div class="count green"><?= $online_customers; ?></div>
							<span class="count_bottom">
								<a href="./?p=10#<?= $_msg->lang('disconnected'); ?>">
									<strong class="red"><?= $offline_customers; ?></strong> <?= $_msg->lang("Off Line") ."\n"; ?>
								</a>
							</span>
						</div><div class="col-md-2 col-sm-4 col-ms-4 col-xs-6 tile_stats_count ellipsis">
							<span class="count_top"><i class="fa fa-dashboard"></i> <?= $_msg->lang("Mbits Sold"); ?></span>
							<div class="count"><?= $total_sold_mbits_download; ?></div>
							<span class="count_bottom"><strong><?= $total_sold_mbits_upload; ?></strong> <?= $_msg->lang("Upload"); ?></span>
						</div>
						<div class="col-md-2 col-sm-4 col-ms-4 col-xs-6 tile_stats_count ellipsis">
							<span class="count_top"><i class="fa fa-tags"></i> <?= $_msg->lang("Open Tickets"); ?></span>
							<div class="count"><a href="./?p=33"><?= $total_open_tickets; ?></a></div>
							<span class="count_bottom"><strong><?= $month_open_tickets; ?></strong> <?= $_msg->lang("This Month"); ?></span>
						</div>
						<div class="col-md-2 col-sm-4 col-ms-4 col-xs-6 tile_stats_count ellipsis">
							<span class="count_top"><i class="fa fa-user"></i> template</span>
							<div class="count">100</div>
							<span class="count_bottom"><i class="red"><i class="fa fa-sort-desc"></i>10% </i> From last Week</span>
						</div>
						<div class="col-md-2 col-sm-4 col-ms-4 col-xs-6 tile_stats_count ellipsis">
							<span class="count_top"><i class="fa fa-user"></i> Template</span>
							<div class="count">100</div>
							<span class="count_bottom"><i class="green"><i class="fa fa-sort-asc"></i>10% </i> From last Week</span>
						</div>
					</div>
					<script src="<?= $_path->js; ?>/Chart.bundle.min.js"></script>
					<div class="row">
<?php
	if(count($moment)>1) {
		$moment_str = "[ '".implode("', '", $moment)."' ]";
		$mbps_download_str = "[ ".implode(", ", $mbps_download)." ]";
		$mbps_upload_str = "[ ".implode(", ", $mbps_upload)." ]";
?>
						<div class="col-xl-9 col-md-12">
							<div class="x_panel" style="min-height: 200px;">
								<div class="x_title">
									<h2><?= $_msg->lang("Main Throughput"); ?></h2>
									<div class="clearfix"></div>
								</div>
								<div class="x_content"><canvas id="chart_mt" height="180"></canvas></div>
								<script>
									$(function () {
										var confMt = {
											type: 'line',
											data: {
												labels: <?= $moment_str; ?>,
												datasets: [{
													label: '<?= $_msg->lang("Upload"); ?>',
													data: <?= $mbps_upload_str; ?>,
													borderColor: 'rgba(224,32,0,0.6)',
													backgroundColor: 'rgba(224,32,0,0.1)'
												},{
													label: '<?= $_msg->lang("Download"); ?>',
													data: <?= $mbps_download_str; ?>,
													borderColor: 'rgba(0,128,224,0.6)',
													backgroundColor: 'rgba(0,128,224,0.1)'
												}]
											},
											options: {
												responsive: true,
												maintainAspectRatio: false,
												scales: {
													xAxes: [{
														type: 'time',
														time: {
															displayFormats: {
																minute: 'HH:mm',
																hour: 'HH:mm',
																day: 'HH:mm'
															},
															unitStepSize: <?= round($diff_moment/60); ?>,
															min: '<?= $moment[0]; ?>',
															max: '<?= $moment[(count($moment)-1)]; ?>'
														}
													}]
												},
												tooltips: {
													callbacks: {
														beforeTitle: (function(ttoj){
															for (var k=0; k<ttoj.length; k++) {
																var tpst = ttoj[k]['xLabel'].toString();
																ttoj[k]['xLabel'] = tpst.replace(/(\d+)-(\d+)-(\d+)/, '$3/$2/$1');
																ttoj[k]['yLabel'] += ' Mbps';
															}
														})
													}
												}
											}
										};
										$.each(confMt.data.datasets, function(i, dataset) {
											dataset.lineTension = 0.3;
											dataset.borderWidth = 1.5;
											dataset.pointBorderWidth = 0.5;
											dataset.pointRadius = 2.5;
											dataset.pointHitRadius = 10;
										});
										var ctxMt = document.getElementById("chart_mt").getContext("2d");
										window.chartMt = new Chart(ctxMt, confMt);
									});
								</script>
							</div>
						</div>
<?php
	} if(count($plans)>1) {
		$plans_str = "[ '".implode("', '", $plans)."' ]";
		$plan_custom_color_str = "[ '".implode("', '", $plan_custom_color)."' ]";
		$customers_per_plan_str = "[ ".implode(", ", $customers_per_plan)." ]";
?>
						<div class="col-xl-3 col-md-5 col-sm-5 col-ms-8 col-xs-12">
							<div class="x_panel" style="min-height: 200px;">
								<div class="x_title">
									<h2><?= $_msg->lang("Users per Plan"); ?></h2>
									<div class="clearfix"></div>
								</div>
								<div class="x_content"><canvas id="chart_up" height="160"></canvas></div>
								<script>
									$(function () {
										var ctxUp = document.getElementById("chart_up").getContext("2d");
										window.chartUp = new Chart(ctxUp, {
											type: 'doughnut',
											data: {
												labels: <?= $plans_str; ?>,
												datasets: [{
													label: '<?= $_msg->lang("Total"); ?>',
													data: <?= $customers_per_plan_str; ?>,
													backgroundColor: <?= $plan_custom_color_str; ?>
												}]
											},
											options: {
												responsive: true,
												maintainAspectRatio: false,
												legend: { position: 'right' }
											}
										});
									});
								</script>
							</div>
						</div>
<?php
	} if(count($customers_download_utilisation)>1) {
		$urlb_str = "[ '". $_msg->lang("1st") ."', '". $_msg->lang("2nd") ."', '". $_msg->lang("3rd");
		for($i=4; $i<=count($customers_download_utilisation); $i++) $urlb_str.= "', '". $_msg->lang("$i"."th");
		$urlb_str.= "' ]";
		$customers_upload_utilisation_str = "[ ".implode(", ", $customers_upload_utilisation)." ]";
		$customers_download_utilisation_str = "[ ".implode(", ", $customers_download_utilisation)." ]";

		$upun = array(); $dwun = array();
		foreach($customers_upload_utilisation as $temp => $vlup)
			$upun[] = ucwords(str_replace('.', ' ', substr($temp, 0, strpos($temp, '@'))));
		foreach($customers_download_utilisation as $temp => $vlup)
			$dwun[] = ucwords(str_replace('.', ' ', substr($temp, 0, strpos($temp, '@'))));
?>
						<div class="col-xl-4 col-md-7 col-sm-7 col-xs-12">
							<div class="x_panel" style="min-height: 200px;">
								<div class="x_title">
									<h2><?= $_msg->lang("Users Ranking"); ?></h2>
									<div class="clearfix"></div>
								</div>
								<input type="hidden" id="usernames_rkup" value='<?= json_encode($upun); ?>' />
								<input type="hidden" id="usernames_rkdw" value='<?= json_encode($dwun); ?>' />
								<div class="x_content">
									<div class="col-md-6 col-sm-6 col-ms-6 col-xs-12"><canvas id="chart_rkup" height="160"></canvas></div>
									<div class="col-md-6 col-sm-6 col-ms-6 col-xs-12"><canvas id="chart_rkdw" height="160"></canvas></div>
								</div>
								<script>
									$(function () {
										var lablRkUp = JSON.parse($("#usernames_rkup").val());
										var ctxRkUp = document.getElementById("chart_rkup").getContext("2d");
										window.chartRkUp = new Chart(ctxRkUp, {
											type: 'horizontalBar',
											data: {
												labels: <?= $urlb_str; ?>,
												datasets: [{
													label: '<?= $_msg->lang("Upload"); ?>',
													data: <?= $customers_upload_utilisation_str; ?>,
													borderColor: 'rgba(224,32,0,0.6)',
													backgroundColor: 'rgba(224,32,0,0.4)',
													borderWidth: 0.5
												}]
											},
											options: {
												responsive: true,
												maintainAspectRatio: false,
												tooltips: {
													callbacks: {
														beforeTitle: (function(ttoj){
															for (var k=0; k<ttoj.length; k++) {
																ttoj[k]['xLabel'] += ' %';
																var lbnm = parseInt(ttoj[k]['yLabel']) - 1;
																ttoj[k]['yLabel'] = lablRkUp[lbnm];
															}
														})
													}
												}
											}
										});
										var lablRkDw = JSON.parse($("#usernames_rkdw").val());
										var ctxRkDw = document.getElementById("chart_rkdw").getContext("2d");
										window.chartRkDw = new Chart(ctxRkDw, {
											type: 'horizontalBar',
											data: {
												labels: <?= $urlb_str; ?>,
												datasets: [{
													label: '<?= $_msg->lang("Download"); ?>',
													data: <?= $customers_download_utilisation_str; ?>,
													borderColor: 'rgba(0,128,224,0.6)',
													backgroundColor: 'rgba(0,128,224,0.4)',
													borderWidth: 0.5
												}]
											},
											options: {
												responsive: true,
												maintainAspectRatio: false,
												tooltips: {
													callbacks: {
														beforeTitle: (function(ttoj){
															for (var k=0; k<ttoj.length; k++) {
																ttoj[k]['xLabel'] += ' %';
																var lbnm = parseInt(ttoj[k]['yLabel']) - 1;
																ttoj[k]['yLabel'] = lablRkDw[lbnm];
															}
														})
													}
												}
											}
										});
									});
								</script>
							</div>
						</div>
<?php	} ?>
					</div>
