<?php
namespace App\Controller;

use App\Event\Statistics;
use Cake\Event\Event;

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
        $this->Auth->allow(['create', 'success', 'ajaxUserChecking', 'passwordRecover', 'ajaxEmailChecking', 'resetPassword', 'edit', 'contact', 'buyCreate', 'confirmation', 'logout', 'switchLanguage', 'changePassword']);
    }

    // Method for ajax password update
    public function changePassword()
    {
        $this->autoRender = false;
        $response['success'] = false;
        $user =$this->Users->get($this->Auth->user('id'));
        if (!empty($this->request->data)) {
            $user = $this->Users->patchEntity($user, [
                    'old_password'  => $this->request->data['old_password'],
                    'password'      => $this->request->data['password1'],
                    'password1'     => $this->request->data['password1'],
                    'password2'     => $this->request->data['password2']
                ],
                ['validate' => 'password']
            );
            if ($this->Users->save($user)) {
                $response['success'] = true;
                $response['message'] = __('PASSWORD_CHANGED_SUCCESS');
            } else {
                $response['errors'] = $user->errors();
                $response['message'] = __('PASSWORD_CHANGED_FAILED');
            }
        }
        echo json_encode($response);
    }

    // Method of switching language 
    public function switchLanguage() {
        $this->autoRender = false;
        $this->Cookie->write('site_language', $this->request->data['lang']);
        echo json_encode(['success' => __('Language successfully switched')]);
    }

    public function create() {
        if ($this->Auth->user()) {
            return $this->redirect(array('controller' => 'quizzes', 'action' => 'index'));
        }
        $user = $this->Users->newEntity();
        $site_language = Configure::read('Config.language');
        if ($this->request->is('post')) {
            require_once(ROOT . '/vendor' . DS . '/recaptcha/src/autoload.php');
            $secret = RECAPTCHA_SERVER_KEY;
            $recaptcha = new \ReCaptcha\ReCaptcha($secret);
            $resp = $recaptcha->verify($this->request->data['g-recaptcha-response'], Router::url('/', true));
            if ($resp->isSuccess()) {
                $this->request->data['account_level'] = 22;
                $this->request->data['expired'] = date('Y-m-d H:i:s', mktime(0, 0, 0, date('m'), date('d')+30, date('Y')));
                $this->request->data['activation'] = $this->randText(16);
                $this->request->data['language'] = $site_language;
                $user = $this->Users->patchEntity($user, $this->request->data);
                $user = $this->Users->save($user);
                if (!empty($user->id)) {
                    // Send email to user for email confirmation
                    $user_email = $this->Email->sendMail($user->email, __('CONFIRM_EMAIL'), $user, 'user_email');
                    $admin_email = $this->Email->sendMail(Configure::read('AdminEmail'), __('[Verkkotesti] New User!'), $user, 'user_create', '', true);
                    $this->request->session()->write('registration', true);
                    $this->redirect(array('action' => 'success'));
                } else {
                    $this->Flash->error(__('The user could not be saved. Please, try again.'));
                }
            } else {
                foreach ($resp->getErrorCodes() as $code) {
                    $message = '<tt>' . $code  . '</tt> ';
                }
                $this->Flash->error($message);
            }
        }
        // language strings
        $lang_strings['empty_name'] = __('REQUIRE_NAME');
        $lang_strings['invalid_characters'] = __('NAME_CONTAINS_INVALID_CHAR');
        $lang_strings['empty_email'] = __('REQUIRE_EMAIL');
        $lang_strings['invalid_email'] = __('INVALID_EMAIL');
        $lang_strings['unique_email'] = __('EMAIL_REGISTERED');
        $lang_strings['empty_password'] = __('REQUIRE_PASSWORD');
        $lang_strings['varify_password'] = __('PASSWORD_NOT_MATCH');
        $lang_strings['character_count'] = __('PASSWORD_MUST_BE_LONGER');
        $lang_strings['empty_captcha'] = __('REQUIRE_CAPTCHA');
        $this->set(compact('lang_strings', 'site_language'));

        $this->loadModel('Helps');
        $create_video = $this->Helps->getVideoByType('create');
        $this->set(compact('create_video', 'user'));

    }

    public function success() {
        $this->set('title_for_layout', __('REGISTRATION_SUCCESS'));
        if ($this->request->session()->check('registration')) {
            $this->Session->delete('registration');
        } else {
            $this->Flash->error(__('NO_DIRECT_ACCESS_PAGE'));
            $this->redirect(array('action' => 'login'));
        }
    }

    public function confirmation($code = null) {
        $this->autoRender = false;
        if (empty($code)) {
            $this->Flash->error(__('NO_DIRECT_ACCESS_PAGE'));
            $this->redirect(array('controller' => 'users', 'action' => 'create'));
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
            $this->Flash->warning(__('ACCOUNT_ENABLED'));
            return $this->redirect(array('controller' => 'users', 'action' => 'create'));
        }
        $user->activation = NULL;
        if ($this->Users->updateAll(array('activation' => NULL), array('id' => $user->id))) {
            $this->Auth->setUser($user);
            //Login Event.
            $this->eventManager()->attach(new Statistics($this));
            $event = new Event('Model.Users.login', $this, [
                'user_id' => $user->id
            ]);
            $this->eventManager()->dispatch($event);
            return $this->redirect($this->Auth->redirectUrl());
        } else {
            $this->Flash->error(__('Invalid try, please try again later!'));
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
                if ($user['isactive']) {
                    // For quiz bank settings
                    if (!empty($user['expired'])) {
                        $days_left = floor((strtotime($user['expired']->format('Y-m-d H:i:s'))-time())/(60*60*24));
                    } else {
                        $days_left = 365; // always acccess for old unpaid users
                    }
                    if ($user['account_level'] == 51) { // for admin
                        $user['quiz_bank_access'] = true;
                    } elseif($user['account_level'] == 22) { // if new user unpaid 
                        $days_left_created = floor((strtotime($user['created']->format('Y-m-d H:i:s'))-time())/(60*60*24));
                        if ($days_left_created >= -30) {
                            $user['quiz_bank_access'] = true;
                        }
                    } elseif(($user['account_level'] == 2) && ($days_left >= 0)) { // if new user unpaid 
                        $user['quiz_bank_access'] = true;
                    } else {

                    }
                    // End of bank settings

                    $this->Auth->setUser($user);
                    //Login Event.
                    $this->eventManager()->attach(new Statistics($this));
                    $event = new Event('Model.Users.login', $this, [
                        'user_id' => $user['id']
                    ]);
                    $this->eventManager()->dispatch($event);
                    return $this->redirect($this->Auth->redirectUrl());
                } else {
                    $this->Session->destroy();
                    $this->Flash->error(__('SORRY_YOUR_ACCOUNT_DISABLED'));
                }
            } else {
                $this->Flash->error(__('USERNAME_OR_PASSWORD_INCORRECT'));
            }
        }
    }

    public function logout()
    {
        $this->Session->destroy();
        $this->Flash->success(__('LOGGED_OUT'));
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
            if (!empty($this->request->data['email'])) {
                unset($this->request->data['email']);
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
                $this->Flash->success(__('SETTINGS_SAVED'));
                return $this->redirect(array('controller' => 'quizzes'));
            } else {
                $this->Flash->error(__('Settings save failed'));
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
        $lang_strings['request_sent'] = __('UPGRADE_PENDING');
        $this->set(compact('user', 'subjects', 'lang_strings'));
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
                    $email_success = $this->Email->sendMail(Configure::read('AdminEmail'), __('GENERAL_INQUERY'), $this->request->data, 'inquary');
                    $this->Flash->success(__('EMAIL_SENT')); 
                } else {
                    $this->Flash->error(__('SOMETHING_WENT_WRONG'));
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
        $this->set('title_for_layout', __('RECOVER_PASSWORD'));
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
                $email_success = $this->Email->sendMail($user->email, __('RESET_PASSWORD'), $user, 'reset_password');
                // pr($email_success);
                // exit;
                if ($email_success) {
                    $this->Flash->success(__('REQUEST_RECEIVED_CHECK_EMAIL'));
                } else {
                    $this->Flash->error(__('SOMETHING_WENT_WRONG'));
                }
            } else {
                $this->Flash->error(__('SOMETHING_WENT_WRONG'));
            }
            return $this->redirect(array('action' => 'password_recover'));
        } 

        $lang_strings['empty_email'] = __('REQUIRE_EMAIL');
        $lang_strings['invalid_email'] = __('INVALID_EMAIL');
        $lang_strings['not_found_email'] = __('EMAIL_NOT_REGISTERED');
        $this->set(compact('lang_strings'));

        $this->loadModel('Helps');
        $password_video = $this->Helps->getVideoByType('password');
        $this->set(compact('password_video'));
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
        $this->set('title_for_layout', __('PASSWORD_RESET'));
        if (empty($reset_code)) {
            return $this->redirect('/');
        }
        $user = $this->Users->findByResetCode($reset_code)->first();
        // pr($user);
        // exit;
        if (empty($user)) {
            $this->Flash->error(__('Password Reset Link Expired.'));
            return $this->redirect(array('controller' => 'users', 'action' => 'passwordRecover'));
        }

        if ($this->request->is(array('post', 'put'))){
            if ($user->id != $this->request->data['id']) {
                $this->Flash->error(__('SOMETHING_WENT_WRONG'));
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
                $this->Flash->success(__('PASSWORD_CHANGED'));    
                return $this->redirect(array('controller'=>'users', 'action'=>'login'));
            } else {
                $this->Flash->error(__('SOMETHING_WENT_WRONG'));
            }
        } 
        unset($user->password);
        $lang_strings['empty_password'] = __('Require New Password');
        $lang_strings['varify_password'] = __('PASSWORD_NOT_MATCH');
        $lang_strings['character_count'] = __('PASSWORD_MUST_BE_LONGER');
        $this->set(compact('lang_strings', 'user'));
    }

    public function buyCreate() {
        if ($this->request->is(array('post', 'put'))) {
            $this->request->data['activation'] = $this->randText(16);
            //ate("Y-m-d H:i:s")
            $date = date('Y-m-d H:i:s', mktime(0, 0, 0, date('m'), date('d'), date('Y') + 1));
            if ($this->request->data['package'] == 29) {
                $package =  __('29_EUR');
                $this->request->data['account_level'] = 1;
            } else {
                $package = __('49_EUR');
                $this->request->data['account_level'] = 2;
            }
            unset($this->request->data['package']);
            $this->request->data['expired'] = $date;
            $this->request->data['language'] = Configure::read('Config.language');
            $user = $this->Users->newEntity();
            $user = $this->Users->patchEntity($user, $this->request->data);
            // pr($user);
            // exit;
            $user = $this->Users->save($user);
            // pr($user);
            // exit;
            if (!empty($user->id)) {
                // Send email to user for email confirmation
                $user_email = $this->Email->sendMail($user->email, __('CONFIRM_EMAIL'), $user, 'user_email');
                // Send email to admin
                $admin_email = $this->Email->sendMail(Configure::read('AdminEmail'), __('[Verkkotesti] New User!'), $user, 'user_create', '', true);
                
                $user->package = $package;
                // Send email for upgrade notice to the admin 
                $upgrade_email = $this->Email->sendMail(Configure::read('AdminEmail'), __('UPGRADE_ACCOUNT'), $user, 'invoice');
                // pr($user_email);
                // pr($admin_email);
                // pr($upgrade_email);
                // exit;
                $this->Session->write('registration', true);
                return $this->redirect(array('action' => 'success'));
            } else {
                $this->Flash->error(__('SOMETHING_WENT_WRONG'));
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
