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

$templates['footer'] = '
				</td>
			</tr>
			</table>
		</div>

		<div class="admin_footer">
			<div>&copy; <a href="{script_url}">{copyright_brand}</a>. All Rights Reserved. | 
			Powered By: <a href="{script_url}">{script_name} {script_version}</a> |
			{performance}</div>
		</div>
	</div>
</div>
{dbg_output}
</body>
</html>
';

?>
