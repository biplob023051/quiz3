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

use Cake\Core\Configure;
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
use Cake\Utility\Inflector;

/**
 * Static content controller
 *
 * This controller will render views from Template/Pages/
 *
 * @link http://book.cakephp.org/3.0/en/controllers/pages-controller.html
 */
class PagesController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->Auth->allow(['display', 'index']);
    }

    /**
     * Displays a view
     *
     * @return void|\Cake\Network\Response
     * @throws \Cake\Network\Exception\ForbiddenException When a directory traversal attempt.
     * @throws \Cake\Network\Exception\NotFoundException When the view file could not
     *   be found or \Cake\View\Exception\MissingTemplateException in debug mode.
     */
    public function display()
    {
        $this->viewBuilder()->layout('default');
        $path = func_get_args();

        $count = count($path);
        if (!$count) {
            return $this->redirect('/');
        }
        if (in_array('..', $path, true) || in_array('.', $path, true)) {
            throw new ForbiddenException();
        }
        $page = $subpage = null;

        if (!empty($path[$count - 1])) {
            $title_for_layout = __(Inflector::humanize($path[$count - 1]));
            $this->set(compact('title_for_layout'));
        }

        if ($this->request->params['pass'][0] == 'contact') {
            $site_language = Configure::read('Config.language');
            $lang_strings['empty_email'] = __('REQUIRE_EMAIL');
            $lang_strings['invalid_email'] = __('INVALID_EMAIL');
            $lang_strings['empty_message'] = __('REQUIRE_MESSAGE');
            $lang_strings['empty_captcha'] = __('PROVE_NOT_ROBOT');
            $this->set(compact('lang_strings', 'site_language'));
        }

        if (($this->request->params['pass'][0] == '1bgfg9sq') || ($this->request->params['pass'][0] == '4bgfg9sq') || ($this->request->params['pass'][0] == '5bgfg9sq') || ($this->request->params['pass'][0] == '9bgfg9sq') || ($this->request->params['pass'][0] == 'prices')) {
            $lang_strings['empty_name'] = __('REQUIRE_NAME');
            $lang_strings['invalid_characters'] = __('NAME_CONTAINS_INVALID_CHAR');
            $lang_strings['empty_email'] = __('REQUIRE_EMAIL');
            $lang_strings['invalid_email'] = __('INVALID_EMAIL');
            $lang_strings['unique_email'] = __('EMAIL_REGISTERED');
            $lang_strings['empty_password'] = __('REQUIRE_PASSWORD');
            $lang_strings['varify_password'] = __('PASSWORD_NOT_MATCH');
            $lang_strings['character_count'] = __('PASSWORD_MUST_BE_LONGER');
            $lang_strings['package_29'] = __('CREATE_ACCOUNT_BUY_29');
            $lang_strings['package_49'] = __('CREATE_ACCOUNT_BUY_49');

            $lang_strings['request_sent'] = __('UPGRADE_PENDING');
            $lang_strings['drag_drop'] = __('DRAG_DROP');
            $lang_strings['upload'] = __('CHOOSE_FILE');
            $lang_strings['validating'] = __('VALIDATING');
            $lang_strings['retry'] = __('RETRY');
            $lang_strings['processing'] = __('PROCESSING');
            $lang_strings['pay_success'] = __('PAY_SUCCESS');
            $lang_strings['pay_failed'] = __('PAY_FAILED');
            $lang_strings['try_refresh'] = __('TRY_REFRESH');
            $lang_strings['confirm'] = __('CONFIRM_CANCEL');
            $lang_strings['downgrade'] = __('DOWNGRADE_PLAN');
            $lang_strings['upgrade'] = __('UPGRADE_PLAN');
            $lang_strings['current_plan'] = __('CURRENT_PLAN');
            $lang_strings['reactivate'] = __('REACTIVATE_SUBSCRIPTION');
            $lang_strings['reactivate_downgrade'] = __('REACTIVATE_AND_DOWNGRADE_SUBSCRIPTION');
            $lang_strings['reactivate_upgrade'] = __('REACTIVATE_AND_UPGRADE_SUBSCRIPTION');
            $lang_strings['next_buy'] = __('CONTINUE_BUY_FOR_NEXT_YEAR');
            $lang_strings['upgrade_next_buy'] = __('UPGRADE_AND_BUY_FOR_NEXT_YEAR');
            $lang_strings['downgrade_next_buy'] = __('DOWNGRADE_AND_BUY_FOR_NEXT_YEAR');

            $lang_strings['incl_tax'] = __('INCL_TAX');
            $lang_strings['excl_tax'] = __('EXCL_TAX');
            $lang_strings['basic_btn_txt'] = __('29_YEARLY_BTN_TEXT');
            $lang_strings['bank_btn_txt'] = __('49_YEARLY_BTN_TEXT');
            $lang_strings['pay_scs_title'] = __('UPGRADE_ACCOUNT');
            $lang_strings['pay_scs_body'] = __('YOU_RECEIVE_INVOICE'); 
            $lang_strings['stripe_pay_scs_title'] = __('STRIPE_PAY_SUC_TITLE');
            $lang_strings['stripe_pay_scs_body'] = __('STRIPE_PAY_SUC_BODY');
            $lang_strings['cancel_title'] = __('CANCEL_PLAN_TITLE');
            $lang_strings['cancel_body'] = __('CANCEL_PLAN_BODY');
            $lang_strings['upgrade_title'] = __('UPGRADE_PLAN_TITLE');
            $lang_strings['upgrade_body'] = __('UPGRADE_PLAN_BODY');
            $lang_strings['downgrade_title'] = __('DOWNGRADE_PLAN_TITLE');
            $lang_strings['downgrade_body'] = __('DOWNGRADE_PLAN_BODY');
            $lang_strings['reactivate_title'] = __('REACTIVATE_TITLE');
            $lang_strings['reactivate_body'] = __('REACTIVATE_BODY');
            $lang_strings['reactivate_upgraded_body'] = __('REACTIVATE_UPGRADED_BODY');
            $lang_strings['reactivate_downgraded_body'] = __('REACTIVATE_DOWNGRADED_BODY'); 
            $lang_strings['next_year_title'] = __('NEXT_YEAR_TITLE');
            $lang_strings['next_year_body'] = __('NEXT_YEAR_BODY');
            $lang_strings['upgrade_next_year_body'] = __('UPGRADE_AND_NEXT_YEAR_BODY');
            $lang_strings['downgrade_next_year_body'] = __('DOWNGRADE_AND_NEXT_YEAR_BODY');
            $lang_strings['required_field'] = __('REQUIRED_FIELD');
            $lang_strings['invalid_card'] = __('INVALID_CARD');
            $lang_strings['invalid_expire'] = __('INVALID_EXPIRE_DATE');
            $lang_strings['invalid_cvc'] = __('INVALID_CVC');
            $lang_strings['max_attachment'] = __('MAXIMUM_ATTACHMENT');

            $this->set(compact('lang_strings'));
        }

        $this->set('current_page', $this->request->params['pass'][0]);

        if (!empty($path[0])) {
            $page = $path[0];
        }
        if (!empty($path[1])) {
            $subpage = $path[1];
        }
        $this->set(compact('page', 'subpage'));

        try {
            $this->render(implode('/', $path));
        } catch (MissingTemplateException $e) {
            if (Configure::read('debug')) {
                throw $e;
            }
            throw new NotFoundException();
        }
    }

    // Method for home page display
    public function index()
    {
        $this->loadModel('Helps');
        $home_video = $this->Helps->getVideoByType('home', $this->getDefaultLanguage());
        $this->set(compact('home_video'));
        $this->set('title_for_layout', __('WELCOME'));   
    }
}
