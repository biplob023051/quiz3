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
	public function sendMail($to, $subject, $data, $template, $from = null) {
		if (empty($from)) {
			$from = array('pietu.halonen@verkkotesti.fi' => 'WebQuiz.fi');
		}
		$Email = new Email();
		$checkEmail = $Email->template($template)
			->emailFormat('html')
			->viewVars(array('data' => $data))
			->to($to)
			->subject($subject)
			->from($from)
			->send();  
		return $checkEmail;
	}

// 	$email = new Email();
// $email->template('welcome', 'fancy')
//     ->emailFormat('html')
//     ->to('bob@example.com')
//     ->from('app@domain.com')
//     ->send();
}