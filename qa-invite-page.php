<?php

/*
	Question2Answer (c) Gideon Greenspan
	QA Invite Plugin (c) Amiya Sahu (developer.amiya@outlook.com)

	http://www.question2answer.org/

	
	File: qa-plugin/qa-invite/qa-invite-page.php
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

	class qa_invite_page {
		
		var $directory;
		var $urltoroot;
		

		function load_module($directory, $urltoroot)
		{
			$this->directory=$directory;
			$this->urltoroot=$urltoroot;
		}

		
		function suggest_requests() // for display in admin interface
		{	
			return array(
				array(
					'title' => qa_lang_html('invite/invite'),
					'request' => 'invite',
					'nav' => 'M', // 'M'=main, 'F'=footer, 'B'=before main, 'O'=opposite main, null=none
				),
			);
		}

		
		function match_request($request)
		{
			if ($request=='invite' && !!qa_is_logged_in())
				return true;

			return false;
		}
		
		function process_request($request)
		{
			$invitation_sent = false ; 
			$errors = false ; 

			$handle          = qa_get_logged_in_handle() ;
			$senders_data    = QA_FINAL_EXTERNAL_USERS ? null : qa_db_select_with_pending(qa_db_user_profile_selectspec($handle, false));
			$sender_name     = !empty($senders_data['name']) ? $senders_data['name'] : $handle ;

			$subs = array(
					'^site_title'  => qa_opt('site_title'),
					'^site_url'    => qa_opt('site_url'),
					'^sender_name' => $sender_name ,
					'^user_url'    => qa_opt('site_url').'user/'.$handle ,
					);

			if (qa_clicked('doinvite')) { 
				if (!qa_check_form_security_code('invite', qa_post_text('code'))){
					$errors['page']=qa_lang_html('misc/form_security_again');
				}
				else {
					// send the invite messages 
					$bcclist = array() ;

					$receipient_addr = qa_post_text('receipient_addr');
					if (!empty($receipient_addr)) {
						$email_arr = explode(',', $receipient_addr);
						foreach ($email_arr as $key => $email) {
							if (qa_email_validate(trim($email))) {
								$bcclist[] = trim($email);
							}
						}

						$message_post     = qa_post_text('message') ;
						$sender_name_post = qa_post_text('sender_name') ;
						$subject_post     = $sender_name_post .' - '.qa_post_text('subject') ;

						if (count($bcclist) && !empty($message_post) && !empty($sender_name_post) && !empty($subject_post) ) {
							$body = qa_lang('invite/greeting'). $message_post . qa_lang('invite/thanks') ;
							$body = strtr($body, $subs ) ;
							$body = nl2br($body) ;
							$params = array(
								'fromemail' => qa_opt('from_email'),
	                        	'fromname'  => qa_opt('site_title') ,
	                        	'toemail'   => null ,
		                        'toname'    => null ,
		                        'bcclist'   => $bcclist,
		                        'subject'   => $subject_post ,
		                        'body'      => $body ,
		                        'html'      => true,
	                        );

	                        invite_email_send_email($params);
	                        $invitation_sent = true ; 
						}else {
							$errors = true ;
						}
					}else {
							$errors = true ;
					}
				}
			}

			/*Prepare content for theme */
			$qa_content           = qa_content_prepare();
			$qa_content['title']  = qa_lang_html('invite/page_title');
			$message              = qa_lang_html('invite/message');
			$subject              = strtr(qa_lang_html('invite/subject') , $subs );
			
			$receipient_addr_post = qa_post_text('receipient_addr');
			$sender_name_post     = qa_post_text('sender_name');
			$subject_post         = qa_post_text('subject');
			$message_post         = qa_post_text('message');

			$qa_content['form']=array(
				'tags'  => 'method="post" action="'.qa_self_html().'"',
				
				'style' => 'tall',
				
				'ok'    => $invitation_sent ?  qa_lang_html('invite/invite_succ') : null,
				
				'title' => qa_lang_html('invite/invite'),
				
				'fields' => array(
						'sender_name' => array(
						'label'       => qa_lang_html('invite/sender_name_lable'),
						'tags'        => 'name="sender_name" placeholder="'.qa_lang_html('invite/sender_name_plc').'"',
						'value'       => $sender_name,
						'error'       => $errors && empty($sender_name_post) ? qa_lang_html('invite/required_field') : null ,
					),
					'receipient_addr' => array(
						'label' => qa_lang_html('invite/receipient_addr_lable'),
						'tags'  => 'name="receipient_addr" placeholder="'.qa_lang_html('invite/receipient_addr_plc').'"',
						'type'  => 'textarea' , 
						'rows'  => 3,
						'note'  => qa_lang_html('invite/receipient_addr_note'),
						'error' => $errors && empty($receipient_addr) ? qa_lang_html('invite/required_field') : null ,
					),
					'subject' => array(
						'label' => qa_lang_html('invite/subject_lable'),
						'tags'  => 'name="subject" placeholder="'.qa_lang_html('invite/subject_plc').'"',
						'value' => $subject ,
						'error' => $errors && empty($subject_post) ? qa_lang_html('invite/required_field') : null ,
					),
					'message' => array(
						'label' => qa_lang_html('invite/message'),
						'tags'  => 'name="message" placeholder="'.qa_lang_html('invite/message_plc').'"',
						'type'  => 'textarea' , 
						'value' => $message , 
						'rows'  => 10 ,
						'error' => $errors && empty($message_post) ? qa_lang_html('invite/required_field') : null ,
					),
				),
				
				'buttons' => array(
					'ok' => array(
						'tags'  => 'name="send_invites" onclick="qa_show_waiting_after(this, false);"',
						'label' => qa_lang_html('invite/send_invites'),
					),
				),
				
				'hidden' => array(
					'code'     => qa_get_form_security_code('invite'),
					'doinvite' => '1',
				),
			);

			return $qa_content;
		}
	
	}
	
/*
	Omit PHP closing tag to help avoid accidental output
*/