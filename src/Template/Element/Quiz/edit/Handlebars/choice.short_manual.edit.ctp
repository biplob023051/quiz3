<div class="row">
    <div class="col-xs-12 col-md-6">
        <div class="well well-sm">
            <div class="row">
                <div class="col-xs-7 col-md-4 col-xs-offset-5 col-md-offset-8">
                    <?php
                    echo $this->Form->input('data.Choice.{{id}}.points', array(
                        'default' => '{{points}}',
                        'div' => array('class' => 'form-group'),
                        'class' => 'form-control c-points',
                        'label' => false,
                        'placeholder' => __('MAX_POINTS'),
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