<div class="row">
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
        <div class="well well-sm">
            <div class="row">
                <div class="col-xs-12 col-md-12">
                    <?php
                    echo $this->Form->input('data.Choice.{{id}}.text', array(
                        'default' => '{{text}}',
                        'div' => array('class' => 'form-group'),
                        'class' => 'form-control c-text',
                        'label' => false,
                        'placeholder' => __('SEPARATE_SEMICOLON'),
                        'id' => 'Choice{{id}}Text'
                    ));
                    ?>
                </div>
            </div>
            <br />
            <div class="row">
                <div class="col-xs-9 col-md-9">
                {{#if case_sensitive}}
                    <?php
                    echo $this->Form->input('data.Question.case_sensitive', array(
                        'value' => 1,
                        'label' => array('text' => __('DEMAND_UPPER_LOWERCASE'), 'class' => 'control-label'),
                        'type' => 'checkbox',
                        'id' => 'case-sensivity',
                        'checked' => "{{case_sensitive}}"
                    ));
                    ?>
                {{else}}
                    <?php
                    echo $this->Form->input('data.Question.case_sensitive', array(
                        'value' => 1,
                        'label' => array('text' => __('DEMAND_UPPER_LOWERCASE'), 'class' => 'control-label'),
                        'type' => 'checkbox',
                        'id' => 'case-sensivity'
                    ));
                    ?>
                {{/if}}
                </div>
                <div class="col-xs-3 col-md-3">
                    <?php
                    echo $this->Form->input('data.Choice.{{id}}.points', array(
                        'default' => '{{points}}',
                        'div' => array('class' => 'form-group'),
                        'class' => 'form-control c-points',
                        'label' => false,
                        'placeholder' => __('POINTS'),
                        'id' => 'Choice{{id}}Points',
                        'type' => 'number',
                        'step' => '0.01'
                    ));
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>