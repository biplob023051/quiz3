<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * QuestionType Entity
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string $answer_field
 * @property bool $multiple_choices
 * @property string $template_name
 * @property int $manual_scoring
 * @property bool $type
 *
 * @property \App\Model\Entity\Question[] $questions
 */
class QuestionType extends Entity
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
