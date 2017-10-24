<style>
    .new-br {
        margin-bottom: 4px;
        margin-left: -2.9%;
    }

    #subjects .checkbox {
        width: 33% !important;
        float: left;
        margin-top: 0px;
    }
</style>
<?php 
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
$lang_strings['downgrade_next_buy'] = __('UPGRADE_AND_BUY_FOR_NEXT_YEAR');

$lang_strings['incl_tax'] = __('INCL_TAX');
$lang_strings['excl_tax'] = __('EXCL_TAX');
$lang_strings['basic_btn_txt'] = __('29_YEARLY_BTN_TEXT');
$lang_strings['bank_btn_txt'] = __('49_YEARLY_BTN_TEXT');
$lang_strings['pay_scs_title'] = __('UPGRADE_ACCOUNT');
$lang_strings['pay_scs_body'] = __('YOU_RECEIVE_INVOICE'); 
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
?>
<script type="text/javascript">
    var lang_strings = <?= json_encode($lang_strings) ?>;
</script>
<?php
//pr($userPermissions);
$this->assign('title', __('SETTINGS'));
$userSubjects = !empty($user->subjects) ? json_decode($user->subjects, true) : array();
?>

<?= $this->Flash->render(); ?>

<?= $this->Form->create($user, ['novalidate' => 'novalidate']); ?>
<div class="row">
    <div class="col-md-6 col-md-offset-3 col-xs-12 col-sm-12">
        <?= $this->Form->input('name', ['label' => __("NAME")]);?>
    </div>
</div>
<div class="row">
    <div class="col-md-6 col-md-offset-3 col-xs-12 col-sm-12">
        <?= $this->Form->input('email', ['label' => __("EMAIL"),'disabled' => true]);?>
    </div>
</div>
<div class="row">
    <div class="col-md-6 col-md-offset-3 col-xs-12 col-sm-12">
        <?php
        $languages = ['en_GB' => 'English', 'fin' => 'Suomi','sv_FI' => 'Svenska'];
        echo $this->Form->input('language', [
            'label' => ['text' => __('LANGUAGE')],
            'options' => $languages,
            'div' => array('class' => 'form-group'),
            'class' => 'form-control'
        ]);
        ?>
    </div>
</div>
<?php if (!empty($subjects)) : ?>
    <div class="row">
        <div class="col-md-6 col-md-offset-3 col-xs-12 col-sm-12" id="subjects">
            <?= $this->Form->input('subjects', [
                'label' => ['text' => __('SUBJECTS'), 'class' => 'col-md-12 new-br'],
                'options' => $subjects,
                'div' => ['class' => 'form-group'],
                'class' => 'form-control no-border',
                'type' => 'select',
                'multiple' => 'checkbox',
                'value' => $userSubjects
            ]);
            ?>
        </div>
    </div>
<?php endif; ?>
<div class="row">
    <div class="col-md-6 col-md-offset-3 col-xs-12 col-sm-12">
        <div class="form-group">
            <button type="button" class="btn btn-green btn-block" data-toggle="modal" data-target="#change-password" id="upgrade_account">
                <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                <span id="btn_text"><?= __('CHANGE_PASSWORD'); ?></span>
            </button>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xs-12 col-md-2 col-md-offset-3">
        <?= $this->Form->input('expire_date', [
                'label' => __("ACCOUNT_EXPIRE"),
                'value' => !empty($user->expired) ? $user->expired->todatestring() : '',
                'type' => 'text',
                'disabled' => true
            ]);
        ?>
    </div>
    <div class="col-xs-12 col-md-2">
        <?php
            $allAccOptions = [
                '0' => __('TRIAL_LIMIT'),
                '1' => __('PAID_29'),
                '2' => __('PAID_49'),
                '22' => __('TRIAL_DAYS'),
                '51' => __('ADMIN')
            ];
            echo $this->Form->input('account_type', [
                'label' => __("CURRENT_PLAN"),
                'value' => $allAccOptions[$user->account_level],
                'disabled' => true
            ]);
        ?>
    </div>
    <?php if ($authUser['account_level'] != 51) : ?>
        <div class="col-xs-12 col-md-2">
            <div class="form-group text" style="margin-top: 22px;">
                <a href="javascript:void(0)" class="btn btn-green form-control" data-toggle="modal" data-target="#invoice-payment"><i class="glyphicon glyphicon-edit"></i> 
                    <?php if (!empty($authUser['customer_id']) && in_array($authUser['plan_switched'], ['CANCELLED', 'CANCELLED_DOWNGRADE'])) : ?>
                        <?= __('REACTIVATE'); ?>
                    <?php else : ?>
                        <?= in_array($authUser['account_level'], [1,2]) ? __('EDIT_PLAN') : __('UPGRADE_ACCOUNT'); ?>
                    <?php endif; ?>
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>
<hr>
<div class="row">
    <div class="col-xs-12 col-md-2 col-md-offset-3">
        <?= $this->Form->submit(__("SAVE"), ['class' => 'btn btn-info2 btn-block']);?>
    </div>
    <div class="col-xs-12 col-md-2">
        <div class="form-group">
            <?= $this->Html->link(__("CANCEL"), '/', ['class' => 'btn btn-default btn-block']);?>
        </div>
    </div>
</div>
<?= $this->Form->end(); ?>

<?= $this->element('Invoice/password'); ?>
<?php if ($authUser['account_level'] != 51) : ?>
    <?= $this->element('Invoice/invoice_success_dialog'); ?>
    <?= $this->element('Invoice/invoice_error_dialog'); ?>
    <?= $this->element('Invoice/payment', ['lang_strings' => $lang_strings]); ?>
<?php endif; ?>

<?= $this->Html->script(array('payment'), array('inline' => false)); ?>