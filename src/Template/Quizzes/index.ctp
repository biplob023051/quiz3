<!-- 
$this->Html->script(array('payment'), array(
    'inline' => false
));
-->
<?=
$this->Html->script(array('invoice'), array(
    'inline' => false
));
$this->assign('title', __('MY_QUIZZES'));
?>

<?= $this->Flash->render(); ?>

<div class="row notice" id="notice-section">
<?php 
    if (empty($userPermissions['upgraded'])) { 
        if (empty($userPermissions['request_sent'])) {
            if (!empty($userPermissions['canCreateQuiz'])) {
                echo '<div class="col-xa-12 col-md-4">';
                echo '<div class="form-group text-right">';
                echo '<span class="expire-notice">' . __('ACCOUNT_WILL_EXPIRE') . ' <span class="days_left">' . $userPermissions['days_left'] . '</span> ' . __('DAYS') . '</span>';
                echo '</div>';
                echo '</div>';

                echo '<div class="col-xa-12 col-md-4 col-md-offset-4">';
                echo '<div class="form-group">';
                echo $this->element('Invoice/invoice_button', array('btn_text' => __('UPGRADE_ACCOUNT')));
                echo '</div>';
                echo '</div>';
            } else {
                echo '<div class="col-xa-12 col-md-8">';
                echo '<div class="form-group text-center test-align">';
                echo '<span class="expire-notice">' . __('ACCOUNT_EXPIRED') . '</span>';
                echo '</div>';
                echo '</div>';

                echo '<div class="col-xa-12 col-md-4">';
                echo '<div class="form-group">';
                echo $this->element('Invoice/invoice_button', array('btn_text' => __('UPGRADE_CREATE_QUIZZ')));
                echo '</div>';
                echo '</div>';
            }
        } else {
            if (!empty($userPermissions['canCreateQuiz'])) {
                echo '<div class="col-xa-12 col-md-4">';
                echo '<div class="form-group text-right">';
                echo '<span class="expire-notice">' . __('ACCOUNT_WILL_EXPIRE') . ' <span class="days_left">' . $userPermissions['days_left'] . '</span> ' . __('DAYS') . '</span>';
                echo '</div>';
                echo '</div>';

                echo '<div class="col-xa-12 col-md-4 col-md-offset-4">';
                echo '<div class="form-group">';
                echo '<button class="btn btn-primary btn-block" disabled="true"  id="upgrade_account"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span>' . __('UPGRADE_PENDING') . '</button>';
                echo '</div>';
                echo '</div>';
            } else {
                echo '<div class="col-xa-12 col-md-8">';
                echo '<div class="form-group text-center test-align">';
                echo '<span class="expire-notice">' . __('ACCOUNT_EXPIRED') . '</span>';
                echo '</div>';
                echo '</div>';

                echo '<div class="col-xa-12 col-md-4">';
                echo '<div class="form-group">';
                echo '<button class="btn btn-primary btn-block" disabled="true"  id="upgrade_account"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span>' . __('UPGRADE_PENDING') . '</button>';
                echo '</div>';
                echo '</div>';
            }
        }
    } else {
        if (($userPermissions['days_left'] < '31') && ($authUser['account_level'] == 1)) { // if expire date soon for previous paid users
            echo '<div class="col-xa-12 col-md-4">';
            echo '<div class="form-group text-right">';
            echo '<span class="expire-notice">' . __('ACCOUNT_WILL_EXPIRE') . ' <span class="days_left">' . $userPermissions['days_left'] . '</span> ' . __('DAYS') . '</span>';
            echo '</div>';
            echo '</div>';

            echo '<div class="col-xa-12 col-md-4 col-md-offset-4">';
            echo '<div class="form-group">';
            echo $this->element('Invoice/invoice_button', array('btn_text' => __('UPGRADE_ACCOUNT')));
            echo '</div>';
            echo '</div>';
        }
    }
?>
</div>

<div class="row">
    <?php if (!empty($userPermissions['canCreateQuiz'])): ?>
        <div class="col-xa-12 col-md-4">
            <div class="form-group">
                <a href="<?php echo $this->Url->build('/quizzes/add'); ?>" class="btn btn-primary btn-block">
                    <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                    <?php echo __('CREATE_NEW_QUIZ'); ?>
                </a>
            </div>
        </div>
    <?php endif; ?>

    <?php if (!empty($userPermissions['access']) && !empty($quiz_created)) : ?>
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
<?php if (!empty($userPermissions['access'])) : ?>
    <!-- Quiz list -->
    <div class="panel panel-default" id="user-quizzes">
        <?php if (!empty($data['quizzes'])) : ?>
            <!-- show quiz list -->
            <table class="table">
                <tbody id="quiz-list">
                    <?php foreach ($data['quizzes'] as $id => $quiz): ?> 
                        <?php $class = empty($quiz->status) ? 'incativeQuiz' : 'activeQuiz'; ?>
                        <tr class="<?php echo $class; ?>">
                            <td style="vertical-align:middle">
                                
                                <div style="width: 40%; float: left">
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
                                </div>
                                <div style="width: 60%; float: left">
                                    <?php if ($quiz->status) : ?>
                                        <?php echo $this->Html->link(__("GIVE_TEST"), '/quizzes/present/' . $quiz->id); ?>
                                    <?php endif; ?>
                                    <mark><?php echo $this->Html->link(__("ANSWERS") . '(' . $quiz->student_count . ')', '/quizzes/table/' . $quiz->id); ?></mark>
                                </div>
                               
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

<?php echo $this->element('Invoice/invoice'); ?>
<?php echo $this->element('Invoice/invoice_success_dialog'); ?>
<?php echo $this->element('Invoice/invoice_error_dialog'); ?>
<?php echo $this->element('Invoice/delete_confirm'); ?>
<?php echo $this->element('Invoice/demo_dialog'); ?>
<?php //echo $this->element('Invoice/payment'); ?>
<div class="modal fade" id="public-quiz" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
</div>

<script type="text/javascript">
    var lang_strings = <?php echo json_encode($lang_strings) ?>;
</script>

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
</style>