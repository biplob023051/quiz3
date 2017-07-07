<div class="modal-dialog modal-v-lg">
    <div class="modal-content">
        <div class="modal-header">
            <?php echo __('PUBLIC_QUIZZES'); ?>
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        </div>
        <div class="modal-body" id="quiz-bank">
        <?php //pr(array_keys($classOptions)); ?>
            <div class="row">
                <div class="col-md-12">
                    <?php 
                        echo $this->Form->input('subjects', array(
                            'templates' => [ 
                                'checkboxWrapper' => '<div class="subjects no-border">{{label}}</div>',
                            ],
                            'options' => $subjectOptions,
                            'type' => 'select',
                            'multiple' => 'checkbox',
                            'value' => empty($selectedSubjects) ? array_keys($subjectOptions) : $selectedSubjects,
                            'label' => false
                        ));
                    ?>
                </div>
                <div class="col-md-12">
                    <?php 
                        echo $this->Form->input('classes', array(
                            'templates' => [ 
                                'checkboxWrapper' => '<div class="classes no-border">{{label}}</div>',
                            ],
                            'options' => $classOptions,
                            'type' => 'select',
                            'multiple' => 'checkbox',
                            'value' => array_keys($classOptions),
                            'label' => false
                        ));
                    ?>
                </div>
            </div>

            <div class="row" id="alert-box" style="display: none;">
                <div class="alert alert-success">
                    <span class="close">&times;</span> 
                    <?php echo __('QUIZ_IMPORTED'); ?>
                </div>
            </div>
        
            <div class="row" id="pagination_content">
                <?php echo $this->element('Quiz/quiz_bank_pagination'); ?>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('CLOSE'); ?></button>
        </div>
    </div>
</div>

<style type="text/css">
    .no-border {
        float: left;
        padding-right: 10px;
    }
</style>