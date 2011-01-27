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

if(!defined("IS_INCLUDED")){
	exit;
}

$templates = array();

$templates['home'] = '
			<script type="text/javascript">
				window.addEvent("domready",function(){
					$("sidebar").setStyle("width","225px");
					$$(".sideNav").setStyle("width","225px");
				});
			</script>
			

			<div class="title">
				VM Manager
			</div>

			<div class="body">
				<div class="feature_block">
					<a href="index.php?app=vps&sec=browse_vps" class="disableLoadingLink">
						<img src="icons/48x48/devices/blockdevice.png" alt="View Virtual Machines" class="disableLoadingLink" /><br />
						View Virtual Machines
					</a>
				</div>
				<div class="feature_block">
					<a href="index.php?app=vps&sec=insert_vps" class="disableLoadingLink">
						<img src="icons/48x48/apps/display.png" alt="Create Virtual Machine" class="disableLoadingLink" /><br />
						Create Virtual Machine
					</a>
				</div>
				<div class="feature_block">
					<a href="index.php?app=vps&sec=status_vps" class="disableLoadingLink">
						<img src="icons/48x48/apps/ksysguard.png" alt="Virtual Machine Status" class="disableLoadingLink" /><br />
						Virtual Machine Status
					</a>
				</div>
				<div class="clear_both"><!--No Content--></div>
			</div>

			<div class="title">
				User Manager
			</div>

			<div class="body">
				<div class="feature_block">
					<a href="index.php?app=users&sec=insert_user" class="disableLoadingLink">
						<img src="icons/48x48/apps/kuser.png" alt="Add User" class="disableLoadingLink" /><br />
						Add User
					</a>
				</div>
				<div class="feature_block">
					<a href="index.php?app=users&sec=browse_clients" class="disableLoadingLink">
						<img src="icons/48x48/filesystems/folder_open.png" alt="View Clients" class="disableLoadingLink" /><br />
						View Clients
					</a>
				</div>
				<div class="feature_block">
					<a href="index.php?app=users&sec=browse_staff" class="disableLoadingLink">
						<img src="icons/48x48/filesystems/folder_home.png" alt="View Staff" class="disableLoadingLink" /><br />
						View Staff
					</a>
				</div>
				<div class="clear_both"><!--No Content--></div>
			</div>
			
			<div class="title">
				{site_name} Settings
			</div>

			<div class="body">
				<div class="feature_block">
					<a href="index.php?app=events&sec=browse_events" class="disableLoadingLink">
						<img src="icons/48x48/apps/kate.png" alt="View System Events" class="disableLoadingLink" /><br />
						View System Events
					</a>
				</div>
				<div class="feature_block">
					<a href="index.php?app=settings" class="disableLoadingLink">
						<img src="icons/48x48/apps/autostart.png" alt="System Settings" class="disableLoadingLink" /><br />
						System Settings
					</a>
				</div>
				<div class="feature_block">
					<a href="index.php?app=update" class="disableLoadingLink">
						<img src="icons/48x48/apps/switchuser.png" alt="Live Update" class="disableLoadingLink" /><br />
						Live Update
					</a>
				</div>
				<div class="feature_block">
					<a href="index.php?app=logviewer" class="disableLoadingLink">
						<img src="icons/48x48/apps/kghostview.png" alt="View Logs" class="disableLoadingLink" /><br />
						View Logs
					</a>
				</div>
				<div class="clear_both"><!--No Content--></div>
			</div>

			<div class="title">
				Management Tools
			</div>

			<div class="body">
				<div class="feature_block">
					<a href="index.php?app=ip_pools" class="disableLoadingLink">
						<img src="icons/48x48/filesystems/Globe2.png" alt="Manage IP Pools" class="disableLoadingLink" /><br />
						Manage IP Pools
					</a>
				</div>
				<div class="feature_block">
					<a href="index.php?app=plans" class="disableLoadingLink">
						<img src="icons/48x48/apps/background.png" alt="Manage Resource Plans" class="disableLoadingLink" /><br />
						Manage Resource Plans
					</a>
				</div>
				<div class="feature_block">
					<a href="index.php?app=servers" class="disableLoadingLink">
						<img src="icons/48x48/devices/hdd_unmount.png" alt="Manage Servers" class="disableLoadingLink" /><br />
						Manage Servers
					</a>
				</div>
				<div class="feature_block">
					<a href="index.php?app=welcomeemail" class="disableLoadingLink">
						<img src="icons/48x48/other/mailappt.png" alt="Change Welcome Email" class="disableLoadingLink" /><br />
						Welcome Email
					</a>
				</div>
				<div class="clear_both"><!--No Content--></div>
			</div>
			<div class="body">
				<!--<div class="feature_block">
					<a href="index.php?app=migrate" class="disableLoadingLink">
						<img src="icons/48x48/other/wizard.png" alt="Migration" class="disableLoadingLink" /><br />
						Migration Center
					</a>
				</div>-->
				<div class="feature_block">
					<a href="index.php?update_license=true" class="disableLoadingLink">
						<img src="icons/48x48/apps/xfmail.png" alt="Update License" class="disableLoadingLink" /><br />
						Update License
					</a>
				</div>
				<div class="clear_both"><!--No Content--></div>
			</div>
			
';

?>
