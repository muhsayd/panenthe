window.addEvent('domready', function(){

	$("plan_id").addEvent('change', function(){
		plan_autofill();
	});

	plan_autofill();

});

var plan_autofill = function(){

	if($("plan_id").value != ""){
		var plan = plan_information[$("plan_id").value];

		$("disk_space").value = plan['disk_space'];
		$("backup_space").value = plan['backup_space'];
		$("swap_space").value = plan['swap_space'];
		$("g_mem").value = plan['g_mem'];
		$("b_mem").value = plan['b_mem'];
		$("cpu_pct").value = plan['cpu_pct'];
		$("cpu_num").value = plan['cpu_num'];
		$("out_bw").value = plan['out_bw'];
		$("in_bw").value = plan['in_bw'];
	}
	
}