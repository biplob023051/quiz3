<?php 
    // pr($given_answer);
    $templateOptions = array('header', 'youtube_video', 'image_url', 'text_field');
    if (!in_array($question->question_type->template_name, $templateOptions))
    echo $this->Form->hidden("Answer.{$question->number}.question_id", array("value" => $question->id, 'data-case' => $question->case_sensitive));
?>
<tr id="q<?php if (!in_array($question->question_type->template_name, $templateOptions)) echo $question->number; ?>"<?php if (in_array($question->question_type->template_name, $templateOptions)) : ?> class="others_type<?php if ($question->question_type->template_name == 'header') : ?> header_type<?php endif; ?>"<?php endif; ?>>
    <td id="question-col-<?php echo $question->id; ?>">                    
        <div class="row">
            <div class="col-xs-12 col-md-6">
                <p>
                    <?php if (($question->question_type->template_name == 'header')) : ?>
                        <span class="h4 header testheader"><?php echo $question->text; ?></span>
                        <br />
                    <?php elseif (($question->question_type->template_name == 'youtube_video')) : ?>
                        
                    <?php elseif (($question->question_type->template_name == 'image_url')) : ?>

                    <?php elseif (($question->question_type->template_name == 'text_field')) : ?>

                    <?php else : ?>
                        <span class="h4" id="question-title-<?php echo $question->id; ?>">
                            <?php if (!empty($question->given_answer)) : ?>
                                <span id="quest-<?php echo $question->id; ?>" class="glyphicon glyphicon-ok-sign text-success std-answer"></span>
                            <?php else : ?>
                                <span id="quest-<?php echo $question->id; ?>" class="glyphicon std-answer"></span>
                            <?php endif; ?>
                            <?php echo '<span class="question_number">' . $question->number . '</span>. ' .  $question->text; ?>    
                        </span>
                        <br />
                    <?php endif; ?>
                    <span class="text-muted"><?php echo $question->explanation ?></span>
                    <?php if (!empty($question->max_allowed)) : ?>
                        <p>
                            <span class="text-muted">
                                <strong>
                                    <?php echo __('CHOOSE_MOST'); ?>
                                </strong>
                                <span class="max_allowed"><?php echo $question->max_allowed; ?></span>
                            </span>
                        </p>
                    <?php endif; ?>
                </p>
            </div>
        </div>
        <div class="choices">
            <?php
            $disabled = (!empty($question->max_allowed) && (count($question->given_answer) == $question->max_allowed)) ? true : false;
            foreach ($question->choices as $choice) {
                $choice->number = $question->number;
                $choice->given_answer = $question->given_answer;
                $choice->disabled = $question->disabled;
                // echo '<pre>';
                // print_r($c);
                echo $this->element("Quiz/live/choice.{$question->question_type->template_name}", array('choice' => $choice));
            }
            ?>
        </div>
    </td>
</tr>