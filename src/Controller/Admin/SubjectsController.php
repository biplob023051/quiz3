<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Network\Exception\NotFoundException;

class SubjectsController extends AppController
{
	public $paginate = [
        'limit' => 3
    ];

    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Paginator');
    }

	// Method for displaying all subjects
	public function index() {
		$this->isAdminUser();
        $this->set('title_for_layout', __('ALL_SUBJECT'));
		$conditions = array(
			'Subjects.is_del IS NULL',
			'Subjects.type IS NULL' 
		);

		try {
           $subjects = $this->paginate($this->Subjects->find('all')->where($conditions));
	    	$this->set(compact('subjects'));
        } catch (NotFoundException $e) { 
            return $this->redirect(array('controller' => 'subjects', 'action' => 'index'));
        }
	}

	// Method for active deactive subjects
	public function active($id, $active=NULL) {
		$this->isAdminUser();
		$subject = $this->Subjects->get($id, [
            'contain' => []
        ]);

		if (!empty($subject)){
			$subject->isactive = $active;
			$this->Subjects->save($subject);
			$message = empty($active) ? __('You have successfully deactivated!') : __('You have successfully activated');
			$this->Flash->success($message);
		} else {
			$this->Flash->error(__('Can not save'));
		}			
		
		if(isset($this->request->query['redirect_url'])){	
			return $this->redirect(urldecode($this->request->query['redirect_url']));
		} else {
			return $this->redirect(array('controller' => 'subjects', 'action' => 'index'));
		}
	}

	/**
	* method of subject soft delete from admin
	*/
	public function delete($id) {
		$this->isAdminUser();
		$subject = $this->Subjects->get($id, [
            'contain' => []
        ]);
		
		if (!empty($subject)){
			$subject->is_del = 1;
			$this->Subjects->save($subject);
			$this->Flash->success(__('You have successfully deleted.'));
		} else {
			$this->Flash->error(__('CAN_NOT_DELETE'));
		}

		if(isset($this->request->query['redirect_url'])){	
			return $this->redirect(urldecode($this->request->query['redirect_url']));
		} else {
			return $this->redirect(array('controller' => 'subjects', 'action' => 'index'));
		}
		
	}

	/*
	* Method for subject create / edit
	*/
	public function insert($id = null) {
		$this->isAdminUser();
		if(empty($id)){
			$this->set('title_for_layout',__('NEW_SUBJECT'));
			$subject = $this->Subjects->newEntity();
		} else {
			$this->set('title_for_layout',__('Edit Subject'));
			$subject = $this->Subjects->get($id, [
	            'contain' => []
	        ]);
		}
		
		if ($this->request->is(array('post','put'))) {
			$subject = $this->Subjects->patchEntity($subject, $this->request->data);
			if ($this->Subjects->save($subject)) {
				$this->Flash->success(__('Subject saved successfully'));
				if(isset($this->request->query['redirect_url'])){			
					return $this->redirect(urldecode($this->request->query['redirect_url']));
				} else {
					return $this->redirect(['controller' => 'subjects', 'action' => 'index']);
				}
			} else {
				$this->Flash->error(__('SUBJECT_SAVE_FAILED'));
			}
		} 
		$this->set(compact('subject'));
        $this->set('_serialize', ['subject']);

	}
}
