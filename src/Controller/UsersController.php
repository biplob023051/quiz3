<?php
namespace App\Controller;

use App\Event\Statistics;
use Cake\Event\Event;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Network\Exception\NotFoundException;
use Cake\Routing\Router;
use Stripe\Stripe;

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
        $this->Auth->allow(['create', 'success', 'ajaxUserChecking', 'passwordRecover', 'ajaxEmailChecking', 'resetPassword', 'edit', 'contact', 'buyCreate', 'confirmation', 'logout', 'switchLanguage', 'changePassword', 'paymentSuccess']);
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
                //$this->request->data['activation'] = $this->randText(16);
                $this->request->data['activation'] = NULL;
                $this->request->data['language'] = $site_language;
                $user = $this->Users->patchEntity($user, $this->request->data);
                $user = $this->Users->save($user);
                if (!empty($user->id)) {
                    // Send email to user for email confirmation
                    //$user_email = $this->Email->sendMail($user->email, __('CONFIRM_EMAIL'), $user, 'user_email');
                    $admin_email = $this->Email->sendMail(Configure::read('AdminEmail'), __('[Verkkotesti] New User!'), $user, 'user_create', $user->email, true);
                    // $this->request->session()->write('registration', true);
                    // $this->redirect(array('action' => 'success'));

                    // New codes added here for removing email confirmation
                    $user->quiz_bank_access = true;
                    $this->Auth->setUser($user);
                    //Login Event.
                    $this->eventManager()->attach(new Statistics($this));
                    $event = new Event('Model.Users.login', $this, [
                        'user_id' => $user->id
                    ]);
                    $this->eventManager()->dispatch($event);
                    //return $this->redirect($this->Auth->redirectUrl());
                    return $this->redirect(array('controller' => 'quizzes', 'action' => 'index'));
                    // End of new code
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
                    } elseif(($user['account_level'] == 1) && ($days_left >= 0) && in_array($user['plan_switched'], ['DOWNGRADE', 'CANCELLED_DOWNGRADE'])) { // if new user unpaid 
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
        $quiz_bank_access = $this->Auth->user('quiz_bank_access');
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
            if (!empty($this->request->data['account_level'])) {
                unset($this->request->data['account_level']);
            }
            $user = $this->Users->patchEntity($user, $this->request->data);
            if (empty($this->request->data['password'])) {
                unset($user->password);
            }
            // pr($user);
            // exit;
            if ($this->Users->save($user)) {
                $user->quiz_bank_access = $quiz_bank_access;
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
        $this->autoRender = false;
        $output['success'] = false;
        if ($this->request->is(array('post', 'put'))) {
            //$this->request->data['activation'] = $this->randText(16);
            $this->request->data['activation'] = NULL;
            //ate("Y-m-d H:i:s")
            $date = date('Y-m-d H:i:s', mktime(0, 0, 0, date('m'), date('d'), date('Y') + 1));
            if ($this->request->data['package'] == 29) {
                $package =  __('29_EUR');
                $this->request->data['account_level'] = 1;
            } else {
                $package = __('49_EUR');
                $this->request->data['account_level'] = 2;
            }
            $package = $this->request->data['package'];
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
            $admin_email = $this->Email->sendMail(Configure::read('AdminEmail'), __('[Verkkotesti] New User!'), $user, 'user_create', $user->email, true);

            if (!empty($user->id)) {

                if ($this->request->data['payment_type'] == 'card') {
                    $plan = ($package == 49) ? 'bank-yearly' : 'basic-yearly';
                    \Stripe\Stripe::setApiKey("sk_test_c6GKutQfn5K3nL2SgknhSAsm");  

                    $customer = \Stripe\Customer::create(array(
                        "card" => $this->request->data['token'],
                        "email" => $user->email,
                        "metadata" => ['name' => $user->name],
                    ));
                    if (!empty($customer->id)) {
                        \Stripe\Subscription::create(array(
                          "customer" => $customer->id,
                          "items" => array(
                            array(
                              "plan" => $plan
                            ),
                          ),
                        ));
                        $this->Users->updateAll(
                            [
                                'customer_id' => $customer->id
                            ], 
                            ['id' => $user->id]
                        );
                        $user->customer_id = $customer->id;
                    } else {
                        $expired = date('Y-m-d H:i:s', mktime(0, 0, 0, date('m'), date('d') + 30, date('Y')));
                        $this->Users->updateAll(
                            [
                                'expired' => $expired,
                                'account_level' => 22
                            ], 
                            ['id' => $user->id]
                        );
                        $this->request->data['account_level'] = 22;
                    }
                } else {
                    $data = $user->toArray();
                    $data['package'] = ($package == 29) ? __('29_EUR') : __('49_EUR');
                    if (!empty($this->request->data['invoice_info']))
                    $data['invoice_info'] = $this->request->data['invoice_info'];
                    if (!empty($this->request->data['temp_photo']) && file_exists(WWW_ROOT . 'uploads/tmp/' . $this->request->data['temp_photo'])) {
                        $email_success = $this->Email->sendMail(Configure::read('AdminEmail'), __('UPGRADE_ACCOUNT'), $data, 'invoice_payment', $data['email'], true, [WWW_ROOT . 'uploads/tmp/' . $this->request->data['temp_photo']]);
                        unlink(WWW_ROOT . 'uploads/tmp/' . $this->request->data['temp_photo']);
                    } else {
                        $email_success = $this->Email->sendMail(Configure::read('AdminEmail'), __('UPGRADE_ACCOUNT'), $data, 'invoice_payment', $data['email'], true);
                    }
                }



                // Send email to user for email confirmation
                //$user_email = $this->Email->sendMail($user->email, __('CONFIRM_EMAIL'), $user, 'user_email');
                // Send email to admin
                
                // $this->Session->write('registration', true);
                // return $this->redirect(array('action' => 'success'));

                // New codes added here for removing email confirmation
                if ($this->request->data['account_level'] == 2) {
                    $user->quiz_bank_access = true;
                }
                $this->Auth->setUser($user);
                //Login Event.
                $this->eventManager()->attach(new Statistics($this));
                $event = new Event('Model.Users.login', $this, [
                    'user_id' => $user->id
                ]);
                $this->eventManager()->dispatch($event);
                //return $this->redirect($this->Auth->redirectUrl());
                $flash_message = ($this->request->data['account_level'] != 22) ? __('BUY_CREATE_SUCCESS') : __('BUY_FAILED_BUT_ACC_CREATE_SUCCESS');
                $this->Flash->success($flash_message);
                $output['success'] = true;
                $output['message'] = $flash_message;
                // End of new code
            } else {
                $this->Flash->error(__('SOMETHING_WENT_WRONG'));
            }
        }
        echo json_encode($output);
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

    public function payment() {
        $this->autoRender = false;
        $output = ['success' => false];
        if ($this->request->data['amount'] == 49) {
            $plan = 'bank-yearly';
            $account_level = 2;
        } else {
            $plan = 'basic-yearly';
            $account_level = 1;
        }
        $plan = ($this->request->data['amount'] == 49) ? 'bank-yearly' : 'basic-yearly';
        \Stripe\Stripe::setApiKey("sk_test_c6GKutQfn5K3nL2SgknhSAsm");
        $customer_id = $this->Auth->user('customer_id');
        if (!$customer_id) {
            $customer = \Stripe\Customer::create(array(
                "card" => $this->request->data['token'],
                "email" => $this->Auth->user('email'),
                "metadata" => ['name' => $this->Auth->user('name')],
            ));
            
            \Stripe\Subscription::create(array(
              "customer" => $customer->id,
              "items" => array(
                array(
                  "plan" => $plan
                ),
              ),
            ));
            $expired = date('Y-m-d H:i:s', mktime(0, 0, 0, date('m'), date('d'), date('Y') + 1));
            $this->Users->updateAll(
                [
                    'account_level' => $account_level,
                    'expired' => $expired,
                    'customer_id' => $customer->id,
                    'plan_switched' => NULL,
                ], 
                ['id' => $this->Auth->user('id')]
            );
            $user = $this->Auth->user();
            $user['account_level'] = $account_level;
            $user['expired'] = $this->formatDateObject($expired);
            $user['customer_id'] = $customer->id;
            $user['plan_switched'] = '';
            $this->Auth->setUser($user);
            $output['success'] = true;
            $output['message'] = __('THANKS_FOR_PURCHASING');
            $this->Flash->success($output['message']);
        } else {
            $output['message'] = __('INVALID_TRY');
        } 
        echo json_encode($output);
    }

    public function changePlan() {
        $this->autoRender = false;
        $output['success'] = false;
        $user = $this->Auth->user();
        $account_level = $user['account_level'];
        $customer_id = $user['customer_id'];
        if (empty($customer_id)) { // Inovice paid user plan change / upgrade
            $chosen_account = $this->request->data['utype'];
            $days_left = floor((strtotime($user['expired']->format('Y-m-d H:i:s'))-time())/(60*60*24));
            if ($chosen_account != $account_level) {
                $output['success'] = true;
                // Upgrade or downgrade account
                if ($chosen_account == 2) {
                    // Upgrade account
                    $user['amount_to_pay'] = (49*$days_left)/365;
                    $user['request_type'] = 2;
                    $flash_message = __('INVOICE_UPGRADE_PLAN_SUCCESS');
                    $email_subject = __('INVOICE_UPGRADE_PLAN');
                } else {
                    $flash_message = __('INVOICE_DOWNGRADE_PLAN_SUCCESS');
                    $user['request_type'] = 1;
                    $user['amount_to_pay'] = 0;
                    $email_subject = __('INVOICE_DOWNGRADE_PLAN');
                }
                $email_success = $this->Email->sendMail(Configure::read('AdminEmail'), $email_subject, $user, 'invoice_plan_modify', $user['email'], true);

                $this->Users->updateAll(
                    [
                        'account_level' => $chosen_account,
                    ], 
                    ['id' => $user['id']]
                );
                $user['account_level'] = $chosen_account;
                $this->Auth->setUser($user);
                $output['message'] = $flash_message;
                $this->Flash->success($flash_message);
            }
            echo json_encode($output);
            exit;
        }
        \Stripe\Stripe::setApiKey("sk_test_c6GKutQfn5K3nL2SgknhSAsm");
        $customer = \Stripe\Customer::retrieve($customer_id);
        $customer_plan = $customer->subscriptions->data[0]->plan['id'];
        $subscription_id = $customer->subscriptions->data[0]->id;
        $subscription = \Stripe\Subscription::retrieve($subscription_id);
        if ($this->request->data['utype'] == 'Cancel') {
            $subscription->cancel(array('at_period_end' => true));
            $plan_switched = ($user['plan_switched'] == 'DOWNGRADE') ? 'CANCELLED_DOWNGRADE' : 'CANCELLED';
            $output['success'] = true;
            $output['message'] = __('SUBSCRIPTION_CANCELLED_SUCCESS');
        } else {
            $plan = ($account_level == 1) ? 'bank-yearly' : 'basic-yearly';
            // Plan upgrade or downgraded
            if ($customer_plan != $plan) {
                $itemID = $subscription->items->data[0]->id;

                \Stripe\Subscription::update($subscription_id, array(
                  "items" => array(
                    array(
                      "id" => $itemID,
                      "plan" => $plan,
                    ),
                  ),
                ));
                // Id plan upgrade create an invoice
                if ($plan == 'bank-yearly') {
                    $invoice = \Stripe\Invoice::create(array(
                        "customer" => $customer_id
                    ));
                    // Immediate invoice send
                    //$invoice = \Stripe\Invoice::retrieve($upgrade_invoice->id);
                    $invoice->pay();
                    $output['message'] = __('UPGRADE_PLAN_SUCCESS');
                    $plan_switched = 'UPGRADE';
                    $account_level = 2;
                } else {
                    $output['message'] = __('DOWNGRADE_PLAN_SUCCESS');
                    $plan_switched = 'DOWNGRADE';
                    $account_level = 1;
                }
                $output['success'] = true;
            }   
        }
        if ($output['success']) {
            $this->Users->updateAll(
                [
                    'account_level' => $account_level,
                    'plan_switched' => $plan_switched,
                ], 
                ['id' => $this->Auth->user('id')]
            );
            $user['plan_switched'] = $plan_switched;
            $user['account_level'] = $account_level;
            if (($account_level == 2) || in_array($plan_switched, ['DOWNGRADE', 'CANCELLED_DOWNGRADE'])) {
                $user['quiz_bank_access'] = true;
            } else {
                $user['quiz_bank_access'] = false;
            }
            $this->Auth->setUser($user);
            $output['account_level'] = $account_level;
            $this->Flash->success($output['message']);
        }
        echo json_encode($output);
    }

    // Reactivate subscription
    public function reactivatePlan() {
        $this->autoRender = false;
        $output['success'] = false;
        \Stripe\Stripe::setApiKey("sk_test_c6GKutQfn5K3nL2SgknhSAsm");
        $customer_id = $this->Auth->user('customer_id');
        $customer = \Stripe\Customer::retrieve($customer_id);
        $customer_plan = $customer->subscriptions->data[0]->plan['id'];
        $subscription_id = $customer->subscriptions->data[0]->id;
        $subscription = \Stripe\Subscription::retrieve($subscription_id);
        $user = $this->Auth->user();


        $itemID = $subscription->items->data[0]->id;

        $plan = ($this->request->data['utype'] == 1) ? 'basic-yearly' : 'bank-yearly';

        \Stripe\Subscription::update($subscription_id, array(
          "items" => array(
            array(
              "id" => $itemID,
              "plan" => $plan,
            ),
          ),
        ));

        $output['success'] = true;
        if (($user['account_level'] == 2) && ($this->request->data['utype'] == 1)) {
            $user['plan_switched'] = 'DOWNGRADE';
            $output['message'] = __('SUBSCRIPTION_REACTIVATION_SUCCESS_AND_DOWNGRADED');
            $output['type'] = 'DOWNGRADE';
        } elseif (($user['account_level'] == 1) && $this->request->data['utype'] == 2) {
            $user['plan_switched'] = 'UPGRADE';
            $output['message'] = __('SUBSCRIPTION_REACTIVATION_SUCCESS_AND_UPGRADED');
            $output['type'] = 'UPGRADE';
        } else {
            $user['plan_switched'] = ($user['account_level'] == 1 && $user['plan_switched'] == 'CANCELLED_DOWNGRADE') ? 'DOWNGRADE' : NULL;
            $output['message'] = __('SUBSCRIPTION_REACTIVATION_SUCCESS');
            $output['type'] = 'REACTIVATE';
        }

        $this->Users->updateAll(
            [
                'account_level' => $this->request->data['utype'],
                'plan_switched' => $user['plan_switched'],
            ], 
            ['id' => $this->Auth->user('id')]
        );
        $user['account_level'] = $this->request->data['utype'];
        
        if (($this->request->data['utype'] == 2) || ($user['plan_switched'] == 'DOWNGRADE')) {
            $user['quiz_bank_access'] = true;
        } else {
            $user['quiz_bank_access'] = false;
        }
        $this->Auth->setUser($user);
        $this->Flash->success($output['message']);

        echo json_encode($output);
    }

    // Webhook after successful payment charge
    public function paymentSuccess() {
        \Stripe\Stripe::setApiKey("sk_test_c6GKutQfn5K3nL2SgknhSAsm");

        // You can find your endpoint's secret in your webhook settings
        $endpoint_secret = "whsec_glUxw47WE7duw2aaeIRR8AY5YWP8JqR8";

        $payload = @file_get_contents("php://input");
        $sig_header = $_SERVER["HTTP_STRIPE_SIGNATURE"];
        $event = null;

        try {
          $event = \Stripe\Webhook::constructEvent(
            $payload, $sig_header, $endpoint_secret
          );
        } catch(\UnexpectedValueException $e) {
          // Invalid payload
          http_response_code(400);
          exit();
        } catch(\Stripe\Error\SignatureVerification $e) {
          // Invalid signature
          http_response_code(400); // PHP 5.4 or greater
          exit();
        }

        // Do something with $event
        if ($event->type == 'customer.subscription.deleted') {
            $customer_id = $event->data->object->customer;
            $user = $this->Users->findByCustomerId($customer_id)->first();
            if (!empty($user)) {
                //$user->expired = date('Y-m-d H:i:s');
                $user->account_level = 22;
                $user->plan_switched = NULL;
                $user->customer_id = NULL;
                $this->Users->save($user);
            }
        }

        // If payment success
        if (($event->type == 'invoice.payment_succeeded') && empty($event->request)) {
            $amount = $event->data->object->amount_due;
            $customer_id = $event->data->object->customer;
            if (($amount == '2900') || ($amount == '4900')) {
                $user = $this->Users->findByCustomerId($customer_id)->first();
                if (!empty($user)) {
                    $user->expired = date('Y-m-d H:i:s', mktime(0, 0, 0, date('m'), date('d'), date('Y') + 1));
                    $this->Users->save($user);
                }
            }
        } 

        http_response_code(200); // PHP 5.4 or greater
        exit();
    }

    // Method of invoice payment
    public function invoicePayment() {
        $this->autoRender = false;
        $output['success'] = false;
        if ($this->request->is(['patch', 'post', 'put'])) {
            if (!empty($this->request->data['amount'])) {
                $account_level = ((int)$this->request->data['amount'] == 29) ? 1 : 2;

                $expired = date('Y-m-d H:i:s', mktime(0, 0, 0, date('m'), date('d'), date('Y') + 1));
                $this->Users->updateAll(
                    [
                        'account_level' => $account_level,
                        'expired' => $expired,
                        'customer_id' => NULL,
                        'plan_switched' => NULL,
                    ], 
                    ['id' => $this->Auth->user('id')]
                );
                $user = $this->Auth->user();
                $user['account_level'] = $account_level;
                $user['expired'] = $this->formatDateObject($expired);
                $user['customer_id'] = '';
                $user['plan_switched'] = '';
                $this->Auth->setUser($user);
                $user['invoice_info'] = $this->request->data['invoice_info'];
                $user['package'] = ($account_level == 1) ? __('29_EUR') : __('49_EUR');
                if (!empty($this->request->data['temp_photo']) && file_exists(WWW_ROOT . 'uploads/tmp/' . $this->request->data['temp_photo'])) {
                    $email_success = $this->Email->sendMail(Configure::read('AdminEmail'), __('UPGRADE_ACCOUNT'), $user, 'invoice_payment', $user['email'], true, [WWW_ROOT . 'uploads/tmp/' . $this->request->data['temp_photo']]);
                    unlink(WWW_ROOT . 'uploads/tmp/' . $this->request->data['temp_photo']);
                } else {
                    $email_success = $this->Email->sendMail(Configure::read('AdminEmail'), __('UPGRADE_ACCOUNT'), $user, 'invoice_payment', $user['email'], true);
                }
                $output['success'] = true;
                $output['message'] = __('THANKS_FOR_PURCHASING');
                $this->Flash->success($output['message']);
            }
        }
        echo json_encode($output);
    }

    // Continue subscription for next year for a invoice paid user
    public function nextYearSubscription() {
        $this->autoRender = false;
        $output['success'] = false;
        if ($this->request->is(['patch', 'post', 'put'])) {
            if (!empty($this->request->data['utype'])) {
                $user = $this->Auth->user();
                $account_level = (int) $this->request->data['utype'];
                $current_account_level = $user['account_level'];

                $now = time();
                $current_expire_date = strtotime($user['expired']->format('Y-m-d'));
                $datediff = $current_expire_date-$now;
                $days_left = floor($datediff / (60 * 60 * 24));
                $additional_charge = 0;

                if ($account_level != $current_account_level) {
                    if ($account_level == 2) {
                        $email_subject =  __('UPGRADED_AND_NEXT_YEAR_PURCHASE');
                        $flash_message = __('THANKS_FOR_UPGRADE_AND_CONTINUE_SUBSCRIPTION');
                        $request_type = 3;
                        if ($days_left > 0) {
                            $additional_charge = (49*$days_left)/365;
                        }
                    } else {
                        $email_subject =  __('DOWNGRADED_AND_NEXT_YEAR_PURCHASE');
                        $flash_message = __('THANKS_FOR_DOWNGRADE_AND_CONTINUE_SUBSCRIPTION');
                        $request_type = 2;
                    }
                } else {
                    $request_type = 1;
                    $email_subject = __('CONTINUE_NEXT_YEAR_SUBSCRIPTION');
                    $flash_message = __('THANKS_FOR_CONTINUE_SUBSCRIPTION');
                }
                $days = ($days_left > 0) ? ($days_left+365) : 365;
                $expired = date('Y-m-d H:i:s', strtotime('+'. $days .' day', time()));

                // pr($days_left);
                // pr($days);
                // pr($additional_charge);
                // pr($expired);
                // exit;
                
                $this->Users->updateAll(
                    [
                        'account_level' => $account_level,
                        'expired' => $expired,
                        'customer_id' => NULL,
                        'plan_switched' => NULL,
                    ], 
                    ['id' => $user['id']]
                );

                $user['expired'] = $this->formatDateObject($expired);
                $user['account_level'] = $account_level;
                $user['customer_id'] = '';
                $user['plan_switched'] = '';
                $this->Auth->setUser($user);
                $user['request_type'] = $request_type;

                if ($account_level == 1) {
                    $user['amount_to_pay'] = 29;
                    $user['package'] = __('29_EUR');
                } else {
                    $user['amount_to_pay'] = 49+$additional_charge;
                    $user['package'] = __('49_EUR');
                }

                
                $email_success = $this->Email->sendMail(Configure::read('AdminEmail'), $email_subject, $user, 'invoice_payment_continue', $user['email'], true);

                $output['success'] = true;
                $output['message'] = $flash_message;
                $output['type'] = $request_type;
                $this->Flash->success($output['message']);
            }
        }
        echo json_encode($output);
    }

}
