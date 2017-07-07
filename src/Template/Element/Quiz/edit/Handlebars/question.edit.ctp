{{#if QDisplay}}
    <tr id="q{{id}}" class="EditQuestionBorder">
{{else}}
    <tr id="q{{id}}" class="EditQuestionBorder" style="display: none;">
{{/if}}
    <td>      
        <?php echo $this->Form->create('Question', ['id' => 'QuestionEditForm']); ?>
        <div class="row">
            <div class="col-md-6 col-xs-12">
                <div class="form-group">
                    <?php
                    echo $this->Form->text('data.Question.text', array(
                        'class' => 'form-control q-text',
                        'placeholder' => __('ENTER_QUESTION'),
                        'value' => '{{text}}',
                        'label' => false,
                        'type' => 'text',
                        'id' => 'QuestionText'
                    ));
                    ?>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="form-group">
                    <?php
                    $_data = array();
                    $_others = array();
                    foreach ($QuestionTypes as $qt) {
                        if (empty($qt['QuestionType']['type'])) {
                            $_data[$qt['QuestionType']['id']] = __($qt['QuestionType']['name']);
                        } else {
                            $_others[$qt['QuestionType']['id']] = __($qt['QuestionType']['name']);
                        }
                    }
                    //array_unshift($_data, __('Question types'));
                    // echo $this->Form->input('Question.question_type_id', array(
                    //     'options' => $_data,
                    //     'default' => $data['QuestionTypes'][0]['QuestionType']['id'],
                    //     'class' => 'form-control choice-type-selector',
                    //     'label' => false,
                    //     'id' => 'qs-{{id}}'
                    // ));
                    ?>
                    

                    <div class="form-group">
                        <div class="input select">
                            <select name="data[Question][question_type_id]" class="form-control choice-type-selector" id="qs-{{id}}">
                                <optgroup label="<?php echo __('QUESTION_TYPES'); ?>">
                                    <?php foreach ($_data as $key => $value) : ?>
                                        <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                                    <?php endforeach; ?>
                                </optgroup>
                                <optgroup label="<?php echo __('OTHERS'); ?>">
                                    <?php foreach ($_others as $key => $value) : ?>
                                        <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                                    <?php endforeach; ?>
                                </optgroup>
                            </select>
                        </div>                
                    </div>

                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 col-xs-12">
                <div class="form-group">
                    <?php
                    echo $this->Form->input('data.Question.explanation', array(
                        'class' => 'form-control q-explanation',
                        'placeholder' => __('EXPLANATION_TEXT'),
                        'value' => '{{explanation}}',
                        'label' => false,
                        'type' => 'text',
                        'id' => 'QuestionExplanation'
                    ));
                    ?>
                </div>           
            </div>
        </div>
        <div class="row" id="max_allowed" style="display: none;">
            <div class="col-md-6 col-xs-12">
                <div class="form-group">
                    <?php
                    echo $this->Form->input('data.Question.max_allowed', array(
                        'class' => 'form-control q-max_allowed',
                        'placeholder' => __('MAX_ALLOWED'),
                        'value' => '{{max_allowed}}',
                        'label' => false,
                        'type' => 'number',
                        'min' => 1,
                        'id' => 'QuestionMaxAllowed'
                    ));
                    ?>
                </div>           
            </div>
        </div>
        <div class="choices">
            {{#choice Choice}}
            {{choice_tpl}}
            {{/choice}}
        </div>

        <button type="button" class="btn btn-success add-choice" style="margin:16px 0 5px;"><?php echo __('ADD_CHOICE') ?></button>
        
            <button type="button" class="btn btn-primary pull-right edit-done" style="margin:16px 0 5px;"><?php echo __('SAVE_QUESTION') ?></button>
        
        <?php echo $this->Form->end(); ?>
    </td>
</tr>