<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Question Type'), ['action' => 'edit', $questionType->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Question Type'), ['action' => 'delete', $questionType->id], ['confirm' => __('Are you sure you want to delete # {0}?', $questionType->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Question Types'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Question Type'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Questions'), ['controller' => 'Questions', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Question'), ['controller' => 'Questions', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="questionTypes view large-9 medium-8 columns content">
    <h3><?= h($questionType->name) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Name') ?></th>
            <td><?= h($questionType->name) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Answer Field') ?></th>
            <td><?= h($questionType->answer_field) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Template Name') ?></th>
            <td><?= h($questionType->template_name) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($questionType->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Manual Scoring') ?></th>
            <td><?= $this->Number->format($questionType->manual_scoring) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Multiple Choices') ?></th>
            <td><?= $questionType->multiple_choices ? __('Yes') : __('No'); ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Type') ?></th>
            <td><?= $questionType->type ? __('Yes') : __('No'); ?></td>
        </tr>
    </table>
    <div class="row">
        <h4><?= __('Description') ?></h4>
        <?= $this->Text->autoParagraph(h($questionType->description)); ?>
    </div>
    <div class="related">
        <h4><?= __('Related Questions') ?></h4>
        <?php if (!empty($questionType->questions)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th scope="col"><?= __('Id') ?></th>
                <th scope="col"><?= __('Quiz Id') ?></th>
                <th scope="col"><?= __('Question Type Id') ?></th>
                <th scope="col"><?= __('Text') ?></th>
                <th scope="col"><?= __('Explanation') ?></th>
                <th scope="col"><?= __('Weight') ?></th>
                <th scope="col"><?= __('Max Allowed') ?></th>
                <th scope="col"><?= __('Case Sensitive') ?></th>
                <th scope="col"><?= __('Created') ?></th>
                <th scope="col"><?= __('Modified') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($questionType->questions as $questions): ?>
            <tr>
                <td><?= h($questions->id) ?></td>
                <td><?= h($questions->quiz_id) ?></td>
                <td><?= h($questions->question_type_id) ?></td>
                <td><?= h($questions->text) ?></td>
                <td><?= h($questions->explanation) ?></td>
                <td><?= h($questions->weight) ?></td>
                <td><?= h($questions->max_allowed) ?></td>
                <td><?= h($questions->case_sensitive) ?></td>
                <td><?= h($questions->created) ?></td>
                <td><?= h($questions->modified) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Questions', 'action' => 'view', $questions->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'Questions', 'action' => 'edit', $questions->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Questions', 'action' => 'delete', $questions->id], ['confirm' => __('Are you sure you want to delete # {0}?', $questions->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
</div>
