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

use Cake\I18n\I18n;
use Intervention\Image\ImageManager;
use Cake\I18n\Time;

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

        $this->loadComponent('Csrf');

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

        $this->loadComponent('Cookie');

        $this->Cookie->configKey('site_language', 'encryption', false);


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

        if (in_array($this->request->action, ['updateStudent', 'ajaxVideo', 'paymentSuccess'])) {
            $this->eventManager()->off($this->Csrf);
        }
        $setting = $this->_getSettings();
        $this->set(compact('setting'));
       
        if (!empty($setting['offline_status'])) {
            if (($this->request->action != 'logout') && ($this->request->action != 'access') && ($this->request->action != 'notice') && ($this->Auth->user('account_level') != 51)) {
                return $this->redirect(['controller' => 'maintenance', 'action' => 'notice', 'prefix' => false]);
            }
        } 

        $eng_domain = false;
        // check user language, default language finish
        if (!$eng_domain) {
            $language = $this->Auth->user('language');
            if (empty($language)) {
                $language = $this->Cookie->read('site_language');
            }
            if (empty($language) or !file_exists(APP . 'Locale' . DS . $language . DS . 'default.po'))
                $language = 'fi';
        } else {
            $language = 'en_GB';
        }
        Configure::write('Config.language', $language);
        I18n::locale($language);
        
        if ($this->Session->check('Choice') && ($this->request->action != 'removeChoice' || $this->request->action != 'isAuthorized')) {
            $this->Session->delete('Choice');
        }

        $this->set('authUser', $this->Auth->user());
        $this->set(compact('language', 'eng_domain'));
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
        $permissions = $this->userPermissions();
        if ($permissions['days_left'] < 0) {
            $this->redirect(['controller' => 'quizzes', 'action' => 'index']);
        }
    }


    // Method for accessing of quiz bank
    public function hasQuizBankAccess()
    {
        if (!$this->Auth->user('quiz_bank_access')) {
            if ($this->request->is('ajax')) {
                echo $this->render('/Elements/no_permission_modal');
                exit;
            } else {
                $this->redirect(array('controller' => 'quizzes', 'action' => 'index'));
            }
        }
    }

    // check user status
    public function userPermissions()
    {
        $c_user = $this->Auth->user();

            // pr($c_user);
            // exit;
        $permissions = array(
            'days_left' => 0,
            'quiz_bank_access' => false,
        );
        if (!empty($c_user['expired'])) {
            $days_left = floor((strtotime($c_user['expired']->format('Y-m-d H:i:s'))-time())/(60*60*24));
        } else {
            $days_left = 365; // always acccess for old unpaid users
        }
        // For admin role 51 always true
        // For paid users role 1 check expire date
        // For unpaid old user role 0, always true
        // For unpaid new user, check 30 days of expire 
        if ($c_user['account_level'] == 51) { // for admin
            $permissions['quiz_bank_access'] = true;
        } elseif($c_user['account_level'] == 22) { // if new user unpaid 
            $days_left_created = floor((strtotime($c_user['created']->format('Y-m-d H:i:s'))-time())/(60*60*24));
            if ($days_left_created >= -30) {
                $permissions['quiz_bank_access'] = true;
            }
        } elseif(($c_user['account_level'] == 2) && ($days_left >= 0)) { // if new user unpaid 
            $permissions['quiz_bank_access'] = true;
        } elseif(($c_user['account_level'] == 1) && ($days_left >= 0) && in_array($c_user['plan_switched'], ['DOWNGRADE', 'CANCELLED_DOWNGRADE'])) { // if new user unpaid 
            $permissions['quiz_bank_access'] = true;
        } elseif($c_user['account_level'] == 0) { // for old user
            
        }
        $permissions['days_left'] = $days_left;
        // pr($permissions);
        // exit;
        return $permissions;
    }

    // Method for random string generate
    public function randText($length=40, $int_only=null)
    {
        $random= "";
        srand((double)microtime()*1000000);
        $strset  = '';
        if (empty($int_only)) {
            $strset.= "ABCDEFGHIJKLMNPQRSTUVWXYZ";
            $strset.= "abcdefghijklmnpqrstuvwxyz";
        }
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


    // Method for image upload
    public function processImage($image, $folder) {
        $original_image = WWW_ROOT . 'uploads/tmp/' . $image;
        $manager = new ImageManager(array('driver' => 'imagick'));
        $resized_image = $manager->make($original_image);
        $resized_image->resize(600, 400, function ($constraint) {
            $constraint->aspectRatio();
        });
        $resized_image->save(WWW_ROOT . 'uploads/'. $folder .'/' . $image);
        $resized_image->destroy();
        unlink($original_image);
    }

    // Method for image upload
    public function processImageLocal($image, $folder) {
        $original_image = WWW_ROOT . 'uploads/tmp/' . $image;
        if (copy($original_image, WWW_ROOT . 'uploads/'. $folder .'/' . $image)) {
            unlink($original_image);
        }
    }

    // Method for time formatting in object
    public function formatDateObject($timestamp) {
        $time = new Time($timestamp);
        $time->i18nFormat(); // outputs '4/20/14, 10:10 PM' for the en-US locale
        $time->i18nFormat(\IntlDateFormatter::FULL); // Use the full date and time format
        $time->i18nFormat([\IntlDateFormatter::FULL, \IntlDateFormatter::SHORT]); // Use full date but short time format
        $time->i18nFormat('yyyy-MM-dd HH:mm:ss'); // outputs '2014-04-20 22:10'
        $time->i18nFormat(Time::UNIX_TIMESTAMP_FORMAT); // outputs '1398031800'
        return $time;
    }
}
