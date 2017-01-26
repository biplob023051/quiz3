<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * QuestionTypes Controller
 *
 * @property \App\Model\Table\QuestionTypesTable $QuestionTypes
 */
class QuestionTypesController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
        $questionTypes = $this->paginate($this->QuestionTypes);

        $this->set(compact('questionTypes'));
        $this->set('_serialize', ['questionTypes']);
    }

    /**
     * View method
     *
     * @param string|null $id Question Type id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $questionType = $this->QuestionTypes->get($id, [
            'contain' => ['Questions']
        ]);

        $this->set('questionType', $questionType);
        $this->set('_serialize', ['questionType']);
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $questionType = $this->QuestionTypes->newEntity();
        if ($this->request->is('post')) {
            $questionType = $this->QuestionTypes->patchEntity($questionType, $this->request->data);
            if ($this->QuestionTypes->save($questionType)) {
                $this->Flash->success(__('The question type has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The question type could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('questionType'));
        $this->set('_serialize', ['questionType']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Question Type id.
     * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $questionType = $this->QuestionTypes->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $questionType = $this->QuestionTypes->patchEntity($questionType, $this->request->data);
            if ($this->QuestionTypes->save($questionType)) {
                $this->Flash->success(__('The question type has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The question type could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('questionType'));
        $this->set('_serialize', ['questionType']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Question Type id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $questionType = $this->QuestionTypes->get($id);
        if ($this->QuestionTypes->delete($questionType)) {
            $this->Flash->success(__('The question type has been deleted.'));
        } else {
            $this->Flash->error(__('The question type could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
