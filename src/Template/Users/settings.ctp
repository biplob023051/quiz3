<?php
//pr($userPermissions);
$this->assign('title', __('SETTINGS'));
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
                'label' => __("NAME")
            ));
        ?>
    </div>
</div>
<div class="row">
    <div class="col-md-5 col-md-offset-3 col-xs-12 col-sm-12">
        <?php
            echo $this->Form->input('email', array(
                'label' => __("EMAIL"),
                'disabled' => true
            ));
        ?>
    </div>
</div>
<?php echo $this->element('password'); ?>
<?php echo $this->element('upgrade', array('userPermissions' => $userPermissions)); ?>
<div class="row">
    <div class="col-md-5 col-md-offset-3 col-xs-12 col-sm-12">
        <?php
        $languages = array(
            'en_GB' => 'English',
            'fin' => 'Suomi',
            'sv_FI' => 'Svenska'
        );
        echo $this->Form->input('language', [
            'label' => ['text' => __('LANGUAGE')],
            'options' => $languages,
            'div' => array('class' => 'form-group'),
            'class' => 'form-control'
        ]);
        ?>
    </div>
</div>
<?php if (!empty($subjects)) : ?>
    <div class="row">
        <div class="col-md-5 col-md-offset-3 col-xs-12 col-sm-12">
            <?php
            echo $this->Form->input('subjects', [
                'label' => ['text' => __('SUBJECTS')],
                'options' => $subjects,
                'div' => array('class' => 'form-group'),
                'class' => 'form-control no-border',
                'type' => 'select',
                'multiple' => 'checkbox',
                'value' => $userSubjects
            ]);
            ?>
        </div>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-xs-12 col-md-2 col-md-offset-3">
        <?php
            echo $this->Form->submit(__("SAVE"), ['class' => 'btn btn-info btn-block']);
        ?>
    </div>
    <div class="col-xs-12 col-md-2">
        <div class="form-group">
            <?php
            echo $this->Html->link(__("CANCEL"), '/', array(
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
<?= $this->element('Invoice/password'); ?>

<script type="text/javascript">
    var lang_strings = <?php echo json_encode($lang_strings) ?>;
</script>

<?= $this->Html->script(array('invoice'), array('inline' => false)); ?>