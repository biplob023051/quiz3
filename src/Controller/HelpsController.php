<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Helps Controller
 *
 * @property \App\Model\Table\HelpsTable $Helps
 */
class HelpsController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Users', 'ParentHelps']
        ];
        $helps = $this->paginate($this->Helps);

        $this->set(compact('helps'));
        $this->set('_serialize', ['helps']);
    }

    /**
     * View method
     *
     * @param string|null $id Help id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $help = $this->Helps->get($id, [
            'contain' => ['Users', 'ParentHelps', 'ChildHelps']
        ]);

        $this->set('help', $help);
        $this->set('_serialize', ['help']);
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $help = $this->Helps->newEntity();
        if ($this->request->is('post')) {
            $help = $this->Helps->patchEntity($help, $this->request->data);
            if ($this->Helps->save($help)) {
                $this->Flash->success(__('The help has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The help could not be saved. Please, try again.'));
            }
        }
        $users = $this->Helps->Users->find('list', ['limit' => 200]);
        $parentHelps = $this->Helps->ParentHelps->find('list', ['limit' => 200]);
        $this->set(compact('help', 'users', 'parentHelps'));
        $this->set('_serialize', ['help']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Help id.
     * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $help = $this->Helps->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $help = $this->Helps->patchEntity($help, $this->request->data);
            if ($this->Helps->save($help)) {
                $this->Flash->success(__('The help has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The help could not be saved. Please, try again.'));
            }
        }
        $users = $this->Helps->Users->find('list', ['limit' => 200]);
        $parentHelps = $this->Helps->ParentHelps->find('list', ['limit' => 200]);
        $this->set(compact('help', 'users', 'parentHelps'));
        $this->set('_serialize', ['help']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Help id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $help = $this->Helps->get($id);
        if ($this->Helps->delete($help)) {
            $this->Flash->success(__('The help has been deleted.'));
        } else {
            $this->Flash->error(__('The help could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
