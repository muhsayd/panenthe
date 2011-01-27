<pre>
<?
include('/opt/panenthe/shared/bridge/python.php');

if(!empty($_GET['action'])){

	$action = $_GET['action'];

	$in_array = array(
		'vps_id' => 1273,
		'real_id' => 110,
		'driver' => 'xen',
		'hostname' => 'asd.vm',
		'server' => array(
			'server_id' => 80,
			'parent_server_id' => 1,
			'hostname' => 'dev1.panenthe.com',
			'ip' => '69.197.128.226',
			'port' => 8032
		),
		'master' => array(
			'server_id' => 1,
			'parent_server_id' => 0,
			'hostname' => 'vm.panenthe.com',
			'ip' => '10.0.0.1',
			'port' => 8032
		)
	);

	switch($action){
		case 'create':
			#unset($in_array['real_id']); // now we require real_id
			$in_array['vps_id'] = '1';
		case 'rebuild':
			$in_array['disk_space'] = '10000000';
			$in_array['backup_space'] = '1000000';
			$in_array['swap_space'] = '100000';
			$in_array['g_mem'] = '3000000';
			$in_array['b_mem'] = '1000000';
			$in_array['cpu_pct'] = '70';
			$in_array['cpu_num'] = '4';
			$in_array['in_bw'] = '10';
			$in_array['out_bw'] = '10';
			$in_array['ost'] = 'centos-5-x86';
			break;
		case 'passwd':
			$in_array['username'] = 'root';
			$in_array['password'] = 'asdasd';
			break;
		case 'modify':
			$in_array['disk_space'] = '15000000';
			$in_array['backup_space'] = '2000000';
			$in_array['swap_space'] = '50000';
			$in_array['g_mem'] = '2000000';
			$in_array['b_mem'] = '1500000';
			$in_array['cpu_pct'] = '90';
			$in_array['cpu_num'] = '2';
			$in_array['in_bw'] = '10';
			$in_array['out_bw'] = '10';
			$in_array['ost'] = 'centos-5-x86';
			break;
		case 'add_ip':
		case 'remove_ip':
			$in_array['ip'] = $_GET['ip'];
			break;
		case 'service_restart':
			$in_array['service'] = $_GET['service'];
			break;
		case 'next_id':
			unset($in_array['real_id']);
			break;
	}

	$array = backend_call('vpsctl', $action, $in_array, true);
	print_r($array);

}

?>
</pre>

<br /><br />

<a href="?action=next_id">Next ID</a>
<br />
<a href="?action=status">Status</a>
<br />
<a href="?action=create">Create 130</a>
<br />
<a href="?action=destroy">Destroy 130</a>
<br />
<a href="?action=modify">Modify 130</a>
<br />
<a href="?action=passwd">Change PW to 'asdasd' 130</a>
<br />
<a href="?action=stop">Stop</a>
<br />
<a href="?action=start">Start</a>
<br />
<a href="?action=poweroff">Poweroff</a>
<br />
<a href="?action=reboot">Reboot</a>
<br />
<a href="?action=service_restart&service=network&ip=10.0.0.1">Net Reset</a>
<br />
<a href="?action=add_ip&ip=10.0.0.1">Add IP 10.0.0.1</a>
<br />
<a href="?action=add_ip&ip=10.0.0.2">Add IP 10.0.0.2</a>
<br />
<a href="?action=remove_ip&ip=10.0.0.1">Remove IP 10.0.0.1</a>
<br />
<a href="?action=remove_ip&ip=10.0.0.2">Remove IP 10.0.0.2</a>
<br />
<a href="?action=remove_all_ips">Remove All IPs</a>
<br />
<a href="?action=usage_disk">Usage Disk</a>
<br />
<a href="?action=usage_mem">Usage Mem</a>
