<?php
namespace Eclipse;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mail {
	function __construct($host, $username, $password, $port) {
		$this->host = $host;
		$this->username = $username;
		$this->password = $password;
		$this->port = $port;
	}

	public function setSender($from_email, $from_name) {
		$this->from_email = $from_email;
		$this->from_name = $from_name;
	}

	public function send() {
		$mail = new PHPMailer(true);
		$mail->isSMTP();
		$mail->Host = $this->host;  //gmail SMTP server
		$mail->SMTPAuth = true;
		//to view proper logging details for success and error messages
		// $mail->SMTPDebug = 1;
		$mail->Username = $this->username;   //email
		$mail->Password = $this->password ;   //16 character obtained from app password created
		$mail->Port = $this->port;                    //SMTP port
		$mail->SMTPSecure = "ssl";
		//sender information
		$mail->setFrom($this->from_email, $this->from_name);

		//receiver email address and name
		$mail->addAddress($this->receiver_email, $this->receiver_name); 

		// Add cc or bcc   
		// $mail->addCC('email@mail.com');  
		// $mail->addBCC('user@mail.com');  
		$mail->isHTML(true);
		$mail->Subject = $this->subject;
		$mail->Body    = $this->body;
		// Send mail   
		if (!$mail->send()) {
			$message = 'Email not sent an error was encountered: ' . $mail->ErrorInfo;
			$success = false;
		} else {
			$message = 'Message has been sent.';
			$success = true;
		}
		$mail->smtpClose();
		return ['success' => $success, 'message' => $message];
	}

	public static function orderConfirm($site) {
		$mail = new Mail(
			$site->mail_host, 
			$site->mail_username, 
			$site->mail_password, 
			$site->mail_port
		);
		$mail->setSender($site->mail_from_address, $site->mail_from_name);

		$mail->receiver_email = 'hqnhatdn@gmail.com';
		$mail->receiver_name = 'Nhat huynh';
		$mail->subject = 'Thank You for Shopping with Stafaz Store - Order #573643 - Total Amount: $125.00';
		$var = '$124';
		$mail->body = get_ob(function () use ($var) {
			_view('mail/order-confirmation', compact('var'));
		});
		return $mail->send();
	}
}