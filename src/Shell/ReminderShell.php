<?php
namespace App\Shell;

use Cake\Console\Shell;
use Cake\Controller\ComponentRegistry;
use App\Controller\Component\EmailComponent;
use Cake\Core\Configure;

class ReminderShell extends Shell
{
	public function initialize() {
        $this->Email = new EmailComponent(new ComponentRegistry());
    }

    public function main()
    {
    	$this->loadModel('Users');
        $conditions = [
            'Users.activation IS NOT NULL',
            'DATE(Users.created)' => date('Y-m-d', strtotime("-1 days"))
        ];
        $users = $this->Users->find('all')->where($conditions)->toArray();
        if (!empty($users)) {
            foreach ($users as $key => $user) {
                $this->Email->sendMail(Configure::read('AdminEmail'), __('[Verkkotesti] User NOT confirmed'), $user, 'reminder_email', '', true);
            }
        }
        // List of temp files
        $now = strtotime("-20 minutes");
        foreach(glob(WWW_ROOT . 'uploads/tmp' . '/*.*') as $file) {
			$imageInfo = pathinfo($file);
			$time_info = explode('_', $imageInfo['basename']);
			if ($now > $time_info[0]) {
				unlink($file);
			}
		}
		
		if (!extension_loaded('imagick')){
		    echo 'imagick not installed';
		}

    }
}