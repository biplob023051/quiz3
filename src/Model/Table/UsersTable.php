<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Users Model
 *
 * @property \Cake\ORM\Association\HasMany $Helps
 * @property \Cake\ORM\Association\HasMany $ImportedQuizzes
 * @property \Cake\ORM\Association\HasMany $Quizzes
 * @property \Cake\ORM\Association\HasMany $Statistics
 *
 * @method \App\Model\Entity\User get($primaryKey, $options = [])
 * @method \App\Model\Entity\User newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\User[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\User|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\User patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\User[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\User findOrCreate($search, callable $callback = null)
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class UsersTable extends Table
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
        $this->addBehavior('Common');
        
        $this->table('users');
        $this->displayField('name');
        $this->primaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('Helps', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('ImportedQuizzes', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('Quizzes', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('Statistics', [
            'foreignKey' => 'user_id'
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
            ->email('email')
            ->requirePresence('email', 'create')
            ->notEmpty('email')
            ->add('email', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->requirePresence('name', 'create')
            ->notEmpty('name');

        $validator
            ->requirePresence('password', 'create')
            ->notEmpty('password')
            ->add('password', [
                'length' => [
                    'rule' => ['minLength', 8],
                    'message' => 'Password must be 8 characters long',
                ]
            ]);


        $validator
            ->allowEmpty('subjects');

        $validator
            ->dateTime('expired')
            ->allowEmpty('expired');

        $validator
            ->integer('account_level')
            ->requirePresence('account_level', 'create')
            ->notEmpty('account_level');

        $validator
            ->allowEmpty('reset_code');

        $validator
            ->dateTime('resettime')
            ->allowEmpty('resettime');

        $validator
            ->allowEmpty('activation');

        $validator
            ->allowEmpty('imported_ids');

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
        $rules->add($rules->isUnique(['email']));

        return $rules;
    }
}
