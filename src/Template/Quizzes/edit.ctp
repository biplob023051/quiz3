<?php
$this->Html->script(array(
    'jquery-ui',
    /* production */
    //'https://cdnjs.cloudflare.com/ajax/libs/handlebars.js/2.0.0/handlebars.min.js',
    'handlebars.min',
    'jquery.serializejson.min',
    'webquiz',
    'edit',
    //'qunit-1.17.1',
    //'tests/edit'
        ), array('inline' => false)
);
$this->Html->css(array(
    //'qunit-1.17.1'
    'jquery-ui'
        ), array('inline' => false)
);
$this->assign('title', __('Edit Quiz'));

if (!empty($data['Quiz']['subjects'])) {
    $selectedSubjects = json_decode($data['Quiz']['subjects'], true);
} else {
    $selectedSubjects = array_keys($subjectOptions); // By default all subjects
}

if (!empty($data['Quiz']['classes'])) {
    $selectedClasses = json_decode($data['Quiz']['classes'], true);
} else {
    $selectedClasses = array_keys($classOptions); // By default all classes
}
?>

<?php echo $this->Session->flash('error'); ?>

<?php
echo $this->Form->create('Quiz', array(
    'inputDefaults' => array(
        'label' => array('class' => 'sr-only'),
        'div' => array('class' => 'form-group'),
        'class' => 'form-control',
    )
));
?>
<!--<div id="qunit"></div>
<div id="qunit-fixture"></div>-->
<div class="row" id="settings">
    <div class="col-xs-12 col-md-12">
        <a href="javascript:void(0)" class="btn btn-default btn-block" id="show-settings">
            <span class="caret-down caret-icon">
                <span class="caret"></span>
            </span>
            <span class="caret caret-right caret-icon"></span>
            <?php echo __('Quiz Settings'); ?>
        </a>
    </div>
    <div class="col-xs-12 col-md-12 settings-options" style="display: none;">
        <div class="form-group">
            <?php 
                echo $this->Form->checkbox('show_result', array('default' => $data['Quiz']['show_result'])); 
                echo $this->Form->label('show_result', __('Show results to the student after finishing the quiz.'));
            ?>
        </div>
        <div class="form-group">
            <?php 
                echo $this->Form->checkbox('anonymous', array('default' => $data['Quiz']['anonymous'])); 
                echo $this->Form->label('anonymous', __('Anonymous participation?'));
            ?>
        </div>
    </div>
    <div class="col-xs-12 col-md-12 settings-options" style="display: none;">
        <hr>
    </div>
    <div class="col-md-12 settings-options" style="display: none;">
        <?php
            echo $this->Form->input('subjects', array(
                'options' => $subjectOptions,
                'div' => array('class' => 'form-group'),
                'class' => 'subjects no-border',
                'type' => 'select',
                'multiple' => 'checkbox',
                'selected' => $selectedSubjects,
                'label' => false
            ));
        ?>
    </div>
    <div class="col-xs-12 col-md-12 settings-options" style="display: none;">
        <hr>
    </div>
    <div class="col-md-12 settings-options" style="display: none;">
        <?php
            echo $this->Form->input('classes', array(
                'options' => $classOptions,
                'div' => array('class' => 'form-group'),
                'class' => 'classes no-border',
                'type' => 'select',
                'multiple' => 'checkbox',
                'selected' => $selectedClasses,
                'label' => false
            ));
        ?>
    </div>
