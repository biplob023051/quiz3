<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Helps Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Users
 * @property \Cake\ORM\Association\BelongsTo $ParentHelps
 * @property \Cake\ORM\Association\HasMany $ChildHelps
 *
 * @method \App\Model\Entity\Help get($primaryKey, $options = [])
 * @method \App\Model\Entity\Help newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Help[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Help|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Help patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Help[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Help findOrCreate($search, callable $callback = null)
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 * @mixin \Cake\ORM\Behavior\TreeBehavior
 */
class HelpsTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->table('helps');
        $this->displayField('title');
        $this->primaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Tree');

        $this->addBehavior('Common');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('ParentHelps', [
            'className' => 'Helps',
            'foreignKey' => 'parent_id'
        ]);
        $this->hasMany('ChildHelps', [
            'className' => 'Helps',
            'foreignKey' => 'parent_id'
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->requirePresence('title', 'create')
            ->notEmpty('title', 'TITLE_REQUIRED');

        $validator
            ->requirePresence('parent_id', 'create')
            ->notEmpty('parent_id', 'MAIN_TITLE_REQUIRED');

        $validator
            ->requirePresence('url', 'create')
            ->notEmpty('url', 'REQUIRED_YOUTUBE_URL')
            ->add('url', [
                'valid-url' => [
                    'rule' => 'url',
                    'message' => 'REQUIRED_YOUTUBE_URL'
                ]
            ]);

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['user_id'], 'Users'));
        $rules->add($rules->existsIn(['parent_id'], 'ParentHelps'));

        return $rules;
    }

    public function validationMainTitle($validator)
    {
        $validator
            ->requirePresence('title', 'create')
            ->notEmpty('title', 'TITLE_REQUIRED');
        return $validator;
    }

    public function validationVideoSection(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->requirePresence('title', 'create')
            ->notEmpty('title', 'TITLE_REQUIRED');

        $validator
            ->requirePresence('type', 'create')
            ->notEmpty('type', 'DISPLAY_PAGE_REQUIRED');

        $validator
            ->requirePresence('url', 'create')
            ->notEmpty('url', 'REQUIRED_YOUTUBE_URL')
            ->add('url', [
                'valid-url' => [
                    'rule' => 'url',
                    'message' => 'REQUIRED_YOUTUBE_URL'
                ]
            ]);

        return $validator;
    }

    // list of active parent 
    public function parentsOptions() {
        $options = $this->find('list', ['keyField' => 'id', 'valueField' => 'title'])
            ->where(['status' => 1, 'type' => 'help', 'parent_id IS NULL'])
            ->order(['lft'=>' DESC', 'rght'=>' ASC'])
            ->toArray();
        return $options;
    } 

    public function getVideoByType($type = null) {
        $video = $this->find('all')
            ->where(['type' => $type, 'status' => 1])
            ->order(['id' => 'desc'])
            ->first();
        $video = !empty($video) ? $video->toArray() : array();
        return $video;
    }
}
