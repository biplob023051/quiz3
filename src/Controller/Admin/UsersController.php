<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 */
class UsersController extends AppController
{

    public function initialize()
    {
        parent::initialize();
        $this->Auth->allow(['access']);
    }

    public function access() {
        // pr($this->request->data);
        // exit;
        if ($this->request->is('post')) {
            $user = $this->Auth->identify();
            if ($user) {
                if ($user['account_level'] != 51) {
                   $this->Auth->logout();
                   $this->Flash->error(__('Unauthorized access to this login area!'));
                    return $this->redirect(array('controller' => 'maintenance', 'action' => 'notice', 'prefix' => false));
                }
                $this->Auth->setUser($user);
                //save statistics data
                $statisticsTable = TableRegistry::get('Statistics');
                $statistic = $statisticsTable->newEntity();
                $statistic->user_id = $this->Auth->user('id');
                $statistic->type = 'user_login';
                $statistic->created = date("Y-m-d H:i:s");
                $statisticsTable->save($statistic);
                return $this->redirect(['controller' => 'maintenance', 'action' => 'settings', 'prefix' => 'admin']);
            }
            $this->Flash->error('Your username or password is incorrect.');
        }

    }
}
