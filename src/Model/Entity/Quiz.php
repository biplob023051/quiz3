<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Quiz Entity
 *
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string $description
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time $modified
 * @property int $student_count
 * @property int $status
 * @property int $random_id
 * @property bool $show_result
 * @property bool $anonymous
 * @property string $subjects
 * @property string $classes
 * @property bool $shared
 * @property int $is_approve
 * @property string $comment
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Random $random
 * @property \App\Model\Entity\ImportedQuiz[] $imported_quizzes
 * @property \App\Model\Entity\Question[] $questions
 * @property \App\Model\Entity\Ranking[] $rankings
 * @property \App\Model\Entity\Student[] $students
 */
class Quiz extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'id' => false
    ];
}
