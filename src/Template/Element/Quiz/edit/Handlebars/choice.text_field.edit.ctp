<div class="row">
    <div class="col-xs-12 col-md-6">
        <div class="well well-sm">
            <div class="row">
                <div class="col-xs-12 col-md-12">
                    <?php
                    echo $this->Form->input('data.Choice.{{id}}.text', array(
                        'default' => '{{text}}',
                        'div' => array('class' => 'form-group'),
                        'class' => 'form-control text_field',
                        'label' => false,
                        'type' => 'textarea',
                        'placeholder' => __('ENTER_TEXTS'),
                        'id' => 'Choice{{id}}Text'
                    ));
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>