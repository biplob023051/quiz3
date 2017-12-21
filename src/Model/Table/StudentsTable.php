<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Students Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Quizzes
 * @property \Cake\ORM\Association\HasMany $Answers
 * @property \Cake\ORM\Association\HasMany $Rankings
 *
 * @method \App\Model\Entity\Student get($primaryKey, $options = [])
 * @method \App\Model\Entity\Student newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Student[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Student|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Student patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Student[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Student findOrCreate($search, callable $callback = null)
 *
 * @mixin \Cake\ORM\Behavior\CounterCacheBehavior
 */
class StudentsTable extends Table
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

        $this->table('students');
        $this->displayField('id');
        $this->primaryKey('id');

        $this->addBehavior('CounterCache', ['Quizzes' => ['student_count']]);

        $this->belongsTo('Quizzes', [
            'foreignKey' => 'quiz_id',
            'joinType' => 'INNER'
        ]);
        $this->hasMany('Answers', [
            'foreignKey' => 'student_id'
        ]);
        // $this->hasMany('Rankings', [
        //     'foreignKey' => 'student_id'
        // ]);
        $this->hasOne('Rankings', [
            'foreignKey' => 'student_id'
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
            ->requirePresence('fname', 'create')
            ->notEmpty('fname');

        $validator
            ->requirePresence('lname', 'create')
            ->notEmpty('lname');

        $validator
            ->requirePresence('class', 'create')
            ->notEmpty('class');

        $validator
            ->boolean('status')
            ->allowEmpty('status');

        $validator
            ->dateTime('submitted')
            ->requirePresence('submitted', 'create')
            ->notEmpty('submitted');

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
        $rules->add($rules->existsIn(['quiz_id'], 'Quizzes'));

        return $rules;
    }

    public function validationInitialRecord(Validator $validator) {
        $validator = $this->validationDefault($validator);
        $validator->remove('fname');
        $validator->remove('lname');
        $validator->remove('class');
        return $validator;
    }
}
