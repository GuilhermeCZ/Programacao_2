<?php
	/*************************************************************************
	* session set file, requested by custom AJaX from start page when toggle *
	* menu side-menu OR change language, in order to set this attributes to  *
	* the current user session (cookie).                                     *
	**************************************************************************/

	if(isset($_POST['ajax'])) {
		require("../config.php");
		require("../login.php");
		// --- Just set session side-menu class
		if(isset($_POST['lm'])) $_SESSION['side-menu'] = ($_POST['lm']=='1') ? ('nav-sm') : ('nav-md');
		// --- Just set session language
		if(isset($_POST['lg'])) $_SESSION['lang'] = $_POST['lg'];
	}
?>
