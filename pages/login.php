<?php
	/*******************************************************************
	* login page file, included by index if the user is not signed in. *
	* dependencies: paths as $_path (config);                          *
	*               msgs as $_msg (config);                            *
	*               session as $_session (login).                      *
	********************************************************************/

// ----- Checking Dependecies ----- //
	if(!isset($_msg)) die("Error: Messages Class not Initialized!");
	if(!isset($_path)) $_msg->err("Class Paths not set!");
	if(!isset($_session)) $_msg->err("Class Session not set!");
?>
		<div id="wrapper">
			<form method="post" action="./" enctype="application/x-www-form-urlencoded">
				<div class="site-logo">
					<h1>
						<a href="./" class="site">authentic</a>
						<img src="<?= $_path->images; ?>/favicon.ico" alt="[a]" style="width: 50px;"> authentic
					</h1>
				</div>
				<div class="col-md-12 form-group has-feedback">
					<input type="text" name="nam" class="form-control has-feedback-left" placeholder="<?= $_msg->lang('Username'); ?>" required />
					<span class="fa fa-user form-control-feedback left" aria-hidden="true"></span>
				</div>
				<div class="col-md-12 form-group has-feedback">
					<input type="password" name="pas" class="form-control has-feedback-left" placeholder="<?= $_msg->lang('Password'); ?>" required />
					<span class="fa fa-lock form-control-feedback left" aria-hidden="true"></span>
				</div>
				<div class="col-md-12">
					<button class="btn btn-primary" type="submit" style="padding: 6px 26px;"><?= $_msg->lang('Sign In'); ?></button>
				</div>
				<div class="clearfix"></div>
			</form>
			<script src="<?= $_path->js; ?>/custom.js?rev=<?= $_gitrev; ?>"></script>
			<script src="<?= $_path->js; ?>/pnotify.custom.min.js"></script>
			<script type="text/javascript">
				$(function() {
					if ($('input[name="nam"]').val().toString().length > 2) {
						if ($('input[name="pas"]').val().toString().length <= 2) $('input[name="pas"]').focus();
					} else $('input[name="nam"]').focus();
<?php	if($_session->error) echo "\t\t\t\t\talertPNotify('alert-danger', '$_session->error', 3000, \$('#wrapper form'))\n"; ?>
				});
			</script>
		</div>
