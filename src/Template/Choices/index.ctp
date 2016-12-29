<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('New Choice'), ['action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Questions'), ['controller' => 'Questions', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Question'), ['controller' => 'Questions', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="choices index large-9 medium-8 columns content">
    <h3><?= __('Choices') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('question_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('points') ?></th>
                <th scope="col"><?= $this->Paginator->sort('weight') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($choices as $choice): ?>
            <tr>
                <td><?= $this->Number->format($choice->id) ?></td>
                <td><?= $choice->has('question') ? $this->Html->link($choice->question->id, ['controller' => 'Questions', 'action' => 'view', $choice->question->id]) : '' ?></td>
                <td><?= $this->Number->format($choice->points) ?></td>
                <td><?= $this->Number->format($choice->weight) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $choice->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $choice->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $choice->id], ['confirm' => __('Are you sure you want to delete # {0}?', $choice->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->prev('< ' . __('previous')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('next') . ' >') ?>
        </ul>
        <p><?= $this->Paginator->counter() ?></p>
    </div>
</div>
