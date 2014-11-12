<?php

/*
	Question2Answer (c) Gideon Greenspan
	QA Invite Plugin (c) Amiya Sahu (developer.amiya@outlook.com)

	http://www.question2answer.org/

	
	File: qa-plugin/qa-invite/qa-invite-utils.php
	Version: See define()s at top of qa-include/qa-base.php

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.
	
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	More about this license: http://www.question2answer.org/license.php
*/
	
if (!function_exists('invite_email_send_email')) {
	function invite_email_send_email($params) {
	    require_once QA_INCLUDE_DIR . 'qa-class.phpmailer.php';
	    $mailer = new PHPMailer();
	    $mailer->CharSet = 'utf-8';
	    $mailer->From     = $params['fromemail'];
	    $mailer->Sender   = $params['fromemail'];
	    $mailer->FromName = $params['fromname'];
	    if (isset($params['toemail'])) {
	          $mailer->AddAddress($params['toemail'], $params['toname']);
	    }
	    $mailer->Subject = $params['subject'];
	    $mailer->Body = $params['body'];
	    if (isset($params['bcclist'])) {
	          foreach ($params['bcclist'] as $email) {
	                $mailer->AddBCC($email);
	          }
	    }
	    if ($params['html']) $mailer->IsHTML(true);
	    if (qa_opt('smtp_active')) {
	          $mailer->IsSMTP();
	          $mailer->Host = qa_opt('smtp_address');
	          $mailer->Port = qa_opt('smtp_port');
	          if (qa_opt('smtp_secure')) $mailer->SMTPSecure = qa_opt('smtp_secure');
	          if (qa_opt('smtp_authenticate')) {
	                $mailer->SMTPAuth = true;
	                $mailer->Username = qa_opt('smtp_username');
	                $mailer->Password = qa_opt('smtp_password');
	          }
	    }
	    return $mailer->Send();
	}
}

/*
	Omit PHP closing tag to help avoid accidental output
*/