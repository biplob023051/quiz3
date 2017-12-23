<?php
namespace App\Controller;
use Cake\Utility\Hash;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\I18n\I18n;

/**
 * Students Controller
 *
 * @property \App\Model\Table\StudentsTable $Students
 */
class StudentsController extends AppController
{

    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Email');
        $this->Auth->allow(['updateStudent', 'updateAnswer', 'submit', 'success']);
    }

    public function updateScore() {
        $this->autoRender = false;
        $data = $this->request->data;
        // pr($data);
        // exit;
        $response = array('success' => false);

        if (!empty($data['score']) || (int) $data['score'] == 0) {


            $this->loadModel('Choices');
            $choice = $this->Choices->findByQuestionId($data['id'])->first();
            // pr($choice);
            // exit;
            if(empty($choice))
                return;


            if ($data['score'] == 'null') {
                $data['score'] = 'NULL';
            } else if ($data['score'] > $choice->points) {
                $data['score'] = $choice->points;
            } else {
                // do nothing
            }
            
            $response['data'] = $this->Students->Answers->updateScore($data['id'], $data['student_id'], $data['score']);
            // pr($response);
            // exit;
            // update ranking score as well
            $ranking = $this->Students->Rankings->findByStudentId($data['student_id'])->first();
            $ranking->score = $ranking->score + $data['score'] - (float) $data['current_score'];
            $this->Students->Rankings->save($ranking);
            $response['success'] = true;
            $response['score'] = $ranking->score;
            $response['student_id'] = $ranking->student_id;
        }
        echo json_encode($response);
    }

    // Update answer
    public function updateAnswer() {
        $this->autoRender = false;
        $response = array('success' => true);
        // pr($this->request->data);
        // exit;
        if (!$this->Session->check('student_id')) { // check student record
            // student record new entry
            $response = $this->recordStudentData();
            $student_id = $response['student_id'];
        } else {
            $student_id = $this->Session->read('student_id');
        } 

        $student = $this->Students->findById($student_id)->contain(
            [
                'Rankings', 
                'Answers' => function ($q) {
                    return $q->where(['question_id' => $this->request->data['question_id']]);
                }
            ]
        )->first();

        $checkbox_record_delete = $this->request->data['checkbox_record_delete'];
        $checkBox = $this->request->data['checkBox'];

        // pr($checkbox_record_delete);
        // exit;

        $data = array();
        $points = 0;
        $total_change = false;
        $ranking = $student->ranking;

        if (!empty($student->answers)) { // Check if answer exist, then modify or delete
            foreach ($student->answers as $key => $answer) {
                if (empty($checkBox)) { // if not check box
                    if ($answer->question_id == $this->request->data['question_id']) { // if question id exist
                        $data['id'] = $answer->id;
                        $points = empty($answer->score) ? 0 : $answer->score; // Need to deduct from ranking point
                    }
                } else {
                    if (($answer->question_id == $this->request->data['question_id']) && ($answer->text == $this->request->data['text'])) { // if question id exist
                        $data['id'] = $answer->id;
                        $points = empty($answer->score) ? 0 : $answer->score; // Need to deduct from ranking point
                    }
                }    
            }
            if ((!empty($checkbox_record_delete) && empty($checkBox))) {
                // Deduct point whatever it is
                $ranking->score = $ranking->score-$points;
            } 
        } 


        if (empty($data)) { // New Answer
            $total_change = true;
        }

        // Compare with choice if its correct or not
        $this->loadModel('Choices');
        $choices = $this->Choices->find('all')
        ->where(['Choices.question_id IN' => (int)$this->request->data['question_id']])
        ->contain(['Questions'])
        ->toArray();
        // pr($choices);
        // exit;
        $checkMax = 0;
        $correct_answer = 0;
        foreach ($choices as $key2 => $value2) {
            // get maxvalue as a total point increment
            if ($checkMax < $value2->points) {
                $checkMax = $value2->points;
            }

            if (($value2->question->question_type_id == 1) || 
                ($value2->question->question_type_id == 3)) {
                // multiple choice one or many
                if ($value2->text == $this->request->data['text']) {
                    $data['score'] = $value2->points;
                    if ((empty($checkbox_record_delete) && !empty($checkBox)) || (!empty($checkbox_record_delete) && empty($checkBox))) {
                        $correct_answer = $correct_answer + $value2->points;
                    } else {
                        $correct_answer = $correct_answer - $value2->points;
                    }
                } 


            } elseif ($value2->question->question_type_id == 2) { // short automatic point
                $student_answer = $this->request->data['text'];
                if (empty($this->request->data['case_sensitive'])) {
                    $student_answer = strtolower($student_answer);
                    $value2->text = strtolower($value2->text);
                }
                $student_answer = preg_replace('/\s+/', ' ', trim($student_answer));
                $ans_string = preg_replace('/\s+/', ' ', trim($value2->text));

                if ($student_answer === $ans_string) { // Compare whole string
                    $data['score'] = $value2->points;
                    $correct_answer = $correct_answer + $value2->points;
                } else {
                    $student_answer = preg_replace('/\s+/', '', $student_answer);
                    $ans_string = preg_replace('/\s+/', '', $value2->text);
                    $words = explode(';', $student_answer);
                    $matched_word = explode(';', $ans_string);
                    foreach ($words as $key => $value) {
                        //if (!empty($value) && (strpos(strtolower($value2['Choice']['text']), strtolower(trim($value))) !== false)) {
                        if (!empty($value) && (in_array($value, $matched_word))) {
                            $data['score'] = $value2->points;
                            $correct_answer = $correct_answer + $value2->points;
                            break;
                        } else {
                            $data['score'] = 0;
                        }
                    }
                }
                $data['text'] = $this->request->data['text'];
            } elseif ($value2->question->question_type_id == 4) {
                // short manual point
                $data['score'] = null;

            } else {
                $data['score'] = null;
            }
        }
        // pr($checkbox_record_delete);
        // exit;

        if ((empty($checkbox_record_delete) && !empty($checkBox)) || (!empty($checkbox_record_delete) && empty($checkBox))) {
            $data['text'] = $this->request->data['text'];
        } else {
            $data['text'] = '';
        }

        $ranking->score = $ranking->score+$correct_answer;
        
        // pr($data);
        // pr($ranking);
        // exit;

        if (empty($data['text']) && !empty($data['id'])) {
            // Deleted answer
            $this->Students->Answers->deleteAll(['Answers.id' => $data['id']]);
        } else { // Update or add new answer
            if (!empty($this->request->data['checkbox_record'])) {
                $data['id'] = '';
            }
            $data['question_id'] = (int) $this->request->data['question_id'];
            $data['student_id'] = (int) $student_id;
            if (empty($data['id'])) {
                $answer = $this->Students->Answers->newEntity();
                $answer = $this->Students->Answers->patchEntity($answer, $data);
                $this->Students->Answers->save($answer);
            } else {
                $answer_id = $data['id'];
                unset($data['id']);
                $this->Students->Answers->updateAll($data, ['id' => $answer_id]);
            }
            // pr($answer);
            // exit;
        }
        $this->Students->Rankings->save($ranking);

        echo json_encode($response);
        exit;
    }

    public function updateStudent() {
        $this->autoRender = false;
        $response = $this->recordStudentData();
        echo json_encode($response);
        exit;
    }

    // student information updating
    private function recordStudentData() {
        $response = array('success' => false);
        if ($this->Session->check('student_id')) {
            // Update student information
            $student_id = (int) $this->Session->read('student_id');
            $student = $this->Students->get($student_id, ['contain' => []]);

            // pr($student);
            // exit;

            $student->fname = !empty($this->request->data['fname']) ? $this->request->data['fname'] : '';
            $student->lname = !empty($this->request->data['lname']) ? $this->request->data['lname'] : '';
            //$data['Student']['class'] = strtolower(preg_replace('/\s+/', '', $this->request->data['class']));
            $student->class = !empty($this->request->data['class']) ? strtolower(preg_replace('/\s+/', '', $this->request->data['class'])) : '';
            $student->submitted = date('Y-m-d H:i:s');
            $student = $this->Students->save($student);
        } else {
            // Find quiz id
            $quiz = $this->Students->Quizzes->findByRandomId((int)$this->request->data['random_id'])->contain(['Questions', 'Users'])->first();
            $questions = Hash::combine($quiz->questions, '{n}.id', '{n}.id');
            $data['quiz_id'] = $quiz->id;
            
            $total = 0;
            
            $this->loadModel('Choices');

            $choices = $this->Choices->find('all', array('conditions' => array('Choices.question_id IN' => $questions)))->contain(['Questions'])->toArray();

            // pr($choices);
            // exit;

            //pr($choices);
            $checkQuestion = array();
            foreach ($choices as $key => $value) {
                if ($value->question->question_type_id == 1) {
                    if (!in_array($value->question->id, $checkQuestion)) {
                        array_push($checkQuestion, $value->question->id);
                        $checkMax = 0;
                        foreach ($choices as $key1 => $value1) {
                            if ($value1->question->question_type_id == 1) {
                                if ($value->question->id == $value1->question->id) {
                                    if ($value1->points > 0) {
                                        $checkMax = $checkMax < $value1->points ? $value1->points : $checkMax;    
                                    }
                                }
                            }
                        } 
                        $total = $total + $checkMax;   
                    }
                        
                } elseif (($value->question->question_type_id == 3) || ($value->question->question_type_id == 2)) {
                    if ($value->points > 0) {
                        $total = $total + $value->points;    
                    }
                        
                } elseif ($value->question->question_type_id == 4) {
                    if (!empty($value->points)) {
                        $total = $total + $value->points;
                    }
                } else {
                    if (!empty($value->points)) {
                        $total = $total + $value->points;
                    }
                }
            }
            
            // save data in ranking table
            $data['ranking']['quiz_id'] = $quiz->id;
            $data['ranking']['total'] = $total;
            $data['ranking']['score'] = 0;

            $data['fname'] = !empty($this->request->data['fname']) ? $this->request->data['fname'] : '';
            $data['lname'] = !empty($this->request->data['lname']) ? $this->request->data['lname'] : '';
            //$data['Student']['class'] = strtolower(preg_replace('/\s+/', '', $this->request->data['class']));
            $data['class'] = !empty($this->request->data['class']) ? strtolower(preg_replace('/\s+/', '', $this->request->data['class'])) : '';
            $data['submitted'] = date('Y-m-d H:i:s');

            $data = $this->Students->newEntity($data, [
                'validate' => 'InitialRecord',
                'associated' => ['Rankings']
            ]);

            $student = $this->Students->save($data);
        }

        //pr($quiz->user);
        //exit;
        if (!empty($student)) {
            // send email to the admin
            // first 3 students answer taken for any first quiz
            // access level should be free user
            if (!empty($quiz) && (empty($quiz->user->account_level) || ($quiz->user->account_level == 22)) && ($quiz->student_count == 2)) {
                $user_email = $this->Email->sendMail(Configure::read('AdminEmail'), __('[Verkkotesti] Quiz given to students'), $quiz->user, 'quiz_taken_started');
            }
            if (!$this->Session->check('student_id')) {
                $this->Session->write('student_id', $student->id);
            }
            $response['success'] = true;
            $response['student_id'] = $student->id;

            $response['message'] = __('Student saved');
        } else {
            $response['message'] = __('Student saved failed');
        }
        return $response;
    }

    public function submit($quizRandomId) {

        // remove unwanted space and make uppercase for student class
        $this->request->data['class'] = !empty($this->request->data['class']) ? strtolower(preg_replace('/\s+/', '', $this->request->data['class'])) : '';
        $quiz = $this->Students->Quizzes->findByRandomId($quizRandomId, ['contain' => []])->select(['id', 'show_result'])->first();
// pr($quiz);
// exit;
        $this->request->data['status'] = 1;
        $this->request->data['submitted'] = date('Y-m-d H:i:s');
        unset($this->request->data['Answer']);
        unset($this->request->data['data']);

        $student_id = $this->request->data['id'];
        unset($this->request->data['id']);
        $this->Students->updateAll($this->request->data, ['id' => $student_id]);
    
        // Delete session data for student quiz auto update
        $runningFor = $this->Session->read('started');
        $this->Session->delete($runningFor);
        $this->Session->delete('started');
        $this->Session->delete('student_id');
        // save std id
        if (!empty($quiz->show_result)) {
            $this->Session->write('show_result', true);
            return $this->redirect(array('action' => 'success', $student_id));
        } else {
            return $this->redirect(array('action' => 'success'));
        }
    }
    
    public function success($std_id = null) {
        I18n::locale($this->Session->read('user_language'));
        if ($std_id && $this->Session->check('show_result')) { // show result true
            $student_result = $this->Students->find('all')
            ->where(['Students.id' => $std_id])
            ->contain(['Answers', 'Rankings'])
            ->first();

            $quiz = $this->Students->Quizzes->find('all')
            ->where(['Quizzes.id' => $student_result->quiz_id])
            ->contain([
                'Questions' => function($q) {
                    return $q->order(['Questions.weight DESC', 'Questions.id ASC']);
                }
            ])
            ->first();
            $this->set(compact('student_result', 'quiz'));
        }
        $this->Session->destroy();
    }

    public function deleteStudent() {
        $this->autoRender = false;
        $response = array('success' => false);
        $student_id = $this->request->data['student_id'];
        $studentInfo = $this->Students->find('all', array(
                'conditions' => array(
                    'Students.id' => $student_id
                ),
                'contain' => ['Quizzes']
            )
        )->first();

        if ($studentInfo->quiz->user_id == $this->Auth->user('id')) {
            // delete ranking data
            $this->Students->Rankings->deleteAll(['Rankings.student_id' => $student_id]);
            // delete answer data
            $this->Students->Answers->deleteAll(['Rankings.student_id' => $student_id]);

            if ($this->Students->delete($studentInfo)) {
                $response['success'] = true;
                $response['message'] = __('REMOVED');
            }

        } else {
            $response['message'] = __('You are not authorized to do this action');
        } 

        echo json_encode($response);
        exit;
    }

    public function confirmDeleteStudent() {
        $this->autoRender = false;
        $response = array('success' => false);
        $student_id = $this->request->data['student_id'];
        $studentInfo = $this->Students->find('all', array(
                'conditions' => array(
                    'Students.id' => $student_id
                ),
                'contain' => array(
                    'Rankings'
                )
            )
        )->first();
        // pr($studentInfo);
        // exit;
        if (!empty($studentInfo)) {
            $response['success'] = true;
            $response['student_id'] = $studentInfo->id;
            $response['student_full_name'] = $studentInfo->fname . ' ' . $studentInfo->lname;
            $response['student_class'] = $studentInfo->class;
            $response['student_score'] = $studentInfo->ranking->score . '/' . $studentInfo->ranking->total;
        } 
        echo json_encode($response);
        exit;
    }

    // Method of student name class update from answer table
    public function ajaxStdUpdate() {
        $this->autoRender = false;
        $response = array('success' => false);
        $details = explode('-', $this->request->data['std_info']);
        $student_record = $this->Students->find('all')
        ->where(['Students.id' => $details[1]])
        ->contain([
            'Quizzes' => function($q) {
                return $q->select('Quizzes.user_id');
            }
        ])
        ->first();
        // pr($details);
        // exit;
        if (!empty($student_record) && ($student_record->quiz->user_id == $this->Auth->user('id'))) { // permission granted
            if ($details[0] == 'class') { // remove unwanted space and make lowercase
                $this->request->data['value_info'] = !empty($this->request->data['value_info']) ? strtolower(preg_replace('/\s+/', '', $this->request->data['value_info'])) : '';
            }
            $field_name = $details[0];
            $student_record->$field_name = $this->request->data['value_info'];
            if ($this->Students->save($student_record)) {
                $response['success'] = true;
                if (empty($this->request->data['value_info'])) {
                    switch ($field_name) {
                        case 'fname':
                            $value_info = __('FIRST_NAME');
                            break;
                        case 'lname':
                            $value_info = __('LAST_NAME');
                            break;
                        case 'class':
                            $value_info = __('CLASS');
                            break;
                        default:
                            $value_info = '';
                            break;
                    }
                    $response['changetext'] = $value_info;
                } else {
                    $response['changetext'] = $this->request->data['value_info'];
                }
            } else {
                $response['message'] = __('SOMETHING_WENT_WRONG');
            }
        } else {
            $response['message'] = __('SOMETHING_WENT_WRONG');
        }
        echo json_encode($response);
        exit;
    }

}
