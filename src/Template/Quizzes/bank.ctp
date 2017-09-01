<?=
$this->Html->script(array('quiz-bank'), array(
    'inline' => false
));
$this->assign('title', $title_for_layout);
?>
<?= $this->Flash->render(); ?>
<div class="row">
    <div class="panel panel-default">
        <div class="panel-body">
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
    </div>
</div>

<div class="modal fade" id="preview-quiz" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
</div>

<script type="text/javascript">
    var lang_strings = <?php echo json_encode($lang_strings) ?>;
</script>

<style type="text/css">
    .no-border {
        float: left;
        padding-right: 10px;
    }
</style>