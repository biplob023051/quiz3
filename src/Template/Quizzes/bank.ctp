<style type="text/css">
    .subjects, .classes {
        width: 33% !important;
        float: left;
        margin-top: 0px;
    }   
    .subjects input[type=checkbox], .classes input[type=checkbox] {
        margin-right: 5px !important;
    }
    #show-settings {
        text-align: left;
    }
    .caret-down, .settings-options {
        display: none;
    }
    .caret {
        border-left: 4px solid transparent;
        border-right: 4px solid transparent;
        border-top: 4px solid #000000;
        display: inline-block;
        height: 0;
        opacity: 0.3;
        vertical-align: middle;
        width: 0;
    }
    .caret-right {
        border-bottom: 4px solid transparent;
        border-top: 4px solid transparent;
        border-left: 4px solid #000000;
        display: inline-block;
        height: 0;
        opacity: 0.3;
        vertical-align: middle;
        width: 0;
    }
    .no-border {
        float: left;
        padding-right: 10px;
    }
</style>
<?=
$this->Html->script(['quiz-bank'.$minify], ['inline' => false]);
$this->assign('title', $title_for_layout);
?>
<?= $this->Flash->render(); ?>
<div class="row">
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="row">
                <div class="col-xs-12 col-md-12 m-b-10">
                    <a href="javascript:void(0)" class="btn btn-default btn-block" id="show-settings">
                        <span class="caret-down caret-icon">
                            <span class="caret"></span>
                        </span>
                        <span class="caret caret-right caret-icon"></span>
                        <?php echo __('SUBJECT_N_CLASSES'); ?>
                    </a>
                </div>
                <div class="col-md-12 settings-options">
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
                <div class="col-xs-12 col-md-12 settings-options"><hr></div>
                <div class="col-md-12 settings-options">
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