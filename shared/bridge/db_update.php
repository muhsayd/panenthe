<?php
/**
 * Panenthe VPS Management
 *
 * This is NOT Free Software
 * This software is NOT Open Source.
 * Please see panenthe.com for more information.
 *
 * Use of this software is binding of a license agreement.
 * This license agreeement may be found at panenthe.com
 *
 * Panenthe DOES NOT offer this software with any WARRANTY whatsoever.
 * Panenthe DOES NOT offer this software with any GUARANTEE whatsoever.
 *
 * @copyright Panenthe, Nullivex LLC. All Rights Reserved.
 * @author Nullivex LLC <contact@nullivex.com>
 * @license http://www.panenthe.com
 * @link http://www.panenthe.com
 *
 */

// chdir to load config
chdir(dirname(__FILE__));

//Load Config
require('../core/config_parser.php');

//Load DB Handler
require(PANENTHE_ROOT.'/ui/dev/lib/dev_db.php');
$db = new dev_db($config['db']);

// wrapper to execute a query
function db_exec($qry, $args){
	try {
		$qry->execute($args);
	} catch(PDOException $e){
		echo $e->getMessage();
		exit(1);
	}
}

//DB Update Class
final class db_update {
	var $db;

	public function __construct($db_connection){
		$this->db = $db_connection;
	}

