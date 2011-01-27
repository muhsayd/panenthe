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

window.addEvent("domready", function(){

	header_clock();

});


//Header Clock
header_clock = function(){

	//Add Leading Zeros
	function clockLeadingZeros(str){
		if(str < 10) str = "0" + str;
		return str;
	}
	//Get Day Orientation
	function clockDayOrientation(str){
		var orientation = "am";
		//if(str == 0) str = 12;
		if(str == 0) return orientation;
		if(str > 11) orientation = "pm";
		return orientation;
	}
	//Fix Hours
	function clockFixHours(str){
		if(str == 0) str = 12;
		if(str>12) str -= 12;
		return str;
	}

	clockId = 0;
	var updateClock = function() {

		var tDate = new Date();
		var Months = new Array("Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");
		//Get Time
		var hours = clockFixHours(tDate.getHours());
		var minutes = clockLeadingZeros(tDate.getMinutes());
		var orientation = clockDayOrientation(tDate.getHours());

		$("header_time_hour").set('text',hours);
		$("header_time_minute").set('text',minutes);
		$("header_time_period").set('text',orientation);
		$("header_date_month").set('text',Months[tDate.getMonth()]);
		$("header_date_day").set('text',(parseInt(tDate.getDate())));
		$("header_date_year").set('text',tDate.getFullYear());

	}

	updateClock();
	var periodicalClock = updateClock.periodical(1000);

}



