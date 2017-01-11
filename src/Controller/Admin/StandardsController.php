<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Network\Exception\NotFoundException;

class StandardsController extends AppController
{
	public $paginate = [
        'limit' => 3
    ];

    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Paginator');
    }

	// Method for displaying all standards
	public function index() {
		$this->isAdminUser();
        $this->set('title_for_layout', __('All Classes'));
		$conditions = array(
			'Standards.is_del IS NULL',
			'Standards.type' => 1 
		);

		try {
           $standards = $this->paginate($this->Standards->find('all')->where($conditions));
	    	$this->set(compact('standards'));
        } catch (NotFoundException $e) { 
            return $this->redirect(array('controller' => 'standards', 'action' => 'index'));
        }
	}

	// Method for active deactive standards
	public function active($id, $active=NULL) {
		$this->isAdminUser();
		$standard = $this->Standards->get($id, [
            'contain' => []
        ]);

		if (!empty($standard)){
			$standard->isactive = $active;
			$this->Standards->save($standard);
			$message = empty($active) ? __('You have successfully deactivated!') : __('You have successfully activated');
			$this->Flash->success($message);
		} else {
			$this->Flash->error(__('Can not save'));
		}			
		
		if(isset($this->request->query['redirect_url'])){	
			return $this->redirect(urldecode($this->request->query['redirect_url']));
		} else {
			return $this->redirect(array('controller' => 'standards', 'action' => 'index'));
		}
	}

	/**
	* method of standard soft delete from admin
	*/
	public function delete($id) {
		$this->isAdminUser();
		$standard = $this->Standards->get($id, [
            'contain' => []
        ]);
		
		if (!empty($standard)){
			$standard->is_del = 1;
			$this->Standards->save($standard);
			$this->Flash->success(__('You have successfully deleted.'));
		} else {
			$this->Flash->error(__('Can not delete'));
		}

		if(isset($this->request->query['redirect_url'])){	
			return $this->redirect(urldecode($this->request->query['redirect_url']));
		} else {
			return $this->redirect(array('controller' => 'standards', 'action' => 'index'));
		}
		
	}

	/*
	* Method for standard create / edit
	*/
	public function insert($id = null) {
		$this->isAdminUser();
		if(empty($id)){
			$this->set('title_for_layout',__('New Class'));
			$standard = $this->Standards->newEntity();
		} else {
			$this->set('title_for_layout',__('Edit Class'));
			$standard = $this->Standards->get($id, [
	            'contain' => []
	        ]);
		}
		
		if ($this->request->is(array('post','put'))) {
			$this->request->data['type'] = 1;
			$standard = $this->Standards->patchEntity($standard, $this->request->data);
			if ($this->Standards->save($standard)) {
				$this->Flash->success(__('Class saved successfully'));
				if(isset($this->request->query['redirect_url'])){			
					return $this->redirect(urldecode($this->request->query['redirect_url']));
				} else {
					return $this->redirect(['controller' => 'standards', 'action' => 'index']);
				}
			} else {
				$this->Flash->error(__('Class saved failed'));
			}
		} 
		$this->set(compact('standard'));
        $this->set('_serialize', ['standard']);

	}
}