	// events table
	public function events($args){
		switch($args[0]){
			case 'insert':
				$message = $args[1];
				$code = $args[2];

				$time = $created = $modified = time();

				$qry = $this->db->prepare('
					INSERT INTO events (
						message, code, time, is_acknowledged, created, modified
					) VALUES (?, ?, ?, ?, ?, ?)
				');

				db_exec($qry, array(
					$message, $code, $time, 0, $created, $modified
				));

				break;
		}
	}

	// ost table
	public function ost($args){
		switch($args[0]){
			case 'insert':
				$name = $args[1];
				$path = $args[2];
				$driver_id = $args[3];
				$arch = $args[4];

				$qry = $this->db->prepare('
					INSERT INTO ost (name, path, driver_id, arch)
					VALUES (?, ?, ?, ?)
				');

				db_exec($qry, array($name, $path, $driver_id, $arch));

				break;
		}
	}

	// server table
	public function server($args){
		switch($args[0]){
			case 'is_locked':
				$server_id = $args[1];
				$is_locked = $args[2];

				$qry = $this->db->prepare('
					UPDATE server SET is_locked = ? WHERE server_id = ?
				');

				db_exec($qry, array($is_locked, $server_id));

				break;
		}
	}

	// server_stats table
	public function server_stats($args){
		switch($args[0]){
			case 'update_attribute':
				$server_id = $args[1];
				$name = $args[2];
				$value = $args[3];

				// look for existing attribute
				$qry = $this->db->prepare('
					SELECT server_stat_id FROM server_stats
					WHERE server_id = ? AND name = ?
				');

				db_exec($qry, array($server_id, $name));
				$data = $qry->fetchAll();

				// already exists, update
				if(count($data) > 0){
					$qry = $this->db->prepare('
						UPDATE server_stats SET value = ?
						WHERE server_stat_id = ?
					');

					db_exec($qry, array($value, $data[0]['server_stat_id']));
				}

				// non-existant, find server_id and insert
				else{
					// insert value
					$qry = $this->db->prepare('
						INSERT INTO server_stats (server_id, name, value)
						VALUES (?, ?, ?)
					');

					db_exec($qry, array($server_id, $name, $value));
				}

				break;
		}
	}

	// vps table
	public function vps($args){
		switch($args[0]){
			case 'delete':
				$vps_id = $args[1];

				$qry = $this->db->prepare('
					DELETE FROM ip_map WHERE vps_id = ?
				');

				db_exec($qry, array($vps_id));

				$qry = $this->db->prepare('
					DELETE FROM vps_stats WHERE vps_id = ?
				');

				db_exec($qry, array($vps_id));

				$qry = $this->db->prepare('
					DELETE FROM vps_status_history WHERE vps_id = ?
				');

				db_exec($qry, array($vps_id));

				$qry = $this->db->prepare('
					DELETE FROM vps_user_map WHERE vps_id = ?
				');

				db_exec($qry, array($vps_id));

				$qry = $this->db->prepare('
					DELETE FROM vps WHERE vps_id = ?
				');

				db_exec($qry, array($vps_id));

				break;

			case 'is_locked':
				$vps_id = $args[1];
				$is_locked = $args[2];

				$qry = $this->db->prepare('
					UPDATE vps SET is_locked = ? WHERE vps_id = ?
				');

				db_exec($qry, array($is_locked, $vps_id));

				break;

			case 'is_running':
				$server_id = $args[1];
				$vps_id = $args[2];
				$is_running = $args[3];

				$qry = $this->db->prepare('
					UPDATE vps SET is_running = ?
					WHERE vps_id = ? AND server_id = ?
				');

				db_exec($qry, array($is_running, $vps_id, $server_id));

				break;

			case 'update_real_id':
				$vps_id = $args[1];
				$real_id = $args[2];

				$qry = $this->db->prepare('
					UPDATE vps SET real_id = ?
					WHERE vps_id = ?
				');

				db_exec($qry, array($real_id, $vps_id));

				break;
		}
	}

	// vps_stats table
	public function vps_stats($args){
		switch($args[0]){
			case 'update_attribute':
				$server_id = $args[1];
				$vps_id = $args[2];
				$name = $args[3];
				$value = $args[4];

				// look for existing attribute
				$qry = $this->db->prepare('
					SELECT vps_stat_id FROM vps_stats AS vs
					LEFT JOIN vps AS v ON v.vps_id = vs.vps_id
					WHERE v.server_id = ? AND v.vps_id = ? AND vs.name = ?
				');

				db_exec($qry, array($server_id, $vps_id, $name));
				$data = $qry->fetchAll();

				// already exists, update
				if(count($data) > 0){
					$qry = $this->db->prepare('
						UPDATE vps_stats SET value = ?
						WHERE vps_stat_id = ?
					');

					db_exec($qry, array($value, $data[0]['vps_stat_id']));
				}

				// non-existant, find vps_id and insert
				else{
					// find vps_id
					$qry = $this->db->prepare('
						SELECT vps_id FROM vps
						WHERE server_id = ? and vps_id = ?
					');

					db_exec($qry, array($server_id, $vps_id));
					$data = $qry->fetchAll();

					// get vps_id
					$vps_id = $data[0]['vps_id'];

					// insert value
					$qry = $this->db->prepare('
						INSERT INTO vps_stats (vps_id, name, value)
						VALUES (?, ?, ?)
					');

					db_exec($qry, array($vps_id, $name, $value));
				}

				break;

			case 'update_bandwidth':
				$server_id = $args[1];
				$vps_id = $args[2];
				$in_bw = intval($args[3]);
				$out_bw = intval($args[4]);

				// look for existing attribute
				$qry = $this->db->prepare('
					SELECT vps_stat_id, vs.value FROM vps_stats AS vs
					LEFT JOIN vps AS v ON v.vps_id = vs.vps_id
					WHERE
						v.server_id = ? AND v.vps_id = ? AND
						vs.name = \'in_bw\'
				');

				db_exec($qry, array($server_id, $vps_id));
				$in_data = $qry->fetchAll();

				// look for existing attribute
				$qry = $this->db->prepare('
					SELECT vps_stat_id, vs.value FROM vps_stats AS vs
					LEFT JOIN vps AS v ON v.vps_id = vs.vps_id
					WHERE
						v.server_id = ? AND v.vps_id = ? AND
						vs.name = \'out_bw\'
				');

				db_exec($qry, array($server_id, $vps_id));
				$out_data = $qry->fetchAll();

				if(count($in_data) == 0 && count($out_data) == 0){
					// find vps_id
					$qry = $this->db->prepare('
						SELECT vps_id FROM vps
						WHERE server_id = ? and vps_id = ?
					');

					db_exec($qry, array($server_id, $vps_id));
					$data = $qry->fetchAll();

					// get vps_id
					$vps_id = $data[0]['vps_id'];
				}

				// in data already exists, update
				if(count($in_data) > 0){
					$in_bw += intval($in_data[0]['value']);
					$vps_stat_id = $in_data[0]['vps_stat_id'];

					$qry = $this->db->prepare('
						UPDATE vps_stats SET value = ?
						WHERE vps_stat_id = ?
					');

					db_exec($qry, array($in_bw, $vps_stat_id));
				}

				// in data non-existant, find vps_id and insert
				else{
					// insert value
					$qry = $this->db->prepare('
						INSERT INTO vps_stats (vps_id, name, value)
						VALUES (?, \'in_bw\', ?)
					');

					db_exec($qry, array($vps_id, $in_bw));
				}

				// out data already exists, update
				if(count($out_data) > 0){
					$out_bw += intval($out_data[0]['value']);
					$vps_stat_id = $out_data[0]['vps_stat_id'];

					$qry = $this->db->prepare('
						UPDATE vps_stats SET value = ?
						WHERE vps_stat_id = ?
					');

					db_exec($qry, array($out_bw, $vps_stat_id));
				}

				// out data non-existant, find vps_id and insert
				else{
					// insert value
					$qry = $this->db->prepare('
						INSERT INTO vps_stats (vps_id, name, value)
						VALUES (?, \'out_bw\', ?)
					');

					db_exec($qry, array($vps_id, $out_bw));
				}

				break;
		}
	}

	// update DB in general
	public function update_db($args){
		$sql_raw = file_get_contents($args[0]);
		$sql_queries = explode(';', $sql_raw);

		// execute queries
		foreach($sql_queries as $sql_query){
			$qry = $this->db->query($sql_query);
		}
	}

}

$db_update = new db_update($db);

// get command line arguments
$database = $_SERVER['argv'][1];
$args = array_splice($_SERVER['argv'], 2);

// execute update
$db_update->$database($args);

?>
