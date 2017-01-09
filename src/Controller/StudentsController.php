<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Students Controller
 *
 * @property \App\Model\Table\StudentsTable $Students
 */
class StudentsController extends AppController
{

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
            $ranking->score = $ranking->score + $data['score'] - $data['current_score'];
            $this->Students->Rankings->save($ranking);
            $response['success'] = true;
            $response['score'] = $ranking->score;
            $response['student_id'] = $ranking->student_id;
        }
        echo json_encode($response);
    }

    // Update answer
    public function update_answer() {
        $this->autoRender = false;
        $response = array('success' => true);
        // pr($this->request->data);
        // exit;
        if (empty($this->request->data['student_id']) && !$this->Session->check('student_id')) { // check student record
            // student record new entry
            $this->request->data['fname'] = '';
            $this->request->data['lname'] = '';
            $this->request->data['class'] = '';
            $response = $this->recordStudentData($this->request->data);
            $this->request->data['student_id'] = $response['student_id'];
        } 

        $student = $this->Students->findById($this->request->data['student_id']);

        $checkbox_record_delete = $this->request->data['checkbox_record_delete'];
        $checkBox = $this->request->data['checkBox'];

        // pr($checkbox_record_delete);
        // exit;

        $data = array();
        $points = 0;
        $total_change = false;
        $ranking['Ranking'] = $student['Ranking'];

        if (!empty($student['Answer'])) { // Check if answer exist, then modify or delete
            foreach ($student['Answer'] as $key => $answer) {
                if (empty($checkBox)) { // if not check box
                    if ($answer['question_id'] == $this->request->data['question_id']) { // if question id exist
                        $data['Answer']['id'] = $answer['id'];
                        $points = empty($answer['score']) ? 0 : $answer['score']; // Need to deduct from ranking point
                    }
                } else {
                    if (($answer['question_id'] == $this->request->data['question_id']) && ($answer['text'] == $this->request->data['text'])) { // if question id exist
                        $data['Answer']['id'] = $answer['id'];
                        $points = empty($answer['score']) ? 0 : $answer['score']; // Need to deduct from ranking point
                    }
                }    
            }
            if ((!empty($checkbox_record_delete) && empty($checkBox))) {
                // Deduct point whatever it is
                $ranking['Ranking']['score'] = $ranking['Ranking']['score']-$points;
            } 
        } 


        if (empty($data)) { // New Answer
            $total_change = true;
        }

        // Compare with choice if its correct or not
        $this->loadModel('Choice');
        $choices = $this->Choices->find('all', array(
            'conditions' => array(
                'Choices.question_id' => (int)$this->request->data['question_id']
            )
        ));
        // pr($choices);
        // exit;
        $checkMax = 0;
        $correct_answer = 0;
        foreach ($choices as $key2 => $value2) {
            // get maxvalue as a total point increment
            if ($checkMax < $value2['Choice']['points']) {
                $checkMax = $value2['Choice']['points'];
            }

            if (($value2['Question']['question_type_id'] == 1) || 
                ($value2['Question']['question_type_id'] == 3)) {
                // multiple choice one or many
                if ($value2['Choice']['text'] == $this->request->data['text']) {
                    $data['Answer']['score'] = $value2['Choice']['points'];
                    if ((empty($checkbox_record_delete) && !empty($checkBox)) || (!empty($checkbox_record_delete) && empty($checkBox))) {
                        $correct_answer = $correct_answer + $value2['Choice']['points'];
                    } else {
                        $correct_answer = $correct_answer - $value2['Choice']['points'];
                    }
                } 


            } elseif ($value2['Question']['question_type_id'] == 2) { // short automatic point
                $student_answer = $this->request->data['text'];
                if (empty($this->request->data['case_sensitive'])) {
                    $student_answer = strtolower($student_answer);
                    $value2['Choice']['text'] = strtolower($value2['Choice']['text']);
                }
                $student_answer = preg_replace('/\s+/', ' ', trim($student_answer));
                $ans_string = preg_replace('/\s+/', ' ', trim($value2['Choice']['text']));

                if ($student_answer === $ans_string) { // Compare whole string
                    $data['Answer']['score'] = $value2['Choice']['points'];
                    $correct_answer = $correct_answer + $value2['Choice']['points'];
                } else {
                    $student_answer = preg_replace('/\s+/', '', $student_answer);
                    $ans_string = preg_replace('/\s+/', '', $value2['Choice']['text']);
                    $words = explode(';', $student_answer);
                    $matched_word = explode(';', $ans_string);
                    foreach ($words as $key => $value) {
                        //if (!empty($value) && (strpos(strtolower($value2['Choice']['text']), strtolower(trim($value))) !== false)) {
                        if (!empty($value) && (in_array($value, $matched_word))) {
                            $data['Answer']['score'] = $value2['Choice']['points'];
                            $correct_answer = $correct_answer + $value2['Choice']['points'];
                            break;
                        } else {
                            $data['Answer']['score'] = 0;
                        }
                    }
                }
                $data['Answer']['text'] = $this->request->data['text'];
            } elseif ($value2['Question']['question_type_id'] == 4) {
                // short manual point
                $data['Answer']['score'] = null;

            } else {
                $data['Answer']['score'] = null;
            }
        }
        // pr($checkbox_record_delete);
        // exit;

        if ((empty($checkbox_record_delete) && !empty($checkBox)) || (!empty($checkbox_record_delete) && empty($checkBox))) {
            $data['Answer']['text'] = $this->request->data['text'];
        } else {
            $data['Answer']['text'] = '';
        }

        $ranking['Ranking']['score'] = $ranking['Ranking']['score']+$correct_answer;
        
        // pr($data);
        // pr($ranking);
        // exit;

        if (empty($data['Answer']['text']) && !empty($data['Answer']['id'])) {
            // Deleted answer
            $this->Students->Answers->delete($data['Answer']['id']);
        } else { // Update or add new answer
            if (!empty($this->request->data['checkbox_record'])) {
                $data['Answer']['id'] = '';
            }
            $data['Answer']['question_id'] = (int) $this->request->data['question_id'];
            $data['Answer']['student_id'] = (int) $this->request->data['student_id'];
            $this->Students->Answers->save($data);
        }
        $this->Students->Rankings->save($ranking);

        echo json_encode($response);
        exit;
    }

    public function update_student() {
        $this->autoRender = false;
        $response = $this->recordStudentData($this->request->data);
        echo json_encode($response);
        exit;
    }

    // student information updating
    private function recordStudentData() {
        $response = array('success' => false);
        $this->loadModel('Quiz');
        if (!empty($this->request->data['student_id']) || $this->Session->check('student_id')) {
            // Update student information
            $data['Student']['id'] = !empty($this->request->data['student_id']) ? $this->request->data['student_id'] : (int) $this->Session->read('student_id');
        } else {
            // Find quiz id
            $quiz = $this->Quizzes->findByRandomId((int)$this->request->data['random_id']);
            $questions = Hash::combine($quiz['Question'], '{n}.id', '{n}.id');
            $data['Student']['quiz_id'] = $quiz['Quiz']['id'];
            
            $total = 0;
            
            $this->loadModel('Choice');

            $choices = $this->Choices->find('all', array('conditions' => array('Choices.question_id' => $questions)));

            //pr($choices);
            $checkQuestion = array();
            foreach ($choices as $key => $value) {
                if ($value['Question']['question_type_id'] == 1) {
                    if (!in_array($value['Question']['id'], $checkQuestion)) {
                        array_push($checkQuestion, $value['Question']['id']);
                        $checkMax = 0;
                        foreach ($choices as $key1 => $value1) {
                            if ($value1['Question']['question_type_id'] == 1) {
                                if ($value['Question']['id'] == $value1['Question']['id']) {
                                    if ($value1['Choice']['points'] > 0) {
                                        $checkMax = $checkMax < $value1['Choice']['points'] ? $value1['Choice']['points'] : $checkMax;    
                                    }
                                }
                            }
                        } 
                        $total = $total + $checkMax;   
                    }
                        
                } elseif (($value['Question']['question_type_id'] == 3) || ($value['Question']['question_type_id'] == 2)) {
                    if ($value['Choice']['points'] > 0) {
                        $total = $total + $value['Choice']['points'];    
                    }
                        
                } elseif ($value['Question']['question_type_id'] == 4) {
                    if (!empty($value['Choice']['points'])) {
                        $total = $total + $value['Choice']['points'];
                    } else {
                        $total = $total + $manual_scoring_short['QuestionType']['manual_scoring'];
                    }
                } else {
                    if (!empty($value['Choice']['points'])) {
                        $total = $total + $value['Choice']['points'];
                    } else {
                        $total = $total + $manual_scoring_essay['QuestionType']['manual_scoring'];
                    }
                }
            }
            
            // save data in ranking table
            $data['Ranking']['quiz_id'] = $quiz['Quiz']['id'];
            $data['Ranking']['total'] = $total;
            $data['Ranking']['score'] = 0;
        }

        $data['Student']['fname'] = $this->request->data['fname'];
        $data['Student']['lname'] = $this->request->data['lname'];
        //$data['Student']['class'] = strtolower(preg_replace('/\s+/', '', $this->request->data['class']));
        $data['Student']['class'] = !empty($this->request->data['class']) ? strtolower(preg_replace('/\s+/', '', $this->request->data['class'])) : '';
        $data['Student']['submitted'] = date('Y-m-d H:i:s');

        $student = $this->Students->saveAssociated($data);
        if (!empty($student)) {
            // send email to the admin
            // first 3 students answer taken for any first quiz
            // access level should be free user
            if (!empty($quiz) && (empty($quiz['User']['account_level']) || ($quiz['User']['account_level'] == 22)) && ($quiz['Quiz']['student_count'] == 2)) {
                $user = $quiz['User'];
                $Email = new CakeEmail();
                $Email->viewVars(array('user' => $user));
                $Email->from(array('pietu.halonen@verkkotesti.fi' => 'WebQuiz.fi'));
                $Email->template('quiz_taken_started');
                $Email->emailFormat('html');
                $Email->to(Configure::read('AdminEmail'));
                $Email->subject(__('[Verkkotesti] Quiz given to students'));
                $Email->send();
            }
            if (!$this->Session->check('student_id')) {
                $this->Session->write('student_id', $this->Students->id);
            }
            $response['success'] = true;
            $response['student_id'] = $this->Students->id;

            $response['message'] = __('Student saved');
        } else {
            $response['message'] = __('Student saved failed');
        }
        return $response;
    }

    public function submit($quizRandomId) {

        // remove unwanted space and make uppercase for student class
        $this->request->data['Student']['class'] = !empty($this->request->data['Student']['class']) ? strtolower(preg_replace('/\s+/', '', $this->request->data['Student']['class'])) : '';
        $data = $this->request->data;
        $this->Students->set($data['Student']);
        if (!$this->Students->validates()) {
            $error = array();
            foreach ($this->Students->validationErrors as $_error) {
                $error[] = $_error[0];
            }
            $this->Session->write('FormData', $data);
            $this->Session->setFlash($error, 'error_form', array(), 'error');
            return $this->redirect(array(
                        'controller' => 'Quiz',
                        'action' => 'live',
                        $quizRandomId
            ));
        }

        $this->loadModel('Quiz');
        $this->Quizzes->unBindModelAll();
        $quiz = $this->Quizzes->findByRandomId($quizRandomId);

        $this->request->data['Student']['status'] = 1;
        $this->request->data['Student']['submitted'] = date('Y-m-d H:i:s');
        unset($this->request->data['Answer']);
        
        $this->Students->save($this->request->data);
    
        // Delete session data for student quiz auto update
        $runningFor = $this->Session->read('started');
        $this->Session->delete($runningFor);
        $this->Session->delete('started');
        $this->Session->delete('student_id');

        // save std id
        if (!empty($quiz['Quiz']['show_result'])) {
            $this->Session->write('show_result', true);
            return $this->redirect(array('action' => 'success', $this->Students->id));
        } else {
            return $this->redirect(array('action' => 'success'));
        }
    }
    
    public function success($std_id = null) {
        if ($this->Session->check('show_result')) { // show result true
            $student_result = $this->Students->find('first', array(
                'conditions' => array('Students.id' => $std_id)
            ));
            $this->Students->Quizzes->Behaviors->load('Containable');
            $quiz = $this->Students->Quizzes->find('first', array(
                'conditions' => array(
                    'Quizzes.id' => $student_result['Quiz']['id'],
                ),
                'contain' => array(
                    'Question' => array(
                        'order' => array('Questions.weight DESC', 'Questions.id ASC'),
                    ),
                )
            ));
            $this->set(compact('student_result', 'quiz'));
            $this->Session->delete('show_result');
        }
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
                $response['message'] = __('Successfully removed');
            }

        } else {
            $response['message'] = __('You are not authorized to continue this operation!');
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
            $response['student_score'] = $studentInfo->rankings[0]->score . '/' . $studentInfo->rankings[0]->total;
        } 
        echo json_encode($response);
        exit;
    }

    // Method of student name class update from answer table
    public function ajax_std_update() {
        $this->autoRender = false;
        $response = array('success' => false);
        $details = explode('-', $this->request->data['std_info']);
        $this->Students->Behaviors->load('Containable');
        $student_record = $this->Students->find('first', array(
            'conditions' => array(
                'Students.id' => $details[1]
            ),
            'contain' => array(
                'Quiz' => array(
                    'fields' => array('Quizzes.id', 'Quizzes.user_id')
                )
            ),
            'fields' => array('Students.id'),
            'recursive' => -1
        ));
        if (!empty($student_record) && ($student_record['Quiz']['user_id'] == $this->Auth->user('id'))) { // permission granted
            $this->Students->id = $student_record['Student']['id'];
            
            if ($details[0] == 'class') { // remove unwanted space and make lowercase
                $this->request->data['value_info'] = !empty($this->request->data['value_info']) ? strtolower(preg_replace('/\s+/', '', $this->request->data['value_info'])) : '';
            }

            if ($this->Students->saveField($details[0], $this->request->data['value_info'])) {
                $response['success'] = true;
                $response['changetext'] = $this->request->data['value_info'];
            } else {
                $response['message'] = __('Something wrong, please try again!');
            }
        } else {
            $response['message'] = __('Something wrong, please try again!');
        }
        echo json_encode($response);
        exit;
    }

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index1()
    {
        $this->paginate = [
            'contain' => ['Quizzes']
        ];
        $students = $this->paginate($this->Students);

        $this->set(compact('students'));
        $this->set('_serialize', ['students']);
    }

    /**
     * View method
     *
     * @param string|null $id Student id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view1($id = null)
    {
        $student = $this->Students->get($id, [
            'contain' => ['Quizzes', 'Answers', 'Rankings']
        ]);

        $this->set('student', $student);
        $this->set('_serialize', ['student']);
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|void Redirects on successful add, renders view otherwise.
     */
    public function add1()
    {
        $student = $this->Students->newEntity();
        if ($this->request->is('post')) {
            $student = $this->Students->patchEntity($student, $this->request->data);
            if ($this->Students->save($student)) {
                $this->Flash->success(__('The student has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The student could not be saved. Please, try again.'));
            }
        }
        $quizzes = $this->Students->Quizzes->find('list', ['limit' => 200]);
        $this->set(compact('student', 'quizzes'));
        $this->set('_serialize', ['student']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Student id.
     * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit1($id = null)
    {
        $student = $this->Students->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $student = $this->Students->patchEntity($student, $this->request->data);
            if ($this->Students->save($student)) {
                $this->Flash->success(__('The student has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The student could not be saved. Please, try again.'));
            }
        }
        $quizzes = $this->Students->Quizzes->find('list', ['limit' => 200]);
        $this->set(compact('student', 'quizzes'));
        $this->set('_serialize', ['student']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Student id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete1($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $student = $this->Students->get($id);
        if ($this->Students->delete($student)) {
            $this->Flash->success(__('The student has been deleted.'));
        } else {
            $this->Flash->error(__('The student could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
