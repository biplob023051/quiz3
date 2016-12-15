<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * User Entity
 *
 * @property int $id
 * @property string $email
 * @property string $name
 * @property string $password
 * @property string $language
 * @property string $subjects
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time $modified
 * @property \Cake\I18n\Time $expired
 * @property int $account_level
 * @property string $reset_code
 * @property \Cake\I18n\Time $resettime
 * @property string $activation
 * @property string $imported_ids
 *
 * @property \App\Model\Entity\Help[] $helps
 * @property \App\Model\Entity\ImportedQuiz[] $imported_quizzes
 * @property \App\Model\Entity\Quiz[] $quizzes
 * @property \App\Model\Entity\Statistic[] $statistics
 */
class User extends Entity
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

    /**
     * Fields that are excluded from JSON versions of the entity.
     *
     * @var array
     */
    protected $_hidden = [
        'password'
    ];
}
