<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * QuestionTypes Model
 *
 * @property \Cake\ORM\Association\HasMany $Questions
 *
 * @method \App\Model\Entity\QuestionType get($primaryKey, $options = [])
 * @method \App\Model\Entity\QuestionType newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\QuestionType[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\QuestionType|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\QuestionType patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\QuestionType[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\QuestionType findOrCreate($search, callable $callback = null)
 */
class QuestionTypesTable extends Table
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

        $this->table('question_types');
        $this->displayField('name');
        $this->primaryKey('id');

        $this->hasMany('Questions', [
            'foreignKey' => 'question_type_id'
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

        $validator
            ->requirePresence('description', 'create')
            ->notEmpty('description');

        $validator
            ->requirePresence('answer_field', 'create')
            ->notEmpty('answer_field');

        $validator
            ->boolean('multiple_choices')
            ->requirePresence('multiple_choices', 'create')
            ->notEmpty('multiple_choices');

        $validator
            ->requirePresence('template_name', 'create')
            ->notEmpty('template_name');

        $validator
            ->integer('manual_scoring')
            ->requirePresence('manual_scoring', 'create')
            ->notEmpty('manual_scoring');

        $validator
            ->boolean('type')
            ->allowEmpty('type');

        return $validator;
    }

    public function isMultipleChoice($questionTypeId) {
        $result = $this->find('all')->where(['id' => $questionTypeId])->select(['multiple_choices'])->first();
        return empty($result) ? NULL : $result->multiple_choices;
    }
}
