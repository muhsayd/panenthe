<?php
/**
 * Panenthe
 *
 * Very light PHP Framework
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

class dev_mail {

    private $config;

	public function __construct($config){
        $this->config = $config;
	}

	/**
	 * Send Mail Function
	 *
	 * @param $info Array - Array of information for the function
	 *
	 * Possible Usage With All Params
	 *	$info = array(
	 *		"To"		=>	"recipient@test.com",
	 *		"From"		=>	"from@test.com",
	 *		"ReplyTo"	=>	"replyto@test.com",
	 *		"Subject"	=>	"subject",
	 *		"Message"	=>	"<b>message</b>",
	 *		"AltMessage"=>	"message", // Optional
	 *		"attachments"	=>	array(
	 *			"images/testattach.gif"
	 *		)
	 *	);
	 */
	public function sendMail($info=array()){

		$default_info = array(
			"To"			=>	"",
			"Subject"		=>	"",
			"Message"		=>	"",
			"AltMessage"	=>	"",
			"From"			=>	$this->config['default_from'],
			"ReplyTo"		=>	$this->config['default_replyto'],
			"Attachments"	=>	array()
		);

		$info = array_merge($default_info,$info);

		//Get Classes
		require_once('phpmailer/phpmailer.php');
		require_once('phpmailer/smtp.php');

		//Configure PHPMailer
		$mail = new PHPMailer(true); //Exceptions enabled

		try{

			if($this->config['smtp_enable'] === true){
				//Configure SMTP
				$mail->IsSMTP();
				$mail->SMTPDebug	=	$this->config['smtp_debug'];
				$mail->Host			=	$this->config['smtp_host'];
				$mail->Port			=	$this->config['smtp_port'];

				if($this->config['smtp_auth'] === true){
					$mail->SMTPAuth		=	true;
					$mail->Username		=	$this->config['smtp_username'];
					$mail->Password		=	$this->config['smtp_password'];
				}
				
			}
			else
			{
				//Set Sendmail
				$mail->IsSendMail();
			}

			//Configure Mail
			$mail->AddReplyTo($info['ReplyTo']);
			$mail->AddAddress($info['To']);
			$mail->SetFrom($info['From']);
			$mail->Subject = $info['Subject'];
			$mail->MsgHTML($info['Message']);

			if(!empty($info['AltMessage'])){
				$mail->AltBody($info['AltMessage']);
			}


			//Add Attachments
			if(
				isset($info['Attachments']) &&
				is_array($info['Attachments']) &&
				count($info['Attachments']) > 0
			){
				foreach($info['Attachments'] AS $attachment){
					$mail->AddAttachment($attachment);
				}
			}

			//Send
			$mail->Send();

		}
		catch (phpmailerException $e){
			if($this->config['debug'] == "true"){
				dev::output_r($e->errorMessage());
			}
		}
	}

}

?>
