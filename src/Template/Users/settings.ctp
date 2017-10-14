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
                    <?php if (in_array($authUser['plan_switched'], ['CANCEL_DOWNGRADE', 'CANCEL_UPGRADE'])) : ?>
                        <?= __('REACTIVATE'); ?>
                    <?php else : ?>
                        <?= in_array($authUser['account_level'], [1,2]) ? __('EDIT_PLAN') : __('UPGRADE_ACCOUNT'); ?>
                    <?php endif; ?>
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php if ($authUser['plan_switched']) : ?>
<div class="row">
    <div class="col-md-6 col-md-offset-3 col-xs-12 col-sm-12">
        <h4 class="text-info">
            <?php 
                if ($authUser['plan_switched'] == 'CANCEL_DOWNGRADE') {
                    echo __('BENEFIT_OF_BASIC_ACCOUNT');
                } else if ($authUser['plan_switched'] == 'CANCEL_UPGRADE') {
                    echo __('BENEFIT_OF_BANK_ACCOUNT');
                } else {
                    
                }
            ?>
        </h4>
    </div>
</div>
<?php endif; ?>
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
    <?= $this->element('Invoice/payment'); ?>
<?php endif; ?>

<?= $this->Html->script(array('payment'), array('inline' => false)); ?>