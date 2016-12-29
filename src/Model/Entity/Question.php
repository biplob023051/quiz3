<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Question Entity
 *
 * @property int $id
 * @property int $quiz_id
 * @property int $question_type_id
 * @property string $text
 * @property string $explanation
 * @property int $weight
 * @property int $max_allowed
 * @property bool $case_sensitive
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time $modified
 *
 * @property \App\Model\Entity\Quiz $quiz
 * @property \App\Model\Entity\QuestionType $question_type
 * @property \App\Model\Entity\Answer[] $answers
 * @property \App\Model\Entity\Choice[] $choices
 */
class Question extends Entity
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
