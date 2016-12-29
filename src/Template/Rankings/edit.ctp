<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $ranking->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $ranking->id)]
            )
        ?></li>
        <li><?= $this->Html->link(__('List Rankings'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Quizzes'), ['controller' => 'Quizzes', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Quiz'), ['controller' => 'Quizzes', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Students'), ['controller' => 'Students', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Student'), ['controller' => 'Students', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="rankings form large-9 medium-8 columns content">
    <?= $this->Form->create($ranking) ?>
    <fieldset>
        <legend><?= __('Edit Ranking') ?></legend>
        <?php
            echo $this->Form->input('quiz_id', ['options' => $quizzes]);
            echo $this->Form->input('student_id', ['options' => $students]);
            echo $this->Form->input('score');
            echo $this->Form->input('total');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
