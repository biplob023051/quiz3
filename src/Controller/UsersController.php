<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Network\Exception\NotFoundException;
use Cake\Routing\Router;

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
        $this->loadComponent('Email');
        $this->Auth->allow(['create', 'success', 'ajaxUserChecking', 'passwordRecover', 'ajaxEmailChecking', 'resetPassword', 'edit', 'contact', 'buyCreate', 'confirmation', 'logout']);
    }

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
        $this->redirect('/');
        //pr();
        //$this->request->query['page'] = 5;
        $users = $this->paginate($this->Users);
        // pr($users);
        // exit;
        $this->set(compact('users'));
        $this->set('_serialize', ['users']);
    }

    /**
     * View method
     *
     * @param string|null $id User id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $user = $this->Users->get($id, [
            'contain' => ['Helps', 'Downloads', 'Quizzes', 'Statistics']
        ]);

        $this->set('user', $user);
        $this->set('_serialize', ['user']);
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $user = $this->Users->newEntity();
        if ($this->request->is('post')) {
            $user = $this->Users->patchEntity($user, $this->request->data);
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The user could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('user'));
        $this->set('_serialize', ['user']);
    }

    /**
     * Edit method
     *
     * @param string|null $id User id.
     * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $user = $this->Users->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $user = $this->Users->patchEntity($user, $this->request->data);
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The user could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('user'));
        $this->set('_serialize', ['user']);
    }

    /**
     * Delete method
     *
     * @param string|null $id User id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $user = $this->Users->get($id);
        if ($this->Users->delete($user)) {
            $this->Flash->success(__('The user has been deleted.'));
        } else {
            $this->Flash->error(__('The user could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function create() {
        if ($this->Auth->user()) {
            return $this->redirect(array('controller' => 'quizzes', 'action' => 'index'));
        }
        // load MathCaptchaComponent on fly
        $site_language = Configure::read('Config.language');
        if ($site_language == 'fi') {
            //$this->MathCaptcha = $this->Components->load('MathCaptcha');
            $this->MathCaptcha = $this->loadComponent('MathCaptcha');
        } else {
            //$this->MathCaptcha = $this->Components->load('QuizCaptcha');
            $this->MathCaptcha = $this->loadComponent('QuizCaptcha');
        }
        $user = $this->Users->newEntity();
        if ($this->request->is('post')) {
            // $this->request->data['activation'] = $this->User->randText(16);
            // pr($this->request->data);
            // exit;
            if ($this->MathCaptcha->validate($this->request->data['captcha'])) {
                $this->request->data['account_level'] = 22;
                $this->request->data['expired'] = date('Y-m-d H:i:s', mktime(0, 0, 0, date('m'), date('d')+30, date('Y')));
                $this->request->data['activation'] = $this->randText(16);
                $user = $this->Users->patchEntity($user, $this->request->data);
                $user = $this->Users->save($user);
                // pr($user);
                // exit;
                if (!empty($user->id)) {
                    // Send email to user for email confirmation
                    $user_email = $this->Email->sendMail($user->email, __('[Verkkotesti Signup] Please confirm your email address!'), $user, 'user_email');
                    //pr($user_email);
                    // Send email to admin
                    //Configure::read('AdminEmail')
                    $admin_email = $this->Email->sendMail(Configure::read('AdminEmail'), __('[Verkkotesti] New User!'), $user, 'user_create');
                    // pr($admin_email);
                    // exit;
                    $this->request->session()->write('registration', true);
                    $this->redirect(array('action' => 'success'));

                } else {
                    $this->Flash->error(__('The user could not be saved. Please, try again.'));
                }

            } else {
                $this->Flash->error(__('The result of the calculation was incorrect. Please try again.'));
            }
        }
        $this->set('captcha', $this->MathCaptcha->getCaptcha());
        // language strings
        $lang_strings['empty_name'] = __('Require Name');
        $lang_strings['invalid_characters'] = __('Name contains invalid character');
        $lang_strings['empty_email'] = __('Require Email Address');
        $lang_strings['invalid_email'] = __('Invalid email');
        $lang_strings['unique_email'] = __('Email already registered');
        $lang_strings['empty_password'] = __('Require Password');
        $lang_strings['varify_password'] = __('Password did not match, please try again');
        $lang_strings['character_count'] = __('Password must be 8 characters long');
        $lang_strings['empty_captcha'] = __('Require Captcha');
        $this->set(compact('lang_strings'));

        $this->loadModel('Helps');
        $query_video = $this->Helps->find('all')
            ->where(['Helps.type' => 'create', 'Helps.status' => 1])
            ->contain([])
            ->order(['Helps.id' => 'desc']);
        $create_video = $query_video->first();
        if (!empty($create_video)) {
            $create_video = $create_video->toArray();
        } else {
            $create_video = array();
        }
        $this->set(compact('create_video', 'user'));

    }

    public function success() {
        $this->set('title_for_layout', __('Registration Success'));
        if ($this->request->session()->check('registration')) {
            $this->Flash->success(__('Thanks for your registration!'));
        } else {
            $this->Flash->error(__('No direct access to this page!'));
            $this->redirect(array('action' => 'login'));
        }
    }

    public function confirmation($code = null) {
        if (empty($code)) {
            $this->Flash->error(__('No direct access to this page!'));
            $this->redirect(array('action' => 'create'));
        }
        $response = explode('y-s', $code);
        if (count($response) == 2) {
            $user_query = $this->Users->find('all')
                ->where(['Users.id' => $response[0], 'Users.activation' => $response[1]])
                ->contain([]);
            $user = $user_query->first();
        } else {
            $user = array();
        }

        if (empty($user)) {
            $this->Flash->error(__('This is embrassing, we didn\'t find you!'));
            $this->redirect(array('action' => 'create'));
        }

        $user->activation = NULL;
        // pr($user);
        // exit;
        if ($this->Users->save($user)) {
            $this->Auth->setUser($user);
            //save statistics data
            $statisticsTable = TableRegistry::get('Statistics');
            $statistic = $statisticsTable->newEntity();
            $statistic->user_id = $this->Auth->user('id');
            $statistic->type = 'user_login';
            $statistic->created = date("Y-m-d H:i:s");
            $statisticsTable->save($statistic);
            return $this->redirect($this->Auth->redirectUrl());
        } else {
            $this->Flash->error(__('This is embrassing, we couldn\'t save you! Please try again.'));
            $this->redirect(array('action' => 'create'));
        }
        
    }

    public function login()
    {
        if ($this->Auth->user()) {
            return $this->redirect(array('controller' => 'quizzes', 'action' => 'index'));
        }
        if ($this->request->is('post')) {
            $user = $this->Auth->identify();
            if ($user) {
                $this->Auth->setUser($user);
                //save statistics data
                $statisticsTable = TableRegistry::get('Statistics');
                $statistic = $statisticsTable->newEntity();
                $statistic->user_id = $this->Auth->user('id');
                $statistic->type = 'user_login';
                $statistic->created = date("Y-m-d H:i:s");
                $statisticsTable->save($statistic);
                return $this->redirect($this->Auth->redirectUrl());
            }
            $this->Flash->error('Your username or password is incorrect.');
        }
    }

    public function logout()
    {
        $this->Session->destroy();
        $this->Flash->success('You have logged out');
        return $this->redirect($this->Auth->logout());
    }

    public function settings() {
        $user_id = $this->Auth->user('id');
        $user = $this->Users->get($user_id, ['contain' => []]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            // pr($this->request->data);
            // exit;
            if (!empty($this->request->data['subjects'])) {
                $this->request->data['subjects'] = json_encode($this->request->data['subjects']);
            } 
            
            if (empty($this->request->data['password'])) {
                unset($this->request->data['password']);
            }
            $user = $this->Users->patchEntity($user, $this->request->data);
            if (empty($this->request->data['password'])) {
                unset($user->password);
            }
            // pr($user);
            // exit;
            if ($this->Users->save($user)) {
                $this->Auth->setUser($user);
                // $this->Session->write('Auth.User.language', $data['User']['language']);
                // $this->Session->write('Auth.User.name', $data['User']['name']);
                // $this->Session->write('Auth.User.subjects', $data['User']['subjects']);
                $this->Flash->success(__('Settings has been saved'));
                return $this->redirect(array('controller' => 'quizzes'));
            } else {
                $this->Flash->error(__('Save failed!'));
            }
        }

        $userPermissions = $this->userPermissions();
        $this->set(compact('userPermissions'));
        unset($user->password);
        // pr($data);
        // exit;
        //$data['canCreateQuiz'] = $this->Users->canCreateQuiz();
        $this->loadModel('Subjects');
        $subjects = $this->Subjects->find('list')
        ->where([
            'Subjects.isactive' => 1,
            'Subjects.is_del IS NULL',
            'Subjects.type IS NULL'
        ])
        ->toArray();
        $this->set(compact('user', 'subjects'));
    }

    public function contact() {
        if ($this->request->is('post')) {
            require_once(ROOT . '/vendor' . DS . '/recaptcha/src/autoload.php');
            $secret = RECAPTCHA_SERVER_KEY;
            $recaptcha = new \ReCaptcha\ReCaptcha($secret);
            // pr($recaptcha);
            // exit;
            $resp = $recaptcha->verify($this->request->data['g-recaptcha-response'], Router::url('/', true));
            // pr($resp);
            // exit;
            if ($resp->isSuccess()) {
                if (!empty($this->request->data['email']) && !empty($this->request->data['message'])) {
                    $email_success = $this->Email->sendMail(Configure::read('AdminEmail'), __('General Inquary'), $this->request->data, 'inquary');
                    $this->Flash->success(__('Your email sent successfully')); 
                } else {
                    $this->Flash->error(__('Something went wrong, please try again later'));
                }
            } else {
                foreach ($resp->getErrorCodes() as $code) {
                    $message = '<tt>' . $code  . '</tt> ';
                }
                $this->Flash->error($message);
            }
            return $this->redirect($this->referer());
        }   
    }

    /*
    * Request for password recover
    */
    public function passwordRecover() {
        $this->set('title_for_layout', __('Password Recover'));
        if ($this->request->is('post')) {
            $usersTable = TableRegistry::get('Users');
            $user = $this->Users->findByEmail($this->request->data['email'])->first();
            // pr($user);
            // exit;
            $user->reset_code = $usersTable->randText(16);
            $user->resettime = $usersTable->getCurrentDateTime();
            // pr($user);
            // exit;
            if ($this->Users->save($user)) {
                $email_success = $this->Email->sendMail($user->email, __('Reset password for your account on Verkkotesti'), $user, 'reset_password');
                // pr($email_success);
                // exit;
                if ($email_success) {
                    $this->Flash->success(__('Your request has been received, please check you email.'));
                } else {
                    $this->Flash->error(__('Something went wrong, please try again later'));
                }
            } else {
                $this->Flash->error(__('Something went wrong, please try again later'));
            }
            return $this->redirect(array('action' => 'password_recover'));
        } 

        $lang_strings['empty_email'] = __('Require Email Address');
        $lang_strings['invalid_email'] = __('Invalid email');
        $lang_strings['not_found_email'] = __('This email has not registered yet!');
        $this->set(compact('lang_strings'));
    }

    /* 
    * Email existance checking for password reset
    */
    public function ajaxEmailChecking() {
        $this->autoRender = false;
        $user = $this->Users->findByEmail($this->request->data['email'])->first();
        if (empty($user)) {
            $response['success'] = false;
        } else {
            $response['success'] = true;
        }
        echo json_encode($response);
    }

    public function resetPassword($reset_code) {
        $this->set('title_for_layout', __('Reset Password'));
        if (empty($reset_code)) {
            return $this->redirect('/');
        }
        $user = $this->Users->findByResetCode($reset_code)->first();
        // pr($user);
        // exit;
        if (empty($user)) {
            throw new NotFoundException(__('Password Reset Link Expired.'));
        }

        if ($this->request->is(array('post', 'put'))){
            if ($user->id != $this->request->data['id']) {
                $this->Flash->error(__('Something went wrong, please try again later'));
                return $this->redirect('/');
            }
            $this->request->data['reset_code'] = NULL;
            $this->request->data['resettime'] = NULL;
            // pr($this->request->data);
            // exit;
            $user = $this->Users->patchEntity($user, $this->request->data);
            // pr($user);
            // exit;
            if ($this->Users->save($user)) {
                $this->Flash->success(__('Your password has been successfully changed.'));    
                return $this->redirect(array('controller'=>'users', 'action'=>'login'));
            } else {
                $this->Flash->error(__('Something went wrong, please try again later'));
            }
        } 
        unset($user->password);
        $lang_strings['empty_password'] = __('Require New Password');
        $lang_strings['varify_password'] = __('Password did not match, please try again');
        $lang_strings['character_count'] = __('Password must be 8 characters long');
        $this->set(compact('lang_strings', 'user'));
    }

    public function buyCreate() {
        if ($this->request->is(array('post', 'put'))) {
            $this->request->data['activation'] = $this->randText(16);
            //ate("Y-m-d H:i:s")
            $date = date('Y-m-d H:i:s', mktime(0, 0, 0, date('m'), date('d'), date('Y') + 1));

            if ($this->request->data['package'] == 29) {
                $package =  __('29 E/Y');
                $this->request->data['account_level'] = 1;
            } else {
                $package = __('49 E/Y');
                $this->request->data['account_level'] = 2;
            }
            unset($this->request->data['package']);
            $this->request->data['expired'] = $date;
            $user = $this->Users->newEntity();
            $user = $this->Users->patchEntity($user, $this->request->data);
            // pr($user);
            // exit;
            $user = $this->Users->save($user);
            // pr($user);
            // exit;
            if (!empty($user->id)) {
                // Send email to user for email confirmation
                $user_email = $this->Email->sendMail($user->email, __('[Verkkotesti Signup] Please confirm your email address!'), $user, 'user_email');
                // Send email to admin
                $admin_email = $this->Email->sendMail(Configure::read('AdminEmail'), __('[Verkkotesti] New User!'), $user, 'user_create');
                
                $user->package = $package;
                // Send email for upgrade notice to the admin 
                $upgrade_email = $this->Email->sendMail(Configure::read('AdminEmail'), __('Upgrade Account'), $user, 'invoice');
                // pr($user_email);
                // pr($admin_email);
                // pr($upgrade_email);
                // exit;
                $this->Flash->write('registration', true);
                return $this->redirect(array('action' => 'success'));
            } else {
                $this->Flash->error(__('Something went wrong, please try again later!'));
                return $this->redirect($this->referer());
            }
        }
    }

     /* 
    * Email existance checking for new registration
    */
    public function ajaxUserChecking() {
        $this->autoRender = false;
        $user = $this->Users->findByEmail($this->request->data['email'])->first();
        if (empty($user)) {
            $response['success'] = true;
        } else {
            $response['success'] = false;
        }
        echo json_encode($response);
    }
}
