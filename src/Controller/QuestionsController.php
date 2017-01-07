<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Questions Controller
 *
 * @property \App\Model\Table\QuestionsTable $Questions
 */
class QuestionsController extends AppController
{

    private function isAuthorized($questionId) {

        if ($questionId == -1)
            return true;
        // get owner true or false
        $ownerId = $this->Questions->getQuestionOwner($questionId, $this->Auth->user('id'));

        if (is_null($ownerId))
            return false;

        return $ownerId;
    }

    public function setPreview($questionId) {
        $this->Session->delete('Choice');
        $data = $this->request->data;
        if (empty($data['Choice'])) {
            $data['Choice'] = array();
        }
        $data['Question']['id'] = $questionId;
        $this->set('data', array(
            'success' => true,
            'Question' => $data['Question'],
            'Choice' => $data['Choice'],
            'dummy' => true
        ));
    }

    public function removeChoice() {
        $data = $this->request->data;
        if (!$this->isAuthorized($data['question_id']))
            throw new ForbiddenException;

        // keep track of choice number
        
        if ($this->Session->check('Choices.' . $data['question_id'])) {
            $choices = $this->Session->read('Choices.' . $data['question_id']);
        } else {
            $this->Session->delete('Choice');
            $choices = $this->Questions->Choices->choicesByQuestionId($data['question_id']);
            $this->Session->write('Choices.' . $data['question_id'], $choices);
        }

        if ($this->Questions->Choices->delete($choices[$data['choice']]['Choice']['id'])) {
            $data = $this->Questions->findById($data['question_id']);
            $this->set('data', array(
                'success' => true,
                //'Question' => $data['Question'],
                'Choice' => $data['Choice']
            ));
        }
    }

    public function save($questionId) {
        $this->Session->delete('Choice');
        // If user is trying to update another user quiz, cancel.
        if (!$this->isAuthorized($questionId))
            throw new ForbiddenException;

        if (isset($this->request->data['Choice'])) {
            // reorder if order break
            $this->request->data['Choice'] = array_values($this->request->data['Choice']);
        }
        $data = $this->request->data;
// pr($data);
// exit;
        // if (empty($data['Question']['text'])) {   
        //     echo json_encode(array('success' => false, 'message' => 'Enter Question'));
        //     exit;
        // }

        if (($data['Question']['question_type_id'] == 1) || 
            ($data['Question']['question_type_id'] == 3)) {
            // multiple_one
            // multiple_many
            $isMultipleChoice = $this
                    ->Question
                    ->QuestionType
                    ->isMultipleChoice($data['Question']['question_type_id']);

            if (is_null($isMultipleChoice))
                throw new BadRequestException;

            $choiceCount = count($data['Choice']);
            if (!$isMultipleChoice) {
                for ($i = 1; $i < $choiceCount; ++$i) {
                    unset($data['Choice'][$i]);
                }
                $choiceCount = 1;
            }

            for ($i = 0; $i < $choiceCount; ++$i) {
                if (empty($data['Choice'][$i]['points']))
                    $data['Choice'][$i]['points'] = 0;

                if (empty($data['Choice'][$i]['text']))
                    $data['Choice'][$i]['text'] = __('Choice %d', $i);

                $data['Choice'][$i]['question_id'] = $questionId;
                unset($data['Choice'][$i]['id']);
            }
        } elseif($data['Question']['question_type_id'] == 2) {
            // short_auto
            if (empty($data['Choice'][0]['text'])) {   
                echo json_encode(array('success' => false, 'message' => __('Enter correct answers!')));
                exit;
            }
            $data['Choice'][0]['points'] = !empty($data['Choice'][0]['points']) ? $data['Choice'][0]['points'] : 0;
            
            // if (empty($data['Choice'][0]['points'])) {   
            //     echo json_encode(array('success' => false, 'message' => __('Enter point!')));
            //     exit;
            // }
        } elseif($data['Question']['question_type_id'] == 4) {
            // short_manual
            $data['Choice'][0]['text'] = 'Short_manual';
            $data['Choice'][0]['points'] = !empty($data['Choice'][0]['points']) ? $data['Choice'][0]['points'] : 0;
        } elseif($data['Question']['question_type_id'] == 5) { // essay type
            // essay
            if (!(isset($data['isNew']) && $data['isNew']) || $questionId != -1) {
                $data['Choice'][0]['points'] = $data['Choice'][0]['text'];
                $data['Choice'][0]['text'] = 'Essay';
            } else {
                $data['Choice'][0]['points'] = !empty($data['Choice'][0]['text']) ? $data['Choice'][0]['text'] : 0;
                $data['Choice'][0]['text'] = 'Essay';
            }
        } elseif($data['Question']['question_type_id'] == 7) { // youtube type
            if (empty($data['Choice'][0]['text'])) {   
                echo json_encode(array('success' => false, 'message' => __('Enter youtube url')));
                exit;
            }
            $youtube = explode('=', $data['Choice'][0]['text']);
            if (count($youtube) > 1) { // if watch mode
                $data['Choice'][0]['text'] = 'https://www.youtube.com/embed/' . $youtube[1];
            } 
        } elseif($data['Question']['question_type_id'] == 8) { // image url type
            // short_auto
            if (empty($data['Choice'][0]['text'])) {   
                echo json_encode(array('success' => false, 'message' => __('Enter image url')));
                exit;
            }
        }

        // If user leave form empty, set the default
        if (empty($data['Question']['text']))
            $data['Question']['text'] = __('New Question');

        // If we are editing a existing question, set the ID
        if (!(isset($data['isNew']) && $data['isNew']) || $questionId != -1) {
            $this->Questions->Choices->deleteAll(array(
                'Choices.question_id' => $questionId
            ));

            $data['Question']['id'] = $questionId;
            $this->Questions->id = $questionId;
        }

        if ($this->Questions->saveAssociated($data)) {
            $data['Question']['id'] = $this->Questions->id;
            if (isset($this->request->data['is_sort'])) { // if choice sorting exist then rearrange array by weight
                // sort by weight asc
                usort($data['Choice'], function($a, $b) {
                    return $a['weight'] - $b['weight'];
                });
                // weight desc
                $data['Choice'] = array_reverse($data['Choice']);
            }

            $this->set('data', array(
                'success' => true,
                'Question' => $data['Question'],
                'Choice' => empty($data['Choice']) ? array() : $data['Choice']
            ));
        }
    }

