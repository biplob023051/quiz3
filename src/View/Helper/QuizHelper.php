<?php
namespace App\View\Helper;
use Cake\View\Helper;
use Cake\ORM\TableRegistry;

class QuizHelper extends Helper {

	public $helpers = array('Time', 'Text');

	public function getHelpPicture($obj, $type, $thumb = false) {
		$prefix = '';
		if ($thumb)
			$prefix = 't_';

		if (!empty($obj['photo'])) {
			return $this->request->webroot . 'uploads/' . $type . '/' . $prefix . $obj['Help']['photo'];
		} else {
			return $this->request->webroot . 'img/' . $prefix . 'no-image-' . $type . '.png';
		}
	}

	public function getImageUtubeChoice($question_id) {
		App::import('Model', 'Choice');
        $choice = new Choice();
        $result = $choice->findByQuestionId($question_id, array('Choice.text'));
        return empty($result) ? '' : $result['Choice']['text'];
	}

	// Function to check if there has points or not in choice array
	// if point, return true otherwise return false
	public function checkPoint($choices = array()) {
		$points = false;
		foreach ($choices as $key => $choice) {
			if ($choice['points'] != '0.00') {
				$points = true;
				break;
			}
		}
		return $points;
	}

	// Quiz bank download
	public function downloadCount() {
		$account_level = $this->request->session()->read('account_level');
		// if ($account_level != 22) {
		// 	return false;
		// }
		$user_id = $this->request->session()->read('id');

		$imported_quizzes = TableRegistry::get('Downloads');
        $download = $imported_quizzes->find()->where(['Downloads.user_id' => $user_id])->count();
        if ($download >= DOWNLOAD_LIMIT) {
        	return true;
        } else {
        	return false;
        }
	}

}
