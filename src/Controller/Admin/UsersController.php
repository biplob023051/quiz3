<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Network\Exception\NotFoundException;
use Cake\Core\Configure;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 */
class UsersController extends AppController
{
    public $paginate = [
        'limit' => 50,
        'maxLimit' => 1000,
        'order' => ['Users.id' => 'ASC'],
        // 'sortWhitelist' => [
        //     'id',
        //     'name',
        //     'email',
        //     'account_level',
        //     'created',
        //     'expired',
        //     'isactive',
        //     'UserStatistic.created'
        // ]
    ];

    public function initialize()
    {
        parent::initialize();
        $this->Auth->allow(['access', 'login']);
    }

    public function login()
    {
        $this->redirect(['action' => 'access']);
    }

    // Method for user manangement panel
    public function index() {
        $this->isAdminUser();
        $this->set('title_for_layout', __('MANAGE_USER'));
        $acc_type = 'all';
        if ($this->request->is(['post','put'])) { // If form submitted
            if (!empty($this->request->data['limit_size'])) {
                $this->Session->write('limit_size', $this->request->data['limit_size']);
            }
            if (!empty($this->request->data['acc_type'])) {
                $this->Session->write('acc_type', $this->request->data['acc_type']);
            }
            return $this->redirect(['action' => 'index']);
        } 
        if ($this->Session->check('limit_size')) {
            $this->paginate['limit'] = $this->Session->read('limit_size');
        }
        if ($this->Session->check('acc_type')) {
            $acc_type = $this->Session->read('acc_type');
        }
        $conditions = [];
        switch ($acc_type) {
            case 'all':
                break;
            case 'active':
                $conditions = ['Users.isactive' => 1];
                break;
            case 'inactive':
                $conditions = ['Users.isactive IS NULL'];
                break;
            case 'expired':
                $conditions = ['DATE(Users.expired) <' => date('Y-m-d')];
                break;
            case 'paid':
                $conditions = [
                    'Users.account_level IN' => [1,2],
                    'DATE(Users.expired) >=' => date('Y-m-d')
                ];
                break;
            case 'trial_days':
                $conditions = [
                    'Users.account_level' => 22
                ];
                break;
            case 'trial_limit':
                $conditions = [
                    'Users.account_level' => 0
                ];
                break;
            default:
                # code...
                break;
        }
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
            'UserStatistic'
        ];
        
        try {
            //$this->paginate['sortWhitelist'] = $this->Users->schema()->columns();
            //$this->paginate['sortWhitelist'][] = 'UserStatistic.created';
            $users = $this->paginate(
                $this->Users->find('all')
                ->where($conditions)
                ->contain($contain)
            )->toArray();
            // pr($users);
            // exit;
            $this->set(compact('users'));
            $this->request->data['limit_size'] = $this->paginate['limit'];
            $this->request->data['acc_type'] = $acc_type;

            $lang_strings['status_active_body'] = __('ACTIVATE_USER');
            $lang_strings['status_inactive_body'] = __('DEACTIVATE_USER');
            $lang_strings['success'] = __('SUCCESS');
            $lang_strings['failed'] = __('FAILED');
            $this->set(compact('lang_strings'));
        } catch (NotFoundException $e) { 
            return $this->redirect(array('controller' => 'users', 'action' => 'index'));
        }
    }

    // Method for updating user information
    public function ajaxUpdate() {
        $this->autoRender = false;
        $response['success'] = false;
        $this->isAdminUser();
        if (!empty($this->request->data['user_info'])) {
            $user_info = explode('-', $this->request->data['user_info']);
            if ((!in_array($user_info[0], ['account_level', 'isactive'])) && empty($this->request->data['value_info'])) {
                $response['message'] = __('INVALID_INPUT');
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
                                    if (in_array($this->request->data['value_info'], [1,2])) {
                                        $user->expired = date('Y-m-d H:i:s', mktime(0, 0, 0, date('m'), date('d'), date('Y') + 1));
                                        $user->plan_switched = NULL;
                                    } elseif ($this->request->data['value_info'] == 22) {
                                        $user->expired = date('Y-m-d H:i:s', mktime(0, 0, 0, date('m'), date('d')+30, date('Y')));
                                    } elseif ($this->request->data['value_info'] == 0) {
                                        $user->expired = NULL;
                                    } else {
                                        $user->expired = date('Y-m-d H:i:s', strtotime('-1 day', time()));
                                    }
                                    $response['expired'] = !empty($user->expired) ? date('Y-m-d', strtotime($user->expired)) : '0000-00-00';
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
        if ($this->request->is('post')) {
            $user = $this->Auth->identify();
            if ($user) {
                if ($user['account_level'] != 51) {
                   $this->Auth->logout();
                   $this->Flash->error(__('Unauthorized access to this login area!'));
                    return $this->redirect(array('controller' => 'maintenance', 'action' => 'notice', 'prefix' => false));
                }
                $user['quiz_bank_access'] = true;
                $this->Auth->setUser($user);
                //save statistics data
                $statisticsTable = TableRegistry::get('Statistics');
                $statistic = $statisticsTable->newEntity();
                $statistic->user_id = $this->Auth->user('id');
                $statistic->type = 'user_login';
                $statistic->created = date("Y-m-d H:i:s");
                $statisticsTable->save($statistic);
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('USERNAME_OR_PASSWORD_INCORRECT'));
        }

    }
}
