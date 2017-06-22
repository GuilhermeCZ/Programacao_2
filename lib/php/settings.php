<?php
	/***************************************************************
	* settings arrange system variables from DataBase into arrays. *
	* dependencies: class pgsql in global $_pgobj (config).        *
	****************************************************************/

	class settings {
	// Load system settings from DataBase //
		function __construct() {
			global $_pgobj;
			if(!isset($_pgobj->error)) return FALSE; // dependency not met
			$_pgobj->query("SELECT * FROM at_settings ORDER BY type, seqn");
			for ($i=0; $i<$_pgobj->rows; $i++) {
				$temp = $_pgobj->fetch_array();
				$data = (preg_match("/^a\:\d+\:\{/", $temp['data'])) ? (unserialize($temp['data'])) : ($temp['data']);
				eval("\$temp_target = &\$this->". $temp['type'] .";");
				$temp_target[$temp['conf']] = $data;
				$this->full[$temp['type']][$temp['conf']] = array('id' => $temp['id']);
				$this->full[$temp['type']][$temp['conf']]['seqn'] = $temp['seqn'];
				$this->full[$temp['type']][$temp['conf']]['data'] = $data;
			}
		}
	// Declaring Varialbles //
		public $ticket_category = array();
		public $ticket_priority = array();
		public $form_field = array();
		public $config = array();
		public $device = array();
		public $full = array('ticket_category' => array(),
									'ticket_priority' => array(),
									'form_field' => array(),
									'config' => array(),
									'device' => array());
		// This variables are the types of settings on the database,
		// if any type ever be added to the database, it shall be added here too.
	}
?>
