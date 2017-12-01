<style type="text/css">
    @media (min-width: 992px) {
        .modal-v-lg {
            width: 1100px;
            height: 900px; /* control height here */
        }
    }

    #quiz-bank {
        padding: 20px;
    }
    .pbutton {
        max-width: 25px !important;
        width: auto;
    }

    .action-box {
        max-width: 100px !important;
        width: auto;
        min-width: 90px !important;
    } 

    .same-width-btn {
        width: 160px !important;
    } 

    .same-width-btn i {
        top: 3px;
    } 
    .sortable {
        font-size: 14px !important;
    }
</style>
<?php
    $lang_strings['delete_quiz_1'] = __('THERE_ARE');
    $lang_strings['delete_quiz_2'] = __('ANSWERS_COMMA');
    $lang_strings['delete_quiz_3'] = __('STUDENTS_AND');
    $lang_strings['delete_quiz_4'] = __('SURELY_DELETE');
    $lang_strings['delete_quiz_5'] = __('DELETE_QUIZ');
    $lang_strings['request_sent'] = __('UPGRADE_PENDING');
    $lang_strings['share_quiz'] = __('SHARE_QUIZ');
    $lang_strings['share_quiz_question'] = __('DO_YOU_SHARE_QUIZ');
    $lang_strings['remove_share'] = __('CANCEL_SHARE_QUIZ');
    $lang_strings['remove_share_question'] = __('DO_YOU_REMOVE_SHARING');
    $lang_strings['remove_shared_quiz'] = __('CANCEL_SHARE');
    $lang_strings['drag_drop'] = __('DRAG_DROP');
    $lang_strings['upload'] = __('CHOOSE_FILE');
    $lang_strings['validating'] = __('VALIDATING');
    $lang_strings['retry'] = __('RETRY');
    $lang_strings['processing'] = __('PROCESSING');
    $lang_strings['pay_success'] = __('PAY_SUCCESS');
    $lang_strings['pay_failed'] = __('PAY_FAILED');
    $lang_strings['try_refresh'] = __('TRY_REFRESH');

    $lang_strings['downgrade'] = __('DOWNGRADE_PLAN');
    $lang_strings['upgrade'] = __('UPGRADE_PLAN');
    $lang_strings['current_plan'] = __('CURRENT_PLAN');
    $lang_strings['reactivate'] = __('REACTIVATE_SUBSCRIPTION');
    $lang_strings['reactivate_downgrade'] = __('REACTIVATE_AND_DOWNGRADE_SUBSCRIPTION');
    $lang_strings['reactivate_upgrade'] = __('REACTIVATE_AND_UPGRADE_SUBSCRIPTION');

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

    $lang_strings['next_buy'] = __('CONTINUE_BUY_FOR_NEXT_YEAR');
    $lang_strings['upgrade_next_buy'] = __('UPGRADE_AND_BUY_FOR_NEXT_YEAR');
    $lang_strings['downgrade_next_buy'] = __('DOWNGRADE_AND_BUY_FOR_NEXT_YEAR');

    $lang_strings['invalid_card'] = __('INVALID_CARD');
    $lang_strings['invalid_expire'] = __('INVALID_EXPIRE_DATE');
    $lang_strings['invalid_cvc'] = __('INVALID_CVC');

    $lang_strings['max_attachment'] = __('MAXIMUM_ATTACHMENT');

?>
<script type="text/javascript">
    var lang_strings = <?php echo json_encode($lang_strings) ?>;
</script>
<!-- $this->Html->script(array('invoice'), array(
    'inline' => false
)); -->
<?= $this->Html->script(['jquery.tablesorter.min', 'payment'], ['inline' => false]); ?>
<?= $this->assign('title', __('MY_QUIZZES')); ?>

<?= $this->Flash->render(); ?>

