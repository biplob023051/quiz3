<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use App\Auth\SimplePasswordHasher;

/**
 * Users Model
 *
 * @property \Cake\ORM\Association\HasMany $Helps
 * @property \Cake\ORM\Association\HasMany $Downloads
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
        $this->hasMany('Downloads', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('Quizzes', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('Statistics', [
            'className' => 'Statistics',
            'foreignKey' => 'user_id'
        ]);
        $this->hasOne('UserStatistic', [
            'className' => 'Statistics',
            'foreignKey' => 'user_id',
            'strategy' => 'select',
            // 'conditions' => function (\Cake\Database\Expression\QueryExpression $exp, \Cake\ORM\Query $query) {
            //     $query->where(['UserStatistic.type' => 'user_login'])->order(['UserStatistic.id' => 'ASC']);
            //     return [];
            // },
            'conditions' => [
                'UserStatistic.type' => 'user_login'
            ],
            'fields' => [
                'UserStatistic.created'
            ]
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
                    'message' => 'PASSWORD_MUST_BE_LONGER',
                ],
                'compare' => [
                    'rule' => ['compareWith', 'passwordVerify'],
                    'message' => __('Password did not match, please try again') 
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


    // Custom password validator
    public function validationPassword(Validator $validator )
    {

        $validator
            ->add('old_password','custom',[
                'rule'=>  function($value, $context){
                    $user = $this->get($context['data']['id']);
                    if ($user) {
                        if ((new SimplePasswordHasher(['hashType' => 'sha256']))->check($value, $user->password)) {
                            return true;
                        }
                    }
                    return false;
                },
                'message'=> 'OLD_PASSWORD_DID_NOT_MATCH',
            ])
            ->notEmpty('old_password');

        $validator
            ->add('password1', [
                'length' => [
                    'rule' => ['minLength', 8],
                    'message' => 'PASSWORD_MUST_BE_LONGER',
                ]
            ])
            ->add('password1',[
                'match'=>[
                    'rule'=> ['compareWith','password2'],
                    'message'=>'Password did not match, please try again',
                ]
            ])
            ->notEmpty('password1');
        $validator
            ->add('password2', [
                'length' => [
                    'rule' => ['minLength', 8],
                    'message' => 'PASSWORD_MUST_BE_LONGER',
                ]
            ])
            ->add('password2',[
                'match'=>[
                    'rule'=> ['compareWith','password1'],
                    'message'=>'Password did not match, please try again',
                ]
            ])
            ->notEmpty('password2');

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
