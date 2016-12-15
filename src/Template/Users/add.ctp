<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('List Users'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Helps'), ['controller' => 'Helps', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Help'), ['controller' => 'Helps', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Imported Quizzes'), ['controller' => 'ImportedQuizzes', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Imported Quiz'), ['controller' => 'ImportedQuizzes', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Quizzes'), ['controller' => 'Quizzes', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Quiz'), ['controller' => 'Quizzes', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Statistics'), ['controller' => 'Statistics', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Statistic'), ['controller' => 'Statistics', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="users form large-9 medium-8 columns content">
    <?= $this->Form->create($user) ?>
    <fieldset>
        <legend><?= __('Add User') ?></legend>
        <?php
            echo $this->Form->input('email');
            echo $this->Form->input('name');
            echo $this->Form->input('password');
            echo $this->Form->input('language');
            echo $this->Form->input('subjects');
            echo $this->Form->input('expired', ['empty' => true]);
            echo $this->Form->input('account_level');
            echo $this->Form->input('reset_code');
            echo $this->Form->input('resettime', ['empty' => true]);
            echo $this->Form->input('activation');
            echo $this->Form->input('imported_ids');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
