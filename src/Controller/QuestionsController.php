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
        $this->autoRender = false;
        $this->Session->delete('Choice');
        $data = $this->request->data;
        if (empty($data['Choice'])) {
            $data['Choice'] = array();
        }
        $data['Question']['id'] = $questionId;
        // pr($data);
        // exit;
        $response = array(
            'success' => true,
            'Question' => $data['Question'],
            'Choice' => $data['Choice'],
            'dummy' => true
        );
        echo json_encode($response);
    }

    public function removeChoice() {
        $data = $this->request->data;
        if (!$this->isAuthorized($data['question_id']))
            throw new ForbiddenException;

        // keep track of choice number
        
        if ($this->Session->check('Choices.' . $data['question_id'])) {
            $choices = $this->Session->read('Choices.' . $data['question_id']);
        } else {
            $this->Session->delete('Choices');
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
        $this->autoRender = false; 
        $this->Session->delete('Choice');
        // If user is trying to update another user quiz, cancel.
        if (!$this->isAuthorized($questionId))
            throw new ForbiddenException;

        // pr($this->request->data);
        // exit;

        if (isset($this->request->data['Choice'])) {
            // reorder if order break
            $c_array = [];
            foreach ($this->request->data['Choice'] as $key => $choice) {
                if (!empty($choice['text'])) {
                    $c_array[] = $choice; 
                }
            }
            if (!empty($c_array))
            $this->request->data['Choice'] = $c_array;
        }
        $data = $this->request->data;

        // if (empty($data['Question']['text'])) {   
        //     echo json_encode(array('success' => false, 'message' => 'Enter Question'));
        //     exit;
        // }

        if (($data['Question']['question_type_id'] == 1) || 
            ($data['Question']['question_type_id'] == 3)) {
            // multiple_one
            // multiple_many
            $isMultipleChoice = $this->Questions->QuestionTypes->isMultipleChoice($data['Question']['question_type_id']);

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
                $data['Choice'][0]['points'] = !empty($data['Choice'][0]['text']) ? $data['Choice'][0]['text'] : 0;
                $data['Choice'][0]['text'] = 'Essay';
            } else {
                if (!empty($data['Choice'])) unset($data['Choice']);
                $data['Choice'][0]['points'] = !empty($data['Choice'][0]['text']) ? $data['Choice'][0]['text'] : 0;
                $data['Choice'][0]['text'] = 'Essay';
            }
        } elseif($data['Question']['question_type_id'] == 7) { // youtube type
            if (empty($data['Choice'][0]['text'])) {   
                echo json_encode(array('success' => false, 'message' => __('Enter youtube url')));
                exit;
            }
            $rx = '~^(?:https?://)?(?:www[.])?(?:youtube[.]com/watch[?]v=|youtu[.]be/) ([^&]{11})~x';
            $has_match = preg_match($rx, $data['Choice'][0]['text'], $matches);
            if (!empty($has_match)) { // if watch mode
                $data['Choice'][0]['text'] = 'https://www.youtube.com/embed/' . $matches[1];
            } else if(strpos($data['Choice'][0]['text'], 'https://www.youtube.com/embed/') !== false) {
                
            } else {
                echo json_encode(array('success' => false, 'message' => __('Invalid youtube video')));
                exit;
            }
        } elseif($data['Question']['question_type_id'] == 8) { // image url type
            // short_auto
            if (empty($data['Choice'][0]['text'])) {   
                echo json_encode(array('success' => false, 'message' => __('Enter image url')));
                exit;
            }
        }

        if ($data['Question']['question_type_id'] == 6) $data['Question']['explanation'] = NULL;
        if ($data['Question']['question_type_id'] != 3) $data['Question']['max_allowed'] = NULL;
        if ($data['Question']['question_type_id'] != 2) $data['Question']['case_sensitive'] = 0;

        // pr($data);
        // exit;

        // If user leave form empty, set the default
        if (empty($data['Question']['text']))
            $data['Question']['text'] = __('New Question');

        // If we are editing a existing question, set the ID
        if (!(isset($data['isNew']) && $data['isNew']) || $questionId != -1) {
            $this->Questions->Choices->deleteAll(array(
                'Choices.question_id' => $questionId
            ));

            $data['Question']['id'] = $questionId;
        }

        $save_data = $data['Question'];
        $save_data['choices'] = empty($data['Choice']) ? array() : $data['Choice'];

        // pr($save_data);
        // exit;
        
        if (!empty($save_data['id'])) {
            $question = $this->Questions->get($questionId, ['contain' => []]);
            $save_data = $this->Questions->patchEntity($question, $save_data, ['associated' => ['Choices']]);
            //$save_data = $this->Questions->patchEntity($save_data, ['associated' => ['Choices']]);
        } else {
            $save_data = $this->Questions->newEntity($save_data, ['associated' => ['Choices']]);
        }

        // pr($save_data);
        // exit;

        if ($this->Questions->save($save_data)) {
            $data['Question']['id'] = $save_data['id'];
            if (isset($this->request->data['is_sort'])) { // if choice sorting exist then rearrange array by weight
                // sort by weight asc
                usort($data['Choice'], function($a, $b) {
                    return $a['weight'] - $b['weight'];
                });
                // weight desc
                $data['Choice'] = array_reverse($data['Choice']);
            }

            $response = array(
                'success' => true,
                'Question' => $data['Question'],
                'Choice' => empty($data['Choice']) ? array() : $data['Choice']
            );

            echo json_encode($response);
        }
    }

    public function delete() {
        $this->autoRender = false;
        $questionId = $this->request->data['id'];
        // If user is trying to delete another user quiz, cancel.
        if ($this->isAuthorized($questionId) && $this->Questions->deleteAll(['Questions.id' => $questionId])) {
            // delete choices related to this question
            $this->Questions->Choices->deleteAll(['Choices.question_id' => $questionId]);
            // delete answers related to this question
            $this->Questions->Answers->deleteAll(['Answers.question_id' => $questionId]);
            $response['success'] = true;
        } else {
            $response['success'] = false;
        }
        echo json_encode($response);
    }

    public function duplicate() {
        $this->autoRender = false;
        $questionId = $this->request->data['id'];
        $question = $this->Questions->get($questionId, ['contain' => ['Quizzes', 'Choices']]);

        // pr($question);
        // exit;

        $response['success'] = false;

        if (!empty($question) && ($question->quiz->user_id == $this->Auth->user('id'))) {
            $copy_question['quiz_id'] = $question->quiz_id;
            $copy_question['question_type_id'] = $question->question_type_id;
            $copy_question['text'] = $question->text;
            $copy_question['explanation'] = $question->explanation;
            $copy_question['weight'] = $question->weight;
            $copy_question['max_allowed'] = $question->max_allowed;
            $copy_question['case_sensitive'] = $question->case_sensitive;

            if (!empty($question->choices)) {
                foreach ($question->choices as $key => $choice) {
                    $copy_question['choices'][$key]['text'] = $choice->text;
                    $copy_question['choices'][$key]['points'] = $choice->points;
                    $copy_question['choices'][$key]['weight'] = $choice->weight;
                }
            }
            $copy_question = $this->Questions->newEntity($copy_question, ['associated' => ['Choices']]);
            // pr($copy_question);
            // exit;
            if ($this->Questions->save($copy_question)) {
               $response['message'] = __('Duplicated Successfully');
                $response['success'] = true;
                $response['id'] = $copy_question->id;
            } else {
                $response['message'] = __('Something went wrong, please try again later!');
            }
        } else {
            $response['message'] = __('Invalid question');
        }

        echo json_encode($response);
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

}
