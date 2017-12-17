<?php $this->assign('title', __('SHARED_QUIZZES')); ?>
<?= $this->Flash->render(); ?>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><span class="glyphicon glyphicon-th"></span> <b><?= $title_for_layout;?></b></h3>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-xa-12 col-md-4 pull-right">
                <?= $this->Form->create('', ['id' => 'quiz-filter']); ?>
                    <?php
                    echo $this->Form->input('Quiz.is_approve', array(
                        'options' => array('all' => __('ALL'), '3' => __('PENDING_QUIZZES'), '1' => __('APPROVED_QUIZZES'), '2' => __('DECLINED_QUIZZES')),
                        'div' => array('class' => 'form-group'),
                        'default' => $filter,
                        'class' => 'form-control',
                        'label' => false
                    ));
                    ?>
                <?= $this->Form->end(); ?>    
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 text-right font-10 font-bold">
                <?php 
                    echo $this->Paginator->counter(
                        'Page {{page}} of {{pages}}, showing {{current}} records out of
                         {{count}} total, starting on record {{start}}, ending on {{end}}'
                    ); 
                ?>
            </div>
        </div> 
        <br>
        <!-- Quiz list -->
        <div class="table-responsive">
            <table cellpadding="0" cellspacing="0"  class="table table-bordered">
                <thead>
                    <tr>
                        <th class="sl-no"><?php echo __('Sl No');?></th>
                        <th class="text-center"><?php echo $this->Paginator->sort('name', __('NAME')); ?></th>
                        <th class="text-center"><?php echo $this->Paginator->sort('Users.name', __('Created By')); ?></th>
                        <th class="text-center"><?php echo $this->Paginator->sort('created', __('CREATED')); ?></th>
                        <th class="text-center"><?php echo $this->Paginator->sort('is_approve', __('STATUS')); ?></th>                
                        <th class="text-center action-box"><?php echo __('ACTIONS'); ?></th>
                    </tr>
                </thead>
                <tbody id="quiz-list">
                    <?php $sl_no=($this->request->params['paging']['Quizzes']['page']-1) * $this->request->params['paging']['Quizzes']['perPage']; ?>
                    <?php if (count($quizzes) > 0) : ?>
                        <?php foreach ($quizzes as $id => $quiz): ?> 
                            <tr>
                                <td class="valign-center"><?php echo ++$sl_no;?></td>
                                <td class="text-center valign-center">
                                   <?php
                                        if ($quiz->is_approve == 2) {
                                            echo '<i class="glyphicon glyphicon-ban-circle text-danger"></i>&nbsp;' . h($quiz->name);
                                        } elseif (!empty($quiz->parent_quiz_id)) {
                                            echo $this->Html->link('<i class="glyphicon glyphicon-share-alt text-success"></i>&nbsp;' . $quiz->name, array('action' => 'edit', $quiz->id, 'prefix' => false) ,array('escape'=>false));
                                        } else {
                                            echo '<i class="glyphicon glyphicon-warning-sign text-warning"></i>&nbsp;' . h($quiz->name);
                                        } 
                                    ?>
                                </td>
                                <td class="text-center valign-center">
                                    <?php 
                                        echo $quiz->user->name;
                                    ?>
                                </td>
                                <td class="text-center valign-center"><?php echo h($quiz->created); ?></td>
                                <td class="text-center valign-center">
                                    <?php
                                        if (empty($quiz->is_approve)) {
                                            echo '<span class="">' . __('Pending') . '</span>';
                                        } else if ($quiz->is_approve == 1) {
                                            echo '<span class="text-success">' . __('Approved') . '</span>';
                                        } else if ($quiz->is_approve == 2) {
                                            echo '<span class="text-danger">' . __('Declined') . '</span>';
                                        } else {
                                            // Do nothing
                                        }
                                    ?>
                                </td>
                                <td class="text-center action-box">
                                    <button type="button"<?php echo ($quiz->is_approve == 1) ? 'disabled=true' : ''; ?> class="btn btn-success btn-sm approve-quiz" quiz-name="<?php echo h($quiz->name); ?>" quiz-id="<?php echo $quiz->id; ?>" title="<?php echo __('Approve Quiz'); ?>"><i class="glyphicon glyphicon-ok"></i></button>
                                    <button type="button"<?php echo ($quiz->is_approve == 2) ? 'disabled=true' : ''; ?> class="btn btn-danger btn-sm decline-quiz" quiz-name="<?php echo h($quiz->name); ?>" parent-id="<?php echo !empty($quiz->parent_quiz_id) ? $quiz->parent_quiz_id : ''; ?>" quiz-id="<?php echo $quiz->id; ?>" title="<?php echo __('Decline Quiz'); ?>"><i class="glyphicon glyphicon-remove"></i></button>
                                    <?php echo $this->Html->link('<i class="glyphicon glyphicon-fullscreen"></i>', array('action' => 'preview', $quiz->id, '?' => array('redirect_url' => $this->request->here)) ,array('escape'=>false,'class'=>'btn btn-success btn-sm', 'target' => 'blank', 'title' => __('Preview Quiz'))); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr><td colspan="6"><?= __('QUIZ_HAS_NOT_FOUND'); ?></td></tr>
                    <?php endif; ?>
                    <!--nocache-->
                </tbody>
                <!--/nocache-->
            </table>
        </div>
        <div class="row">
            <div class="col-md-12 text-center">
                <ul class="pagination pagination-sm">
                    <?php echo $this->Paginator->prev('&larr; ' . __('PREVIOUS'),array('tag'=>'li','escape'=>false),'<a>&larr; '. __('PREVIOUS') .'</a>',array('class'=>'disabled','tag'=>'li','escape'=>false));
                    echo $this->Paginator->numbers(array('tag'=>'li','separator'=>null,'currentClass'=>'active','currentTag'=>'a','modulus'=>'4','first' => 2, 'last' => 2,'ellipsis'=>'<li><a>...</a></li>'));
                    echo $this->Paginator->next(__('NEXT') . ' &rarr;',array('tag'=>'li','escape'=>false),'<a>&rarr; '. __('NEXT') .'</a>',array('class'=>'disabled','tag'=>'li','escape'=>false));?>
                </ul>
            </div>
        </div>
    </div>
</div>

<?= $this->element('common-modal'); ?>

<script type="text/javascript">
    var lang_strings = <?php echo json_encode($lang_strings) ?>;
</script>
<style type="text/css">
    .sl-no {
        max-width: 32px !important;
        min-width: 32px !important;
        width: auto;
    }

    .action-box {
        max-width: 150px !important;
        width: auto;
        min-width: 120px !important;
    } 

    .table>tbody>tr>td.valign-center {
        vertical-align: middle;
    }
</style>

<?= $this->Html->script(['admin-shared'.$minify], ['inline' => false]); ?>