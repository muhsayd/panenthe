<pre>
<?
include('/opt/panenthe/shared/bridge/python.php');

if(!empty($_GET['action'])){

	$action = $_GET['action'];

	$in_array = array(
		'driver' => 'ovz',
		'reboot' => true,
		'server' => array(
			'server_id' => 1,
			'parent_server_id' => 0,
			'hostname' => 'dev1.panenthe.com',
			'ip' => '69.197.128.226',
			'port' => 8032
		)
	);

	/*/ dev2
	$in_array['server']['remote_server']=array(
		'server_id' => 1,
		'parent_server_id' => 0,
		'hostname' => 'dev2.panenthe.com',
		'ip' => '69.197.128.234',
		'port' => 8032
	);
	//*/

	// dev4
	$in_array['server']['remote_server']=array(
		'server_id' => 1,
		'parent_server_id' => 0,
		'hostname' => 'dev4.panenthe.com',
		'ip' => '71.194.58.38',
		'port' => 7180
	);
	//*/

	# special cases
	switch($action){
	}

	$array = backend_call('driverctl', $action, $in_array, true);
	print_r($array);

}

?>
</pre>

<br /><br /><hr /><br /><br />

<a href="?action=install&driver=ovz">ovz install on dev2</a>
<br />
<a href="?action=uninstall&driver=ovz">ovz uninstall on dev2</a>
<br />
<a href="?action=activate&driver=ovz">ovz activate on dev2</a>
<br />
<a href="?action=deactivate&driver=ovz">ovz deactivate on dev2</a>
<br /><br />
<a href="?action=start&driver=ovz">ovz start on dev2</a>
<br />
<a href="?action=stop&driver=ovz">ovz stop on dev2</a>
<br />
<a href="?action=restart&driver=ovz">ovz restart on dev2</a>
<br /><br />
<a href="?action=status&driver=ovz">ovz status on dev2</a>
<br />
<a href="?action=cleanup&driver=ovz">ovz cleanup on dev2</a>

<br /><br /><hr /><br /><br />

<a href="?action=install&driver=xen">xen install on dev2</a>
<br />
<a href="?action=uninstall&driver=xen">xen uninstall on dev2</a>
<br />
<a href="?action=activate&driver=xen">xen activate on dev2</a>
<br />
<a href="?action=deactivate&driver=xen">xen deactivate on dev2</a>
<br /><br />
<a href="?action=start&driver=xen">xen start on dev2</a>
<br />
<a href="?action=stop&driver=xen">xen stop on dev2</a>
<br />
<a href="?action=restart&driver=xen">xen restart on dev2</a>
<br /><br />
<a href="?action=status&driver=xen">xen status on dev2</a>
<br />
<a href="?action=cleanup&driver=xen">xen cleanup on dev2</a>
