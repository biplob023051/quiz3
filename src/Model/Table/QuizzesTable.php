<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Quizzes Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Users
 * @property \Cake\ORM\Association\BelongsTo $Randoms
 * @property \Cake\ORM\Association\HasMany $Downloads
 * @property \Cake\ORM\Association\HasMany $Questions
 * @property \Cake\ORM\Association\HasMany $Rankings
 * @property \Cake\ORM\Association\HasMany $Students
 *
 * @method \App\Model\Entity\Quiz get($primaryKey, $options = [])
 * @method \App\Model\Entity\Quiz newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Quiz[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Quiz|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Quiz patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Quiz[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Quiz findOrCreate($search, callable $callback = null)
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class QuizzesTable extends Table
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

        $this->table('quizzes');
        $this->displayField('name');
        $this->primaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Randoms', [
            'foreignKey' => 'random_id',
            'joinType' => 'INNER'
        ]);
        $this->hasMany('Downloads', [
            'foreignKey' => 'quiz_id'
        ]);
        $this->hasMany('Questions', [
            'foreignKey' => 'quiz_id'
        ]);
        $this->hasMany('Rankings', [
            'foreignKey' => 'quiz_id'
        ]);
        $this->hasMany('Students', [
            'foreignKey' => 'quiz_id'
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
            ->requirePresence('name', 'create')
            ->notEmpty('name');

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
        return $rules;
    }

    public function checkPermission($quizId, $user_id, $contain = []) {
        $result = $this->findByIdAndUserId($quizId, $user_id)->select(['id', 'random_id'])->contain($contain)->first();
        return $result;
    }

    // Find individual quiz
    // params $id, $user_id, $contain array
    public function getAQuizRel($id, $user_id) {
        $result = $this->find('all', array(
            'conditions' => array(
                'Quizzes.id' => $id,
                'Quizzes.user_id' => $user_id
            )
        ))
        ->contain([
            'Questions' => function($q) {
                $q->select([
                     'Questions.quiz_id',
                     'total' => $q->func()->count('Questions.quiz_id')
                ])
                ->group(['Questions.quiz_id']);
                return $q;
            },
            'Users'
        ])->first();
        return $result;
    }

    public function quizDetails($quizId, $filter) {
        $studentOptions = array();
        
        if (isset($filter['daterange']) && $filter['daterange'] !== 'all') {

            switch ($filter['daterange']) {
                case 'today':
                    if (isset($filter['class']) and $filter['class'] !== 'all') {
                        $studentOptions = array(
                            "date(Students.submitted) > DATE_SUB(NOW(), INTERVAL 1 DAY)",
                            "Students.class" => $filter['class']
                        );

                    } else {
                        $studentOptions = array(
                            "date(Students.submitted) > DATE_SUB(NOW(), INTERVAL 1 DAY)",
                        );       
                    }
                    break;
                case 'this_week':
                    if (isset($filter['class']) and $filter['class'] !== 'all') {
                        $studentOptions = array(
                            "date(Students.submitted) > DATE_SUB(NOW(), INTERVAL 1 WEEK)",
                            "Students.class" => $filter['class']
                        );
                    } else {
                        $studentOptions = array(
                            "date(Students.submitted) > DATE_SUB(NOW(), INTERVAL 1 WEEK)",
                        );
                    }
                    break;
                case 'this_month':
                    if (isset($filter['class']) and $filter['class'] !== 'all') {
                        $studentOptions = array(
                            "date(Students.submitted) > DATE_SUB(NOW(), INTERVAL 1 MONTH)",
                            "Students.class" => $filter['class']
                        );
                    } else {
                        $studentOptions = array(
                            "date(Students.submitted) > DATE_SUB(NOW(), INTERVAL 1 MONTH)",
                        );
                    }
                    break;
                case 'this_year':
                    if (isset($filter['class']) and $filter['class'] !== 'all') {
                        $studentOptions = array(
                            "date(Students.submitted) > DATE_SUB(NOW(), INTERVAL 1 YEAR)",
                            "Students.class" => $filter['class']
                        );
                    } else {
                        $studentOptions = array(
                            "date(Students.submitted) > DATE_SUB(NOW(), INTERVAL 1 YEAR)",
                        );
                    }
                    break;
            }
        } else {
            if (isset($filter['class']) and $filter['class'] !== 'all') {
                $studentOptions = array(
                    "Students.class" => $filter['class']
                );
            }
        }

        // pr($studentOptions);
        // exit;

        $result = $this->find('all', array(
                'conditions' => array(
                    'Quizzes.id' => $quizId,
                ),
                'contain' => array(
                    'Users', 
                    'Questions' => function($q) {
                        $q->where(['Questions.question_type_id IN' => array(1,2,3,4,5)])
                        ->contain(['Answers', 'Choices', 'QuestionTypes'])
                        ->order(['Questions.weight DESC', 'Questions.id ASC']);
                        return $q;
                    }, 
                    'Students' => function($q) use ($studentOptions) {
                        return $q->where($studentOptions)->contain(['Rankings', 'Answers']);
                    }, 
                    'Rankings'
                )
            )
        )->first();

        // pr($result);
        // exit;
        
        return $result;
    }

    // Method for checking ajax_latest
    public function checkNewUpdate($quizId, $filter) {
        $studentOptions[] = array(
            'Students.status IS NULL',
            'Students.submitted >=' => date('Y-m-d H:i:s', strtotime('-1 hour'))
        );
    
        if (isset($filter['class']) && ($filter['class'] !== 'all')) {
            $studentOptions[] = array(
                'Students.class' => $filter['class']
            );
        }

        $result = $this->find('all', array(
                'conditions' => array(
                    'Quizzes.id' => $quizId,
                ),
                'contain' => array(
                    'Users', 
                    'Questions' => function($q) {
                        return $q->where(['Questions.question_type_id IN' => [1,2,3,4,5]])
                        ->order(['Questions.weight DESC', 'Questions.id ASC'])
                        ->contain([
                            'Answers', 
                            'Choices', 
                            'QuestionTypes'
                        ]);
                    }, 
                    'Students' => function($q) use ($studentOptions) {
                        return $q->where($studentOptions)->contain(['Rankings', 'Answers']);
                    }
                )
            )
        )->first();
        
        return $result;
    }

}
