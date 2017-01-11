<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link      http://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Event\Event;
use Cake\Core\Configure;
use Cake\Auth\SimplePasswordHasher; //include this line

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link http://book.cakephp.org/3.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{
    public $helpers = [
        'Html' => [
            'className' => 'Bootstrap.BootstrapHtml'
        ],
        'Form' => [
            'className' => 'Bootstrap.BootstrapForm'
        ],
        // 'Paginator' => [
        //     'className' => 'Bootstrap.BootstrapPaginator'
        // ],
        'Modal' => [
            'className' => 'Bootstrap.BootstrapModal'
        ]
    ];

    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('Security');`
     *
     * @return void
     */
    public $Session;
    public function initialize()
    {
        parent::initialize();

        $this->loadComponent('RequestHandler');
        $this->loadComponent('Flash');

        $this->loadComponent('Auth', [
            'authenticate' => [
                'Form' => [
                    'scope' =>  ['Users.activation IS NULL'],
                    'fields' => [
                        'username' => 'email',
                        'password' => 'password'
                    ],
                    'passwordHasher' => [
                        'className' => 'Simple',
                        'hashType' => 'sha256'
                        // 'hashers' => [
                        //     'Default',
                        //     'Weak' => ['hashType' => 'sha1']
                        // ]
                    ]
                    // 'passwordHasher' => [
                    //     'className' => 'Simple',
                    //     'hashType' => 'sha256'
                    // ]
                ]
            ],
            'loginAction' => [
                'controller' => 'Users',
                'action' => 'login'
            ],
            'loginRedirect' => '/',
            'logoutRedirect' => [
                'controller' => 'Users',
                'action' => 'login'
            ],
            'unauthorizedRedirect' => $this->referer() // If unauthorized, return them to page they were just on
        ]);

        // Allow the display action so our pages controller
        // continues to work.
        $this->Auth->allow(['display', 'index', 'logout']);

        /*
         * Enable the following components for recommended CakePHP security settings.
         * see http://book.cakephp.org/3.0/en/controllers/components/security.html
         */
        //$this->loadComponent('Security');
        //$this->loadComponent('Csrf');
        $this->Session = $this->request->session();
    }

    public function beforeFilter(\Cake\Event\Event $event)
    {
        $result = parent::beforeFilter ($event) ;
        $setting = $this->_getSettings();
        $this->set(compact('setting'));
       
        // if (!empty($setting['offline_status'])) {
        //     if (($this->request->action != 'logout') && ($this->request->action != 'admin_access') && ($this->request->action != 'notice') && ($this->Auth->user('account_level') != 51)) {
        //         $this->redirect(array('controller' => 'maintenance', 'action' => 'notice'));
        //     }
        // } 

        // // check user language, default language finish
        // $language = $this->Auth->user('language');
        // if (empty($language) or !file_exists(APP . 'Locale' . DS . $language . DS . 'LC_MESSAGES' . DS . 'default.po'))
        //     $language = 'fin';
        // Configure::write('Config.language', $language);
        // if ($this->Session->check('Choice') && ($this->request->action != 'removeChoice' || $this->request->action != 'isAuthorized')) {
        //     $this->Session->delete('Choice');
        // }

        Configure::write('Config.language', 'fin');

        $this->set('authUser', $this->Auth->user());
    }


    /**
     * Before render callback.
     *
     * @param \Cake\Event\Event $event The beforeRender event.
     * @return \Cake\Network\Response|null|void
     */
    public function beforeRender(Event $event)
    {
        if (!array_key_exists('_serialize', $this->viewVars) &&
            in_array($this->response->type(), ['application/json', 'application/xml'])
        ) {
            $this->set('_serialize', true);
        }
    }

    // check account expiration
    public function accountStatus() 
    {
        $this->loadModel('Users');
        $c_user = $this->Users->find('all')->where(['Users.id' => $this->Auth->user('id')])->first()->toArray();
        $access = false;
        if (!empty($c_user['expired'])) {
            $days_left = floor((strtotime($c_user['expired'])-time())/(60*60*24));
        }

        // For admin role 51 always true
        // For paid users role 1 check expire date
        // For unpaid old user role 0, always true
        // For unpaid new user, check 30 days of expire 
        if ($c_user['account_level'] == 51) { // for admin
            $access = true;
        } elseif(($c_user['account_level'] == 1) && ($days_left >= 0)) { // for paid users
            $access = true;
        } elseif($c_user['account_level'] == 22) { // if new user unpaid 
            $days_left_created = floor((strtotime($c_user['created'])-time())/(60*60*24));
            if ($days_left_created >= -30) {
                $access = true;
            }
            
        } elseif($c_user['account_level'] == 2) { // if new user unpaid 
            $access = true;
        } elseif($c_user['account_level'] == 0) { // for old user
            $access = true;
        }

        if (empty($access)) {
            $this->redirect(array('controller' => 'quiz', 'action' => 'index'));
        }
    }


    // Method for accessing of quiz bank
    public function hasQuizBankAccess()
    {
        $this->loadModel('Users');
        $c_user = $this->Users->find()->where(['Users.id' => $this->Auth->user('id')])->first();
        // pr($c_user);
        // exit;
        if (!in_array($c_user['account_level'], array(2, 22, 51))) {
            if ($this->request->is('ajax')) {
                echo $this->render('/Elements/no_permission_modal');
                exit;
            } else {
                $this->redirect(array('controller' => 'quiz', 'action' => 'index'));
            }
        }
    }

    // check user status
    public function userPermissions()
    {
        $this->loadModel('Users');
        $c_user = $this->Users->find('all')->where(['Users.id' => $this->Auth->user('id')])->first()->toArray();
            // pr($c_user);
            // exit;
        $access = false;
        $canCreateQuiz = false;
        $request_sent = false;
        $permissions = array(
            'access' => false,
            'canCreateQuiz' => false,
            'upgraded' => false,
            'request_sent' => false,
            'days_left' => 0
        );
        if (!empty($c_user['expired'])) {
            $days_left = floor((strtotime($c_user['expired'])-time())/(60*60*24));
        } else {
            $days_left = 365; // always acccess for old unpaid users
        }
        // For admin role 51 always true
        // For paid users role 1 check expire date
        // For unpaid old user role 0, always true
        // For unpaid new user, check 30 days of expire 
        if ($c_user['account_level'] == 51) { // for admin
            $permissions['access'] = true;
            $permissions['canCreateQuiz'] = true;
            $permissions['upgraded'] = true;
            $permissions['quiz_bank_access'] = true;
        } elseif(($c_user['account_level'] == 1) && ($days_left >= 0)) { // for paid users
            $permissions['access'] = true;
            $permissions['canCreateQuiz'] = true;
            $permissions['upgraded'] = true;
        } elseif($c_user['account_level'] == 22) { // if new user unpaid 
            if ($days_left > 30) { // if days left greater than 30 then upgrade request sent
                $permissions['request_sent'] = true;
            }
            $days_left_created = floor((strtotime($c_user['created'])-time())/(60*60*24));

            if ($days_left_created >= -30) {
                $permissions['access'] = true;
                $permissions['canCreateQuiz'] = true;
                $permissions['quiz_bank_access'] = true;
            }
            
        } elseif($c_user['account_level'] == 2) { // if new user unpaid 
            $permissions['access'] = true;
            $permissions['canCreateQuiz'] = true;
            $permissions['upgraded'] = true;
            $permissions['quiz_bank_access'] = true;
        } elseif($c_user['account_level'] == 0) { // for old user
            $this->loadModel('Quiz');
            $quiz = $this->Quiz->find('first', array(
                'conditions' => array(
                    'Quiz.user_id' => $this->Auth->user('id')
                ),
                'recursive' => -1
            ));
            $permissions['access'] = true;
            $permissions['canCreateQuiz'] = empty($quiz) ? true : false;
            if (!empty($c_user['expired'])) {
                $permissions['request_sent'] = true;
            }
        }
        $permissions['days_left'] = $days_left;
        return $permissions;
    }

    // Method for random string generate
    public function randText($length=40)
    {
        $random= "";
        srand((double)microtime()*1000000);
        $strset  = "ABCDEFGHIJKLMNPQRSTUVWXYZ";
        $strset.= "abcdefghijklmnpqrstuvwxyz";
        $strset.= "123456789";
        // Add the special characters to $strset if needed
        
        for($i = 0; $i < $length; $i++) {
            $random.= substr($strset,(rand()%(strlen($strset))), 1);
        }
        return $random;
    }

    /**
     * Get global site settings
     * @return array
     */
    public function _getSettings()
    {
        $this->loadModel('Settings');
        $this->Settings->cacheQueries = true;
        $settings = $this->Settings->find('list', ['keyField' => 'field', 'valueField' => 'value'])->toArray();
        return $settings;
    }

    // Admin checking and redirection
    public function isAdminUser() {
        if ($this->Auth->user('account_level') != 51) {
            return $this->redirect(['controller' => 'Users', 'action' => 'logout', 'prefix' => false]);
        }
    }
}