    public function delete() {
        $questionId = $this->request->data['id'];

        // If user is trying to delete another user quiz, cancel.
        if (!$this->isAuthorized($questionId))
            throw new ForbiddenException;

        if ($this->Questions->delete($questionId)) {
            // delete choices related to this question
            $this->Questions->Choices->deleteAll(array('Choices.question_id' => $questionId));
            // delete answers related to this question
            $this->Questions->Answers->deleteAll(array('Answers.question_id' => $questionId));
            $this->set('data', array(
                'success' => true
            ));
        }
    }

    public function duplicate() {
        $questionId = $this->request->data['id'];

        // If user is trying to delete another user quiz, cancel.
        if (!$this->isAuthorized($questionId))
            throw new ForbiddenException;

        $this->Questions->unbindModelAll(array('Choice'));
        $question = $this->Questions->findById($questionId);

        $response['success'] = false;

        if (!empty($question)) {
            $question['Question']['id'] = '';
            //$question['Question']['text'] = __('Copy of:') . ' ' . $question['Question']['text'];
            unset($question['Question']['created']);
            unset($question['Question']['modified']);
            if (!empty($question['Choice'])) {
                foreach ($question['Choice'] as $key => $choice) {
                    $question['Choice'][$key]['id'] = '';
                }
            }
            if ($this->Questions->saveAll($question, array('deep' => true))) {
               $response['message'] = __('Duplicated Successfully');
                $response['success'] = true;
                $response['id'] = $this->Questions->id;
            } else {
                $response['message'] = __('Something went wrong, please try again later!');
            }
        } else {
            $response['message'] = __('Invalid question');
        }

        $this->set('data', $response);
    }

    // ajax sorting question on drag drop
    public function ajaxSort() {
        $question_ids = $this->request->data['question_ids'];
        $max_weight = count($question_ids);
        foreach ($question_ids as $key => $id) {
            $question = $this->Questions->get($id);
            // pr($question);
            // exit;
            $question->weight = $max_weight--;
            $this->Questions->save($question);
        }
        // $this->set('data', array(
        //     'success' => true,
        //     'no' => count($question_ids)
        // ));

        echo json_encode(array('success' => true));
    }

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index1()
    {
        $this->paginate = [
            'contain' => ['Quizzes', 'QuestionTypes']
        ];
        $questions = $this->paginate($this->Questions);

        $this->set(compact('questions'));
        $this->set('_serialize', ['questions']);
    }

    /**
     * View method
     *
     * @param string|null $id Question id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view1($id = null)
    {
        $question = $this->Questions->get($id, [
            'contain' => ['Quizzes', 'QuestionTypes', 'Answers', 'Choices']
        ]);

        $this->set('question', $question);
        $this->set('_serialize', ['question']);
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|void Redirects on successful add, renders view otherwise.
     */
    public function add1()
    {
        $question = $this->Questions->newEntity();
        if ($this->request->is('post')) {
            $question = $this->Questions->patchEntity($question, $this->request->data);
            if ($this->Questions->save($question)) {
                $this->Flash->success(__('The question has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The question could not be saved. Please, try again.'));
            }
        }
        $quizzes = $this->Questions->Quizzes->find('list', ['limit' => 200]);
        $questionTypes = $this->Questions->QuestionTypes->find('list', ['limit' => 200]);
        $this->set(compact('question', 'quizzes', 'questionTypes'));
        $this->set('_serialize', ['question']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Question id.
     * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit1($id = null)
    {
        $question = $this->Questions->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $question = $this->Questions->patchEntity($question, $this->request->data);
            if ($this->Questions->save($question)) {
                $this->Flash->success(__('The question has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The question could not be saved. Please, try again.'));
            }
        }
        $quizzes = $this->Questions->Quizzes->find('list', ['limit' => 200]);
        $questionTypes = $this->Questions->QuestionTypes->find('list', ['limit' => 200]);
        $this->set(compact('question', 'quizzes', 'questionTypes'));
        $this->set('_serialize', ['question']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Question id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete1($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $question = $this->Questions->get($id);
        if ($this->Questions->delete($question)) {
            $this->Flash->success(__('The question has been deleted.'));
        } else {
            $this->Flash->error(__('The question could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
