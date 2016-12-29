<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Ranking Entity
 *
 * @property int $id
 * @property int $quiz_id
 * @property int $student_id
 * @property float $score
 * @property float $total
 * @property \Cake\I18n\Time $created
 *
 * @property \App\Model\Entity\Quiz $quiz
 * @property \App\Model\Entity\Student $student
 */
class Ranking extends Entity
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
