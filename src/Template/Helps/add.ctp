<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('List Helps'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Parent Helps'), ['controller' => 'Helps', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Parent Help'), ['controller' => 'Helps', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="helps form large-9 medium-8 columns content">
    <?= $this->Form->create($help) ?>
    <fieldset>
        <legend><?= __('Add Help') ?></legend>
        <?php
            echo $this->Form->input('user_id', ['options' => $users]);
            echo $this->Form->input('parent_id', ['options' => $parentHelps, 'empty' => true]);
            echo $this->Form->input('title');
            echo $this->Form->input('slug');
            echo $this->Form->input('sub_title');
            echo $this->Form->input('body');
            echo $this->Form->input('url');
            echo $this->Form->input('url_src');
            echo $this->Form->input('photo');
            echo $this->Form->input('status');
            echo $this->Form->input('type');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
