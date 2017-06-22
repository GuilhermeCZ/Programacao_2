<?php
	/***********************************************************
	* sshc creates a SSHv2 connection using PHP SSH2 extension *
	* and stores the resulting socket to $this->conn           *
	* this extension depends on "php-pecl-ssh2" package!       *
	************************************************************/

	class sshc {
		// Initialize persistent connection //
		function __construct($host, $usnm, $pass, $port=22) {
			// Check SSH2 Extension...
			if(!function_exists('ssh2_connect')) return $this->err("SSH2 Extension not found!");
			// Check IP Address...
			if(!filter_var($host, FILTER_VALIDATE_IP) === false) $ipad = $host;
			else {
				$temp = gethostbyname($host);
				if(!filter_var($temp, FILTER_VALIDATE_IP) === false) $ipad = $temp;
				else return $this->err("Invalid Host Name!");
			}
			// Set Connection...
			function falloff($rsn, $msg, $lng) {
				$this->error = "Server disconnected! $rsn ($msg)";
				return TRUE;
			}
			$callback = array( "disconnect" => "falloff");
			$this->method = array( "kex" => "diffie-hellman-group1-sha1" );
			set_time_limit(4);

			$this->conn = ssh2_connect($ipad, $port, $this->method, $callback);
			if(!is_resource($this->conn)) return $this->err("Connection Failed!");
			if(!ssh2_fingerprint($this->conn)) return $this->err("Fingerprint Failed!");
			if(!ssh2_auth_password($this->conn, $usnm, $pass)) return $this->err("Authentication Failed!");
		}
		// Initialize variables //
		public $error = "";
		public $result = FALSE;
		public $output = "";
		public $rows = 0;
		// Exec Function //
		function exec($cmnd) {
			if($resource = ssh2_exec($this->conn, $cmnd)) {
				stream_set_blocking($resource, 1);
				$this->result = fgets($resource);
				$this->output = "";
				$this->rows = 0;
				while($this->result) {
					$this->output.= $this->result . PHP_EOL;
					$this->result = fgets($resource);
					$this->rows++;
				} fclose($resource);
			} else return $this->err("Command Execution Failed!");
			return $this->result;
		}
		// Search Function //
		function search($strg) {
			$temp = explode(PHP_EOL, $this->output);
			for($i=0; $i<count($temp); $i++) if(stristr($temp[$i], $strg)) break;
			return $temp[$i];
		}
		// Error Function //
		function err($str) {
			$this->error = $str;
			return FALSE;
		}
		// Close Function //
		function close() {
			if(is_resource($this->conn)) if($this->exec("exit")) $this->exec("quit");
		}
		public function __destruct() { $this->close(); }
	}
?>
