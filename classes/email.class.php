<?php
/**
 * D2PW Solutions
 *
 * Description: Envio de correos por medio de funciones como PHPMAILER y JANGO(Web Services)
 *
 * @author Javier Mijares y Miguel Aguero
 * @version 1.0  2010/10/21
 */

require_once("externals/phpmailer/class.phpmailer.php");

class Email extends Model {
    /******************************************************************
    /*******  VARIABLES
    /******************************************************************/
    var $table = 'emails';
    var $config = array();
    var $debugInfo = array('debug'=>EMAIL_DEBUG_SEND,'emailDebug'=>EMAIL_DEBUG); //0: send emails, 1: send Email only to Debug Email, 2:Off 
    var $variables = array();
    /******************************************************************
    /*******  CONSTRUCTOR
    /******************************************************************/
    function __construct($config = array('smtpServer'=>EMAIL_SERVER_HOST, 'smtpUser'=>EMAIL_SERVER_USER, 'smtpPassword'=>EMAIL_SERVER_PASS, 'appDomainRoot'=>DOMAIN_ROOT, 'skeletonFile'=>'', 'emailEngine'=>EMAIL_ENGINE, 'transGroupID'=>EMAIL_TRANSACTIONAL_GROUP_ID), $debugInfo=array('debug'=>EMAIL_DEBUG_SEND, 'emailDebug'=>EMAIL_DEBUG)){
       
        foreach($config as $key=>$value){
            $this->config[$key] = $value;
        }
        $this->config['skeletonFile'] = (!$this->config['skeletonFile'])?  COREROOT.'lib/common/email_skeleton.php' : $this->config['skeletonFile'];  
        $this->debugInfo['debug'] = $debugInfo['debug'];
        $this->debugInfo['emailDebug'] = $debugInfo['emailDebug'];
         //var_dump($this->debugInfo);
    }
    /******************************************************************
    /*******  FUNCIONES
    /******************************************************************/
    /**
     * Asigna un valor al array llamado variables
     * @param $name
     * @param $value
     */
    function setVariable($name, $value) {
        $this->variables[$name] = $value;
    }
    /**
     * Limpia el arraya llamado variables
     */
    function clearVariables(){
        $this->variables = array();
    }
    /**
     * funcion para manejar los parametros del servidor y el envio
     * @param  $from
     * @param  $to
     * @param  $emailTemplateCode
     * @param  $languageId
     * @param  $attachment
     */
    function send($from=array('name'=>'John', 'email'=>'jdoe@sample.com'), $to=array(), $emailTemplateCode, $languageId, $attachment = null, $titulo = '') {
        $resultEmail = array();
        //$to=array('vladuma2@gmail.com');
        $subject1="";
        
        $message=array('html'=>'','text'=>'');
        
        $template = new EmailTemplate();
        
        $skeleton ="";
        if($this->config['skeletonFile']!=null){
            $skeleton = $template->loadTemplateSkeleton($this->config['skeletonFile'],$this->config['appDomainRoot']);
        }
        $templateData =  $template->find(array('code'=>$emailTemplateCode,'language_id'=>$languageId));
        
        if($templateData){
            if($skeleton!='')
                $contentHtml = str_replace('%body_html%',$templateData[0]['body_html'],$skeleton);
            else
                $contentHtml = $templateData[0]['body_html'];
            
                
            //$subject = mb_detect_encoding($templateData[0]['subject']);
            if(!empty($titulo)){
                $subject = $titulo;
            }else{
                $subject1 = $templateData[0]['subject'];
                $subject = utf8_decode($subject1);
            }
            
            $message=array('html'=>$contentHtml,'text'=>$templateData[0]['body_text']);
        }
        
        if($this->debugInfo['debug']!=0)
            $subject1="" . $subject;

        $this->variables['#server_path#'] = DOMAIN_ROOT;
        $this->variables['#system_name#'] = SYSTEM_NAME;
        $this->variables['#email_from#'] = EMAIL_FROM;
        $this->variables['#email_from_name#'] = EMAIL_FROM_NAME;
        $message = str_ireplace("src=\"images","src=\"".DOMAIN_ROOT."images",$message);
        $message = str_ireplace("background=\"images","background=\"".DOMAIN_ROOT."images",$message);
        foreach($this->variables as $name=>$value){
            $message = str_replace($name,stripslashes($value),$message);
            $subject = str_replace($name,stripslashes($value),$subject);
        }
//        print_r($to);
//       die();
        switch(strtolower($this->config['emailEngine'])){
            case 'jango':
                $this->sendJango($from, $to, $subject, $message, $templateData, $attachment);
                break;
            default:
                $this->sendLocal($from, $to, $subject, $message, $templateData, $attachment);
                break;
        }
    }
    /**
     * Funcion para realizar el envio de los correos con rutinas locales
     * @param $from
     * @param $to
     * @param $subject
     * @param $message
     * @param $templateData
     * @param $attachment
     */
    function sendLocal($from, $to, $subject, $message, $templateData, $attachment){
        //documentation: http://phpmailer.worxware.com/index.php?pg=methods
        $mail = new PHPMailer();
        $mail->IsSMTP();

        $mail->From     = $from['email'];
        $mail->FromName = $from['name'];
        $mail->Host     = $this->config['smtpServer'];

        if($this->config['useSmtp']){
                $mail->SMTPAuth = true;     						// turn on SMTP authentication
                $mail->Username = $this->config['smtpUser'];  		// SMTP username
                $mail->Password = $this->config['smtpPassword']; 	// SMTP password
        }
        //if($to_send_CC){ $mail->AddCC($to_send_CC); }
        //if($to_send_BCC){ $mail->AddBCC($to_send_BCC);  }

        $mail->WordWrap = 80;	// set word wrap
        $mail->IsHTML(true);	// send as HTML

        switch($this->debugInfo['debug']){
            case 0: //send email to recipients
                foreach($to as $recipient)
                    $mail->AddAddress($recipient);
                break;
            case 1: //send email to debug account
                $emailList = split(",",$this->debugInfo['emailDebug']);
                if(count($emailList)>0){
                    foreach($emailList as $mailD){
                        $mail->AddAddress($mailD);
                    }
                }else{
                    $mail->AddAddress($this->debugInfo['emailDebug']);
                }
                break;
        }

        //$mail->AddAttachment($attach,$attach_name);
        $mail->Subject  =  $subject;
        $mail->Body     =  $message['html'];
        $mail->AltBody  =  $message['text'];
        if(!empty($attachment))	$mail->AddAttachment($attachment);
        $status = $mail->Send();
        $errorInfo =$mail->ErrorInfo;
        // if(!$status)
        //    echo $errorInfo;

        $resultEmail['email_sender'] = $from['email'];
        $resultEmail['email_recipient'] = implode(',',$to);
        $resultEmail['email_template_id'] = ($templateData)? $templateData[0]['id']: 0;
        $resultEmail['status'] = intVal($status);
        $resultEmail['status_message'] = $errorInfo;
		$resultEmail['message'] = "mesaje";

        $this->save($resultEmail);
        $this->id =null;
        $this->clearVariables();

        $mail->ClearAllRecipients();
        $mail->ClearAttachments();
    }
    /**
     * Funcion para realizar el envio de los correos mediante el web services de JANGO SMTP
     * @param $from
     * @param $to
     * @param $subject
     * @param $message
     * @param $templateData
     * @param $attachment
     */
    function sendJango($from, $to, $subject, $message, $templateData, $attachment){
	$client = new SoapClient('http://api.jangomail.com/api.asmx?WSDL');
        //var_dump($this->debugInfo['debug']);
	switch($this->debugInfo['debug']){ 
            case 0: //send email to recipients
               foreach($to as $recipient){
                        $this->sendTransactionalEmail($client, $from, $recipient, $subject, $message, $templateData, $attachment);
                }
                break;
            case 1: //send email to debug account
                $emailList = preg_split(",",$this->debugInfo['emailDebug']);
                if(count($emailList)>0){
                    foreach($emailList as $mailD){
                            $this->sendTransactionalEmail($client, $from, $mailD, $subject, $message, $templateData, $attachment);
                    }
                } else {
                    $this->sendTransactionalEmail($client, $from, $this->debugInfo['emailDebug'], $subject, $message, $templateData, $attachment);
                }
                break;
        }
    }
    /**
     * Realiza el envio final del correo por el web services y guarda en la tabla emails el numero de la transaccion
     * @param $client
     * @param $from
     * @param $recipient
     * @param $subject
     * @param $message
     * @param $templateData
     * @param $attachment
     */
    function sendTransactionalEmail($client, $from, $recipient, $subject, $message, $templateData, $attachment){
        //var_dump($client);
        $options = "OpenTrack=True,ClickTrack=True,UseSystemMAILFROM=False,TransactionalGroupID=".$this->config['transGroupID'];
        $arrOptions = array('Username'          => $this->config['smtpUser'],
                            'Password'       	=> $this->config['smtpPassword'],
                            'FromEmail'       	=> $from['email'],
                            'FromName'       	=> $from['name'],
                            'ToEmailAddress'    => $recipient,
                            'Subject'       	=> utf8_encode($subject),
                            'MessagePlain'      => utf8_encode($message['text']),
                            'MessageHTML'       => $message['html'],
                            'Options'       	=> $options);
        try{
                        $result = $client->SendTransactionalEmail($arrOptions);
                        $arrResult = preg_split( '/\r\n|\r|\n/',$result->SendTransactionalEmailResult);
                }catch (Exception $e){
                        $arrResult[0]=1;
                        $arrResult[1]= base64_encode($e->getMessage());
                        $arrResult[2]=0;
                }
        $resultEmail['email_sender'] = $from['email'];
        $resultEmail['email_recipient'] = $recipient;
        $resultEmail['email_template_id'] = ($templateData)? $templateData[0]['id']: 0;
        $resultEmail['status'] = intVal($arrResult[0] == 0 ? 1 : 0);
        $resultEmail['status_message'] = $arrResult[1];
        $resultEmail['transactional_id'] = $arrResult[2];
	//	$resultEmail['contenido'] =  serialize($message);		
		$resultEmail['message'] =  addslashes($message['html']);
        $this->save($resultEmail);
        $this->id =null;
        $this->clearVariables();
    }
}
?>
