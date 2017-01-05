<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Quiz'), ['action' => 'edit', $quiz->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Quiz'), ['action' => 'delete', $quiz->id], ['confirm' => __('Are you sure you want to delete # {0}?', $quiz->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Quizzes'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Quiz'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Imported Quizzes'), ['controller' => 'Downloads', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Imported Quiz'), ['controller' => 'Downloads', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Questions'), ['controller' => 'Questions', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Question'), ['controller' => 'Questions', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Rankings'), ['controller' => 'Rankings', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Ranking'), ['controller' => 'Rankings', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Students'), ['controller' => 'Students', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Student'), ['controller' => 'Students', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="quizzes view large-9 medium-8 columns content">
    <h3><?= h($quiz->name) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('User') ?></th>
            <td><?= $quiz->has('user') ? $this->Html->link($quiz->user->name, ['controller' => 'Users', 'action' => 'view', $quiz->user->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Name') ?></th>
            <td><?= h($quiz->name) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Description') ?></th>
            <td><?= h($quiz->description) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($quiz->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Student Count') ?></th>
            <td><?= $this->Number->format($quiz->student_count) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Status') ?></th>
            <td><?= $this->Number->format($quiz->status) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Random Id') ?></th>
            <td><?= $this->Number->format($quiz->random_id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Is Approve') ?></th>
            <td><?= $this->Number->format($quiz->is_approve) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created') ?></th>
            <td><?= h($quiz->created) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Modified') ?></th>
            <td><?= h($quiz->modified) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Show Result') ?></th>
            <td><?= $quiz->show_result ? __('Yes') : __('No'); ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Anonymous') ?></th>
            <td><?= $quiz->anonymous ? __('Yes') : __('No'); ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Shared') ?></th>
            <td><?= $quiz->shared ? __('Yes') : __('No'); ?></td>
        </tr>
    </table>
    <div class="row">
        <h4><?= __('Subjects') ?></h4>
        <?= $this->Text->autoParagraph(h($quiz->subjects)); ?>
    </div>
    <div class="row">
        <h4><?= __('Classes') ?></h4>
        <?= $this->Text->autoParagraph(h($quiz->classes)); ?>
    </div>
    <div class="row">
        <h4><?= __('Comment') ?></h4>
        <?= $this->Text->autoParagraph(h($quiz->comment)); ?>
    </div>
    <div class="related">
        <h4><?= __('Related Imported Quizzes') ?></h4>
        <?php if (!empty($quiz->imported_quizzes)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th scope="col"><?= __('Id') ?></th>
                <th scope="col"><?= __('User Id') ?></th>
                <th scope="col"><?= __('Quiz Id') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($quiz->imported_quizzes as $importedQuizzes): ?>
            <tr>
                <td><?= h($importedQuizzes->id) ?></td>
                <td><?= h($importedQuizzes->user_id) ?></td>
                <td><?= h($importedQuizzes->quiz_id) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Downloads', 'action' => 'view', $importedQuizzes->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'Downloads', 'action' => 'edit', $importedQuizzes->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Downloads', 'action' => 'delete', $importedQuizzes->id], ['confirm' => __('Are you sure you want to delete # {0}?', $importedQuizzes->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
    <div class="related">
        <h4><?= __('Related Questions') ?></h4>
        <?php if (!empty($quiz->questions)): ?>
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
            <?php foreach ($quiz->questions as $questions): ?>
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
    <div class="related">
        <h4><?= __('Related Rankings') ?></h4>
        <?php if (!empty($quiz->rankings)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th scope="col"><?= __('Id') ?></th>
                <th scope="col"><?= __('Quiz Id') ?></th>
                <th scope="col"><?= __('Student Id') ?></th>
                <th scope="col"><?= __('Score') ?></th>
                <th scope="col"><?= __('Total') ?></th>
                <th scope="col"><?= __('Created') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($quiz->rankings as $rankings): ?>
            <tr>
                <td><?= h($rankings->id) ?></td>
                <td><?= h($rankings->quiz_id) ?></td>
                <td><?= h($rankings->student_id) ?></td>
                <td><?= h($rankings->score) ?></td>
                <td><?= h($rankings->total) ?></td>
                <td><?= h($rankings->created) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Rankings', 'action' => 'view', $rankings->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'Rankings', 'action' => 'edit', $rankings->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Rankings', 'action' => 'delete', $rankings->id], ['confirm' => __('Are you sure you want to delete # {0}?', $rankings->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
    <div class="related">
        <h4><?= __('Related Students') ?></h4>
        <?php if (!empty($quiz->students)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th scope="col"><?= __('Id') ?></th>
                <th scope="col"><?= __('Quiz Id') ?></th>
                <th scope="col"><?= __('Fname') ?></th>
                <th scope="col"><?= __('Lname') ?></th>
                <th scope="col"><?= __('Class') ?></th>
                <th scope="col"><?= __('Status') ?></th>
                <th scope="col"><?= __('Submitted') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($quiz->students as $students): ?>
            <tr>
                <td><?= h($students->id) ?></td>
                <td><?= h($students->quiz_id) ?></td>
                <td><?= h($students->fname) ?></td>
                <td><?= h($students->lname) ?></td>
                <td><?= h($students->class) ?></td>
                <td><?= h($students->status) ?></td>
                <td><?= h($students->submitted) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Students', 'action' => 'view', $students->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'Students', 'action' => 'edit', $students->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Students', 'action' => 'delete', $students->id], ['confirm' => __('Are you sure you want to delete # {0}?', $students->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
</div>
