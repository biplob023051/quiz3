<?= $this->Form->create('Student'); ?>

<div class="panel panel-primary" id="small-margin">
    <div class="panel-heading">
        <div class="row">
            <div class="col-xs-12 col-md-12">
                <h4><?php echo __('Quiz student view'); ?></h4>
            </div>
        </div>
        <?php if (empty($data->anonymous)) : ?>
            <div class="row">
                <div class="col-xs-12 col-md-4">
                    <?= $this->Form->input('fname', ['placeholder' => __('First Name'), 'label' => false]); ?>
                </div>
                <div class="col-xs-12 col-md-4">
                    <?= $this->Form->input('lname', ['placeholder' => __('Last Name'), 'label' => false]);
                    ?>
                </div>
                <div class="col-xs-12 col-md-4">
                    <?= $this->Form->input('class', ['placeholder' => __('Class'), 'label' => false]);?>
                </div>
            </div>
        <?php endif; ?>
        <div class="row">
            <div class="col-xs-12 col-md-12">
                <p><?= $data->description; ?></p>
            </div>
        </div>
    </div>
    <div class="panel-body">
        <table class="table table-condensed" id="questions">
            <tbody>
                <?php
                $i = 1;
                $othersQuestionType = array(6, 7, 8); // this categories for others type questions
                $answered = array();
                foreach ($data['questions'] as $question) {
                    $question['given_answer'] = '';
                    $choices_number = count($question->choices);
                    if (!$question['question_type']['multiple_choices'] && $choices_number > 1) {
                        for ($i = 1; $i < $choices_number; ++$i) {
                            unset($question->choices[$i]);
                        }
                    }
                    $question['number'] = $i;
                    echo $this->element('Quiz/preview/question', array('question' => $question));
                    if (!in_array($question->question_type_id, $othersQuestionType)) { 
                        // only considered main question for numbering
                        // not others type questions
                        ++$i;
                    }
                }
                ?>
            </tbody>
        </table>
        <div class="row">
            <div class="col-xs-12 col-md-3 pull-right">
                <button type="button" class="btn btn-primary btn-lg btn-block" data-dismiss="modal"><?php echo __('Close') ?></button>
                <!-- <button type="button" class="btn btn-default" data-dismiss="modal"><?php //echo __('Close'); ?></button> -->
            </div>
        </div>
    </div>
</div>
<?php echo $this->Form->end(); ?>

<style type="text/css">
    /*.modal-backdrop {
        
    }*/

    #small-margin {
        margin: 10px 20px;
    }

    .modal-backdrop {
       background-color: #fff;
    }
</style>