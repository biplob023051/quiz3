<?php $no_access = $this->Quiz->downloadCount(); 
// pr($quizzes);
// exit;
?>
<div class="table-responsive">
    <?php if(!empty($no_access)) : ?>
        <div class="col-md-12" id="import-error">
            <h5 class="alert alert-danger"><?php echo __('YOU_HAVE_EXCEEDED_MAX_IMPORT'); ?></h5>
        </div>
    <?php endif; ?>
   
    <table cellpadding="0" cellspacing="0"  class="table table-bordered">
        <thead>
            <tr>
                <th class="pbutton text-center"><?php echo !empty($no_access) ? $this->Form->checkbox('checkbox', array('name'=>'selectAll','label'=>false,'id'=>'selectAll','hiddenField'=>false, 'disabled' => true)) : $this->Form->checkbox('checkbox', array('name'=>'selectAll','label'=>false,'id'=>'selectAll','hiddenField'=>false));?></th>
                <th class="text-center" id="name-sort">
                    <?php if (!empty($order_field) && ($order_field == 'name') && !empty($order_type)) : ?>
                        <a href="javascript:void(0)" data-rel="<?php echo $order_type; ?>"><?php echo __('NAME'); ?></a>
                    <?php else : ?>
                        <a href="javascript:void(0)" data-rel="asc"><?php echo __('NAME'); ?></a>
                    <?php endif; ?>
                </th>
                <th class="text-center"><?php echo __('SUBJECTS'); ?></th>
                <th class="text-center"><?php echo __('CLASSES'); ?></th>
                <th class="text-center" id="created-sort">
                    <?php if (!empty($order_field) && ($order_field == 'created') && !empty($order_type)) : ?>
                        <a href="javascript:void(0)" data-rel="<?php echo $order_type; ?>"><?php echo __('CREATED'); ?></a>
                    <?php else : ?>
                        <a href="javascript:void(0)" data-rel="asc"><?php echo __('CREATED'); ?></a>
                    <?php endif; ?>
                    
                </th>
                <th class="text-center action-box"><?php echo __('ACTIONS'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($quizzes)) : ?>
                <tr>
                    <td colspan="5"><?php echo __('SHARED_QUIZ_NOT_FOUND'); ?></td>
                </tr>
            <?php else : ?>
                <?php foreach ($quizzes as $quiz): ?>
                    <tr>
                        <td class="pbutton text-center"><?php echo !empty($no_access) ? $this->Form->checkbox('checkbox', array('data-id' => $quiz->random_id,'name'=>'data[Quiz][id][]', 'class'=>'chkselect', 'disabled' => true)) : $this->Form->checkbox('checkbox', array('data-id' => $quiz->random_id,'name'=>'data[Quiz][id][]', 'class'=>'chkselect'));?></td>
                        <td class="text-center"><?php echo h($quiz->name); ?></td>
                        <?php
                            $related_subjects = '';
                            if ($quiz->subjects) {
                                $subjects = json_decode($quiz->subjects, true);
                                foreach ($subjects as $key => $subject) {
                                    if ($subject == 0) {
                                        $related_subjects .= !empty($subjectOptions[$subject]) ? $subjectOptions[$subject] : '';
                                        break;
                                    } else {
                                        $related_subjects .= !empty($subjectOptions[$subject]) ? $subjectOptions[$subject] . ', ' : '';
                                    }
                                }
                            }
                        ?>
                        <td class="text-center"><?php echo !empty($related_subjects) ? rtrim($related_subjects, ', ') : __('Undefined'); ?></td>
                        <?php
                            $related_classes = '';
                            if ($quiz->classes) {
                                $classes = json_decode($quiz->classes, true);
                                foreach ($classes as $key => $class) {
                                    if ($class == 0) {
                                        $related_classes .= !empty($classOptions[$class]) ? $classOptions[$class] : '';
                                        break;
                                    } else {
                                        $related_classes .= !empty($classOptions[$class]) ? $classOptions[$class]  . ', ' : '';
                                    }
                                }
                            }
                        ?>
                        <td class="text-center"><?php echo !empty($related_classes) ? rtrim($related_classes, ', ') : __('Undefined'); ?></td>
                        <td class="text-center"><?php echo $quiz->created; ?></td>
                        <td class="text-center action-box">
                            <button type="button" class="btn btn-success btn-sm import-quiz"<?php echo !empty($no_access) ? '  disabled' : ''; ?> random-id="<?php echo $quiz->random_id; ?>" title="<?php echo __('IMPORT_QUIZ'); ?>"><i class="glyphicon glyphicon-save"></i></button>
                            <button type="button" class="btn btn-success btn-sm view-quiz" random-id="<?php echo $quiz->random_id; ?>" title="<?php echo __('PREVIEW_QUIZ'); ?>"><i class="glyphicon glyphicon-search"></i></button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php if (!empty($quizzes)) : ?>
    <div class="row">
        <div class="col-md-10 col-offset-md-2">
            <button type="button" class="btn btn-success btn-sm multiple-import-quiz"<?php echo !empty($no_access) ? '  disabled' : ''; ?> title="<?php echo __('IMPORT_SELECTED'); ?>"><i class="glyphicon glyphicon-save"></i><?php echo __('IMPORT_SELECTED'); ?></button>
        </div>
    </div>
<?php endif; ?>
<div class="row">
    <div class="col-md-12 text-center">
        <ul class="pagination pagination-sm">
            <?php 
            echo $this->Paginator->prev('&larr; ' . __('PREVIOUS'),array('tag'=>'li','escape'=>false),'<a>&larr; '. __('PREVIOUS') .'</a>',array('class'=>'disabled','tag'=>'li','escape'=>false));
            echo $this->Paginator->numbers(array('tag'=>'li','separator'=>null,'currentClass'=>'active','currentTag'=>'a','modulus'=>'4','first' => 2, 'last' => 2,'ellipsis'=>'<li><a>...</a></li>'));
            echo $this->Paginator->next(__('NEXT') . ' &rarr;',array('tag'=>'li','escape'=>false),'<a>&rarr; '. __('NEXT') .'</a>',array('class'=>'disabled','tag'=>'li','escape'=>false));
            ?>
        </ul>
    </div>
</div>