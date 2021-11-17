<?php
	header("Content-type: application/json");
	require_once("phpmailer/class.phpmailer.php");
	require_once("helper.class.php");

	/*
		Uso de recaptcha en php y js
		http://www.codexworld.com/new-google-recaptcha-with-php/
	*/

	$userName = trim(stripslashes($_POST["name"]));
    $userEmail = trim(stripslashes($_POST["email"]));    
    $userPhone = trim(stripslashes($_POST["phone"]));    
    $userHour = trim(stripslashes($_POST["hour"]));
	$gRecaptchaResponse = trim(stripslashes($_POST["recaptcha"]));

	/*
		$inputs = array(
							"nombre" => $userName, "correo" => $userEmail,  "telefono" => $userPhone,
							"hour" => $userHour, "reCaptcha" => $gRecaptchaResponse
						);
								
		foreach ($inputs as $name=>$value){
			if(!isset($value) || empty($value)){
				echo(json_encode(array("type" => "fail", "message" => "El campo $name es obligatorio.")));
					die;
			}
		}
	*/
	
	if(Helper::GoogleRecaptchaValid($gRecaptchaResponse) == FALSE){
		echo(json_encode(array("type" => "fail", "message" => "El captcha es incorrecto, intente nuevamente.")));
		die;
	}
	
	
	
	$phpMailer = new PHPMailer(true);
	$phpMailer->IsSMTP();
	$phpMailer->Host = Helper::$SMTP_HOST;
	$phpMailer->SMTPDebug = 0;
	$phpMailer->SMTPAuth = !Helper::$SMTP_ISLOCAL;
	$phpMailer->Port = Helper::$PORT;
	$phpMailer->SMTPSecure = 'tls';
	$phpMailer->Priority = 1;
	$phpMailer->Username = Helper::$SMTP_USERNAME;
	$phpMailer->CharSet = "UTF-8";
	$phpMailer->Password = Helper::$SMTP_PASSWORD;
	$phpMailer->AddReplyTo($userEmail, $userName);
	$phpMailer->SetFrom(Helper::$SMTP_FROM, $userName.' ('.$userEmail.')');
	$phpMailer->AddAddress(Helper::$MAIL_TO, "Fundacion Aiken");
	$phpMailer->AddBCC(Helper::$MAIL_BCC, "Fundacion Aiken");    
	$phpMailer->Subject = 'Mensaje desde fundacionaiken.org.ar/ma-padrinazgo';
	$phpMailer->MsgHTML("El siguiente mensaje fue enviado desde el formulario de contacto <b>Sé padrino o madrina de niños y niñas en duelo 
						 <a href='https://www.fundacionaiken.org.ar/ma-padrinazgo'>fundacionaiken.org.ar/ma-padrinazgo</a>.</b>
                         <br/><br/>
						 <b>Nombre y Apellido: </b>$userName (<i>$userEmail</i>)
						 <br/>
						 <b>Teléfono: </b>$userPhone
						 <br/>
						 <b>Horario: </b>$userHour
						 <br/><br/>
                         <b>Al responder este correo se respondera a $userEmail</b><br/>");
		
	
	try {
		$sent = $phpMailer->Send();
		$try = 1;
		while (!$sent && $try < 5) {
				sleep(3);
				$sent = $phpMailer->Send();
				$try = $try + 1;
		}
	} catch (Exception $e) {
		echo(json_encode(array("type" => "fail", "message" => $e->getMessage())));
		die;
	}

    if($sent){    
        $phpMailer->ClearAddresses();
        $phpMailer->ClearBCCs();            
        $phpMailer->ClearAttachments();      
        $phpMailer->ClearReplyTos();
        $phpMailer->AddReplyTo(Helper::$SMTP_FROM, 'Fundacion Aiken');
        $phpMailer->SetFrom(Helper::$SMTP_FROM, 'Fundacion Aiken');  
        $phpMailer->AddAddress($userEmail, $userName);  
        $phpMailer->Subject = 'Sus datos fueron enviados correctamente.';                                 
		$phpMailer->MsgHTML('<b>'.$userName.'</b> recibimos tus datos correctamente.
							 <br/><br/>
							 Muchas gracias por visitar nuestro sitio web.
							 <br/><br/>
							 No responda a este mensaje, es un envío automático.
							 <br><b>Fundación Aiken</b>.');

        try {
			$sent = $phpMailer->Send();                                             
			$try = 1; 
			while (!$sent && $try < 5) {
				sleep(3);         
				$sent = $phpMailer->Send();
				$try = $try + 1;    
			}
		} catch (Exception $e) {
			echo(json_encode(array("type" => "fail", "message" => $e->getMessage())));
			die;
		}
    }
    
	if ($sent) {
		 echo(json_encode(array("type" => "success")));
	}else{
		 echo(json_encode(array("type" => "fail", "message" => "Ocurrio un error al intentar enviar el mensaje, intente nuevamente.")));
		// echo(json_encode(array("type" => "fail", "message" => $phpMailer->ErrorInfo)));
	}
die;