<?php if (($userPermissions['days_left'] < 30) && empty($authUser['customer_id'])) : ?>
<div class="row notice" id="notice-section">
    <?php if ($userPermissions['days_left'] > 0) : // Will expire ?>
        <div class="col-xa-12 col-md-4">
            <div class="form-group text-right">
                <span class="expire-notice"><?= __('ACCOUNT_WILL_EXPIRE') ?><span class="days_left"><?= $userPermissions['days_left'] ?></span><?= __('DAYS') ?></span>
            </div>
        </div>
        <div class="col-xa-12 col-md-4 col-md-offset-4">
            <div class="form-group">
                <?php $button_text = in_array($authUser['account_level'], [1,2]) ? __('CONTINUE_SUBSCRIPTION') : __('UPGRADE_ACCOUNT'); ?>
                <?= $this->element('Invoice/invoice_button', array('btn_text' => (($authUser['account_level'] == 1) && empty($authUser['customer_id'])) ? __('SEND_INVOICE_AGAIN') : $button_text)); ?>
            </div>
        </div>
    <?php else : // Expired ?>
        <?php $expired = true; ?>
        <div class="col-xa-12 col-md-8">
            <div class="form-group text-center test-align">
                <span class="expire-notice"><?= __('ACCOUNT_EXPIRED'); ?></span>
            </div>
        </div>
        <div class="col-xa-12 col-md-4">
            <div class="form-group">
                <?= $this->element('Invoice/invoice_button', array('btn_text' => __('UPGRADE_CREATE_QUIZZ'))); ?>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<div class="row">
    <?php if (empty($expired) && (($authUser['account_level'] != 0) || ($authUser['account_level'] == 0 && empty($quiz_created)))) : ?>
        <div class="col-xa-12 col-md-4">
            <div class="form-group">
                <a href="<?php echo $this->Url->build('/quizzes/add'); ?>" class="btn btn-primary btn-block">
                    <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                    <?php echo __('CREATE_NEW_QUIZ'); ?>
                </a>
            </div>
        </div>
    <?php endif; ?>

    <?php if (empty($expired) && !empty($quiz_created)) : ?>
        <div class="col-xa-12 col-md-3 pull-right">
            <?= $this->Form->create('', ['id' => 'quiz-filter']); ?>
                <div class="form-group">
                    <div class="input select">
                        <select name="status" class="form-control" id="QuizStatus">
                            <optgroup label="<?php echo __('QUIZ_ARCHIVING'); ?>">
                                <?php 
                                    foreach ($quizTypes as $key => $value) {
                                        if ($key == $filter) {
                                            echo '<option selected="selected" value="'. $key . '">'. $value .'</option>';
                                        } else {
                                            echo '<option value="'. $key .'">'. $value .'</option>';
                                        }
                                    } 
                                ?>
                            </optgroup>
                            <optgroup label="<?php echo __('QUIZ_SHARING'); ?>">
                                <?php 
                                    foreach ($quizSharedType as $key => $value) {
                                        if ($key == $filter) {
                                            echo '<option selected="selected" value="'. $key . '">'. $value .'</option>';
                                        } else {
                                            echo '<option value="'. $key .'">'. $value .'</option>';
                                        }
                                    } 
                                ?>
                            </optgroup>
                        </select>
                    </div>                
                </div>
            <?= $this->Form->end(); ?>   
        </div>

    <?php endif; ?>
