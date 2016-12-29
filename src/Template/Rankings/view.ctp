<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Ranking'), ['action' => 'edit', $ranking->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Ranking'), ['action' => 'delete', $ranking->id], ['confirm' => __('Are you sure you want to delete # {0}?', $ranking->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Rankings'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Ranking'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Quizzes'), ['controller' => 'Quizzes', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Quiz'), ['controller' => 'Quizzes', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Students'), ['controller' => 'Students', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Student'), ['controller' => 'Students', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="rankings view large-9 medium-8 columns content">
    <h3><?= h($ranking->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Quiz') ?></th>
            <td><?= $ranking->has('quiz') ? $this->Html->link($ranking->quiz->name, ['controller' => 'Quizzes', 'action' => 'view', $ranking->quiz->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Student') ?></th>
            <td><?= $ranking->has('student') ? $this->Html->link($ranking->student->id, ['controller' => 'Students', 'action' => 'view', $ranking->student->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($ranking->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Score') ?></th>
            <td><?= $this->Number->format($ranking->score) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Total') ?></th>
            <td><?= $this->Number->format($ranking->total) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created') ?></th>
            <td><?= h($ranking->created) ?></td>
        </tr>
    </table>
</div>
