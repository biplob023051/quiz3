<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Network\Exception\NotFoundException;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 */
class UsersController extends AppController
{
    public $paginate = [
        'limit' => 10,
        'order' => ['Users.id' => 'ASC']
    ];

    public function initialize()
    {
        parent::initialize();
        $this->Auth->allow(['access']);
    }

    // Method for user manangement panel
    public function index() {
        $this->set('title_for_layout', __('MANAGE_USER'));
        $conditions = [];
        $contain = [
            'Statistics' => function($q) {
                $q->select([
                    'Statistics.user_id',
                    'total_login' => $q->func()->count('Statistics.user_id'),
                ])
                ->where([
                    'Statistics.type' => 'user_login',
                    'DATE(Statistics.created) >= (CURDATE() - INTERVAL 1 MONTH )'
                ])
                ->group(['Statistics.user_id']);
                return $q;
            },
            'Quizzes' => function($q) {
                $q->select([
                    'Quizzes.user_id',
                    'total_quiz' => $q->func()->count('Quizzes.user_id')
                ])
                ->group(['Quizzes.user_id']);
                return $q;
            },
            'UserStatistics' => function($q) {
                $q->select([
                    'UserStatistics.created',
                    'UserStatistics.user_id'
                ])
                ->where(['UserStatistics.type' => 'user_login'])
                ->order(['UserStatistics.created' => 'DESC'])
                ->limit(10);
                return $q;
            }
        ];
        try {
            $users = $this->paginate(
                $this->Users->find('all')
                ->where($conditions)
                ->contain($contain)
            );
            $this->set(compact('users'));
        } catch (NotFoundException $e) { 
            return $this->redirect(array('controller' => 'users', 'action' => 'index'));
        }
    }

    // Method for updating user information
    public function ajaxUpdate() {
        $this->autoRender = false;
        $response['success'] = false;
        if (!empty($this->request->data['user_info'])) {
            $user_info = explode('-', $this->request->data['user_info']);
            if ((!in_array($user_info[0], ['account_level', 'isactive'])) && empty($this->request->data['value_info'])) {
                $response['message'] = __('VALUE_REQUIRED');
            } else {
                if (in_array($user_info[0], ['name', 'expired', 'account_level', 'isactive'])) {
                    $validate = true;
                    if ($user_info[0] == 'expired') {
                        $test_date = $this->request->data['value_info'];
                        $test_arr  = explode('-', $test_date);
                        if (count($test_arr) == 3) {
                            if (checkdate($test_arr[1], $test_arr[2], $test_arr[0])) {
                               
                            } else {
                                $validate = false;
                            }
                        } else {
                            $validate = false;
                        }
                    }
                    if ($user_info[0] == 'isactive' && empty($this->request->data['value_info'])) {
                        $this->request->data['value_info'] = NULL;
                    }
                    
                    if ($validate) {
                        $user = $this->Users->find()
                            ->where([
                                'id' => $user_info[1], 
                                'account_level !=' => 51
                            ])
                            ->select(['id'])
                            ->first();
                        if ($user) {
                            switch ($user_info[0]) {
                                case 'name':
                                    $user->name = $this->request->data['value_info'];
                                    break;
                                case 'account_level':
                                    $user->account_level = $this->request->data['value_info'];
                                    break;
                                case 'expired':
                                    $user->expired = $this->request->data['value_info'];
                                    break;
                                case 'isactive':
                                    $user->isactive = $this->request->data['value_info'];
                                    break;
                                default:
                                    # code...
                                    break;
                            }
                            if ($this->Users->save($user)) {
                                $response['success'] = true;
                                $response['message'] = __('USER_UPDATE_SUCCESS');
                            } else {
                                $response['message'] = __('SOMETHING_WENT_WRONG');
                            }
                        } else {
                            $response['message'] = __('SOMETHING_WENT_WRONG');
                        }
                    } else {
                        $response['message'] = __('INVALID_INPUT');
                    }
                }
            }
        }
        echo json_encode($response);
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
            $this->Flash->error(__('USERNAME_OR_PASSWORD_INCORRECT'));
        }

    }
}
