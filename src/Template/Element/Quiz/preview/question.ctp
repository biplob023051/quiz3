<?php 
    $templateOptions = array('header', 'youtube_video', 'image_url');
    $inlinePointTemplates = array('short_auto', 'short_manual', 'essay');
?>
<tr id="q<?php echo $question->id ?>"<?php if (in_array($question->question_type->template_name, $templateOptions)) : ?> class="others_type<?php if ($question->question_type->template_name == 'header') : ?> header_type<?php endif; ?>"<?php endif; ?>>
<td>
    <div class="row">
        <div class="col-xs-12 col-md-6">            
            <p>
                <?php if ($question->question_type->template_name == 'header') : ?>
                    <span class="h4 header"><?php echo $question->text; ?></span>
                    <br />
                <?php elseif ($question->question_type->template_name == 'youtube_video') : ?>
                    
                <?php elseif ($question->question_type->template_name == 'image_url') : ?>
                    
                <?php else : ?>
                    <span class="h4"><?php echo '<span class="question_number">' . $question->number . '</span>. ' .  $question->text; ?></span>
                    <br />
                <?php endif; ?>
                <span class="text-muted"><?php echo $question->explanation ?></span>
                <?php if (!empty($question->max_allowed)) : ?>
                    <p>
                        <span class="text-muted">
                            <strong>
                                <?php echo __('Choose at most'); ?>
                            </strong>
                            <?php echo $question->max_allowed; ?>
                        </span>
                    </p>
                <?php endif; ?>
                <?php if (!empty($question->case_sensitive)) : ?>
                    <p>
                        <span class="text-muted">
                            <strong>
                                <?php echo __('Demand exact upper- and lowercase letters'); ?>
                            </strong>
                        </span>
                    </p>
                <?php endif; ?>
            </p>
        </div>
        <?php if (empty($class_preview)) : ?>
            <div class="col-xs-12 col-md-3">
                <?php 
                    if (in_array($question->question_type->template_name, $inlinePointTemplates)) :
                        echo !empty($question->choices[0]->points) ? $question->choices[0]->points : '0.00';
                    endif; 
                ?>
            </div>
        <?php endif; ?>
    </div>
    <div class="preview"> <!-- before it was choices -->
        <?php
        foreach ($question->choices as $c) {
            echo $this->element("Quiz/preview/choice.{$question->question_type->template_name}", array('choice' => $c, 'class_preview' => !empty($class_preview) ? $class_preview : false));
        }
        ?>
    </div>  
</td>                      
</tr>