</div>
<?php if (empty($expired)) : ?>
    <!-- Quiz list -->
    <div class="panel panel-default" id="user-quizzes">
        <?php if (!empty($data['quizzes'])) : ?>
            <!-- show quiz list -->
            <table class="table">
                <thead>
                    <tr>
                        <th class="sortable header" style="border-right: 1px solid #eee"><?= __('QUIZ_SORT'); ?></th>
                        <th colspan="2"></th>
                    </tr>
                </thead>
                <tbody id="quiz-list">
                    <?php foreach ($data['quizzes'] as $id => $quiz): ?> 
                        <?php $class = empty($quiz->status) ? 'incativeQuiz' : 'activeQuiz'; ?>
                        <tr class="<?php echo $class; ?>">
                            <td style="vertical-align:middle" class="col-md-5">
                                <?php 
                                    if (empty($quiz->shared)) {
                                        echo $this->Html->link($quiz->name, array('action' => 'edit', $quiz->id) ,array('escape'=>false,'class'=>'quiz-name'));
                                    } else {
                                        if ($quiz->is_approve == 2) {
                                            echo $this->Html->link('<i class="glyphicon glyphicon-ban-circle text-danger"></i>&nbsp;' . $quiz->name, array('action' => 'edit', $quiz->id) ,array('escape'=>false, 'title' => __('Share declined') ,'class'=>'quiz-name'));
                                        } elseif ($quiz->is_approve == 1) {
                                            echo $this->Html->link('<i class="glyphicon glyphicon-share-alt text-success"></i>&nbsp;' . $quiz->name, array('action' => 'edit', $quiz->id) ,array('escape'=>false, 'title' => __('QUIZ_SHARED') ,'class'=>'quiz-name'));
                                        } else {
                                            echo $this->Html->link('<i class="glyphicon glyphicon-warning-sign text-warning"></i>&nbsp;' . $quiz->name, array('action' => 'edit', $quiz->id) ,array('escape'=>false, 'title' => __('SHARE_PENDING') ,'class'=>'quiz-name'));
                                        } 
                                    }
                                ?>
                            </td>
                            <td>
                                <?php if ($quiz->status) : ?>
                                    <?php echo $this->Html->link(__("GIVE_TEST"), '/quizzes/present/' . $quiz->id); ?>
                                <?php endif; ?>
                                <mark><?php echo $this->Html->link(__("ANSWERS") . '(' . $quiz->student_count . ')', '/quizzes/table/' . $quiz->id); ?></mark>
                            </td>
                            <td align="right">
                                <ul class="nav navbar-nav navbar-right no-margin">
                                    <li class="dropdown">
                                        <a href="#" data-toggle="dropdown" class="dropdown-toggle" aria-expanded="false">
                                            <?php echo __('ACTIONS'); ?> 
                                            <b class="caret"></b>
                                        </a>
                                        <ul class="dropdown-menu" role="menu">
                                            <?php if (!empty($quiz->questions)) : ?>
                                                <li>
                                                    <?php if (empty($quiz->shared)) : ?>
                                                        <button type="button" class="btn btn-success btn-sm share-quiz same-width-btn" quiz-id="<?php echo $quiz->id; ?>" quiz-name="<?php echo $quiz->name; ?>" title="<?php echo __('SHARE_QUIZ'); ?>"><i class="glyphicon share"></i> <?php echo __('SHARE_QUIZ'); ?></button>
                                                    <?php else : ?>
                                                        <button type="button" class="btn btn-success btn-sm remove-share same-width-btn" quiz-id="<?php echo $quiz->id; ?>" quiz-name="<?php echo $quiz->name; ?>" title="<?php echo __('CANCEL_SHARE'); ?>"><i class="glyphicon share"></i> <?php echo __('CANCEL_SHARE'); ?></button>
                                                    <?php endif; ?>
                                                </li>
                                            <?php endif; ?>
                                            <li>
                                                <button type="button" class="btn btn-danger btn-sm delete-quiz same-width-btn" quiz-id="<?php echo $quiz->id; ?>" title="<?php echo __('REMOVE_QUIZ'); ?>"><i class="glyphicon trash"></i> <?php echo __('REMOVE_QUIZ'); ?></button>
                                            </li>
                                            <li>
                                                <?php if ($quiz->status) : ?>
                                                    <button type="button" class="btn btn-default btn-sm active-quiz same-width-btn" status="<?php echo $quiz->status; ?>" id="<?php echo $quiz->id; ?>" title="<?php echo __('ARCHIVE_QUIZ'); ?>"><i class="glyphicon archive"></i> <?php echo __('ARCHIVE_QUIZ'); ?></button>
                                                <?php else: ?>
                                                    <button type="button" class="btn btn-default btn-sm active-quiz same-width-btn" status="<?php echo $quiz->status; ?>" id="<?php echo $quiz->id; ?>" title="<?php echo __('ACTIVATE_QUIZ'); ?>"><i class="glyphicon recycle"></i> <?php echo __('ACTIVATE_QUIZ'); ?></button>
                                                <?php endif; ?>
                                            </li>
                                             <li>
                                                <button type="button" class="btn btn-success btn-sm duplicate-quiz same-width-btn" quiz-id="<?php echo $quiz->id; ?>" title="<?php echo __('DUPLICATE_QUIZ'); ?>"><i class="glyphicon duplicate"></i> <?php echo __('DUPLICATE_QUIZ'); ?></button>
                                            </li>
                                            <?php if (($quiz->shared == 1) && ($quiz->is_approve == 2)) : ?>
                                                <li>
                                                    <button type="button" class="btn btn-danger btn-sm view-reason same-width-btn" quiz-id="<?php echo $quiz->id; ?>" title="<?php echo __('VIEW_DECLINE_REASON'); ?>"><i class="glyphicon glyphicon-ban-circle"></i> <?php echo __('VIEW_DECLINE_REASON'); ?></button>
                                                </li>
                                            <?php endif; ?>
                                        </ul>
                                    </li>
                                </ul>
                            </td>
                        </tr>
                        <?php echo $this->element('Quiz/decline_reason', array('quiz' => $quiz)); ?>
                    <?php endforeach; ?>
                    <!--nocache-->
                </tbody>
                <!--/nocache-->
            </table>
        <?php else : ?>
            <?php if (empty($quiz_created)) : ?>
                <!-- show dummy data installation module -->
                <div class="row">
                    <div id="demo-data">
                        <div class="col-md-10 col-md-offset-1">
                            <p class="text-center"><?php echo __('WELCOME') ?></p>
                            <p class="text-center"><?php echo __('IF_WANT_DEMOS_CLICK') . '<b>"' . __('LOAD_DEMO_QUIZZES') . '"</b>.'; ?></p>
                            <p class="text-center"><?php echo __('DELETE_DEMO_QUIZZES'); ?></p>
                            <p class="text-center"><?php echo __('IF_WANT_START_CLICK') . '<b> "' . __('CREATE_NEW_QUIZ') . '"</b>.'; ?></p>
                        </div>
                        <div class="col-md-4 col-md-offset-4"><button type="button" class="btn btn-gray btn-block" data-toggle="modal" data-target="#demo-dialog" id="upgrade_account"><span class="glyphicon glyphicon-import" aria-hidden="true"></span><span> <?php echo __('LOAD_DEMO_QUIZZES'); ?></span></button>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
        
    </div>
<?php endif; ?>

<?php //echo $this->element('Invoice/invoice'); ?>
<?php echo $this->element('Invoice/invoice_success_dialog'); ?>
<?php echo $this->element('Invoice/invoice_error_dialog'); ?>
<?php echo $this->element('Invoice/delete_confirm'); ?>
<?php echo $this->element('Invoice/demo_dialog'); ?>
<?php echo $this->element('Invoice/payment', ['lang_strings' => $lang_strings]); ?>
<?= $this->Html->script(['quiz-index'], ['inline' => false]); ?>