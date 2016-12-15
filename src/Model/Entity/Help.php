<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Help Entity
 *
 * @property int $id
 * @property int $user_id
 * @property int $parent_id
 * @property string $title
 * @property string $slug
 * @property string $sub_title
 * @property string $body
 * @property string $url
 * @property string $url_src
 * @property string $photo
 * @property int $status
 * @property string $type
 * @property int $lft
 * @property int $rght
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time $modified
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\ParentHelp $parent_help
 * @property \App\Model\Entity\ChildHelp[] $child_helps
 */
class Help extends Entity
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
