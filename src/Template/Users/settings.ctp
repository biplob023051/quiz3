<?php
//pr($userPermissions);
$this->assign('title', __('Settings'));
$userSubjects = !empty($user->subjects) ? json_decode($user->subjects, true) : array();
?>

<?= $this->Flash->render(); ?>

<?php
    echo $this->Form->create($user, ['novalidate' => 'novalidate']);
?>
<div class="row">
    <div class="col-md-5 col-md-offset-3 col-xs-12 col-sm-12">
        <?php
            echo $this->Form->input('name', array(
                'label' => __("Name")
            ));
        ?>
    </div>
</div>
<div class="row">
    <div class="col-md-5 col-md-offset-3 col-xs-12 col-sm-12">
        <?php
            echo $this->Form->input('email', array(
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
            'div' => array('class' => 'form-group'),
            'class' => 'form-control'
        ));
        ?>
    </div>
</div>
<?php if (!empty($subjects)) : ?>
    <div class="row">
        <div class="col-md-5 col-md-offset-3 col-xs-12 col-sm-12">
            <?php
            echo $this->Form->input('subjects', array(
                'options' => $subjects,
                'div' => array('class' => 'form-group'),
                'class' => 'form-control no-border',
                'type' => 'select',
                'multiple' => 'checkbox',
                'value' => $userSubjects
            ));
            ?>
        </div>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-xs-12 col-md-2 col-md-offset-3">
        <?php
        echo $this->Form->submit(__("Save"), ['class' => 'btn btn-info btn-block']);
        ?>
        <!-- <div class="form-group">
            <button type="submit" class="btn btn-info btn-block"><?php echo __("Save") ?></button>
        </div> -->
    </div>
    <div class="col-xs-12 col-md-2">
        <div class="form-group">
            <?php
            echo $this->Html->link(__("Cancel"), '/', array(
                'class' => 'btn btn-default btn-block'
            ));
            ?>
        </div>
    </div>
</div>
<?= $this->Form->end(); ?>


<?= $this->element('Invoice/invoice'); ?>
<?= $this->element('Invoice/invoice_success_dialog'); ?>
<?= $this->element('Invoice/invoice_error_dialog'); ?>

<script type="text/javascript">
    var lang_strings = <?php echo json_encode($lang_strings) ?>;
</script>

<?= $this->Html->script(array('invoice'), array('inline' => false)); ?>