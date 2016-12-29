<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Choice'), ['action' => 'edit', $choice->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Choice'), ['action' => 'delete', $choice->id], ['confirm' => __('Are you sure you want to delete # {0}?', $choice->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Choices'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Choice'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Questions'), ['controller' => 'Questions', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Question'), ['controller' => 'Questions', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="choices view large-9 medium-8 columns content">
    <h3><?= h($choice->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Question') ?></th>
            <td><?= $choice->has('question') ? $this->Html->link($choice->question->id, ['controller' => 'Questions', 'action' => 'view', $choice->question->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($choice->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Points') ?></th>
            <td><?= $this->Number->format($choice->points) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Weight') ?></th>
            <td><?= $this->Number->format($choice->weight) ?></td>
        </tr>
    </table>
    <div class="row">
        <h4><?= __('Text') ?></h4>
        <?= $this->Text->autoParagraph(h($choice->text)); ?>
    </div>
</div>
