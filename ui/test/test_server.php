<pre>
<?
include('/opt/panenthe/shared/bridge/python.php');

if(!empty($_GET['action']) && !empty($_GET['ctl'])){

	$action = $_GET['action'];
	$ctl = $_GET['ctl'];

	$in_array = array(
		'server_id' => 1,
		'parent_server_id' => 0,
		'hostname' => 'srv1.panenthe.com',
		'ip' => '208.71.212.150',
		'port' => 8032
	);

	if(
		$ctl == 'serverctl' ||
		$action == 'install_key' ||
		$action == 'remove_key'
	){
		$in_array['remote_server']=array(
			'server_id' => 1,
			'parent_server_id' => 0,
			'hostname' => 'srv1.panenthe.com',
			'ip' => '208.71.212.166',
			'port' => 8032
		);
	}

	switch($action){
		case 'install_key':
			$in_array['remote_server']['password'] = 'pan1029';
			break;
		case 'ssh_port':
			$in_array['new_ssh_port'] = 8032;
			break;
		case 'passwd':
			$in_array['username'] = 'root';
			$in_array['password'] = 'pan1029';
			break;
	}

	$array = backend_call($_GET['ctl'], $action, $in_array, true);
	print_r($array);

}

?>
</pre>

<br /><br />

<a href="?ctl=masterctl&action=install_key">Master: Install Key on srv3</a>
<br />
<a href="?ctl=masterctl&action=remove_key">Master: Remove Key on srv3</a>
<br />
<a href="?ctl=masterctl&action=remove_local_keys">Master: Remove Local Keys</a>
<br />
<a href="?ctl=masterctl&action=generate_keys">Master: Generate Keys</a>
<br /><br />
<a href="?ctl=serverctl&action=ssh_port">SSH Port Change 8032 srv3</a>
<br />
<a href="?ctl=serverctl&action=passwd">Change Root Password</a>
<br />
<a href="?ctl=serverctl&action=status_update_all">Status Update All</a>
<br />
<a href="?ctl=serverctl&action=shutdown">Shutdown srv3</a>
<br />
<a href="?ctl=serverctl&action=reboot">Reboot srv3</a>
