<?php
	/******************************************************************
	* list page file, included by start if selected (also ?p=1).      *
	* dependencies: msgs as $_msg (config); session &$ (login).        *
	*******************************************************************/

	// ----- Checking Dependecies ----- //
	if(!isset($_msg)) die("Error: Messages Class not Initialized!");
	if(!isset($_session)) $_msg->err("Class Session not set!");
	// ----- Defining List Page Variables ----- //
	switch ($page_number) {
		case 12:	$list_title = $_msg->lang("Administrators");
					$list_type = "admn"; break;
		case 11:	$list_title = $_msg->lang("Technicians");
					$list_type = "tech"; break;
		default:	$list_title = $_msg->lang("Customers");
					$list_type = "inet"; break;
	}
?>
					<div class="x_panel">
						<div class="x_title">
							<h2><?= $_msg->lang("List") ." &raquo; ". $list_title; ?></h2>
							<button class="btn btn-primary pull-right strong" onclick="window.location='./?p=<?= ($page_number + 10); ?>';"><?= $_msg->lang("Add"); ?></button>
							<div class="col-md-2 col-sm-2 col-ms-2 col-xs-2 pull-right">
								<i class="fa fa-refresh fa-spin fa-fw" style="visibility: hidden;"></i>
							</div>
							<div class="clearfix"></div>
						</div>
						<div class="x_content">
							<input type="hidden" id="type" value="<?= $list_type; ?>" />
							<div id="pnsc" style="top: -12px;"></div>
							<h5 class="col-xs-6"><strong><?= $_msg->lang("Total"); ?>: </strong><span id="total_result">00</span></h5>
							<input type="search" id="search" class="form-control pull-right" placeholder="<?= $_msg->lang('Search'); ?>" style="max-width: 200px;" />
							<table id="otpt" class="table responsive jambo_table">
								<thead>
									<tr class="headings">
										<th><?= $_msg->lang("Full Name"); ?></th>
										<th><?= $_msg->lang("Register Date"); ?></th>
										<th><?= $_msg->lang("MAC"); ?></th>
										<th><?= $_msg->lang("Plan"); ?></th>
										<th class="no-link last"><?= $_msg->lang("Status"); ?></th>
									</tr>
								</thead>
								<tbody></tbody>
							</table>
						</div>
						<div id="modal_details" class="modal fade" role="dialog">
							<div class="modal-dialog">
								<div class="modal-content">
									<div class="modal-header">
										<button class="close" data-dismiss="modal">×</button>
										<strong style="padding-right: 20px;"><?= $_msg->lang("Details"); ?></strong>
										<div class="btn-group">
											<button id="details_history" class="btn btn-default"><?= $_msg->lang("History"); ?></button>
<?php	if($list_type == 'eqip' || $list_type == 'inet') { ?>
											<button id="details_support" class="btn btn-default"><?= $_msg->lang("Support"); ?></button>
<?php	} ?>
											<button id="details_edit" class="btn btn-default" onclick="list_detailsEdit(this);"><?= $_msg->lang("Edit"); ?></button>
										</div>
<?php	if($list_type == 'inet') { ?>
										<div class="btn-group">
											<button id="details_ticket" class="btn btn-warning"><?= $_msg->lang("Open Ticket"); ?></button>
										</div>
<?php	} if($_session->groupname != 'tech') { ?>
										<div class="btn-group">
<?php		if($list_type != 'eqip') { ?>
											<button id="details_disable" class="btn btn-default" data-enable="<?= $_msg->lang('Enable'); ?>"><?= $_msg->lang("Disable"); ?></button>
<?php		} ?>
											<button id="details_enable" class="btn btn-default"><?= $_msg->lang("Delete"); ?></button>
										</div>
<?php	} ?>
									</div>
									<div class="modal-body"></div>
									<div class="modal-footer">
										<button class="btn btn-default form-edit" onclick="list_detailsEdit($('#modal_details #details_edit'));"><?= $_msg->lang('Cancel'); ?></button>
										<button class="btn btn-info form-edit" onclick=""><?= $_msg->lang('Save'); ?></button>
									</div>
								</div>
							</div>
						</div>
						<script src="<?= $_path->js; ?>/bootstrap-select.min.js"></script>
						<script type="text/javascript" >
							$(function () {
							// Define hashChange listener
								window.addEventListener('hashchange', function() {
								// Get current hashes
									var wths = new String(window.location.href).split('#');
									var wtsc = (wths[1] == undefined) ? ('') : (wths[1]);
								// Do the AJaJSON request
									list_doit($("#type").val(), wtsc);
								});
							// Prepare document loading animation
								$(document).ajaxStart(function (){ $('.fa-spin').css("visibility", 'visible');})
												.ajaxStop(function (){ $('.fa-spin').css("visibility", 'hidden'); });
							// Get current hashes
								var wths = new String(window.location.href).split('#');
								var wtsc = (wths[1] == undefined) ? ('') : (wths[1]);
								$("#search").val(wtsc);
								$("#search").on("input", function () {
									var wths = new String(window.location.href).split('#');
									window.location = wths[0] +'#'+ $(this).val();
								});
							// --- Initialize the table
								list_doit($("#type").val(), wtsc);
								// colocar funcões nestes IDs => "hs","sp","ed","en","ds","dl"; (_id)
							});
						</script>
					</div>
