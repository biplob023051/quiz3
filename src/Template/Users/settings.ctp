<?php
//pr($userPermissions);
$this->Html->script(array(
    'invoice'
        ), array(
    'inline' => false
));
$this->assign('title', __('Settings'));

$userSubjects = !empty($data['User']['subjects']) ? json_decode($data['User']['subjects'], true) : array();
?>

<?php echo $this->Session->flash('notification'); ?>
<?php echo $this->Session->flash('error'); ?>

<?php
echo $this->Form->create('User', array(
    'inputDefaults' => array(
        'div' => array('class' => 'form-group'),
        'class' => 'form-control',
    )
));
?>
<div class="row">
    <div class="col-md-5 col-md-offset-3 col-xs-12 col-sm-12">
        <?php
        echo $this->Form->input('name', array(
            'default' => $data['User']['name'],
            'label' => __("Name")
        ));
        ?>
    </div>
</div>
<div class="row">
    <div class="col-md-5 col-md-offset-3 col-xs-12 col-sm-12">
        <?php
        echo $this->Form->input('email', array(
            'default' => $data['User']['email'],
            'label' => __("Email")
        ));
        ?>
    </div>
</div>
<div class="row">
    <div class="col-md-5 col-md-offset-3 col-xs-12 col-sm-12">
        <?php
        echo $this->Form->input('password', array(
            'label' => __("Password"),
            'placeholders' => __("Fill if you want to change password"),
            'required' => false
        ));
        ?>
    </div>
</div>
<?php echo $this->element('upgrade', array('userPermissions' => $userPermissions)); ?>
<div class="row">
    <div class="col-md-5 col-md-offset-3 col-xs-12 col-sm-12">
        <?php
        $languages = array(
            'eng' => 'English',
            'fin' => 'Suomi'
        );
        echo $this->Form->input('language', array(
            'options' => $languages,
            'default' => $data['User']['language'],
            'div' => array('class' => 'form-group'),
            'class' => 'form-control'
        ));
        ?>
    </div>
</div>
<?php if (!empty($data['subjects'])) : ?>
    <div class="row">
        <div class="col-md-5 col-md-offset-3 col-xs-12 col-sm-12">
            <?php
            echo $this->Form->input('subjects', array(
                'options' => $data['subjects'],
                'div' => array('class' => 'form-group'),
                'class' => 'form-control no-border',
                'type' => 'select',
                'multiple' => 'checkbox',
                'selected' => $userSubjects
            ));
            ?>
        </div>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-xs-12 col-md-2 col-md-offset-3">
        <div class="form-group">
            <button type="submit" class="btn btn-info btn-block"><?php echo __("Save") ?></button>
        </div>
    </div>
    <div class="col-xs-12 col-md-2">
        <div class="form-group">
            <?php
            echo $this->Html->link(__("Cancel"), '/quiz', array(
                'class' => 'btn btn-default btn-block'
            ));
            ?>
        </div>
    </div>
</div>
<?php echo $this->Form->end(); ?>


<?php echo $this->element('Invoice/invoice'); ?>
<?php echo $this->element('Invoice/invoice_success_dialog'); ?>
<?php echo $this->element('Invoice/invoice_error_dialog'); ?>


<script id="app-data" type="application/json">
    <?php
    echo json_encode(array(
        'baseUrl' => $this->Html->url('/', true)
    ));
    ?>
</script>