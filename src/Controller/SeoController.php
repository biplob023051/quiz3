<?php
namespace App\Controller;

use App\Controller\AppController;

use Cake\Core\Configure;

class SeoController extends AppController  
{  
	public function initialize()
    {
        parent::initialize();

        //$this->loadComponent('RequestHandler');
        $this->Auth->allow(['robots']);
    }


    public function robots()  
    {  
        $this->RequestHandler->respondAs('text');
        $this->viewBuilder()->layout('ajax');
    }  
}