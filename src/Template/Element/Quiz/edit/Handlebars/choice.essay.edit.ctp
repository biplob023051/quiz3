<div class="row">
    <div class="col-xs-12 col-md-6">
        <div class="well well-sm">
            <div class="row">
                <div class="col-xs-12 col-md-12">
                    <?php
                    echo $this->Form->input('data.Choice.{{id}}.text', array(
                        'default' => '{{text}}',
                        'disabled' => 'disabled',
                        'div' => array('class' => 'form-group'),
                        'class' => 'form-control c-text',
                        'label' => false,
                        'type' => 'textarea',
                        'id' => false
                    ));
                    ?>
                </div>
            </div>
            <br />
            <div class="row">
                <div class="col-xs-7 col-md-4 col-xs-offset-5 col-md-offset-8">
                    <?php
                    echo $this->Form->input('data.Choice.{{id}}.text', array(
                        'default' => '{{points}}',
                        'div' => array('class' => 'form-group'),
                        'class' => 'form-control c-points',
                        'label' => false,
                        'type' => 'number',
                        'step' => '1',
                        'min' => '1',
                        'placeholder' => __("MAX_POINTS")
                    ));
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>