</div>
<div class="panel panel-primary">
    <div class="panel-heading">
        <div class="row">
            <div class="col-xs-12 col-md-6">
                <?php
                if (isset($initial)) {
                    echo $this->Form->input('Quiz.name', array(
                        'placeholder' => __('Name the quiz'),
                        'class' => 'form-control input-lg'
                    ));
                } else {
                    echo $this->Form->input('Quiz.name', array(
                        'default' => $data['Quiz']['name'],
                        'placeholder' => __('Name the quiz'),
                        'class' => 'form-control input-lg'
                    ));
                }
                ?>
            </div>
            <div class="col-xs-12 col-md-6">
                <?php
                echo $this->Form->input('Quiz.description', array(
                    'default' => $data['Quiz']['description'],
                    'placeholder' => __('Describe the quiz to respondents'),
                    'class' => 'form-control input-lg'
                ));
                ?>
            </div>
        </div>
    </div>
    <?php echo $this->Form->end(); ?>
    <table class="table table-striped" id="questions">
        <tbody>
            <!--nocache-->
            <?php
            $i = 1;
            $othersQuestionType = array(6, 7, 8); // this categories for others type questions
            foreach ($data['Question'] as $question) {
                
                $choices_number = count($question['Choice']);
                if (!$question['QuestionType']['multiple_choices'] && $choices_number > 1) {
                    for ($i = 1; $i < $choices_number; ++$i) {
                        unset($question['Choice'][$i]);
                    }
                }

                $question['number'] = $i;
                echo $this->element('Quiz/edit/question', $question);
                if (!in_array($question['question_type_id'], $othersQuestionType)) { 
                    // only considered main question for numbering
                    // not others type questions
                    ++$i;
                }
            }
            ?>
            <!--/nocache-->
        </tbody>
    </table>

</div>

<div class="row">
    <div class="col-xs-12 col-md-3 col-md-offset-6">
        <div class="form-group">
            <button type="button" class="btn btn-primary btn-lg btn-block" id="add-question"><?php echo __('Add New Question') ?></button>

        </div>
    </div>
    <div class="col-xs-12 col-md-3">
        <div class="form-group">
            <input type="submit" class="btn btn-default btn-lg btn-block" id="submit-quiz" value="<?php echo __('Finish'); ?>" />
        </div>
    </div>
</div>

<script id="app-data" type="application/json">
<?php
echo json_encode(array(
    'baseUrl' => $this->Html->url('/', true),
    'questionTypes' => $data['QuestionTypes'],
    'quizId' => $data['Quiz']['id']
));
?>
</script>

<script id="question-preview-template" type="text/x-handlebars-template">
<?php echo $this->element('Quiz/edit/Handlebars/question.preview'); ?>
</script>

<script id="question-edit-template" type="text/x-handlebars-template">
<?php echo $this->element("Quiz/edit/Handlebars/question.edit", $data); ?>
</script>


<?php foreach ($data['QuestionTypes'] as $qt): ?>

    <script id="choice-<?php echo $qt['QuestionType']['template_name'] ?>-edit-template" type="text/x-handlebars-template">
    <?php echo $this->element("Quiz/edit/Handlebars/choice.{$qt['QuestionType']['template_name']}.edit"); ?>
    </script>

    <script id="choice-<?php echo $qt['QuestionType']['template_name'] ?>-preview-template" type="text/x-handlebars-template">
        <?php echo $this->element("Quiz/edit/Handlebars/choice.{$qt['QuestionType']['template_name']}.preview"); ?>
    </script>

<?php endforeach; ?>

<script type="text/javascript">
    <?php if (!empty($initial)) : ?>
        var initial = true;
    <?php else : ?>
        var initial = false;
    <?php endif; ?>
    <?php if (!empty($no_question)) : ?>
        var no_question = true;
    <?php else : ?>
        var no_question = false;
    <?php endif; ?>
    var lang_strings = <?php echo json_encode($lang_strings) ?>;
</script>

<style type="text/css">
.placeholder {
    border: 1px solid green;
    background-color: white;
    -webkit-box-shadow: 0px 0px 10px #888;
    -moz-box-shadow: 0px 0px 10px #888;
    box-shadow: 0px 0px 10px #888;
    width: 50%;
}
.tile {
    height: 70px;
}
.grid {
    margin-top: 1em;
}
#settings {
    margin: 5px 0px;
}
#show-settings {
    text-align: left;
}
#settings .col-md-12 {
    padding: 0;
    margin: 0;
} 
.settings-options {
    padding: 0px 50px !important;
}
.settings-options label {
    padding: 0 5px !important;
}
.settings-options .form-group {
    margin-bottom: 0px;
}
.no-border {
    float: left;
    padding-right: 10px;
}
.caret-down {
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

</style>