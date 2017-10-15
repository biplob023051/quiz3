<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\Mailer\Email;

class EmailComponent extends Component {
    /**
	 * Send a data to a user 
	 * @param string $email
	 * @param string $name
	 * @param string $data
	 */
	public function sendMail($to, $subject, $data, $template, $from = null, $bcc = '', $attachments = []) {
		if (empty($from)) {
			$from = array('pietu.halonen@verkkotesti.fi' => 'WebQuiz.fi');
		}
		$Email = new Email();
		$Email->template($template)
			->emailFormat('html')
			->viewVars(array('data' => $data))
			->to($to)
			->subject($subject)
			->from($from);
		if (!empty($bcc)) $Email->bcc('jorkka.bubbero@gmail.com');
		if (!empty($attachments)) {
			$Email->attachments($attachments);
		}
		$checkEmail = $Email->send();
		return $checkEmail;
	}
}