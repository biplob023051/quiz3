{{#if points}}
    <div class="row choice-<?php echo '{{id}}'; ?>">
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 make-sortable">
            <div class="pull-right choice-arrow">
                <i class="glyphicon glyphicon-resize-vertical"></i>
            </div>
            <div class="well well-sm">
                <div class="row">
                    <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1" align="right">
                        <label>
                            <input type="radio" disabled  />
                        </label>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                        <?php
                        echo $this->Form->input('data.Choice.{{id}}.text', array(
                            'default' => '{{text}}',
                            'class' => 'form-control c-text',
                            'label' => false,
                            'placeholder' => __('CHOICE'),
                            'id' => 'Choice{{id}}Text',
                            'value' => '{{text}}'
                        ));
                        ?>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-4 col-xs-4">
                        <?php
                        echo $this->Form->input('data.Choice.{{id}}.points', array(
                            'class' => 'form-control c-points',
                            'placeholder' => __('POINTS'),
                            'default' => '{{points}}',
                            'label' => false,
                            'type' => 'number',
                            'step' => '1',
                            'id' => 'Choice{{id}}Points',
                            'value' => '{{points}}'
                        ));
                        ?>
                    </div>
                    <?php
                    echo $this->Form->input('data.Choice.{{id}}.weight', array(
                        'class' => 'c-weight',
                        'type' => 'hidden',
                        'default' => '{{weight}}',
                        'id' => 'Choice{{id}}Weight',
                        'value' => '{{weight}}'
                    ));
                    ?>
                    <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
                        <?php echo $this->Form->button('<i class="glyphicon close"></i>', array('type' => 'button', 'choice' => '{{id}}', 'class' => 'remove-choice', 'title' => __('REMOVE_CHOICE'))); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
{{else}}
    <div class="row choice-<?php echo '{{id}}'; ?>"> 
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 make-sortable">
            <div class="pull-right choice-arrow">
                <i class="glyphicon glyphicon-resize-vertical"></i>
            </div>
            <div class="well well-sm">
                <div class="row">
                    <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1" align="right">
                        <label>
                            <input type="radio" disabled  />
                        </label>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                        <?php
                        echo $this->Form->input('data.Choice.{{id}}.text', array(
                            'default' => '{{text}}',
                            'class' => 'form-control c-text',
                            'label' => false,
                            'placeholder' => __('CHOICE'),
                            'id' => 'Choice{{id}}Text',
                            'value' => '{{text}}'
                        ));
                        ?>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-4 col-xs-4">
                        <?php
                        echo $this->Form->input('data.Choice.{{id}}.points', array(
                            'class' => 'form-control c-points',
                            'placeholder' => __('POINTS'),
                            'default' => 0,
                            'label' => false,
                            'type' => 'number',
                            'step' => '1',
                            'id' => 'Choice{{id}}Points',
                            'value' => '{{points}}'
                        ));
                        ?>
                    </div>
                    <?php
                    echo $this->Form->input('data.Choice.{{id}}.weight', array(
                        'class' => 'c-weight',
                        'type' => 'hidden',
                        'default' => '{{weight}}',
                        'id' => 'Choice{{id}}Weight',
                        'value' => '{{weight}}'
                    ));
                    ?>
                    <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
                        <?php echo $this->Form->button('<i class="glyphicon close"></i>', array('type' => 'button', 'choice' => '{{id}}', 'class' => 'remove-choice', 'title' => __('REMOVE_CHOICE'))); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
{{/if}}