<?php
namespace App\Controller;

use App\Controller\AppController;

class InvoiceController extends AppController
{
	public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Email');
    }

	public function create() 
	{
		$this->loadModel('Users');
        $user = $this->Users->get($this->Auth->user('id'), ['contain' => []]);
        // increate user account expired time
        $this->request->data['expired'] = date('Y-m-d H:i:s', mktime(0, 0, 0, date('m'), date('d'), date('Y') + 1));
        if ($this->request->data['package'] == 29) {
            $user->package =  __('29 E/Y');
            $this->request->data['account_level'] = 1;
        } else {
            $user->package = __('49 E/Y');
            $this->request->data['account_level'] = 2;
        }
        unset($this->request->data['package']);
       	$user = $this->Users->patchEntity($user, $this->request->data);
       	unset($user->password);
        if ($this->Users->save($user)) {
        	$email_success = $this->Email->sendMail('test@test.com', __('Upgrade Account'), $user, 'invoice');
        	echo json_encode(array('success' => true));
        } else {
        	echo json_encode(array('success' => false));
        }
        exit;
    }
}