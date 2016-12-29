<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Rankings Controller
 *
 * @property \App\Model\Table\RankingsTable $Rankings
 */
class RankingsController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Quizzes', 'Students']
        ];
        $rankings = $this->paginate($this->Rankings);

        $this->set(compact('rankings'));
        $this->set('_serialize', ['rankings']);
    }

    /**
     * View method
     *
     * @param string|null $id Ranking id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $ranking = $this->Rankings->get($id, [
            'contain' => ['Quizzes', 'Students']
        ]);

        $this->set('ranking', $ranking);
        $this->set('_serialize', ['ranking']);
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $ranking = $this->Rankings->newEntity();
        if ($this->request->is('post')) {
            $ranking = $this->Rankings->patchEntity($ranking, $this->request->data);
            if ($this->Rankings->save($ranking)) {
                $this->Flash->success(__('The ranking has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The ranking could not be saved. Please, try again.'));
            }
        }
        $quizzes = $this->Rankings->Quizzes->find('list', ['limit' => 200]);
        $students = $this->Rankings->Students->find('list', ['limit' => 200]);
        $this->set(compact('ranking', 'quizzes', 'students'));
        $this->set('_serialize', ['ranking']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Ranking id.
     * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $ranking = $this->Rankings->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $ranking = $this->Rankings->patchEntity($ranking, $this->request->data);
            if ($this->Rankings->save($ranking)) {
                $this->Flash->success(__('The ranking has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The ranking could not be saved. Please, try again.'));
            }
        }
        $quizzes = $this->Rankings->Quizzes->find('list', ['limit' => 200]);
        $students = $this->Rankings->Students->find('list', ['limit' => 200]);
        $this->set(compact('ranking', 'quizzes', 'students'));
        $this->set('_serialize', ['ranking']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Ranking id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $ranking = $this->Rankings->get($id);
        if ($this->Rankings->delete($ranking)) {
            $this->Flash->success(__('The ranking has been deleted.'));
        } else {
            $this->Flash->error(__('The ranking could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
