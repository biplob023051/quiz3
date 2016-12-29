<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Student Entity
 *
 * @property int $id
 * @property int $quiz_id
 * @property string $fname
 * @property string $lname
 * @property string $class
 * @property bool $status
 * @property \Cake\I18n\Time $submitted
 *
 * @property \App\Model\Entity\Quiz $quiz
 * @property \App\Model\Entity\Answer[] $answers
 * @property \App\Model\Entity\Ranking[] $rankings
 */
class Student extends Entity
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
