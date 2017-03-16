<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Core\Configure;

/**
 * Helps Controller
 *
 * @property \App\Model\Table\HelpsTable $Helps
 */
class MaintenanceController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Email');
        $this->Auth->allow(['notice']);
    }

    public function notice() {
        // Remove maintenance mode
        $setting = $this->_getSettings();
        if (empty($setting['offline_status']))
        $this->redirect(array('controller' => 'quiz', 'action' => 'index'));
        $this->set('title_for_layout', __('Pardon for the dust!'));
        $this->render('/Element/Maintenance/notice');
    }

    public function loadDummyData() {
        $this->autoRender = false;
        $this->loadModel('Quizzes');
        $created_quiz = $this->Quizzes->find('all')->where(['Quizzes.user_id' => $this->Auth->user('id')])->first();
        if (!empty($created_quiz)) {
            $this->Flash->error(__('No direct access on this location'));
            return $this->redirect(array('controller' => 'quiz', 'action' => 'index'));
        }
        $this->importQuizzes($this->Auth->user('id'));
        $first_quiz_create = $this->Email->sendMail(Configure::read('AdminEmail'), __('[Verkkotesti] Demo quizzes loaded'), $this->Auth->user(), 'first_quiz_create');
        $this->Flash->success(__('Imported successfully'));
        return $this->redirect($this->referer());
    }

    public function importQuizzes($user_id) {
        $this->loadModel('Quizzes');
        $quizes[0]['name'] = 'MALLITESTI: Fruits and vegetables';
        $quizes[0]['description'] = 'Tässä kokeessa testataan hedelmien ja vihannesten sanastoa englanniksi. (MALLITESTI: voit poistaa tämän testin kun et tarvitse sitä enää.)';
        $quizes[0]['student_count'] = 9;

        $quizes[1]['name'] = 'MALLITESTI: Musiikin kotitehtävä';
        $quizes[1]['description'] = 'Ennen seuraavaa tuntia, katso oheiset videot ja tee niihin liittyvät tehtävät. (MALLITESTI: voit poistaa tämän testin kun et tarvitse sitä enää.)';
        $quizes[1]['student_count'] = 9;
        $quizes[1]['show_result'] = 1;

        $quizes[2]['name'] = 'MALLITESTI: Itsearviolomake';
        $quizes[2]['description'] = 'Täytä oheinen itsearviolomake huolellisesti. (MALLITESTI: voit poistaa tämän testin kun et tarvitse sitä enää.)';
        $quizes[2]['student_count'] = 9;

        foreach ($quizes as $key1 => $quiz) {
            $quiz['user_id'] = $user_id;
            $new_quiz = $this->Quizzes->newEntity();
            $new_quiz = $this->Quizzes->patchEntity($new_quiz, $quiz);
            //$new_quiz = $this->Quizzes->save($new_quiz);
            // pr($new_quiz);
            // exit;
            if ($this->Quizzes->save($new_quiz)) { // Save Quiz
                $new_quiz->random_id = $new_quiz->id . $this->randText(2, true);
                $this->Quizzes->save($new_quiz);
                $questions = array(); // Prevent duplicate questions
                $question_ids = array(); // Question id array

                if ($key1 == 0) { // Quiz 1
                    $questions[0]['quiz_id'] = $new_quiz->id;
                    $questions[0]['question_type_id'] = 2;
                    $questions[0]['text'] = 'Käännä englanniksi "omena".';
                    $questions[0]['explanation'] = 'Oikea vastaus +2p';
                    $questions[0]['weight'] = 3;
                    $questions[1]['quiz_id'] = $new_quiz->id;
                    $questions[1]['question_type_id'] = 2;
                    $questions[1]['text'] = 'Käännä englanniksi "mansikka".';
                    $questions[1]['explanation'] = 'Oikea vastaus +2p';
                    $questions[1]['weight'] = 2;
                    $questions[2]['quiz_id'] = $new_quiz->id;
                    $questions[2]['question_type_id'] = 2;
                    $questions[2]['text'] = 'Käännä englanniksi "porkkana.';
                    $questions[2]['explanation'] = 'Oikea vastaus +2p';
                    $questions[2]['weight'] = 1;

                    $questions[3]['quiz_id'] = $new_quiz->id;
                    $questions[3]['question_type_id'] = 6;
                    $questions[3]['text'] = 'Käännössanat';
                    $questions[3]['weight'] = 4;

                    $questions[4]['quiz_id'] = $new_quiz->id;
                    $questions[4]['question_type_id'] = 6;
                    $questions[4]['text'] = 'Monivalinnat';

                    $questions[5]['quiz_id'] = $new_quiz->id;
                    $questions[5]['question_type_id'] = 3;
                    $questions[5]['text'] = 'Mitkä seuraavista ovat hedelmiä?';
                    $questions[5]['explanation'] = 'Oikeasta vastauksesta +1p';
                    $questions[5]['max_allowed'] = 3;

                    $questions[6]['quiz_id'] = $new_quiz->id;
                    $questions[6]['question_type_id'] = 1;
                    $questions[6]['text'] = 'Mikä seuraavista on "ananas" englanniksi?';
                    $questions[6]['explanation'] = 'Oikeasta vastauksesta +1p';

                    $questions[7]['quiz_id'] = $new_quiz->id;
                    $questions[7]['question_type_id'] = 6;
                    $questions[7]['text'] = 'Avoimet tehtävät';

                    $questions[8]['quiz_id'] = $new_quiz->id;
                    $questions[8]['question_type_id'] = 5;
                    $questions[8]['text'] = 'Muodosta englanniksi lause, jossa käytät KAHTA hedelmää';
                    $questions[8]['explanation'] = 'Hedelmistä 0-2p, muusta lauseesta 0-4p.';

                    $questions[9]['quiz_id'] = $new_quiz->id;
                    $questions[9]['question_type_id'] = 4;
                    $questions[9]['text'] = 'Nimeä englanniksi suosikkihedelmäsi.';
                    
                    foreach ($questions as $key2 => $question) {
                        $new_question = $this->Quizzes->Questions->newEntity();
                        $new_question = $this->Quizzes->Questions->patchEntity($new_question, $question);
                        if ($this->Quizzes->Questions->save($new_question)) { // save related question
                            $choices = array();
                            $question_ids[] = $new_question->id;
                            // save related choice
                            if ($key2 == 0) { // first question choices
                                $choice['question_id'] = $new_question->id;
                                $choice['text'] = 'apple';
                                $choice['points'] = 2.00;
                                $choice['weight'] = NULL;
                                $new_choice = $this->Quizzes->Questions->Choices->newEntity();
                                $new_choice = $this->Quizzes->Questions->Choices->patchEntity($new_choice, $choice);
                                $this->Quizzes->Questions->Choices->save($new_choice);
                            } elseif ($key2 == 1) { // first question choices
                                $choice['question_id'] = $new_question->id;
                                $choice['text'] = 'strawberry';
                                $choice['points'] = 2.00;
                                $choice['weight'] = NULL;
                                $new_choice = $this->Quizzes->Questions->Choices->newEntity();
                                $new_choice = $this->Quizzes->Questions->Choices->patchEntity($new_choice, $choice);
                                $this->Quizzes->Questions->Choices->save($new_choice);
                            } elseif ($key2 == 2) { // first question choices
                                $choice['question_id'] = $new_question->id;
                                $choice['text'] = 'carrot';
                                $choice['points'] = 2.00;
                                $choice['weight'] = NULL;
                                $new_choice = $this->Quizzes->Questions->Choices->newEntity();
                                $new_choice = $this->Quizzes->Questions->Choices->patchEntity($new_choice, $choice);
                                $this->Quizzes->Questions->Choices->save($new_choice);
                            } elseif ($key2 == 5) { // 3/4 question has not choice
                                $choices[0]['question_id'] = $new_question->id;
                                $choices[0]['text'] = 'Orange';
                                $choices[0]['points'] = 1.00;
                                $choices[0]['weight'] = NULL;

                                $choices[1]['question_id'] = $new_question->id;
                                $choices[1]['text'] = 'Cucumber';
                                $choices[1]['points'] = 0.00;
                                $choices[1]['weight'] = 5;

                                $choices[2]['question_id'] = $new_question->id;
                                $choices[2]['text'] = 'Banana';
                                $choices[2]['points'] = 1.00;
                                $choices[2]['weight'] = 4;

                                $choices[3]['question_id'] = $new_question->id;
                                $choices[3]['text'] = 'Grass';
                                $choices[3]['points'] = 0.00;
                                $choices[3]['weight'] = 3;

                                $choices[4]['question_id'] = $new_question->id;
                                $choices[4]['text'] = 'Pear';
                                $choices[4]['points'] = 1.00;
                                $choices[4]['weight'] = 1;

                                $choices[5]['question_id'] = $new_question->id;
                                $choices[5]['text'] = 'Birch';
                                $choices[5]['points'] = 0.00;
                                $choices[5]['weight'] = 2;

                                foreach ($choices as $key3 => $choice) {
                                    $new_choice = $this->Quizzes->Questions->Choices->newEntity();
                                    $new_choice = $this->Quizzes->Questions->Choices->patchEntity($new_choice, $choice);
                                    $this->Quizzes->Questions->Choices->save($new_choice);
                                }
                            } elseif ($key2 == 6) { 
                                $choices[0]['question_id'] = $new_question->id;
                                $choices[0]['text'] = 'Ananas';
                                $choices[0]['points'] = 0.00;
                                $choices[0]['weight'] = NULL;

                                $choices[1]['question_id'] = $new_question->id;
                                $choices[1]['text'] = 'Birchorange';
                                $choices[1]['points'] = 0.00;
                                $choices[1]['weight'] = NULL;

                                $choices[2]['question_id'] = $new_question->id;
                                $choices[2]['text'] = 'Pineapple';
                                $choices[2]['points'] = 1.00;
                                $choices[2]['weight'] = NULL;

                                $choices[3]['question_id'] = $new_question->id;
                                $choices[3]['text'] = 'Oakfruit';
                                $choices[3]['points'] = 0.00;
                                $choices[3]['weight'] = NULL;

                                foreach ($choices as $key3 => $choice) {
                                    $new_choice = $this->Quizzes->Questions->Choices->newEntity();
                                    $new_choice = $this->Quizzes->Questions->Choices->patchEntity($new_choice, $choice);
                                    $this->Quizzes->Questions->Choices->save($new_choice);
                                }
                            } elseif ($key2 == 8) { // 7 skipped first question choices
                                $choice['question_id'] = $new_question->id;
                                $choice['text'] = 'Essay';
                                $choice['points'] = 6.00;
                                $choice['weight'] = NULL;
                                $new_choice = $this->Quizzes->Questions->Choices->newEntity();
                                $new_choice = $this->Quizzes->Questions->Choices->patchEntity($new_choice, $choice);
                                $this->Quizzes->Questions->Choices->save($new_choice);
                            } elseif ($key2 == 9) { // first question choices
                                $choice['question_id'] = $new_question->id;
                                $choice['text'] = 'Short_manual';
                                $choice['points'] = 3.00;
                                $choice['weight'] = NULL;
                                $new_choice = $this->Quizzes->Questions->Choices->newEntity();
                                $new_choice = $this->Quizzes->Questions->Choices->patchEntity($new_choice, $choice);
                                $this->Quizzes->Questions->Choices->save($new_choice);
                            } else {
                                // do nothing
                            }


                        }
                    }
                    
                    // Save Student data for first Quiz
                    // first quiz students
                    $students = array();
                    $students[0]['quiz_id'] = $new_quiz->id;
                    $students[0]['fname'] = 'Armi';
                    $students[0]['lname'] = 'Arvaaja';
                    $students[0]['class'] = '4a';
                    $students[0]['status'] = 1;
                    $students[0]['submitted'] = date('Y-m-d H:i:s');

                    $students[1]['quiz_id'] = $new_quiz->id;
                    $students[1]['fname'] = 'Siiri';
                    $students[1]['lname'] = 'Sähäkkä';
                    $students[1]['class'] = '4a';
                    $students[1]['status'] = 1;
                    $students[1]['submitted'] = date('Y-m-d H:i:s');

                    $students[2]['quiz_id'] = $new_quiz->id;
                    $students[2]['fname'] = 'Ossi';
                    $students[2]['lname'] = 'Osaaja';
                    $students[2]['class'] = '4a';
                    $students[2]['status'] = 1;
                    $students[2]['submitted'] = date('Y-m-d H:i:s');

                    $students[3]['quiz_id'] = $new_quiz->id;
                    $students[3]['fname'] = 'Jaakko';
                    $students[3]['lname'] = 'Janoinen';
                    $students[3]['class'] = '4c';
                    $students[3]['status'] = 1;
                    $students[3]['submitted'] = date('Y-m-d H:i:s');

                    $students[4]['quiz_id'] = $new_quiz->id;
                    $students[4]['fname'] = 'Veera';
                    $students[4]['lname'] = 'Vikkelä';
                    $students[4]['class'] = '4c';
                    $students[4]['status'] = 1;
                    $students[4]['submitted'] = date('Y-m-d H:i:s');

                    $students[5]['quiz_id'] = $new_quiz->id;
                    $students[5]['fname'] = 'Kerttu';
                    $students[5]['lname'] = 'Kekseliäs';
                    $students[5]['class'] = '4c';
                    $students[5]['status'] = 1;
                    $students[5]['submitted'] = date('Y-m-d H:i:s');

                    $students[6]['quiz_id'] = $new_quiz->id;
                    $students[6]['fname'] = 'Uuno';
                    $students[6]['lname'] = 'Uninen';
                    $students[6]['class'] = '4f';
                    $students[6]['status'] = 1;
                    $students[6]['submitted'] = date('Y-m-d H:i:s');

                    $students[7]['quiz_id'] = $new_quiz->id;
                    $students[7]['fname'] = 'Leevi';
                    $students[7]['lname'] = 'Loistava';
                    $students[7]['class'] = '4f';
                    $students[7]['status'] = 1;
                    $students[7]['submitted'] = date('Y-m-d H:i:s');

                    $students[8]['quiz_id'] = $new_quiz->id;
                    $students[8]['fname'] = 'Vertti';
                    $students[8]['lname'] = 'Vemmelsääri';
                    $students[8]['class'] = '4f';
                    $students[8]['status'] = 1;
                    $students[8]['submitted'] = date('Y-m-d H:i:s');

                    foreach ($students as $key4 => $student) { // Save all student of first quiz
                        $new_student = $this->Quizzes->Students->newEntity();
                        $new_student = $this->Quizzes->Students->patchEntity($new_student, $student);
                        if ($this->Quizzes->Students->save($new_student)) { // Save student data
                            $answers = array();
                            if ($key4 == 0) { // First student answer/ranking save
                                $answers[0]['question_id'] = $question_ids[0];
                                $answers[0]['student_id'] = $new_student->id;
                                $answers[0]['text'] = 'appel';
                                $answers[0]['score'] = 0.00;

                                $answers[1]['question_id'] = $question_ids[1];
                                $answers[1]['student_id'] = $new_student->id;
                                $answers[1]['text'] = 'strooberri';
                                $answers[1]['score'] = 0.00;

                                $answers[2]['question_id'] = $question_ids[2];
                                $answers[2]['student_id'] = $new_student->id;
                                $answers[2]['text'] = 'kärot';
                                $answers[2]['score'] = 0.00;

                                // 2 skipped
                                $answers[3]['question_id'] = $question_ids[5];
                                $answers[3]['student_id'] = $new_student->id;
                                $answers[3]['text'] = 'Banana';
                                $answers[3]['score'] = 1.00;

                                $answers[4]['question_id'] = $question_ids[5];
                                $answers[4]['student_id'] = $new_student->id;
                                $answers[4]['text'] = 'Pear';
                                $answers[4]['score'] = 1.00;

                                $answers[5]['question_id'] = $question_ids[5];
                                $answers[5]['student_id'] = $new_student->id;
                                $answers[5]['text'] = 'Cucumber';
                                $answers[5]['score'] = 0.00;

            
                                $answers[6]['question_id'] = $question_ids[6];
                                $answers[6]['student_id'] = $new_student->id;
                                $answers[6]['text'] = 'Ananas';
                                $answers[6]['score'] = 0.00;

                                // 1 skipped
                                $answers[7]['question_id'] = $question_ids[8];
                                $answers[7]['student_id'] = $new_student->id;
                                $answers[7]['text'] = 'I laik appel but I dont like banana.';
                                $answers[7]['score'] = 2.00;

                                $answers[8]['question_id'] = $question_ids[9];
                                $answers[8]['student_id'] = $new_student->id;
                                $answers[8]['text'] = 'banaana';
                                $answers[8]['score'] = 1.00;

                                foreach ($answers as $key5 => $answer) { // save answers
                                    $new_answer = $this->Quizzes->Students->Answers->newEntity();
                                    $new_answer = $this->Quizzes->Students->Answers->patchEntity($new_answer, $answer);
                                    $this->Quizzes->Students->Answers->save($new_answer);
                                } // end of anwers saving

                                // Prepare ranking data
                                $ranking['quiz_id'] = $new_quiz->id;
                                $ranking['student_id'] = $new_student->id;
                                $ranking['score'] = 5.00;
                                $ranking['total'] = 19.00; 

                                $new_ranking = $this->Quizzes->Rankings->newEntity();
                                $new_ranking = $this->Quizzes->Rankings->patchEntity($new_ranking, $ranking);
                                $this->Quizzes->Rankings->save($new_ranking);
                            } // First student answers/ranking save end

                            if ($key4 == 1) { // Second student answer/ranking save
                                $answers[0]['question_id'] = $question_ids[0];
                                $answers[0]['student_id'] = $new_student->id;
                                $answers[0]['text'] = 'apple';
                                $answers[0]['score'] = 2.00;

                                $answers[1]['question_id'] = $question_ids[1];
                                $answers[1]['student_id'] = $new_student->id;
                                $answers[1]['text'] = 'strooberri';
                                $answers[1]['score'] = 0.00;

                                $answers[2]['question_id'] = $question_ids[2];
                                $answers[2]['student_id'] = $new_student->id;
                                $answers[2]['text'] = 'carrot';
                                $answers[2]['score'] = 2.00;

                                $answers[3]['question_id'] = $question_ids[5];
                                $answers[3]['student_id'] = $new_student->id;
                                $answers[3]['text'] = 'Banana';
                                $answers[3]['score'] = 1.00;

                                $answers[4]['question_id'] = $question_ids[5];
                                $answers[4]['student_id'] = $new_student->id;
                                $answers[4]['text'] = 'Pear';
                                $answers[4]['score'] = 1.00;

                                $answers[5]['question_id'] = $question_ids[5];
                                $answers[5]['student_id'] = $new_student->id;
                                $answers[5]['text'] = 'Orange';
                                $answers[5]['score'] = 1.00;

                                $answers[6]['question_id'] = $question_ids[6];
                                $answers[6]['student_id'] = $new_student->id;
                                $answers[6]['text'] = 'Pineapple';
                                $answers[6]['score'] = 1.00;

                                $answers[7]['question_id'] = $question_ids[8];
                                $answers[7]['student_id'] = $new_student->id;
                                $answers[7]['text'] = 'My favourite fruits are banana and apple.';
                                $answers[7]['score'] = 6.00;

                                $answers[8]['question_id'] = $question_ids[9];
                                $answers[8]['student_id'] = $new_student->id;
                                $answers[8]['text'] = 'apple';
                                $answers[8]['score'] = 3.00;

                                foreach ($answers as $key5 => $answer) { // save answers
                                    $new_answer = $this->Quizzes->Students->Answers->newEntity();
                                    $new_answer = $this->Quizzes->Students->Answers->patchEntity($new_answer, $answer);
                                    $this->Quizzes->Students->Answers->save($new_answer);
                                } // end of anwers saving

                                // Prepare ranking data
                                $ranking['quiz_id'] = $new_quiz->id;
                                $ranking['student_id'] = $new_student->id;
                                $ranking['score'] = 17.00;
                                $ranking['total'] = 19.00; 

                                $new_ranking = $this->Quizzes->Rankings->newEntity();
                                $new_ranking = $this->Quizzes->Rankings->patchEntity($new_ranking, $ranking);
                                $this->Quizzes->Rankings->save($new_ranking);
                            } // Second student answers/ranking save end

                            if ($key4 == 2) { // Third student answer/ranking save
                                $answers[0]['question_id'] = $question_ids[0];
                                $answers[0]['student_id'] = $new_student->id;
                                $answers[0]['text'] = 'apple';
                                $answers[0]['score'] = 2.00;

                                $answers[1]['question_id'] = $question_ids[1];
                                $answers[1]['student_id'] = $new_student->id;
                                $answers[1]['text'] = 'strawberry';
                                $answers[1]['score'] = 2.00;

                                $answers[2]['question_id'] = $question_ids[2];
                                $answers[2]['student_id'] = $new_student->id;
                                $answers[2]['text'] = 'carrot';
                                $answers[2]['score'] = 2.00;

                                $answers[3]['question_id'] = $question_ids[5];
                                $answers[3]['student_id'] = $new_student->id;
                                $answers[3]['text'] = 'Banana';
                                $answers[3]['score'] = 1.00;

                                $answers[4]['question_id'] = $question_ids[5];
                                $answers[4]['student_id'] = $new_student->id;
                                $answers[4]['text'] = 'Pear';
                                $answers[4]['score'] = 1.00;

                                $answers[5]['question_id'] = $question_ids[5];
                                $answers[5]['student_id'] = $new_student->id;
                                $answers[5]['text'] = 'Orange';
                                $answers[5]['score'] = 1.00;

                                $answers[6]['question_id'] = $question_ids[6];
                                $answers[6]['student_id'] = $new_student->id;
                                $answers[6]['text'] = 'Pineapple';
                                $answers[6]['score'] = 1.00;

                                $answers[7]['question_id'] = $question_ids[8];
                                $answers[7]['student_id'] = $new_student->id;
                                $answers[7]['text'] = 'Fruit salad is the best when it has pineapple and banana.';
                                $answers[7]['score'] = 6.00;

                                $answers[8]['question_id'] = $question_ids[9];
                                $answers[8]['student_id'] = $new_student->id;
                                $answers[8]['text'] = 'pineapple';
                                $answers[8]['score'] = 3.00;

                                foreach ($answers as $key5 => $answer) { // save answers
                                    $new_answer = $this->Quizzes->Students->Answers->newEntity();
                                    $new_answer = $this->Quizzes->Students->Answers->patchEntity($new_answer, $answer);
                                    $this->Quizzes->Students->Answers->save($new_answer);
                                } // end of anwers saving

                                // Prepare ranking data
                                $ranking['quiz_id'] = $new_quiz->id;
                                $ranking['student_id'] = $new_student->id;
                                $ranking['score'] = 19.00;
                                $ranking['total'] = 19.00; 

                                $new_ranking = $this->Quizzes->Rankings->newEntity();
                                $new_ranking = $this->Quizzes->Rankings->patchEntity($new_ranking, $ranking);
                                $this->Quizzes->Rankings->save($new_ranking);
                            } // Third student answers/ranking save end

                            if ($key4 == 3) { // Fourth student answer/ranking save
                                $answers[0]['question_id'] = $question_ids[0];
                                $answers[0]['student_id'] = $new_student->id;
                                $answers[0]['text'] = 'apple';
                                $answers[0]['score'] = 2.00;

                                $answers[1]['question_id'] = $question_ids[1];
                                $answers[1]['student_id'] = $new_student->id;
                                $answers[1]['text'] = '-';
                                $answers[1]['score'] = 0.00;

                                $answers[2]['question_id'] = $question_ids[2];
                                $answers[2]['student_id'] = $new_student->id;
                                $answers[2]['text'] = 'carrot';
                                $answers[2]['score'] = 2.00;

                                $answers[3]['question_id'] = $question_ids[5];
                                $answers[3]['student_id'] = $new_student->id;
                                $answers[3]['text'] = 'Banana';
                                $answers[3]['score'] = 1.00;

                                $answers[4]['question_id'] = $question_ids[5];
                                $answers[4]['student_id'] = $new_student->id;
                                $answers[4]['text'] = 'Pear';
                                $answers[4]['score'] = 1.00;

                                $answers[5]['question_id'] = $question_ids[5];
                                $answers[5]['student_id'] = $new_student->id;
                                $answers[5]['text'] = 'Orange';
                                $answers[5]['score'] = 1.00;

                                $answers[6]['question_id'] = $question_ids[6];
                                $answers[6]['student_id'] = $new_student->id;
                                $answers[6]['text'] = 'Pineapple';
                                $answers[6]['score'] = 1.00;

                                $answers[7]['question_id'] = $question_ids[8];
                                $answers[7]['student_id'] = $new_student->id;
                                $answers[7]['text'] = 'I am allerkik to apple but not to banana.';
                                $answers[7]['score'] = 5.00;

                                $answers[8]['question_id'] = $question_ids[9];
                                $answers[8]['student_id'] = $new_student->id;
                                $answers[8]['text'] = 'banana';
                                $answers[8]['score'] = 3.00;

                                foreach ($answers as $key5 => $answer) { // save answers
                                    $new_answer = $this->Quizzes->Students->Answers->newEntity();
                                    $new_answer = $this->Quizzes->Students->Answers->patchEntity($new_answer, $answer);
                                    $this->Quizzes->Students->Answers->save($new_answer);
                                } // end of anwers saving

                                // Prepare ranking data
                                $ranking['quiz_id'] = $new_quiz->id;
                                $ranking['student_id'] = $new_student->id;
                                $ranking['score'] = 15.00;
                                $ranking['total'] = 19.00; 

                                $new_ranking = $this->Quizzes->Rankings->newEntity();
                                $new_ranking = $this->Quizzes->Rankings->patchEntity($new_ranking, $ranking);
                                $this->Quizzes->Rankings->save($new_ranking);
                            } // Fourth student answers/ranking save end

                            if ($key4 == 4) { // 5th student answer/ranking save
                                $answers[0]['question_id'] = $question_ids[0];
                                $answers[0]['student_id'] = $new_student->id;
                                $answers[0]['text'] = 'apple';
                                $answers[0]['score'] = 2.00;

                                $answers[1]['question_id'] = $question_ids[1];
                                $answers[1]['student_id'] = $new_student->id;
                                $answers[1]['text'] = 'strawberry';
                                $answers[1]['score'] = 2.00;

                                $answers[2]['question_id'] = $question_ids[2];
                                $answers[2]['student_id'] = $new_student->id;
                                $answers[2]['text'] = 'carrot';
                                $answers[2]['score'] = 2.00;

                                $answers[3]['question_id'] = $question_ids[5];
                                $answers[3]['student_id'] = $new_student->id;
                                $answers[3]['text'] = 'Banana';
                                $answers[3]['score'] = 1.00;

                                $answers[4]['question_id'] = $question_ids[5];
                                $answers[4]['student_id'] = $new_student->id;
                                $answers[4]['text'] = 'Pear';
                                $answers[4]['score'] = 1.00;

                                $answers[5]['question_id'] = $question_ids[5];
                                $answers[5]['student_id'] = $new_student->id;
                                $answers[5]['text'] = 'Orange';
                                $answers[5]['score'] = 1.00;

                                $answers[6]['question_id'] = $question_ids[6];
                                $answers[6]['student_id'] = $new_student->id;
                                $answers[6]['text'] = 'Ananas';
                                $answers[6]['score'] = 0.00;

                                $answers[7]['question_id'] = $question_ids[8];
                                $answers[7]['student_id'] = $new_student->id;
                                $answers[7]['text'] = 'In Spain I ate lots of oranges and bananas.';
                                $answers[7]['score'] = 6.00;

                                $answers[8]['question_id'] = $question_ids[9];
                                $answers[8]['student_id'] = $new_student->id;
                                $answers[8]['text'] = 'cherry';
                                $answers[8]['score'] = 1.00;

                                foreach ($answers as $key5 => $answer) { // save answers
                                    $new_answer = $this->Quizzes->Students->Answers->newEntity();
                                    $new_answer = $this->Quizzes->Students->Answers->patchEntity($new_answer, $answer);
                                    $this->Quizzes->Students->Answers->save($new_answer);
                                } // end of anwers saving

                                // Prepare ranking data
                                $ranking['quiz_id'] = $new_quiz->id;
                                $ranking['student_id'] = $new_student->id;
                                $ranking['score'] = 16.00;
                                $ranking['total'] = 19.00; 

                                $new_ranking = $this->Quizzes->Rankings->newEntity();
                                $new_ranking = $this->Quizzes->Rankings->patchEntity($new_ranking, $ranking);
                                $this->Quizzes->Rankings->save($new_ranking);
                            } // 5th student answers/ranking save end

                            if ($key4 == 5) { // 6th student answer/ranking save
                                $answers[0]['question_id'] = $question_ids[0];
                                $answers[0]['student_id'] = $new_student->id;
                                $answers[0]['text'] = 'apple';
                                $answers[0]['score'] = 2.00;

                                $answers[1]['question_id'] = $question_ids[1];
                                $answers[1]['student_id'] = $new_student->id;
                                $answers[1]['text'] = 'strawberry';
                                $answers[1]['score'] = 2.00;

                                $answers[2]['question_id'] = $question_ids[2];
                                $answers[2]['student_id'] = $new_student->id;
                                $answers[2]['text'] = 'carrot';
                                $answers[2]['score'] = 2.00;

                                $answers[3]['question_id'] = $question_ids[5];
                                $answers[3]['student_id'] = $new_student->id;
                                $answers[3]['text'] = 'Banana';
                                $answers[3]['score'] = 1.00;

                                $answers[4]['question_id'] = $question_ids[5];
                                $answers[4]['student_id'] = $new_student->id;
                                $answers[4]['text'] = 'Pear';
                                $answers[4]['score'] = 1.00;

                                $answers[5]['question_id'] = $question_ids[5];
                                $answers[5]['student_id'] = $new_student->id;
                                $answers[5]['text'] = 'Orange';
                                $answers[5]['score'] = 1.00;

                                $answers[6]['question_id'] = $question_ids[6];
                                $answers[6]['student_id'] = $new_student->id;
                                $answers[6]['text'] = 'Pineapple';
                                $answers[6]['score'] = 1.00;

                                $answers[7]['question_id'] = $question_ids[8];
                                $answers[7]['student_id'] = $new_student->id;
                                $answers[7]['text'] = 'Yesterday I ate pineapple and today I will eat pears.';
                                $answers[7]['score'] = 6.00;

                                $answers[8]['question_id'] = $question_ids[9];
                                $answers[8]['student_id'] = $new_student->id;
                                $answers[8]['text'] = 'apple';
                                $answers[8]['score'] = 3.00;

                                foreach ($answers as $key5 => $answer) { // save answers
                                    $new_answer = $this->Quizzes->Students->Answers->newEntity();
                                    $new_answer = $this->Quizzes->Students->Answers->patchEntity($new_answer, $answer);
                                    $this->Quizzes->Students->Answers->save($new_answer);
                                } // end of anwers saving

                                // Prepare ranking data
                                $ranking['quiz_id'] = $new_quiz->id;
                                $ranking['student_id'] = $new_student->id;
                                $ranking['score'] = 19.00;
                                $ranking['total'] = 19.00; 

                                $new_ranking = $this->Quizzes->Rankings->newEntity();
                                $new_ranking = $this->Quizzes->Rankings->patchEntity($new_ranking, $ranking);
                                $this->Quizzes->Rankings->save($new_ranking);
                            } // 6th student answers/ranking save end

                            if ($key4 == 6) { // 7th student answer/ranking save
                                $answers[0]['question_id'] = $question_ids[0];
                                $answers[0]['student_id'] = $new_student->id;
                                $answers[0]['text'] = 'en tiijä';
                                $answers[0]['score'] = 0.00;

                                $answers[1]['question_id'] = $question_ids[1];
                                $answers[1]['student_id'] = $new_student->id;
                                $answers[1]['text'] = 'mää';
                                $answers[1]['score'] = 0.00;

                                $answers[2]['question_id'] = $question_ids[2];
                                $answers[2]['student_id'] = $new_student->id;
                                $answers[2]['text'] = 'sdgfasd';
                                $answers[2]['score'] = 0.00;

                                $answers[3]['question_id'] = $question_ids[5];
                                $answers[3]['student_id'] = $new_student->id;
                                $answers[3]['text'] = 'Cucumber';
                                $answers[3]['score'] = 0.00;

                                $answers[4]['question_id'] = $question_ids[5];
                                $answers[4]['student_id'] = $new_student->id;
                                $answers[4]['text'] = 'Banana';
                                $answers[4]['score'] = 1.00;

                                $answers[5]['question_id'] = $question_ids[5];
                                $answers[5]['student_id'] = $new_student->id;
                                $answers[5]['text'] = 'Grass';
                                $answers[5]['score'] = 0.00;

                                $answers[6]['question_id'] = $question_ids[6];
                                $answers[6]['student_id'] = $new_student->id;
                                $answers[6]['text'] = 'Ananas';
                                $answers[6]['score'] = 0.00;

                                $answers[7]['question_id'] = $question_ids[8];
                                $answers[7]['student_id'] = $new_student->id;
                                $answers[7]['text'] = 'ananas and banana';
                                $answers[7]['score'] = 1.00;

                                $answers[8]['question_id'] = $question_ids[9];
                                $answers[8]['student_id'] = $new_student->id;
                                $answers[8]['text'] = 'ananas';
                                $answers[8]['score'] = 0.00;

                                foreach ($answers as $key5 => $answer) { // save answers
                                    $new_answer = $this->Quizzes->Students->Answers->newEntity();
                                    $new_answer = $this->Quizzes->Students->Answers->patchEntity($new_answer, $answer);
                                    $this->Quizzes->Students->Answers->save($new_answer);
                                } // end of anwers saving

                                // Prepare ranking data
                                $ranking['quiz_id'] = $new_quiz->id;
                                $ranking['student_id'] = $new_student->id;
                                $ranking['score'] = 2.00;
                                $ranking['total'] = 19.00; 

                                $new_ranking = $this->Quizzes->Rankings->newEntity();
                                $new_ranking = $this->Quizzes->Rankings->patchEntity($new_ranking, $ranking);
                                $this->Quizzes->Rankings->save($new_ranking);
                            } // 7th student answers/ranking save end

                            if ($key4 == 7) { // 8th student answer/ranking save
                                $answers[0]['question_id'] = $question_ids[0];
                                $answers[0]['student_id'] = $new_student->id;
                                $answers[0]['text'] = 'apple';
                                $answers[0]['score'] = 2.00;

                                $answers[1]['question_id'] = $question_ids[1];
                                $answers[1]['student_id'] = $new_student->id;
                                $answers[1]['text'] = 'stwarberry';
                                $answers[1]['score'] = 1.00;

                                $answers[2]['question_id'] = $question_ids[2];
                                $answers[2]['student_id'] = $new_student->id;
                                $answers[2]['text'] = 'carrot';
                                $answers[2]['score'] = 2.00;

                                $answers[3]['question_id'] = $question_ids[5];
                                $answers[3]['student_id'] = $new_student->id;
                                $answers[3]['text'] = 'Banana';
                                $answers[3]['score'] = 1.00;

                                $answers[4]['question_id'] = $question_ids[5];
                                $answers[4]['student_id'] = $new_student->id;
                                $answers[4]['text'] = 'Pear';
                                $answers[4]['score'] = 1.00;

                                $answers[5]['question_id'] = $question_ids[5];
                                $answers[5]['student_id'] = $new_student->id;
                                $answers[5]['text'] = 'Orange';
                                $answers[5]['score'] = 1.00;

                                $answers[6]['question_id'] = $question_ids[6];
                                $answers[6]['student_id'] = $new_student->id;
                                $answers[6]['text'] = 'Pineapple';
                                $answers[6]['score'] = 1.00;

                                $answers[7]['question_id'] = $question_ids[8];
                                $answers[7]['student_id'] = $new_student->id;
                                $answers[7]['text'] = 'Apple is the best and banana is second best.';
                                $answers[7]['score'] = 5.00;

                                $answers[8]['question_id'] = $question_ids[9];
                                $answers[8]['student_id'] = $new_student->id;
                                $answers[8]['text'] = 'banana';
                                $answers[8]['score'] = 3.00;

                                foreach ($answers as $key5 => $answer) { // save answers
                                    $new_answer = $this->Quizzes->Students->Answers->newEntity();
                                    $new_answer = $this->Quizzes->Students->Answers->patchEntity($new_answer, $answer);
                                    $this->Quizzes->Students->Answers->save($new_answer);
                                } // end of anwers saving

                                // Prepare ranking data
                                $ranking['quiz_id'] = $new_quiz->id;
                                $ranking['student_id'] = $new_student->id;
                                $ranking['score'] = 17.00;
                                $ranking['total'] = 19.00; 

                                $new_ranking = $this->Quizzes->Rankings->newEntity();
                                $new_ranking = $this->Quizzes->Rankings->patchEntity($new_ranking, $ranking);
                                $this->Quizzes->Rankings->save($new_ranking);
                            } // 8th student answers/ranking save end

                            if ($key4 == 8) { // 9th student answer/ranking save
                                $answers[0]['question_id'] = $question_ids[0];
                                $answers[0]['student_id'] = $new_student->id;
                                $answers[0]['text'] = 'apple';
                                $answers[0]['score'] = 2.00;

                                $answers[1]['question_id'] = $question_ids[1];
                                $answers[1]['student_id'] = $new_student->id;
                                $answers[1]['text'] = '-';
                                $answers[1]['score'] = 0.00;

                                $answers[2]['question_id'] = $question_ids[2];
                                $answers[2]['student_id'] = $new_student->id;
                                $answers[2]['text'] = 'carrot';
                                $answers[2]['score'] = 2.00;

                                $answers[3]['question_id'] = $question_ids[5];
                                $answers[3]['student_id'] = $new_student->id;
                                $answers[3]['text'] = 'Banana';
                                $answers[3]['score'] = 1.00;

                                $answers[4]['question_id'] = $question_ids[5];
                                $answers[4]['student_id'] = $new_student->id;
                                $answers[4]['text'] = 'Cucumber';
                                $answers[4]['score'] = 0.00;

                                $answers[5]['question_id'] = $question_ids[5];
                                $answers[5]['student_id'] = $new_student->id;
                                $answers[5]['text'] = 'Orange';
                                $answers[5]['score'] = 1.00;

                                $answers[6]['question_id'] = $question_ids[6];
                                $answers[6]['student_id'] = $new_student->id;
                                $answers[6]['text'] = 'Pineapple';
                                $answers[6]['score'] = 1.00;

                                $answers[7]['question_id'] = $question_ids[8];
                                $answers[7]['student_id'] = $new_student->id;
                                $answers[7]['text'] = 'I dont like apple but I like orange.';
                                $answers[7]['score'] = 5.00;

                                $answers[8]['question_id'] = $question_ids[9];
                                $answers[8]['student_id'] = $new_student->id;
                                $answers[8]['text'] = 'orange';
                                $answers[8]['score'] = 3.00;

                                foreach ($answers as $key5 => $answer) { // save answers
                                    $new_answer = $this->Quizzes->Students->Answers->newEntity();
                                    $new_answer = $this->Quizzes->Students->Answers->patchEntity($new_answer, $answer);
                                    $this->Quizzes->Students->Answers->save($new_answer);
                                } // end of anwers saving

                                // Prepare ranking data
                                $ranking['quiz_id'] = $new_quiz->id;
                                $ranking['student_id'] = $new_student->id;
                                $ranking['score'] = 15.00;
                                $ranking['total'] = 19.00; 

                                $new_ranking = $this->Quizzes->Rankings->newEntity();
                                $new_ranking = $this->Quizzes->Rankings->patchEntity($new_ranking, $ranking);
                                $this->Quizzes->Rankings->save($new_ranking);
                            } // 9th student answers/ranking save end


                        } // if student data saved end
                    } // end of first quiz students 

                } // Quiz 1 end

                if ($key1 == 1) { // Quiz 2
                    $questions[0]['quiz_id'] = $new_quiz->id;
                    $questions[0]['question_type_id'] = 6;
                    $questions[0]['text'] = 'Kapulaote';
                    $questions[0]['explanation'] = '';
                    $questions[0]['weight'] = NULL;
                    $questions[0]['max_allowed'] = NULL;

                    $questions[1]['quiz_id'] = $new_quiz->id;
                    $questions[1]['question_type_id'] = 7;
                    $questions[1]['text'] = 'New Question';
                    $questions[1]['explanation'] = 'Katso video ja vastaa sen jälkeen alla oleviin kysymyksiin. Kaikkien kysymysten vastaukset löytyvät videosta.';
                    $questions[1]['weight'] = NULL;
                    $questions[1]['max_allowed'] = NULL;

                    $questions[2]['quiz_id'] = $new_quiz->id;
                    $questions[2]['question_type_id'] = 3;
                    $questions[2]['text'] = 'Mitkä sormet puristavat rumpukapulaa?';
                    $questions[2]['explanation'] = 'Jokaisesta oikeasta valinnasta +1p.';
                    $questions[2]['weight'] = NULL;
                    $questions[2]['max_allowed'] = 2;

                    $questions[3]['quiz_id'] = $new_quiz->id;
                    $questions[3]['question_type_id'] = 3;
                    $questions[3]['text'] = 'Mitkä kolme seuraavista ovat tyypillisiä virheitä rumpukapulaotteessa?';
                    $questions[3]['explanation'] = 'Jokaisesta oikeasta valinnasta +1p.';
                    $questions[3]['weight'] = NULL;
                    $questions[3]['max_allowed'] = 3;

                    $questions[4]['quiz_id'] = $new_quiz->id;
                    $questions[4]['question_type_id'] = 6;
                    $questions[4]['text'] = 'Rumpusetin osat';
                    $questions[4]['explanation'] = '';
                    $questions[4]['weight'] = NULL;
                    $questions[4]['max_allowed'] = NULL;

                    $questions[5]['quiz_id'] = $new_quiz->id;
                    $questions[5]['question_type_id'] = 7;
                    $questions[5]['text'] = 'Katso video ja vastaa sen jälkeen alla oleviin kysymyksiin. Kaikkien kysymysten vastaukset löytyvät videosta.';
                    $questions[5]['explanation'] = 'Katso video ja vastaa sen jälkeen alla oleviin kysymyksiin. Kaikkien kysymysten vastaukset löytyvät videosta.';
                    $questions[5]['weight'] = NULL;
                    $questions[5]['max_allowed'] = NULL;

                    $questions[6]['quiz_id'] = $new_quiz->id;
                    $questions[6]['question_type_id'] = 1;
                    $questions[6]['text'] = 'Mitkä ovat kaksi bassorummun tavallista soittotekniikkaa?';
                    $questions[6]['explanation'] = 'Valitse mielestäsi sopivin vaihtoehto. Oikeasta vastauksesta +2p, väärästä -2p.';
                    $questions[6]['weight'] = NULL;
                    $questions[6]['max_allowed'] = NULL;

                    $questions[7]['quiz_id'] = $new_quiz->id;
                    $questions[7]['question_type_id'] = 3;
                    $questions[7]['text'] = 'Mitkä kolme rumpusetin osaa tarvitaan lähes kaikkien rumpukomppien soittamiseen?';
                    $questions[7]['explanation'] = 'Jokaisesta oikeasta vastauksesta +1p.';
                    $questions[7]['weight'] = NULL;
                    $questions[7]['max_allowed'] = 3;

                    $questions[8]['quiz_id'] = $new_quiz->id;
                    $questions[8]['question_type_id'] = 6;
                    $questions[8]['text'] = 'Bonus-video:';
                    $questions[8]['explanation'] = '';
                    $questions[8]['weight'] = NULL;
                    $questions[8]['max_allowed'] = NULL;

                    $questions[9]['quiz_id'] = $new_quiz->id;
                    $questions[9]['question_type_id'] = 7;
                    $questions[9]['text'] = 'New Question';
                    $questions[9]['explanation'] = 'Seuraavalla tunnilla opettelemme beat-kompin. Voit tutustua siihen jo ennalta katsomalla oheisen videon.';
                    $questions[9]['weight'] = NULL;
                    $questions[9]['max_allowed'] = NULL;

                    foreach ($questions as $key2 => $question) {
                        $new_question = $this->Quizzes->Questions->newEntity();
                        $new_question = $this->Quizzes->Questions->patchEntity($new_question, $question);
                        if ($this->Quizzes->Questions->save($new_question)) { // save related question
                            $question_ids[] = $new_question->id;
                            $choices = array();
                            // save related choice
                            if ($key2 == 1) { // 0 skip first question choices
                                $choice['question_id'] = $new_question->id;
                                $choice['text'] = 'https://www.youtube.com/embed/dCLfOu-QT58';
                                $choice['points'] = 0.00;
                                $choice['weight'] = NULL;
                                $new_choice = $this->Quizzes->Questions->Choices->newEntity();
                                $new_choice = $this->Quizzes->Questions->Choices->patchEntity($new_choice, $choice);
                                $this->Quizzes->Questions->Choices->save($new_choice);
                            } elseif ($key2 == 2) { // first question choices
                                $choices[0]['question_id'] = $new_question->id;
                                $choices[0]['text'] = 'Peukalo';
                                $choices[0]['points'] = 1.00;
                                $choices[0]['weight'] = NULL;

                                $choices[1]['question_id'] = $new_question->id;
                                $choices[1]['text'] = 'Etusormi';
                                $choices[1]['points'] = 1.00;
                                $choices[1]['weight'] = NULL;

                                $choices[2]['question_id'] = $new_question->id;
                                $choices[2]['text'] = 'Keskisormi';
                                $choices[2]['points'] = 0.00;
                                $choices[2]['weight'] = NULL;

                                $choices[3]['question_id'] = $new_question->id;
                                $choices[3]['text'] = 'Nimetön';
                                $choices[3]['points'] = 0.00;
                                $choices[3]['weight'] = NULL;

                                $choices[4]['question_id'] = $new_question->id;
                                $choices[4]['text'] = 'Pikkusormi';
                                $choices[4]['points'] = 0.00;
                                $choices[4]['weight'] = NULL;

                                foreach ($choices as $key3 => $choice) {
                                    $new_choice = $this->Quizzes->Questions->Choices->newEntity();
                                    $new_choice = $this->Quizzes->Questions->Choices->patchEntity($new_choice, $choice);
                                    $this->Quizzes->Questions->Choices->save($new_choice);
                                }
                            } elseif ($key2 == 3) { // first question choices
                                $choices[0]['question_id'] = $new_question->id;
                                $choices[0]['text'] = 'Ote liian keskeltä kapulaa';
                                $choices[0]['points'] = 1.00;
                                $choices[0]['weight'] = NULL;

                                $choices[1]['question_id'] = $new_question->id;
                                $choices[1]['text'] = 'Ote väärällä kädellä';
                                $choices[1]['points'] = 0.00;
                                $choices[1]['weight'] = NULL;

                                $choices[2]['question_id'] = $new_question->id;
                                $choices[2]['text'] = 'Käsi puristaa liian kovaa kapulasta';
                                $choices[2]['points'] = 1.00;
                                $choices[2]['weight'] = NULL;

                                $choices[3]['question_id'] = $new_question->id;
                                $choices[3]['text'] = 'Etusormi on kapulan yläpuolella';
                                $choices[3]['points'] = 1.00;
                                $choices[3]['weight'] = NULL;

                                $choices[4]['question_id'] = $new_question->id;
                                $choices[4]['text'] = 'Väärän muotoiset kapulat';
                                $choices[4]['points'] = 0.00;
                                $choices[4]['weight'] = NULL;

                                foreach ($choices as $key3 => $choice) {
                                    $new_choice = $this->Quizzes->Questions->Choices->newEntity();
                                    $new_choice = $this->Quizzes->Questions->Choices->patchEntity($new_choice, $choice);
                                    $this->Quizzes->Questions->Choices->save($new_choice);
                                }
                            } elseif ($key2 == 5) { // 4 skip question has not choice
                                $choice['question_id'] = $new_question->id;
                                $choice['text'] = 'https://www.youtube.com/embed/0HBPjqY_sNE';
                                $choice['points'] = 0.00;
                                $choice['weight'] = NULL;
                                $new_choice = $this->Quizzes->Questions->Choices->newEntity();
                                $new_choice = $this->Quizzes->Questions->Choices->patchEntity($new_choice, $choice);
                                $this->Quizzes->Questions->Choices->save($new_choice);
                            } elseif ($key2 == 6) { 
                                $choices[0]['question_id'] = $new_question->id;
                                $choices[0]['text'] = 'Polkaistaan varpailla tai polkaistaan kynsipuolella.';
                                $choices[0]['points'] = -2.00;
                                $choices[0]['weight'] = NULL;

                                $choices[1]['question_id'] = $new_question->id;
                                $choices[1]['text'] = 'Kantapää on ilmassa tai kantapää on kiinni pedaalissa.';
                                $choices[1]['points'] = 2.00;
                                $choices[1]['weight'] = NULL;

                                $choices[2]['question_id'] = $new_question->id;
                                $choices[2]['text'] = 'Jalka on pedaalilla poikittain tai suorassa.';
                                $choices[2]['points'] = -2.00;
                                $choices[2]['weight'] = NULL;

                                $choices[3]['question_id'] = $new_question->id;
                                $choices[3]['text'] = 'Soitetaan sukka jalassa tai ilman sukkaa.';
                                $choices[3]['points'] = -2.00;
                                $choices[3]['weight'] = NULL;

                                foreach ($choices as $key3 => $choice) {
                                    $new_choice = $this->Quizzes->Questions->Choices->newEntity();
                                $new_choice = $this->Quizzes->Questions->Choices->patchEntity($new_choice, $choice);
                                $this->Quizzes->Questions->Choices->save($new_choice);
                                }
                            } elseif ($key2 == 7) { // first question choices
                                $choices[0]['question_id'] = $new_question->id;
                                $choices[0]['text'] = 'Crash-pelti';
                                $choices[0]['points'] = 0.00;
                                $choices[0]['weight'] = NULL;

                                $choices[1]['question_id'] = $new_question->id;
                                $choices[1]['text'] = 'Bassorumpu';
                                $choices[1]['points'] = 1.00;
                                $choices[1]['weight'] = NULL;

                                $choices[2]['question_id'] = $new_question->id;
                                $choices[2]['text'] = 'Hi-hat';
                                $choices[2]['points'] = 1.00;
                                $choices[2]['weight'] = NULL;

                                $choices[3]['question_id'] = $new_question->id;
                                $choices[3]['text'] = 'Ride-pelti';
                                $choices[3]['points'] = 0.00;
                                $choices[3]['weight'] = NULL;

                                $choices[4]['question_id'] = $new_question->id;
                                $choices[4]['text'] = 'Virveli';
                                $choices[4]['points'] = 1.00;
                                $choices[4]['weight'] = NULL;

                                $choices[5]['question_id'] = $new_question->id;
                                $choices[5]['text'] = 'Tomit';
                                $choices[5]['points'] = 0.00;
                                $choices[5]['weight'] = NULL;

                                foreach ($choices as $key3 => $choice) {
                                    $new_choice = $this->Quizzes->Questions->Choices->newEntity();
                                    $new_choice = $this->Quizzes->Questions->Choices->patchEntity($new_choice, $choice);
                                    $this->Quizzes->Questions->Choices->save($new_choice);
                                }
                            } elseif ($key2 == 9) { // 8 skip question has not choice
                                $choice['question_id'] = $new_question->id;
                                $choice['text'] = 'https://www.youtube.com/embed/OrTACJTB_Gs';
                                $choice['points'] = 0.00;
                                $choice['weight'] = NULL;
                                $new_choice = $this->Quizzes->Questions->Choices->newEntity();
                                $new_choice = $this->Quizzes->Questions->Choices->patchEntity($new_choice, $choice);
                                $this->Quizzes->Questions->Choices->save($new_choice);
                            } else {
                                // do nothing
                            }
                        }
                    }

                    // 2nd quiz students
                    $students = array();
                    $students[0]['quiz_id'] = $new_quiz->id;
                    $students[0]['fname'] = 'Kalle';
                    $students[0]['lname'] = 'Koululainen';
                    $students[0]['class'] = '6a';
                    $students[0]['status'] = 1;
                    $students[0]['submitted'] = date('Y-m-d H:i:s');

                    $students[1]['quiz_id'] = $new_quiz->id;
                    $students[1]['fname'] = 'Fanni';
                    $students[1]['lname'] = 'Fanittaja';
                    $students[1]['class'] = '6a';
                    $students[1]['status'] = 1;
                    $students[1]['submitted'] = date('Y-m-d H:i:s');

                    $students[2]['quiz_id'] = $new_quiz->id;
                    $students[2]['fname'] = 'Aija';
                    $students[2]['lname'] = 'Avulias';
                    $students[2]['class'] = '6a';
                    $students[2]['status'] = 1;
                    $students[2]['submitted'] = date('Y-m-d H:i:s');

                    $students[3]['quiz_id'] = $new_quiz->id;
                    $students[3]['fname'] = 'Jarkko';
                    $students[3]['lname'] = 'Jonottaja';
                    $students[3]['class'] = '6c';
                    $students[3]['status'] = 1;
                    $students[3]['submitted'] = date('Y-m-d H:i:s');

                    $students[4]['quiz_id'] = $new_quiz->id;
                    $students[4]['fname'] = 'Anna';
                    $students[4]['lname'] = 'Arvoituksellinen';
                    $students[4]['class'] = '6c';
                    $students[4]['status'] = 1;
                    $students[4]['submitted'] = date('Y-m-d H:i:s');

                    $students[5]['quiz_id'] = $new_quiz->id;
                    $students[5]['fname'] = 'Valtteri';
                    $students[5]['lname'] = 'Vaihtolämpöinen';
                    $students[5]['class'] = '6c';
                    $students[5]['status'] = 1;
                    $students[5]['submitted'] = date('Y-m-d H:i:s');

                    $students[6]['quiz_id'] = $new_quiz->id;
                    $students[6]['fname'] = 'Lauri';
                    $students[6]['lname'] = 'Laurinpoika';
                    $students[6]['class'] = '6d';
                    $students[6]['status'] = 1;
                    $students[6]['submitted'] = date('Y-m-d H:i:s');

                    $students[7]['quiz_id'] = $new_quiz->id;
                    $students[7]['fname'] = 'Sirkku';
                    $students[7]['lname'] = 'Sirkuttaja';
                    $students[7]['class'] = '6d';
                    $students[7]['status'] = 1;
                    $students[7]['submitted'] = date('Y-m-d H:i:s');

                    $students[8]['quiz_id'] = $new_quiz->id;
                    $students[8]['fname'] = 'Jonna';
                    $students[8]['lname'] = 'Jouhea';
                    $students[8]['class'] = '6d';
                    $students[8]['status'] = 1;
                    $students[8]['submitted'] = date('Y-m-d H:i:s');
                    
                    foreach ($students as $key4 => $student) { // Save all student of first quiz
                        $new_student = $this->Quizzes->Students->newEntity();
                        $new_student = $this->Quizzes->Students->patchEntity($new_student, $student);
                        if ($this->Quizzes->Students->save($new_student)) { // Save student data
                            $answers = array();
                            if ($key4 == 0) { // First student answer/ranking save
                                // 0, 1 skip
                                $answers[0]['question_id'] = $question_ids[2];
                                $answers[0]['student_id'] = $new_student->id;
                                $answers[0]['text'] = 'Peukalo';
                                $answers[0]['score'] = 1.00;

                                $answers[1]['question_id'] = $question_ids[2];
                                $answers[1]['student_id'] = $new_student->id;
                                $answers[1]['text'] = 'Etusormi';
                                $answers[1]['score'] = 1.00;

                                $answers[2]['question_id'] = $question_ids[3];
                                $answers[2]['student_id'] = $new_student->id;
                                $answers[2]['text'] = 'Ote liian keskeltä kapulaa';
                                $answers[2]['score'] = 1.00;

                                $answers[3]['question_id'] = $question_ids[3];
                                $answers[3]['student_id'] = $new_student->id;
                                $answers[3]['text'] = 'Käsi puristaa liian kovaa kapulasta';
                                $answers[3]['score'] = 1.00;

                                $answers[4]['question_id'] = $question_ids[3];
                                $answers[4]['student_id'] = $new_student->id;
                                $answers[4]['text'] = 'Etusormi on kapulan yläpuolella';
                                $answers[4]['score'] = 1.00;

                                // 4/5 skipped
                                $answers[5]['question_id'] = $question_ids[6];
                                $answers[5]['student_id'] = $new_student->id;
                                $answers[5]['text'] = 'Kantapää on ilmassa tai kantapää on kiinni pedaalissa.';
                                $answers[5]['score'] = 2.00;


                                $answers[6]['question_id'] = $question_ids[7];
                                $answers[6]['student_id'] = $new_student->id;
                                $answers[6]['text'] = 'Bassorumpu';
                                $answers[6]['score'] = 1.00;

                                $answers[7]['question_id'] = $question_ids[7];
                                $answers[7]['student_id'] = $new_student->id;
                                $answers[7]['text'] = 'Hi-hat';
                                $answers[7]['score'] = 1.00;

                                $answers[8]['question_id'] = $question_ids[7];
                                $answers[8]['student_id'] = $new_student->id;
                                $answers[8]['text'] = 'Virveli';
                                $answers[8]['score'] = 1.00;


                                foreach ($answers as $key5 => $answer) { // save answers
                                    $new_answer = $this->Quizzes->Students->Answers->newEntity();
                                    $new_answer = $this->Quizzes->Students->Answers->patchEntity($new_answer, $answer);
                                    $this->Quizzes->Students->Answers->save($new_answer);
                                } // end of anwers saving

                                // Prepare ranking data
                                $ranking['quiz_id'] = $new_quiz->id;
                                $ranking['student_id'] = $new_student->id;
                                $ranking['score'] = 10.00;
                                $ranking['total'] = 10.00; 

                                $new_ranking = $this->Quizzes->Rankings->newEntity();
                                $new_ranking = $this->Quizzes->Rankings->patchEntity($new_ranking, $ranking);
                                $this->Quizzes->Rankings->save($new_ranking);
                            } // First student answers/ranking save end

                            if ($key4 == 1) { // 2nd student answer/ranking save
                                // 0, 1 skip
                                $answers[0]['question_id'] = $question_ids[2];
                                $answers[0]['student_id'] = $new_student->id;
                                $answers[0]['text'] = 'Peukalo';
                                $answers[0]['score'] = 1.00;

                                $answers[1]['question_id'] = $question_ids[2];
                                $answers[1]['student_id'] = $new_student->id;
                                $answers[1]['text'] = 'Etusormi';
                                $answers[1]['score'] = 1.00;

                                $answers[2]['question_id'] = $question_ids[3];
                                $answers[2]['student_id'] = $new_student->id;
                                $answers[2]['text'] = 'Ote liian keskeltä kapulaa';
                                $answers[2]['score'] = 1.00;

                                $answers[3]['question_id'] = $question_ids[3];
                                $answers[3]['student_id'] = $new_student->id;
                                $answers[3]['text'] = 'Käsi puristaa liian kovaa kapulasta';
                                $answers[3]['score'] = 1.00;

                                $answers[4]['question_id'] = $question_ids[3];
                                $answers[4]['student_id'] = $new_student->id;
                                $answers[4]['text'] = 'Etusormi on kapulan yläpuolella';
                                $answers[4]['score'] = 1.00;

                                // 4/5 skipped
                                $answers[5]['question_id'] = $question_ids[6];
                                $answers[5]['student_id'] = $new_student->id;
                                $answers[5]['text'] = 'Kantapää on ilmassa tai kantapää on kiinni pedaalissa.';
                                $answers[5]['score'] = 2.00;


                                $answers[6]['question_id'] = $question_ids[7];
                                $answers[6]['student_id'] = $new_student->id;
                                $answers[6]['text'] = 'Bassorumpu';
                                $answers[6]['score'] = 1.00;

                                $answers[7]['question_id'] = $question_ids[7];
                                $answers[7]['student_id'] = $new_student->id;
                                $answers[7]['text'] = 'Hi-hat';
                                $answers[7]['score'] = 1.00;

                                $answers[8]['question_id'] = $question_ids[7];
                                $answers[8]['student_id'] = $new_student->id;
                                $answers[8]['text'] = 'Virveli';
                                $answers[8]['score'] = 1.00;


                                foreach ($answers as $key5 => $answer) { // save answers
                                    $new_answer = $this->Quizzes->Students->Answers->newEntity();
                                    $new_answer = $this->Quizzes->Students->Answers->patchEntity($new_answer, $answer);
                                    $this->Quizzes->Students->Answers->save($new_answer);
                                } // end of anwers saving

                                // Prepare ranking data
                                $ranking['quiz_id'] = $new_quiz->id;
                                $ranking['student_id'] = $new_student->id;
                                $ranking['score'] = 10.00;
                                $ranking['total'] = 10.00; 

                                $new_ranking = $this->Quizzes->Rankings->newEntity();
                                $new_ranking = $this->Quizzes->Rankings->patchEntity($new_ranking, $ranking);
                                $this->Quizzes->Rankings->save($new_ranking);
                            } // 2nd student answers/ranking save end

                            if ($key4 == 2) { // 3rd student answer/ranking save
                                // 0, 1 skip
                                $answers[0]['question_id'] = $question_ids[2];
                                $answers[0]['student_id'] = $new_student->id;
                                $answers[0]['text'] = 'Peukalo';
                                $answers[0]['score'] = 1.00;

                                $answers[1]['question_id'] = $question_ids[2];
                                $answers[1]['student_id'] = $new_student->id;
                                $answers[1]['text'] = 'Etusormi';
                                $answers[1]['score'] = 1.00;

                                $answers[2]['question_id'] = $question_ids[3];
                                $answers[2]['student_id'] = $new_student->id;
                                $answers[2]['text'] = 'Ote liian keskeltä kapulaa';
                                $answers[2]['score'] = 1.00;

                                $answers[3]['question_id'] = $question_ids[3];
                                $answers[3]['student_id'] = $new_student->id;
                                $answers[3]['text'] = 'Ote väärällä kädellä';
                                $answers[3]['score'] = 0.00;

                                $answers[4]['question_id'] = $question_ids[3];
                                $answers[4]['student_id'] = $new_student->id;
                                $answers[4]['text'] = 'Etusormi on kapulan yläpuolella';
                                $answers[4]['score'] = 1.00;

                                // 4/5 skipped
                                $answers[5]['question_id'] = $question_ids[6];
                                $answers[5]['student_id'] = $new_student->id;
                                $answers[5]['text'] = 'Kantapää on ilmassa tai kantapää on kiinni pedaalissa.';
                                $answers[5]['score'] = 2.00;


                                $answers[6]['question_id'] = $question_ids[7];
                                $answers[6]['student_id'] = $new_student->id;
                                $answers[6]['text'] = 'Bassorumpu';
                                $answers[6]['score'] = 1.00;

                                $answers[7]['question_id'] = $question_ids[7];
                                $answers[7]['student_id'] = $new_student->id;
                                $answers[7]['text'] = 'Hi-hat';
                                $answers[7]['score'] = 1.00;

                                $answers[8]['question_id'] = $question_ids[7];
                                $answers[8]['student_id'] = $new_student->id;
                                $answers[8]['text'] = 'Virveli';
                                $answers[8]['score'] = 1.00;


                                foreach ($answers as $key5 => $answer) { // save answers
                                    $new_answer = $this->Quizzes->Students->Answers->newEntity();
                                    $new_answer = $this->Quizzes->Students->Answers->patchEntity($new_answer, $answer);
                                    $this->Quizzes->Students->Answers->save($new_answer);
                                } // end of anwers saving

                                // Prepare ranking data
                                $ranking['quiz_id'] = $new_quiz->id;
                                $ranking['student_id'] = $new_student->id;
                                $ranking['score'] = 9.00;
                                $ranking['total'] = 10.00; 

                                $new_ranking = $this->Quizzes->Rankings->newEntity();
                                $new_ranking = $this->Quizzes->Rankings->patchEntity($new_ranking, $ranking);
                                $this->Quizzes->Rankings->save($new_ranking);
                            } // 3rd student answers/ranking save end

                            if ($key4 == 3) { // 4th student answer/ranking save
                                // 0, 1 skip
                                $answers[0]['question_id'] = $question_ids[2];
                                $answers[0]['student_id'] = $new_student->id;
                                $answers[0]['text'] = 'Peukalo';
                                $answers[0]['score'] = 1.00;

                                $answers[1]['question_id'] = $question_ids[2];
                                $answers[1]['student_id'] = $new_student->id;
                                $answers[1]['text'] = 'Etusormi';
                                $answers[1]['score'] = 1.00;

                                $answers[2]['question_id'] = $question_ids[3];
                                $answers[2]['student_id'] = $new_student->id;
                                $answers[2]['text'] = 'Ote liian keskeltä kapulaa';
                                $answers[2]['score'] = 1.00;

                                $answers[3]['question_id'] = $question_ids[3];
                                $answers[3]['student_id'] = $new_student->id;
                                $answers[3]['text'] = 'Käsi puristaa liian kovaa kapulasta';
                                $answers[3]['score'] = 1.00;

                                $answers[4]['question_id'] = $question_ids[3];
                                $answers[4]['student_id'] = $new_student->id;
                                $answers[4]['text'] = 'Etusormi on kapulan yläpuolella';
                                $answers[4]['score'] = 1.00;

                                // 4/5 skipped
                                $answers[5]['question_id'] = $question_ids[6];
                                $answers[5]['student_id'] = $new_student->id;
                                $answers[5]['text'] = 'Kantapää on ilmassa tai kantapää on kiinni pedaalissa.';
                                $answers[5]['score'] = 2.00;


                                $answers[6]['question_id'] = $question_ids[7];
                                $answers[6]['student_id'] = $new_student->id;
                                $answers[6]['text'] = 'Bassorumpu';
                                $answers[6]['score'] = 1.00;

                                $answers[7]['question_id'] = $question_ids[7];
                                $answers[7]['student_id'] = $new_student->id;
                                $answers[7]['text'] = 'Hi-hat';
                                $answers[7]['score'] = 1.00;

                                $answers[8]['question_id'] = $question_ids[7];
                                $answers[8]['student_id'] = $new_student->id;
                                $answers[8]['text'] = 'Virveli';
                                $answers[8]['score'] = 1.00;


                                foreach ($answers as $key5 => $answer) { // save answers
                                    $new_answer = $this->Quizzes->Students->Answers->newEntity();
                                    $new_answer = $this->Quizzes->Students->Answers->patchEntity($new_answer, $answer);
                                    $this->Quizzes->Students->Answers->save($new_answer);
                                } // end of anwers saving

                                // Prepare ranking data
                                $ranking['quiz_id'] = $new_quiz->id;
                                $ranking['student_id'] = $new_student->id;
                                $ranking['score'] = 10.00;
                                $ranking['total'] = 10.00; 

                                $new_ranking = $this->Quizzes->Rankings->newEntity();
                                $new_ranking = $this->Quizzes->Rankings->patchEntity($new_ranking, $ranking);
                                $this->Quizzes->Rankings->save($new_ranking);
                            } // 4th student answers/ranking save end

                            if ($key4 == 4) { // 5th student answer/ranking save
                                // 0, 1 skip
                                $answers[0]['question_id'] = $question_ids[2];
                                $answers[0]['student_id'] = $new_student->id;
                                $answers[0]['text'] = 'Peukalo';
                                $answers[0]['score'] = 1.00;

                                $answers[1]['question_id'] = $question_ids[2];
                                $answers[1]['student_id'] = $new_student->id;
                                $answers[1]['text'] = 'Etusormi';
                                $answers[1]['score'] = 1.00;

                                $answers[2]['question_id'] = $question_ids[3];
                                $answers[2]['student_id'] = $new_student->id;
                                $answers[2]['text'] = 'Ote liian keskeltä kapulaa';
                                $answers[2]['score'] = 1.00;

                                $answers[3]['question_id'] = $question_ids[3];
                                $answers[3]['student_id'] = $new_student->id;
                                $answers[3]['text'] = 'Käsi puristaa liian kovaa kapulasta';
                                $answers[3]['score'] = 1.00;

                                $answers[4]['question_id'] = $question_ids[3];
                                $answers[4]['student_id'] = $new_student->id;
                                $answers[4]['text'] = 'Etusormi on kapulan yläpuolella';
                                $answers[4]['score'] = 1.00;

                                // 4/5 skipped
                                $answers[5]['question_id'] = $question_ids[6];
                                $answers[5]['student_id'] = $new_student->id;
                                $answers[5]['text'] = 'Kantapää on ilmassa tai kantapää on kiinni pedaalissa.';
                                $answers[5]['score'] = 2.00;


                                $answers[6]['question_id'] = $question_ids[7];
                                $answers[6]['student_id'] = $new_student->id;
                                $answers[6]['text'] = 'Bassorumpu';
                                $answers[6]['score'] = 1.00;

                                $answers[7]['question_id'] = $question_ids[7];
                                $answers[7]['student_id'] = $new_student->id;
                                $answers[7]['text'] = 'Hi-hat';
                                $answers[7]['score'] = 1.00;

                                $answers[8]['question_id'] = $question_ids[7];
                                $answers[8]['student_id'] = $new_student->id;
                                $answers[8]['text'] = 'Virveli';
                                $answers[8]['score'] = 1.00;


                                foreach ($answers as $key5 => $answer) { // save answers
                                    $new_answer = $this->Quizzes->Students->Answers->newEntity();
                                    $new_answer = $this->Quizzes->Students->Answers->patchEntity($new_answer, $answer);
                                    $this->Quizzes->Students->Answers->save($new_answer);
                                } // end of anwers saving

                                // Prepare ranking data
                                $ranking['quiz_id'] = $new_quiz->id;
                                $ranking['student_id'] = $new_student->id;
                                $ranking['score'] = 10.00;
                                $ranking['total'] = 10.00; 

                                $new_ranking = $this->Quizzes->Rankings->newEntity();
                                $new_ranking = $this->Quizzes->Rankings->patchEntity($new_ranking, $ranking);
                                $this->Quizzes->Rankings->save($new_ranking);
                            } // 5th student answers/ranking save end

                            if ($key4 == 5) { // 6th student answer/ranking save
                                // 0, 1 skip
                                $answers[0]['question_id'] = $question_ids[2];
                                $answers[0]['student_id'] = $new_student->id;
                                $answers[0]['text'] = 'Peukalo';
                                $answers[0]['score'] = 1.00;

                                $answers[1]['question_id'] = $question_ids[2];
                                $answers[1]['student_id'] = $new_student->id;
                                $answers[1]['text'] = 'Etusormi';
                                $answers[1]['score'] = 1.00;

                                $answers[2]['question_id'] = $question_ids[3];
                                $answers[2]['student_id'] = $new_student->id;
                                $answers[2]['text'] = 'Ote liian keskeltä kapulaa';
                                $answers[2]['score'] = 1.00;

                                $answers[3]['question_id'] = $question_ids[3];
                                $answers[3]['student_id'] = $new_student->id;
                                $answers[3]['text'] = 'Käsi puristaa liian kovaa kapulasta';
                                $answers[3]['score'] = 1.00;

                                $answers[4]['question_id'] = $question_ids[3];
                                $answers[4]['student_id'] = $new_student->id;
                                $answers[4]['text'] = 'Etusormi on kapulan yläpuolella';
                                $answers[4]['score'] = 1.00;

                                // 4/5 skipped
                                $answers[5]['question_id'] = $question_ids[6];
                                $answers[5]['student_id'] = $new_student->id;
                                $answers[5]['text'] = 'Kantapää on ilmassa tai kantapää on kiinni pedaalissa.';
                                $answers[5]['score'] = 2.00;


                                $answers[6]['question_id'] = $question_ids[7];
                                $answers[6]['student_id'] = $new_student->id;
                                $answers[6]['text'] = 'Bassorumpu';
                                $answers[6]['score'] = 1.00;

                                $answers[7]['question_id'] = $question_ids[7];
                                $answers[7]['student_id'] = $new_student->id;
                                $answers[7]['text'] = 'Hi-hat';
                                $answers[7]['score'] = 1.00;

                                $answers[8]['question_id'] = $question_ids[7];
                                $answers[8]['student_id'] = $new_student->id;
                                $answers[8]['text'] = 'Virveli';
                                $answers[8]['score'] = 1.00;


                                foreach ($answers as $key5 => $answer) { // save answers
                                    $new_answer = $this->Quizzes->Students->Answers->newEntity();
                                    $new_answer = $this->Quizzes->Students->Answers->patchEntity($new_answer, $answer);
                                    $this->Quizzes->Students->Answers->save($new_answer);
                                } // end of anwers saving

                                // Prepare ranking data
                                $ranking['quiz_id'] = $new_quiz->id;
                                $ranking['student_id'] = $new_student->id;
                                $ranking['score'] = 10.00;
                                $ranking['total'] = 10.00; 

                                $new_ranking = $this->Quizzes->Rankings->newEntity();
                                $new_ranking = $this->Quizzes->Rankings->patchEntity($new_ranking, $ranking);
                                $this->Quizzes->Rankings->save($new_ranking);
                            } // 6th student answers/ranking save end

                            if ($key4 == 6) { // 6th student answer/ranking save
                                // 0, 1 skip
                                $answers[0]['question_id'] = $question_ids[2];
                                $answers[0]['student_id'] = $new_student->id;
                                $answers[0]['text'] = 'Peukalo';
                                $answers[0]['score'] = 1.00;

                                $answers[1]['question_id'] = $question_ids[2];
                                $answers[1]['student_id'] = $new_student->id;
                                $answers[1]['text'] = 'Etusormi';
                                $answers[1]['score'] = 1.00;

                                $answers[2]['question_id'] = $question_ids[3];
                                $answers[2]['student_id'] = $new_student->id;
                                $answers[2]['text'] = 'Ote liian keskeltä kapulaa';
                                $answers[2]['score'] = 1.00;

                                $answers[3]['question_id'] = $question_ids[3];
                                $answers[3]['student_id'] = $new_student->id;
                                $answers[3]['text'] = 'Ote väärällä kädellä';
                                $answers[3]['score'] = 0.00;

                                $answers[4]['question_id'] = $question_ids[3];
                                $answers[4]['student_id'] = $new_student->id;
                                $answers[4]['text'] = 'Etusormi on kapulan yläpuolella';
                                $answers[4]['score'] = 1.00;

                                // 4/5 skipped
                                $answers[5]['question_id'] = $question_ids[6];
                                $answers[5]['student_id'] = $new_student->id;
                                $answers[5]['text'] = 'Polkaistaan varpailla tai polkaistaan kynsipuolella.';
                                $answers[5]['score'] = -2.00;


                                $answers[6]['question_id'] = $question_ids[7];
                                $answers[6]['student_id'] = $new_student->id;
                                $answers[6]['text'] = 'Crash-pelti';
                                $answers[6]['score'] = 0.00;

                                $answers[7]['question_id'] = $question_ids[7];
                                $answers[7]['student_id'] = $new_student->id;
                                $answers[7]['text'] = 'Hi-hat';
                                $answers[7]['score'] = 1.00;

                                $answers[8]['question_id'] = $question_ids[7];
                                $answers[8]['student_id'] = $new_student->id;
                                $answers[8]['text'] = 'Virveli';
                                $answers[8]['score'] = 1.00;


                                foreach ($answers as $key5 => $answer) { // save answers
                                    $new_answer = $this->Quizzes->Students->Answers->newEntity();
                                    $new_answer = $this->Quizzes->Students->Answers->patchEntity($new_answer, $answer);
                                    $this->Quizzes->Students->Answers->save($new_answer);
                                } // end of anwers saving

                                // Prepare ranking data
                                $ranking['quiz_id'] = $new_quiz->id;
                                $ranking['student_id'] = $new_student->id;
                                $ranking['score'] = 4.00;
                                $ranking['total'] = 10.00; 

                                $new_ranking = $this->Quizzes->Rankings->newEntity();
                                $new_ranking = $this->Quizzes->Rankings->patchEntity($new_ranking, $ranking);
                                $this->Quizzes->Rankings->save($new_ranking);
                            } // 7th student answers/ranking save end

                            if ($key4 == 7) { // 8th student answer/ranking save
                                // 0, 1 skip
                                $answers[0]['question_id'] = $question_ids[2];
                                $answers[0]['student_id'] = $new_student->id;
                                $answers[0]['text'] = 'Peukalo';
                                $answers[0]['score'] = 1.00;

                                $answers[1]['question_id'] = $question_ids[2];
                                $answers[1]['student_id'] = $new_student->id;
                                $answers[1]['text'] = 'Etusormi';
                                $answers[1]['score'] = 1.00;

                                $answers[2]['question_id'] = $question_ids[3];
                                $answers[2]['student_id'] = $new_student->id;
                                $answers[2]['text'] = 'Ote liian keskeltä kapulaa';
                                $answers[2]['score'] = 1.00;

                                $answers[3]['question_id'] = $question_ids[3];
                                $answers[3]['student_id'] = $new_student->id;
                                $answers[3]['text'] = 'Käsi puristaa liian kovaa kapulasta';
                                $answers[3]['score'] = 1.00;

                                $answers[4]['question_id'] = $question_ids[3];
                                $answers[4]['student_id'] = $new_student->id;
                                $answers[4]['text'] = 'Etusormi on kapulan yläpuolella';
                                $answers[4]['score'] = 1.00;

                                // 4/5 skipped
                                $answers[5]['question_id'] = $question_ids[6];
                                $answers[5]['student_id'] = $new_student->id;
                                $answers[5]['text'] = 'Kantapää on ilmassa tai kantapää on kiinni pedaalissa.';
                                $answers[5]['score'] = 2.00;


                                $answers[6]['question_id'] = $question_ids[7];
                                $answers[6]['student_id'] = $new_student->id;
                                $answers[6]['text'] = 'Bassorumpu';
                                $answers[6]['score'] = 1.00;

                                $answers[7]['question_id'] = $question_ids[7];
                                $answers[7]['student_id'] = $new_student->id;
                                $answers[7]['text'] = 'Hi-hat';
                                $answers[7]['score'] = 1.00;

                                $answers[8]['question_id'] = $question_ids[7];
                                $answers[8]['student_id'] = $new_student->id;
                                $answers[8]['text'] = 'Virveli';
                                $answers[8]['score'] = 1.00;


                                foreach ($answers as $key5 => $answer) { // save answers
                                    $new_answer = $this->Quizzes->Students->Answers->newEntity();
                                    $new_answer = $this->Quizzes->Students->Answers->patchEntity($new_answer, $answer);
                                    $this->Quizzes->Students->Answers->save($new_answer);
                                } // end of anwers saving

                                // Prepare ranking data
                                $ranking['quiz_id'] = $new_quiz->id;
                                $ranking['student_id'] = $new_student->id;
                                $ranking['score'] = 10.00;
                                $ranking['total'] = 10.00; 

                                $new_ranking = $this->Quizzes->Rankings->newEntity();
                                $new_ranking = $this->Quizzes->Rankings->patchEntity($new_ranking, $ranking);
                                $this->Quizzes->Rankings->save($new_ranking);
                            } // 8th student answers/ranking save end

                            if ($key4 == 8) { // 9th student answer/ranking save
                                // 0, 1 skip
                                $answers[0]['question_id'] = $question_ids[2];
                                $answers[0]['student_id'] = $new_student->id;
                                $answers[0]['text'] = 'Peukalo';
                                $answers[0]['score'] = 1.00;

                                $answers[1]['question_id'] = $question_ids[2];
                                $answers[1]['student_id'] = $new_student->id;
                                $answers[1]['text'] = 'Etusormi';
                                $answers[1]['score'] = 1.00;

                                $answers[2]['question_id'] = $question_ids[3];
                                $answers[2]['student_id'] = $new_student->id;
                                $answers[2]['text'] = 'Ote liian keskeltä kapulaa';
                                $answers[2]['score'] = 1.00;

                                $answers[3]['question_id'] = $question_ids[3];
                                $answers[3]['student_id'] = $new_student->id;
                                $answers[3]['text'] = 'Käsi puristaa liian kovaa kapulasta';
                                $answers[3]['score'] = 1.00;

                                $answers[4]['question_id'] = $question_ids[3];
                                $answers[4]['student_id'] = $new_student->id;
                                $answers[4]['text'] = 'Etusormi on kapulan yläpuolella';
                                $answers[4]['score'] = 1.00;

                                // 4/5 skipped
                                $answers[5]['question_id'] = $question_ids[6];
                                $answers[5]['student_id'] = $new_student->id;
                                $answers[5]['text'] = 'Kantapää on ilmassa tai kantapää on kiinni pedaalissa.';
                                $answers[5]['score'] = 2.00;


                                $answers[6]['question_id'] = $question_ids[7];
                                $answers[6]['student_id'] = $new_student->id;
                                $answers[6]['text'] = 'Bassorumpu';
                                $answers[6]['score'] = 1.00;

                                $answers[7]['question_id'] = $question_ids[7];
                                $answers[7]['student_id'] = $new_student->id;
                                $answers[7]['text'] = 'Hi-hat';
                                $answers[7]['score'] = 1.00;

                                $answers[8]['question_id'] = $question_ids[7];
                                $answers[8]['student_id'] = $new_student->id;
                                $answers[8]['text'] = 'Virveli';
                                $answers[8]['score'] = 1.00;


                                foreach ($answers as $key5 => $answer) { // save answers
                                    $new_answer = $this->Quizzes->Students->Answers->newEntity();
                                    $new_answer = $this->Quizzes->Students->Answers->patchEntity($new_answer, $answer);
                                    $this->Quizzes->Students->Answers->save($new_answer);
                                } // end of anwers saving

                                // Prepare ranking data
                                $ranking['quiz_id'] = $new_quiz->id;
                                $ranking['student_id'] = $new_student->id;
                                $ranking['score'] = 10.00;
                                $ranking['total'] = 10.00; 

                                $new_ranking = $this->Quizzes->Rankings->newEntity();
                                $new_ranking = $this->Quizzes->Rankings->patchEntity($new_ranking, $ranking);
                                $this->Quizzes->Rankings->save($new_ranking);
                            } // 9th student answers/ranking save end
                        }
                    } // 2nd quiz student end

                } // Quiz 2 end

                if ($key1 == 2) { // Quiz 3
                    $questions[0]['quiz_id'] = $new_quiz->id;
                    $questions[0]['question_type_id'] = 1;
                    $questions[0]['text'] = 'Keskityn opetukseen ja tehtäviin';
                    $questions[0]['explanation'] = '';
                    $questions[0]['weight'] = 12;
                    $questions[0]['max_allowed'] = NULL;

                    $questions[1]['quiz_id'] = $new_quiz->id;
                    $questions[1]['question_type_id'] = 1;
                    $questions[1]['text'] = 'Viittaan kysymyksiin';
                    $questions[1]['explanation'] = '';
                    $questions[1]['weight'] = 11;
                    $questions[1]['max_allowed'] = NULL;

                    $questions[2]['quiz_id'] = $new_quiz->id;
                    $questions[2]['question_type_id'] = 1;
                    $questions[2]['text'] = 'Haluan tutustua minulle vieraampiinkin oppikokonaisuuksiin';
                    $questions[2]['explanation'] = '';
                    $questions[2]['weight'] = 10;
                    $questions[2]['max_allowed'] = NULL;

                    $questions[3]['quiz_id'] = $new_quiz->id;
                    $questions[3]['question_type_id'] = 6;
                    $questions[3]['text'] = 'Opiskelu';
                    $questions[3]['explanation'] = '';
                    $questions[3]['weight'] = 13;
                    $questions[3]['max_allowed'] = NULL;

                    $questions[4]['quiz_id'] = $new_quiz->id;
                    $questions[4]['question_type_id'] = 6;
                    $questions[4]['text'] = 'Käytös ja huolellisuus';
                    $questions[4]['explanation'] = '';
                    $questions[4]['weight'] = 8;
                    $questions[4]['max_allowed'] = NULL;

                    $questions[5]['quiz_id'] = $new_quiz->id;
                    $questions[5]['question_type_id'] = 1;
                    $questions[5]['text'] = 'Kannan osaltani vastuuta luokkatilan siisteydestä';
                    $questions[5]['explanation'] = '';
                    $questions[5]['weight'] = 7;
                    $questions[5]['max_allowed'] = NULL;

                    $questions[6]['quiz_id'] = $new_quiz->id;
                    $questions[6]['question_type_id'] = 1;
                    $questions[6]['text'] = 'Käsittelen opetusvälineitä asiallisesti';
                    $questions[6]['explanation'] = '';
                    $questions[6]['weight'] = 6;
                    $questions[6]['max_allowed'] = NULL;

                    $questions[7]['quiz_id'] = $new_quiz->id;
                    $questions[7]['question_type_id'] = 1;
                    $questions[7]['text'] = 'Saavun ajoissa tunnille';
                    $questions[7]['explanation'] = '';
                    $questions[7]['weight'] = 5;
                    $questions[7]['max_allowed'] = NULL;

                    $questions[8]['quiz_id'] = $new_quiz->id;
                    $questions[8]['question_type_id'] = 1;
                    $questions[8]['text'] = 'Pyydän puheenvuoroa viittaamalla';
                    $questions[8]['explanation'] = '';
                    $questions[8]['weight'] = 4;
                    $questions[8]['max_allowed'] = NULL;

                    $questions[9]['quiz_id'] = $new_quiz->id;
                    $questions[9]['question_type_id'] = 6;
                    $questions[9]['text'] = 'Ryhmässä toimiminen';
                    $questions[9]['explanation'] = '';
                    $questions[9]['weight'] = 3;
                    $questions[9]['max_allowed'] = NULL;

                    $questions[10]['quiz_id'] = $new_quiz->id;
                    $questions[10]['question_type_id'] = 1;
                    $questions[10]['text'] = 'Autan ja neuvon luokkatovereita';
                    $questions[10]['explanation'] = '';
                    $questions[10]['weight'] = 2;
                    $questions[10]['max_allowed'] = NULL;

                    $questions[11]['quiz_id'] = $new_quiz->id;
                    $questions[11]['question_type_id'] = 1;
                    $questions[11]['text'] = 'Toimin aktiivisesti osana ryhmää (esim. ryhmätöissä)';
                    $questions[11]['explanation'] = '';
                    $questions[11]['weight'] = 1;
                    $questions[11]['max_allowed'] = NULL;

                    $questions[12]['quiz_id'] = $new_quiz->id;
                    $questions[12]['question_type_id'] = 1;
                    $questions[12]['text'] = 'Pyrin kehittymään pitkäjänteisesti';
                    $questions[12]['explanation'] = '';
                    $questions[12]['weight'] = 9;
                    $questions[12]['max_allowed'] = NULL;

                    $questions[13]['quiz_id'] = $new_quiz->id;
                    $questions[13]['question_type_id'] = 1;
                    $questions[13]['text'] = 'Annan toisillekin työrauhan';
                    $questions[13]['explanation'] = '';
                    $questions[13]['weight'] = NULL;
                    $questions[13]['max_allowed'] = NULL;

                    $questions[14]['quiz_id'] = $new_quiz->id;
                    $questions[14]['question_type_id'] = 6;
                    $questions[14]['text'] = 'Arvosana';
                    $questions[14]['explanation'] = '';
                    $questions[14]['weight'] = NULL;
                    $questions[14]['max_allowed'] = NULL;

                    $questions[15]['quiz_id'] = $new_quiz->id;
                    $questions[15]['question_type_id'] = 1;
                    $questions[15]['text'] = 'Mielestäni oikea arvosana todistukseeni on';
                    $questions[15]['explanation'] = '';
                    $questions[15]['weight'] = NULL;
                    $questions[15]['max_allowed'] = NULL;

                    $questions[16]['quiz_id'] = $new_quiz->id;
                    $questions[16]['question_type_id'] = 5;
                    $questions[16]['text'] = 'Vapaa sana';
                    $questions[16]['explanation'] = 'Alla olevaan tekstikenttään voit kertoa tarkemmin ajatuksiasi.';
                    $questions[16]['weight'] = NULL;
                    $questions[16]['max_allowed'] = NULL;

                    foreach ($questions as $key2 => $question) {
                        $new_question = $this->Quizzes->Questions->newEntity();
                        $new_question = $this->Quizzes->Questions->patchEntity($new_question, $question);
                        if ($this->Quizzes->Questions->save($new_question)) { // save related question
                            $question_ids[] = $new_question->id;
                            $choices = array();

                            // save related choice
                            if ($key2 == 0) { // first question choices # 231
                                $choices[0]['question_id'] = $new_question->id;
                                $choices[0]['text'] = 'Jatkuvasti';
                                $choices[0]['points'] = 0.00;
                                $choices[0]['weight'] = NULL;

                                $choices[1]['question_id'] = $new_question->id;
                                $choices[1]['text'] = 'Joskus';
                                $choices[1]['points'] = 0.00;
                                $choices[1]['weight'] = NULL;

                                $choices[2]['question_id'] = $new_question->id;
                                $choices[2]['text'] = 'Harvoin';
                                $choices[2]['points'] = 0.00;
                                $choices[2]['weight'] = NULL;

                                $choices[3]['question_id'] = $new_question->id;
                                $choices[3]['text'] = 'En koskaan';
                                $choices[3]['points'] = 0.00;
                                $choices[3]['weight'] = NULL;

                                foreach ($choices as $key3 => $choice) {
                                    $new_choice = $this->Quizzes->Questions->Choices->newEntity();
                                    $new_choice = $this->Quizzes->Questions->Choices->patchEntity($new_choice, $choice);
                                    $this->Quizzes->Questions->Choices->save($new_choice);
                                }
                            } elseif ($key2 == 1) { // 2nd question choices #232
                                $choices[0]['question_id'] = $new_question->id;
                                $choices[0]['text'] = 'Jatkuvasti';
                                $choices[0]['points'] = 0.00;
                                $choices[0]['weight'] = NULL;

                                $choices[1]['question_id'] = $new_question->id;
                                $choices[1]['text'] = 'Joskus';
                                $choices[1]['points'] = 0.00;
                                $choices[1]['weight'] = NULL;

                                $choices[2]['question_id'] = $new_question->id;
                                $choices[2]['text'] = 'Harvoin';
                                $choices[2]['points'] = 0.00;
                                $choices[2]['weight'] = NULL;

                                $choices[3]['question_id'] = $new_question->id;
                                $choices[3]['text'] = 'En koskaan';
                                $choices[3]['points'] = 0.00;
                                $choices[3]['weight'] = NULL;

                                foreach ($choices as $key3 => $choice) {
                                    $new_choice = $this->Quizzes->Questions->Choices->newEntity();
                                    $new_choice = $this->Quizzes->Questions->Choices->patchEntity($new_choice, $choice);
                                    $this->Quizzes->Questions->Choices->save($new_choice);
                                }
                            } elseif ($key2 == 2) { //third question choices
                                $choices[0]['question_id'] = $new_question->id;
                                $choices[0]['text'] = 'Jatkuvasti';
                                $choices[0]['points'] = 0.00;
                                $choices[0]['weight'] = NULL;

                                $choices[1]['question_id'] = $new_question->id;
                                $choices[1]['text'] = 'Joskus';
                                $choices[1]['points'] = 0.00;
                                $choices[1]['weight'] = NULL;

                                $choices[2]['question_id'] = $new_question->id;
                                $choices[2]['text'] = 'Harvoin';
                                $choices[2]['points'] = 0.00;
                                $choices[2]['weight'] = NULL;

                                $choices[3]['question_id'] = $new_question->id;
                                $choices[3]['text'] = 'En koskaan';
                                $choices[3]['points'] = 0.00;
                                $choices[3]['weight'] = NULL;

                                foreach ($choices as $key3 => $choice) {
                                    $new_choice = $this->Quizzes->Questions->Choices->newEntity();
                                    $new_choice = $this->Quizzes->Questions->Choices->patchEntity($new_choice, $choice);
                                    $this->Quizzes->Questions->Choices->save($new_choice);
                                }
                            } elseif ($key2 == 5) { // 3/4 skip 6th question choices
                                $choices[0]['question_id'] = $new_question->id;
                                $choices[0]['text'] = 'Jatkuvasti';
                                $choices[0]['points'] = 0.00;
                                $choices[0]['weight'] = NULL;

                                $choices[1]['question_id'] = $new_question->id;
                                $choices[1]['text'] = 'Joskus';
                                $choices[1]['points'] = 0.00;
                                $choices[1]['weight'] = NULL;

                                $choices[2]['question_id'] = $new_question->id;
                                $choices[2]['text'] = 'Harvoin';
                                $choices[2]['points'] = 0.00;
                                $choices[2]['weight'] = NULL;

                                $choices[3]['question_id'] = $new_question->id;
                                $choices[3]['text'] = 'En koskaan';
                                $choices[3]['points'] = 0.00;
                                $choices[3]['weight'] = NULL;

                                foreach ($choices as $key3 => $choice) {
                                    $new_choice = $this->Quizzes->Questions->Choices->newEntity();
                                    $new_choice = $this->Quizzes->Questions->Choices->patchEntity($new_choice, $choice);
                                    $this->Quizzes->Questions->Choices->save($new_choice);
                                }
                            } elseif ($key2 == 6) { // 7th question choices
                                $choices[0]['question_id'] = $new_question->id;
                                $choices[0]['text'] = 'Jatkuvasti';
                                $choices[0]['points'] = 0.00;
                                $choices[0]['weight'] = NULL;

                                $choices[1]['question_id'] = $new_question->id;
                                $choices[1]['text'] = 'Joskus';
                                $choices[1]['points'] = 0.00;
                                $choices[1]['weight'] = NULL;

                                $choices[2]['question_id'] = $new_question->id;
                                $choices[2]['text'] = 'Harvoin';
                                $choices[2]['points'] = 0.00;
                                $choices[2]['weight'] = NULL;

                                $choices[3]['question_id'] = $new_question->id;
                                $choices[3]['text'] = 'En koskaan';
                                $choices[3]['points'] = 0.00;
                                $choices[3]['weight'] = NULL;

                                foreach ($choices as $key3 => $choice) {
                                    $new_choice = $this->Quizzes->Questions->Choices->newEntity();
                                    $new_choice = $this->Quizzes->Questions->Choices->patchEntity($new_choice, $choice);
                                    $this->Quizzes->Questions->Choices->save($new_choice);
                                }
                            } elseif ($key2 == 7) { // 3/4 skip 8th question choices
                                $choices[0]['question_id'] = $new_question->id;
                                $choices[0]['text'] = 'Jatkuvasti';
                                $choices[0]['points'] = 0.00;
                                $choices[0]['weight'] = NULL;

                                $choices[1]['question_id'] = $new_question->id;
                                $choices[1]['text'] = 'Joskus';
                                $choices[1]['points'] = 0.00;
                                $choices[1]['weight'] = NULL;

                                $choices[2]['question_id'] = $new_question->id;
                                $choices[2]['text'] = 'Harvoin';
                                $choices[2]['points'] = 0.00;
                                $choices[2]['weight'] = NULL;

                                $choices[3]['question_id'] = $new_question->id;
                                $choices[3]['text'] = 'En koskaan';
                                $choices[3]['points'] = 0.00;
                                $choices[3]['weight'] = NULL;

                                foreach ($choices as $key3 => $choice) {
                                    $new_choice = $this->Quizzes->Questions->Choices->newEntity();
                                    $new_choice = $this->Quizzes->Questions->Choices->patchEntity($new_choice, $choice);
                                    $this->Quizzes->Questions->Choices->save($new_choice);
                                }
                            } elseif ($key2 == 8) { // 9th question choices
                                $choices[0]['question_id'] = $new_question->id;
                                $choices[0]['text'] = 'Jatkuvasti';
                                $choices[0]['points'] = 0.00;
                                $choices[0]['weight'] = NULL;

                                $choices[1]['question_id'] = $new_question->id;
                                $choices[1]['text'] = 'Joskus';
                                $choices[1]['points'] = 0.00;
                                $choices[1]['weight'] = NULL;

                                $choices[2]['question_id'] = $new_question->id;
                                $choices[2]['text'] = 'Harvoin';
                                $choices[2]['points'] = 0.00;
                                $choices[2]['weight'] = NULL;

                                $choices[3]['question_id'] = $new_question->id;
                                $choices[3]['text'] = 'En koskaan';
                                $choices[3]['points'] = 0.00;
                                $choices[3]['weight'] = NULL;

                                foreach ($choices as $key3 => $choice) {
                                    $new_choice = $this->Quizzes->Questions->Choices->newEntity();
                                    $new_choice = $this->Quizzes->Questions->Choices->patchEntity($new_choice, $choice);
                                    $this->Quizzes->Questions->Choices->save($new_choice);
                                }
                            } elseif ($key2 == 10) { // 7th question choices
                                $choices[0]['question_id'] = $new_question->id;
                                $choices[0]['text'] = 'Jatkuvasti';
                                $choices[0]['points'] = 0.00;
                                $choices[0]['weight'] = NULL;

                                $choices[1]['question_id'] = $new_question->id;
                                $choices[1]['text'] = 'Joskus';
                                $choices[1]['points'] = 0.00;
                                $choices[1]['weight'] = NULL;

                                $choices[2]['question_id'] = $new_question->id;
                                $choices[2]['text'] = 'Harvoin';
                                $choices[2]['points'] = 0.00;
                                $choices[2]['weight'] = NULL;

                                $choices[3]['question_id'] = $new_question->id;
                                $choices[3]['text'] = 'En koskaan';
                                $choices[3]['points'] = 0.00;
                                $choices[3]['weight'] = NULL;

                                foreach ($choices as $key3 => $choice) {
                                    $new_choice = $this->Quizzes->Questions->Choices->newEntity();
                                    $new_choice = $this->Quizzes->Questions->Choices->patchEntity($new_choice, $choice);
                                    $this->Quizzes->Questions->Choices->save($new_choice);
                                }
                            } elseif ($key2 == 11) { // 3/4 skip 8th question choices
                                $choices[0]['question_id'] = $new_question->id;
                                $choices[0]['text'] = 'Jatkuvasti';
                                $choices[0]['points'] = 0.00;
                                $choices[0]['weight'] = NULL;

                                $choices[1]['question_id'] = $new_question->id;
                                $choices[1]['text'] = 'Joskus';
                                $choices[1]['points'] = 0.00;
                                $choices[1]['weight'] = NULL;

                                $choices[2]['question_id'] = $new_question->id;
                                $choices[2]['text'] = 'Harvoin';
                                $choices[2]['points'] = 0.00;
                                $choices[2]['weight'] = NULL;

                                $choices[3]['question_id'] = $new_question->id;
                                $choices[3]['text'] = 'En koskaan';
                                $choices[3]['points'] = 0.00;
                                $choices[3]['weight'] = NULL;

                                foreach ($choices as $key3 => $choice) {
                                    $new_choice = $this->Quizzes->Questions->Choices->newEntity();
                                    $new_choice = $this->Quizzes->Questions->Choices->patchEntity($new_choice, $choice);
                                    $this->Quizzes->Questions->Choices->save($new_choice);
                                }
                            } elseif ($key2 == 12) { // 9th question choices
                                $choices[0]['question_id'] = $new_question->id;
                                $choices[0]['text'] = 'Jatkuvasti';
                                $choices[0]['points'] = 0.00;
                                $choices[0]['weight'] = NULL;

                                $choices[1]['question_id'] = $new_question->id;
                                $choices[1]['text'] = 'Joskus';
                                $choices[1]['points'] = 0.00;
                                $choices[1]['weight'] = NULL;

                                $choices[2]['question_id'] = $new_question->id;
                                $choices[2]['text'] = 'Harvoin';
                                $choices[2]['points'] = 0.00;
                                $choices[2]['weight'] = NULL;

                                $choices[3]['question_id'] = $new_question->id;
                                $choices[3]['text'] = 'En koskaan';
                                $choices[3]['points'] = 0.00;
                                $choices[3]['weight'] = NULL;

                                foreach ($choices as $key3 => $choice) {
                                    $new_choice = $this->Quizzes->Questions->Choices->newEntity();
                                    $new_choice = $this->Quizzes->Questions->Choices->patchEntity($new_choice, $choice);
                                    $this->Quizzes->Questions->Choices->save($new_choice);
                                }
                            } elseif ($key2 == 13) { // 9th question choices
                                $choices[0]['question_id'] = $new_question->id;
                                $choices[0]['text'] = 'Jatkuvasti';
                                $choices[0]['points'] = 0.00;
                                $choices[0]['weight'] = NULL;

                                $choices[1]['question_id'] = $new_question->id;
                                $choices[1]['text'] = 'Joskus';
                                $choices[1]['points'] = 0.00;
                                $choices[1]['weight'] = NULL;

                                $choices[2]['question_id'] = $new_question->id;
                                $choices[2]['text'] = 'Harvoin';
                                $choices[2]['points'] = 0.00;
                                $choices[2]['weight'] = NULL;

                                $choices[3]['question_id'] = $new_question->id;
                                $choices[3]['text'] = 'En koskaan';
                                $choices[3]['points'] = 0.00;
                                $choices[3]['weight'] = NULL;

                                foreach ($choices as $key3 => $choice) {
                                    $new_choice = $this->Quizzes->Questions->Choices->newEntity();
                                    $new_choice = $this->Quizzes->Questions->Choices->patchEntity($new_choice, $choice);
                                    $this->Quizzes->Questions->Choices->save($new_choice);
                                }
                            } elseif ($key2 == 15) { // 9th question choices
                                $choices[0]['question_id'] = $new_question->id;
                                $choices[0]['text'] = '10';
                                $choices[0]['points'] = 0.00;
                                $choices[0]['weight'] = NULL;

                                $choices[1]['question_id'] = $new_question->id;
                                $choices[1]['text'] = '9';
                                $choices[1]['points'] = 0.00;
                                $choices[1]['weight'] = NULL;

                                $choices[2]['question_id'] = $new_question->id;
                                $choices[2]['text'] = '8';
                                $choices[2]['points'] = 0.00;
                                $choices[2]['weight'] = NULL;

                                $choices[3]['question_id'] = $new_question->id;
                                $choices[3]['text'] = '7';
                                $choices[3]['points'] = 0.00;
                                $choices[3]['weight'] = NULL;

                                $choices[1]['question_id'] = $new_question->id;
                                $choices[1]['text'] = '6';
                                $choices[1]['points'] = 0.00;
                                $choices[1]['weight'] = NULL;

                                $choices[2]['question_id'] = $new_question->id;
                                $choices[2]['text'] = '5';
                                $choices[2]['points'] = 0.00;
                                $choices[2]['weight'] = NULL;

                                $choices[3]['question_id'] = $new_question->id;
                                $choices[3]['text'] = '4';
                                $choices[3]['points'] = 0.00;
                                $choices[3]['weight'] = NULL;

                                foreach ($choices as $key3 => $choice) {
                                    $new_choice = $this->Quizzes->Questions->Choices->newEntity();
                                    $new_choice = $this->Quizzes->Questions->Choices->patchEntity($new_choice, $choice);
                                    $this->Quizzes->Questions->Choices->save($new_choice);
                                }
                            } elseif ($key2 == 16) { // 0 skip first question choices
                                $choice['question_id'] = $new_question->id;
                                $choice['text'] = 'Essay';
                                $choice['points'] = 0.00;
                                $choice['weight'] = NULL;
                                $new_choice = $this->Quizzes->Questions->Choices->newEntity();
                                $new_choice = $this->Quizzes->Questions->Choices->patchEntity($new_choice, $choice);
                                $this->Quizzes->Questions->Choices->save($new_choice);
                            } else {
                                // do nothing
                            }
                        }
                    }

                    // 3rd quiz students
                    $students = array();
                    $students[0]['quiz_id'] = $new_quiz->id;
                    $students[0]['fname'] = 'Aapo';
                    $students[0]['lname'] = 'Ahkera';
                    $students[0]['class'] = '7a';
                    $students[0]['status'] = 1;
                    $students[0]['submitted'] = date('Y-m-d H:i:s');

                    $students[1]['quiz_id'] = $new_quiz->id;
                    $students[1]['fname'] = 'Kake';
                    $students[1]['lname'] = 'Kängsteri';
                    $students[1]['class'] = '7a';
                    $students[1]['status'] = 1;
                    $students[1]['submitted'] = date('Y-m-d H:i:s');

                    $students[2]['quiz_id'] = $new_quiz->id;
                    $students[2]['fname'] = 'Tiina';
                    $students[2]['lname'] = 'Terävä';
                    $students[2]['class'] = '7a';
                    $students[2]['status'] = 1;
                    $students[2]['submitted'] = date('Y-m-d H:i:s');

                    $students[3]['quiz_id'] = $new_quiz->id;
                    $students[3]['fname'] = 'Liisa';
                    $students[3]['lname'] = 'Lupsakka';
                    $students[3]['class'] = '7b';
                    $students[3]['status'] = 1;
                    $students[3]['submitted'] = date('Y-m-d H:i:s');

                    $students[4]['quiz_id'] = $new_quiz->id;
                    $students[4]['fname'] = 'Jonne';
                    $students[4]['lname'] = 'Jopomies';
                    $students[4]['class'] = '7b';
                    $students[4]['status'] = 1;
                    $students[4]['submitted'] = date('Y-m-d H:i:s');

                    $students[5]['quiz_id'] = $new_quiz->id;
                    $students[5]['fname'] = 'Kaija';
                    $students[5]['lname'] = 'Keskiverto';
                    $students[5]['class'] = '7b';
                    $students[5]['status'] = 1;
                    $students[5]['submitted'] = date('Y-m-d H:i:s');

                    $students[6]['quiz_id'] = $new_quiz->id;
                    $students[6]['fname'] = 'Veeti';
                    $students[6]['lname'] = 'Verraton';
                    $students[6]['class'] = '7c';
                    $students[6]['status'] = 1;
                    $students[6]['submitted'] = date('Y-m-d H:i:s');

                    $students[7]['quiz_id'] = $new_quiz->id;
                    $students[7]['fname'] = 'Sirpa';
                    $students[7]['lname'] = 'Sipakka';
                    $students[7]['class'] = '7c';
                    $students[7]['status'] = 1;
                    $students[7]['submitted'] = date('Y-m-d H:i:s');

                    $students[8]['quiz_id'] = $new_quiz->id;
                    $students[8]['fname'] = 'Kiia';
                    $students[8]['lname'] = 'Ketterä';
                    $students[8]['class'] = '7c';
                    $students[8]['status'] = 1;
                    $students[8]['submitted'] = date('Y-m-d H:i:s');
                    
                    foreach ($students as $key4 => $student) { // Save all student of first quiz
                        $new_student = $this->Quizzes->Students->newEntity();
                        $new_student = $this->Quizzes->Students->patchEntity($new_student, $student);
                        if ($this->Quizzes->Students->save($new_student)) { // Save student data
                            $answers = array();
                            if ($key4 == 0) { // First student answer/ranking save
                                $answers[0]['question_id'] = $question_ids[0];
                                $answers[0]['student_id'] = $new_student->id;
                                $answers[0]['text'] = 'Jatkuvasti';
                                $answers[0]['score'] = 0.00;

                                $answers[1]['question_id'] = $question_ids[1];
                                $answers[1]['student_id'] = $new_student->id;
                                $answers[1]['text'] = 'Jatkuvasti';
                                $answers[1]['score'] = 0.00;

                                $answers[2]['question_id'] = $question_ids[2];
                                $answers[2]['student_id'] = $new_student->id;
                                $answers[2]['text'] = 'Jatkuvasti';
                                $answers[2]['score'] = 0.00;

                                $answers[3]['question_id'] = $question_ids[5];
                                $answers[3]['student_id'] = $new_student->id;
                                $answers[3]['text'] = 'Joskus';
                                $answers[3]['score'] = 0.00;

                                $answers[4]['question_id'] = $question_ids[6];
                                $answers[4]['student_id'] = $new_student->id;
                                $answers[4]['text'] = 'Joskus';
                                $answers[4]['score'] = 0.00;

                                $answers[5]['question_id'] = $question_ids[7];
                                $answers[5]['student_id'] = $new_student->id;
                                $answers[5]['text'] = 'Joskus';
                                $answers[5]['score'] = 0.00;

                                $answers[6]['question_id'] = $question_ids[7];
                                $answers[6]['student_id'] = $new_student->id;
                                $answers[6]['text'] = 'Jatkuvasti';
                                $answers[6]['score'] = 0.00;

                                $answers[7]['question_id'] = $question_ids[8];
                                $answers[7]['student_id'] = $new_student->id;
                                $answers[7]['text'] = 'Jatkuvasti';
                                $answers[7]['score'] = 0.00;

                                $answers[8]['question_id'] = $question_ids[10];
                                $answers[8]['student_id'] = $new_student->id;
                                $answers[8]['text'] = 'Jatkuvasti';
                                $answers[8]['score'] = 0.00;

                                $answers[9]['question_id'] = $question_ids[11];
                                $answers[9]['student_id'] = $new_student->id;
                                $answers[9]['text'] = 'Jatkuvasti';
                                $answers[9]['score'] = 0.00;

                                $answers[10]['question_id'] = $question_ids[12];
                                $answers[10]['student_id'] = $new_student->id;
                                $answers[10]['text'] = 'Jatkuvasti';
                                $answers[10]['score'] = 0.00;

                                $answers[11]['question_id'] = $question_ids[13];
                                $answers[11]['student_id'] = $new_student->id;
                                $answers[11]['text'] = 'Jatkuvasti';
                                $answers[11]['score'] = 0.00;

                                $answers[12]['question_id'] = $question_ids[15];
                                $answers[12]['student_id'] = $new_student->id;
                                $answers[12]['text'] = '9';
                                $answers[12]['score'] = 0.00;

                                $answers[13]['question_id'] = $question_ids[16];
                                $answers[13]['student_id'] = $new_student->id;
                                $answers[13]['text'] = 'Haaveilen kympistäkin, mutta en kehdannut pistää sitä tuohon ylös.';
                                $answers[13]['score'] = NULL;

                                foreach ($answers as $key5 => $answer) { // save answers
                                    $new_answer = $this->Quizzes->Students->Answers->newEntity();
                                    $new_answer = $this->Quizzes->Students->Answers->patchEntity($new_answer, $answer);
                                    $this->Quizzes->Students->Answers->save($new_answer);
                                } // end of anwers saving

                                // Prepare ranking data
                                $ranking['quiz_id'] = $new_quiz->id;
                                $ranking['student_id'] = $new_student->id;
                                $ranking['score'] = 0.00;
                                $ranking['total'] = 0.00; 

                                $new_ranking = $this->Quizzes->Rankings->newEntity();
                                $new_ranking = $this->Quizzes->Rankings->patchEntity($new_ranking, $ranking);
                                $this->Quizzes->Rankings->save($new_ranking);
                            } // First student answers/ranking save end

                            if ($key4 == 1) { // 2nd student answer/ranking save
                                $answers[0]['question_id'] = $question_ids[0];
                                $answers[0]['student_id'] = $new_student->id;
                                $answers[0]['text'] = 'Harvoin';
                                $answers[0]['score'] = 0.00;

                                $answers[1]['question_id'] = $question_ids[1];
                                $answers[1]['student_id'] = $new_student->id;
                                $answers[1]['text'] = 'En koskaan';
                                $answers[1]['score'] = 0.00;

                                $answers[2]['question_id'] = $question_ids[2];
                                $answers[2]['student_id'] = $new_student->id;
                                $answers[2]['text'] = 'Harvoin';
                                $answers[2]['score'] = 0.00;

                                $answers[3]['question_id'] = $question_ids[5];
                                $answers[3]['student_id'] = $new_student->id;
                                $answers[3]['text'] = 'Harvoin';
                                $answers[3]['score'] = 0.00;

                                $answers[4]['question_id'] = $question_ids[6];
                                $answers[4]['student_id'] = $new_student->id;
                                $answers[4]['text'] = 'Joskus';
                                $answers[4]['score'] = 0.00;

                                $answers[5]['question_id'] = $question_ids[7];
                                $answers[5]['student_id'] = $new_student->id;
                                $answers[5]['text'] = 'Harvoin';
                                $answers[5]['score'] = 0.00;


                                $answers[6]['question_id'] = $question_ids[8];
                                $answers[6]['student_id'] = $new_student->id;
                                $answers[6]['text'] = 'Harvoin';
                                $answers[6]['score'] = 0.00;

                                $answers[7]['question_id'] = $question_ids[10];
                                $answers[7]['student_id'] = $new_student->id;
                                $answers[7]['text'] = 'En koskaan';
                                $answers[7]['score'] = 0.00;

                                $answers[8]['question_id'] = $question_ids[11];
                                $answers[8]['student_id'] = $new_student->id;
                                $answers[8]['text'] = 'En koskaan';
                                $answers[8]['score'] = 0.00;

                                $answers[9]['question_id'] = $question_ids[12];
                                $answers[9]['student_id'] = $new_student->id;
                                $answers[9]['text'] = 'En koskaan';
                                $answers[9]['score'] = 0.00;

                                $answers[10]['question_id'] = $question_ids[13];
                                $answers[10]['student_id'] = $new_student->id;
                                $answers[10]['text'] = 'Harvoin';
                                $answers[10]['score'] = 0.00;

                                $answers[11]['question_id'] = $question_ids[15];
                                $answers[11]['student_id'] = $new_student->id;
                                $answers[11]['text'] = '7';
                                $answers[11]['score'] = 0.00;

                                $answers[12]['question_id'] = $question_ids[16];
                                $answers[12]['student_id'] = $new_student->id;
                                $answers[12]['text'] = 'Ei mitn';
                                $answers[12]['score'] = NULL;

                                foreach ($answers as $key5 => $answer) { // save answers
                                    $new_answer = $this->Quizzes->Students->Answers->newEntity();
                                    $new_answer = $this->Quizzes->Students->Answers->patchEntity($new_answer, $answer);
                                    $this->Quizzes->Students->Answers->save($new_answer);
                                } // end of anwers saving

                                // Prepare ranking data
                                $ranking['quiz_id'] = $new_quiz->id;
                                $ranking['student_id'] = $new_student->id;
                                $ranking['score'] = 0.00;
                                $ranking['total'] = 0.00; 

                                $new_ranking = $this->Quizzes->Rankings->newEntity();
                                $new_ranking = $this->Quizzes->Rankings->patchEntity($new_ranking, $ranking);
                                $this->Quizzes->Rankings->save($new_ranking);
                            } // 2nd student answers/ranking save end

                            if ($key4 == 2) { // Third student answer/ranking save
                                $answers[0]['question_id'] = $question_ids[0];
                                $answers[0]['student_id'] = $new_student->id;
                                $answers[0]['text'] = 'Jatkuvasti';
                                $answers[0]['score'] = 0.00;

                                $answers[1]['question_id'] = $question_ids[1];
                                $answers[1]['student_id'] = $new_student->id;
                                $answers[1]['text'] = 'Harvoin';
                                $answers[1]['score'] = 0.00;

                                $answers[2]['question_id'] = $question_ids[2];
                                $answers[2]['student_id'] = $new_student->id;
                                $answers[2]['text'] = 'Jatkuvasti';
                                $answers[2]['score'] = 0.00;

                                $answers[3]['question_id'] = $question_ids[5];
                                $answers[3]['student_id'] = $new_student->id;
                                $answers[3]['text'] = 'Joskus';
                                $answers[3]['score'] = 0.00;

                                $answers[4]['question_id'] = $question_ids[6];
                                $answers[4]['student_id'] = $new_student->id;
                                $answers[4]['text'] = 'Jatkuvasti';
                                $answers[4]['score'] = 0.00;

                    
                                $answers[5]['question_id'] = $question_ids[7];
                                $answers[5]['student_id'] = $new_student->id;
                                $answers[5]['text'] = 'Jatkuvasti';
                                $answers[5]['score'] = 0.00;

                                $answers[6]['question_id'] = $question_ids[8];
                                $answers[6]['student_id'] = $new_student->id;
                                $answers[6]['text'] = 'Harvoin';
                                $answers[6]['score'] = 0.00;

                                $answers[7]['question_id'] = $question_ids[10];
                                $answers[7]['student_id'] = $new_student->id;
                                $answers[7]['text'] = 'En koskaan';
                                $answers[7]['score'] = 0.00;

                                $answers[8]['question_id'] = $question_ids[11];
                                $answers[8]['student_id'] = $new_student->id;
                                $answers[8]['text'] = 'Joskus';
                                $answers[8]['score'] = 0.00;

                                $answers[9]['question_id'] = $question_ids[12];
                                $answers[9]['student_id'] = $new_student->id;
                                $answers[9]['text'] = 'Jatkuvasti';
                                $answers[9]['score'] = 0.00;

                                $answers[10]['question_id'] = $question_ids[13];
                                $answers[10]['student_id'] = $new_student->id;
                                $answers[10]['text'] = 'Jatkuvasti';
                                $answers[10]['score'] = 0.00;

                                $answers[11]['question_id'] = $question_ids[15];
                                $answers[11]['student_id'] = $new_student->id;
                                $answers[11]['text'] = '8';
                                $answers[11]['score'] = 0.00;

                                $answers[12]['question_id'] = $question_ids[16];
                                $answers[12]['student_id'] = $new_student->id;
                                $answers[12]['text'] = 'En kehtaa oikein puhua ääneen vaikka asia kiinnostaakin minua. Voisinkohan tehdä lisätehtäviä kirjallisesti niin saisin näyttää taitojani?';
                                $answers[12]['score'] = NULL;

                                foreach ($answers as $key5 => $answer) { // save answers
                                    $new_answer = $this->Quizzes->Students->Answers->newEntity();
                                    $new_answer = $this->Quizzes->Students->Answers->patchEntity($new_answer, $answer);
                                    $this->Quizzes->Students->Answers->save($new_answer);
                                } // end of anwers saving

                                // Prepare ranking data
                                $ranking['quiz_id'] = $new_quiz->id;
                                $ranking['student_id'] = $new_student->id;
                                $ranking['score'] = 0.00;
                                $ranking['total'] = 0.00; 

                                $new_ranking = $this->Quizzes->Rankings->newEntity();
                                $new_ranking = $this->Quizzes->Rankings->patchEntity($new_ranking, $ranking);
                                $this->Quizzes->Rankings->save($new_ranking);
                            } // Third student answers/ranking save end

                            if ($key4 == 3) { // 4th student answer/ranking save
                                $answers[0]['question_id'] = $question_ids[0];
                                $answers[0]['student_id'] = $new_student->id;
                                $answers[0]['text'] = 'Joskus';
                                $answers[0]['score'] = 0.00;

                                $answers[1]['question_id'] = $question_ids[1];
                                $answers[1]['student_id'] = $new_student->id;
                                $answers[1]['text'] = 'Harvoin';
                                $answers[1]['score'] = 0.00;

                                $answers[2]['question_id'] = $question_ids[2];
                                $answers[2]['student_id'] = $new_student->id;
                                $answers[2]['text'] = 'Harvoin';
                                $answers[2]['score'] = 0.00;

                                $answers[3]['question_id'] = $question_ids[5];
                                $answers[3]['student_id'] = $new_student->id;
                                $answers[3]['text'] = 'En koskaan';
                                $answers[3]['score'] = 0.00;

                                $answers[4]['question_id'] = $question_ids[6];
                                $answers[4]['student_id'] = $new_student->id;
                                $answers[4]['text'] = 'Jatkuvasti';
                                $answers[4]['score'] = 0.00;

                                $answers[5]['question_id'] = $question_ids[7];
                                $answers[5]['student_id'] = $new_student->id;
                                $answers[5]['text'] = 'Joskus';
                                $answers[5]['score'] = 0.00;

                                $answers[6]['question_id'] = $question_ids[8];
                                $answers[6]['student_id'] = $new_student->id;
                                $answers[6]['text'] = 'Harvoin';
                                $answers[6]['score'] = 0.00;

                                $answers[7]['question_id'] = $question_ids[10];
                                $answers[7]['student_id'] = $new_student->id;
                                $answers[7]['text'] = 'Joskus';
                                $answers[7]['score'] = 0.00;

                                $answers[8]['question_id'] = $question_ids[11];
                                $answers[8]['student_id'] = $new_student->id;
                                $answers[8]['text'] = 'Joskus';
                                $answers[8]['score'] = 0.00;

                                $answers[9]['question_id'] = $question_ids[12];
                                $answers[9]['student_id'] = $new_student->id;
                                $answers[9]['text'] = 'Harvoin';
                                $answers[9]['score'] = 0.00;

                                $answers[10]['question_id'] = $question_ids[13];
                                $answers[10]['student_id'] = $new_student->id;
                                $answers[10]['text'] = 'Jatkuvasti';
                                $answers[10]['score'] = 0.00;

                                $answers[11]['question_id'] = $question_ids[15];
                                $answers[11]['student_id'] = $new_student->id;
                                $answers[11]['text'] = '8';
                                $answers[11]['score'] = 0.00;

                                $answers[12]['question_id'] = $question_ids[16];
                                $answers[12]['student_id'] = $new_student->id;
                                $answers[12]['text'] = 'Tulee juteltua kavereiden kanssa ehkä vähän enemmän kuin pitäisi...';
                                $answers[12]['score'] = NULL;

                                foreach ($answers as $key5 => $answer) { // save answers
                                    $new_answer = $this->Quizzes->Students->Answers->newEntity();
                                    $new_answer = $this->Quizzes->Students->Answers->patchEntity($new_answer, $answer);
                                    $this->Quizzes->Students->Answers->save($new_answer);
                                } // end of anwers saving

                                // Prepare ranking data
                                $ranking['quiz_id'] = $new_quiz->id;
                                $ranking['student_id'] = $new_student->id;
                                $ranking['score'] = 0.00;
                                $ranking['total'] = 0.00; 

                                $new_ranking = $this->Quizzes->Rankings->newEntity();
                                $new_ranking = $this->Quizzes->Rankings->patchEntity($new_ranking, $ranking);
                                $this->Quizzes->Rankings->save($new_ranking);
                            } // 4th student answers/ranking save end

                            if ($key4 == 4) { // 5th student answer/ranking save
                                $answers[0]['question_id'] = $question_ids[0];
                                $answers[0]['student_id'] = $new_student->id;
                                $answers[0]['text'] = 'Joskus';
                                $answers[0]['score'] = 0.00;

                                $answers[1]['question_id'] = $question_ids[1];
                                $answers[1]['student_id'] = $new_student->id;
                                $answers[1]['text'] = 'Jatkuvasti';
                                $answers[1]['score'] = 0.00;

                                $answers[2]['question_id'] = $question_ids[2];
                                $answers[2]['student_id'] = $new_student->id;
                                $answers[2]['text'] = 'Joskus';
                                $answers[2]['score'] = 0.00;

                                $answers[3]['question_id'] = $question_ids[5];
                                $answers[3]['student_id'] = $new_student->id;
                                $answers[3]['text'] = 'Harvoin';
                                $answers[3]['score'] = 0.00;

                                $answers[4]['question_id'] = $question_ids[6];
                                $answers[4]['student_id'] = $new_student->id;
                                $answers[4]['text'] = 'Jatkuvasti';
                                $answers[4]['score'] = 0.00;

                                $answers[5]['question_id'] = $question_ids[7];
                                $answers[5]['student_id'] = $new_student->id;
                                $answers[5]['text'] = 'Jatkuvasti';
                                $answers[5]['score'] = 0.00;

                                $answers[6]['question_id'] = $question_ids[8];
                                $answers[6]['student_id'] = $new_student->id;
                                $answers[6]['text'] = 'Harvoin';
                                $answers[6]['score'] = 0.00;

                                $answers[7]['question_id'] = $question_ids[10];
                                $answers[7]['student_id'] = $new_student->id;
                                $answers[7]['text'] = 'Jatkuvasti';
                                $answers[7]['score'] = 0.00;

                                $answers[8]['question_id'] = $question_ids[11];
                                $answers[8]['student_id'] = $new_student->id;
                                $answers[8]['text'] = 'Jatkuvasti';
                                $answers[8]['score'] = 0.00;

                                $answers[9]['question_id'] = $question_ids[12];
                                $answers[9]['student_id'] = $new_student->id;
                                $answers[9]['text'] = 'Joskus';
                                $answers[9]['score'] = 0.00;

                                $answers[10]['question_id'] = $question_ids[13];
                                $answers[10]['student_id'] = $new_student->id;
                                $answers[10]['text'] = 'Joskus';
                                $answers[10]['score'] = 0.00;

                                $answers[11]['question_id'] = $question_ids[15];
                                $answers[11]['student_id'] = $new_student->id;
                                $answers[11]['text'] = '9';
                                $answers[11]['score'] = 0.00;

                                $answers[12]['question_id'] = $question_ids[16];
                                $answers[12]['student_id'] = $new_student->id;
                                $answers[12]['text'] = 'Osallistun mielellään tunnin keskusteluihin. Välillä ehkä meinaa lähteä lapasesta.';
                                $answers[12]['score'] = NULL;

                                foreach ($answers as $key5 => $answer) { // save answers
                                    $new_answer = $this->Quizzes->Students->Answers->newEntity();
                                    $new_answer = $this->Quizzes->Students->Answers->patchEntity($new_answer, $answer);
                                    $this->Quizzes->Students->Answers->save($new_answer);
                                } // end of anwers saving

                                // Prepare ranking data
                                $ranking['quiz_id'] = $new_quiz->id;
                                $ranking['student_id'] = $new_student->id;
                                $ranking['score'] = 0.00;
                                $ranking['total'] = 0.00; 

                                $new_ranking = $this->Quizzes->Rankings->newEntity();
                                $new_ranking = $this->Quizzes->Rankings->patchEntity($new_ranking, $ranking);
                                $this->Quizzes->Rankings->save($new_ranking);
                            } // 5th student answers/ranking save end

                            if ($key4 == 5) { // 6th student answer/ranking save
                                $answers[0]['question_id'] = $question_ids[0];
                                $answers[0]['student_id'] = $new_student->id;
                                $answers[0]['text'] = 'Joskus';
                                $answers[0]['score'] = 0.00;

                                $answers[1]['question_id'] = $question_ids[1];
                                $answers[1]['student_id'] = $new_student->id;
                                $answers[1]['text'] = 'Joskus';
                                $answers[1]['score'] = 0.00;

                                $answers[2]['question_id'] = $question_ids[2];
                                $answers[2]['student_id'] = $new_student->id;
                                $answers[2]['text'] = 'Joskus';
                                $answers[2]['score'] = 0.00;

                                $answers[3]['question_id'] = $question_ids[5];
                                $answers[3]['student_id'] = $new_student->id;
                                $answers[3]['text'] = 'Joskus';
                                $answers[3]['score'] = 0.00;

                                $answers[4]['question_id'] = $question_ids[6];
                                $answers[4]['student_id'] = $new_student->id;
                                $answers[4]['text'] = 'Joskus';
                                $answers[4]['score'] = 0.00;

                                $answers[5]['question_id'] = $question_ids[7];
                                $answers[5]['student_id'] = $new_student->id;
                                $answers[5]['text'] = 'Joskus';
                                $answers[5]['score'] = 0.00;

                                $answers[6]['question_id'] = $question_ids[8];
                                $answers[6]['student_id'] = $new_student->id;
                                $answers[6]['text'] = 'Joskus';
                                $answers[6]['score'] = 0.00;

                                $answers[7]['question_id'] = $question_ids[10];
                                $answers[7]['student_id'] = $new_student->id;
                                $answers[7]['text'] = 'Joskus';
                                $answers[7]['score'] = 0.00;

                                $answers[8]['question_id'] = $question_ids[11];
                                $answers[8]['student_id'] = $new_student->id;
                                $answers[8]['text'] = 'Joskus';
                                $answers[8]['score'] = 0.00;

                                $answers[9]['question_id'] = $question_ids[12];
                                $answers[9]['student_id'] = $new_student->id;
                                $answers[9]['text'] = 'Joskus';
                                $answers[9]['score'] = 0.00;

                                $answers[10]['question_id'] = $question_ids[13];
                                $answers[10]['student_id'] = $new_student->id;
                                $answers[10]['text'] = 'Joskus';
                                $answers[10]['score'] = 0.00;

                                $answers[11]['question_id'] = $question_ids[15];
                                $answers[11]['student_id'] = $new_student->id;
                                $answers[11]['text'] = '7';
                                $answers[11]['score'] = 0.00;

                                $answers[12]['question_id'] = $question_ids[16];
                                $answers[12]['student_id'] = $new_student->id;
                                $answers[12]['text'] = '-';
                                $answers[12]['score'] = NULL;

                                foreach ($answers as $key5 => $answer) { // save answers
                                    $new_answer = $this->Quizzes->Students->Answers->newEntity();
                                    $new_answer = $this->Quizzes->Students->Answers->patchEntity($new_answer, $answer);
                                    $this->Quizzes->Students->Answers->save($new_answer);
                                } // end of anwers saving

                                // Prepare ranking data
                                $ranking['quiz_id'] = $new_quiz->id;
                                $ranking['student_id'] = $new_student->id;
                                $ranking['score'] = 0.00;
                                $ranking['total'] = 0.00; 

                                $new_ranking = $this->Quizzes->Rankings->newEntity();
                                $new_ranking = $this->Quizzes->Rankings->patchEntity($new_ranking, $ranking);
                                $this->Quizzes->Rankings->save($new_ranking);
                            } // 6th student answers/ranking save end

                            if ($key4 == 6) { // 7th student answer/ranking save
                                $answers[0]['question_id'] = $question_ids[0];
                                $answers[0]['student_id'] = $new_student->id;
                                $answers[0]['text'] = 'Jatkuvasti';
                                $answers[0]['score'] = 0.00;

                                $answers[1]['question_id'] = $question_ids[1];
                                $answers[1]['student_id'] = $new_student->id;
                                $answers[1]['text'] = 'Jatkuvasti';
                                $answers[1]['score'] = 0.00;

                                $answers[2]['question_id'] = $question_ids[2];
                                $answers[2]['student_id'] = $new_student->id;
                                $answers[2]['text'] = 'Jatkuvasti';
                                $answers[2]['score'] = 0.00;

                                $answers[3]['question_id'] = $question_ids[5];
                                $answers[3]['student_id'] = $new_student->id;
                                $answers[3]['text'] = 'Jatkuvasti';
                                $answers[3]['score'] = 0.00;

                                $answers[4]['question_id'] = $question_ids[6];
                                $answers[4]['student_id'] = $new_student->id;
                                $answers[4]['text'] = 'Jatkuvasti';
                                $answers[4]['score'] = 0.00;

                                $answers[5]['question_id'] = $question_ids[7];
                                $answers[5]['student_id'] = $new_student->id;
                                $answers[5]['text'] = 'Jatkuvasti';
                                $answers[5]['score'] = 0.00;

                                $answers[6]['question_id'] = $question_ids[8];
                                $answers[6]['student_id'] = $new_student->id;
                                $answers[6]['text'] = 'Jatkuvasti';
                                $answers[6]['score'] = 0.00;

                                $answers[7]['question_id'] = $question_ids[10];
                                $answers[7]['student_id'] = $new_student->id;
                                $answers[7]['text'] = 'Jatkuvasti';
                                $answers[7]['score'] = 0.00;

                                $answers[8]['question_id'] = $question_ids[11];
                                $answers[8]['student_id'] = $new_student->id;
                                $answers[8]['text'] = 'Jatkuvasti';
                                $answers[8]['score'] = 0.00;

                                $answers[9]['question_id'] = $question_ids[12];
                                $answers[9]['student_id'] = $new_student->id;
                                $answers[9]['text'] = 'Jatkuvasti';
                                $answers[9]['score'] = 0.00;

                                $answers[10]['question_id'] = $question_ids[13];
                                $answers[10]['student_id'] = $new_student->id;
                                $answers[10]['text'] = 'Jatkuvasti';
                                $answers[10]['score'] = 0.00;

                                $answers[11]['question_id'] = $question_ids[15];
                                $answers[11]['student_id'] = $new_student->id;
                                $answers[11]['text'] = '10';
                                $answers[11]['score'] = 0.00;

                                $answers[12]['question_id'] = $question_ids[16];
                                $answers[12]['student_id'] = $new_student->id;
                                $answers[12]['text'] = 'Koen, että olen ansainnut parhaimman arvosanan näistä opinnoista.';
                                $answers[12]['score'] = NULL;

                                foreach ($answers as $key5 => $answer) { // save answers
                                    $new_answer = $this->Quizzes->Students->Answers->newEntity();
                                    $new_answer = $this->Quizzes->Students->Answers->patchEntity($new_answer, $answer);
                                    $this->Quizzes->Students->Answers->save($new_answer);
                                } // end of anwers saving

                                // Prepare ranking data
                                $ranking['quiz_id'] = $new_quiz->id;
                                $ranking['student_id'] = $new_student->id;
                                $ranking['score'] = 0.00;
                                $ranking['total'] = 0.00; 

                                $new_ranking = $this->Quizzes->Rankings->newEntity();
                                $new_ranking = $this->Quizzes->Rankings->patchEntity($new_ranking, $ranking);
                                $this->Quizzes->Rankings->save($new_ranking);
                            } // 7th student answers/ranking save end

                            if ($key4 == 7) { // 8th student answer/ranking save
                                $answers[0]['question_id'] = $question_ids[0];
                                $answers[0]['student_id'] = $new_student->id;
                                $answers[0]['text'] = 'Joskus';
                                $answers[0]['score'] = 0.00;

                                $answers[1]['question_id'] = $question_ids[1];
                                $answers[1]['student_id'] = $new_student->id;
                                $answers[1]['text'] = 'Harvoin';
                                $answers[1]['score'] = 0.00;

                                $answers[2]['question_id'] = $question_ids[2];
                                $answers[2]['student_id'] = $new_student->id;
                                $answers[2]['text'] = 'Joskus';
                                $answers[2]['score'] = 0.00;

                                $answers[3]['question_id'] = $question_ids[5];
                                $answers[3]['student_id'] = $new_student->id;
                                $answers[3]['text'] = 'Jatkuvasti';
                                $answers[3]['score'] = 0.00;

                                $answers[4]['question_id'] = $question_ids[6];
                                $answers[4]['student_id'] = $new_student->id;
                                $answers[4]['text'] = 'Jatkuvasti';
                                $answers[4]['score'] = 0.00;

                                $answers[5]['question_id'] = $question_ids[7];
                                $answers[5]['student_id'] = $new_student->id;
                                $answers[5]['text'] = 'Joskus';
                                $answers[5]['score'] = 0.00;

                                $answers[6]['question_id'] = $question_ids[8];
                                $answers[6]['student_id'] = $new_student->id;
                                $answers[6]['text'] = 'Harvoin';
                                $answers[6]['score'] = 0.00;

                                $answers[7]['question_id'] = $question_ids[10];
                                $answers[7]['student_id'] = $new_student->id;
                                $answers[7]['text'] = 'Harvoin';
                                $answers[7]['score'] = 0.00;

                                $answers[8]['question_id'] = $question_ids[11];
                                $answers[8]['student_id'] = $new_student->id;
                                $answers[8]['text'] = 'Joskus';
                                $answers[8]['score'] = 0.00;

                                $answers[9]['question_id'] = $question_ids[12];
                                $answers[9]['student_id'] = $new_student->id;
                                $answers[9]['text'] = 'Joskus';
                                $answers[9]['score'] = 0.00;

                                $answers[10]['question_id'] = $question_ids[13];
                                $answers[10]['student_id'] = $new_student->id;
                                $answers[10]['text'] = 'Joskus';
                                $answers[10]['score'] = 0.00;

                                $answers[11]['question_id'] = $question_ids[15];
                                $answers[11]['student_id'] = $new_student->id;
                                $answers[11]['text'] = '8';
                                $answers[11]['score'] = 0.00;

                                $answers[12]['question_id'] = $question_ids[16];
                                $answers[12]['student_id'] = $new_student->id;
                                $answers[12]['text'] = 'Ei lisättävää';
                                $answers[12]['score'] = NULL;

                                foreach ($answers as $key5 => $answer) { // save answers
                                    $new_answer = $this->Quizzes->Students->Answers->newEntity();
                                    $new_answer = $this->Quizzes->Students->Answers->patchEntity($new_answer, $answer);
                                    $this->Quizzes->Students->Answers->save($new_answer);
                                } // end of anwers saving

                                // Prepare ranking data
                                $ranking['quiz_id'] = $new_quiz->id;
                                $ranking['student_id'] = $new_student->id;
                                $ranking['score'] = 0.00;
                                $ranking['total'] = 0.00; 

                                $new_ranking = $this->Quizzes->Rankings->newEntity();
                                $new_ranking = $this->Quizzes->Rankings->patchEntity($new_ranking, $ranking);
                                $this->Quizzes->Rankings->save($new_ranking);
                            } // 8th student answers/ranking save end

                            if ($key4 == 8) { // 9th student answer/ranking save
                                $answers[0]['question_id'] = $question_ids[0];
                                $answers[0]['student_id'] = $new_student->id;
                                $answers[0]['text'] = 'Jatkuvasti';
                                $answers[0]['score'] = 0.00;

                                $answers[1]['question_id'] = $question_ids[1];
                                $answers[1]['student_id'] = $new_student->id;
                                $answers[1]['text'] = 'Jatkuvasti';
                                $answers[1]['score'] = 0.00;

                                $answers[2]['question_id'] = $question_ids[2];
                                $answers[2]['student_id'] = $new_student->id;
                                $answers[2]['text'] = 'Jatkuvasti';
                                $answers[2]['score'] = 0.00;

                                $answers[3]['question_id'] = $question_ids[5];
                                $answers[3]['student_id'] = $new_student->id;
                                $answers[3]['text'] = 'Harvoin';
                                $answers[3]['score'] = 0.00;

                                $answers[4]['question_id'] = $question_ids[6];
                                $answers[4]['student_id'] = $new_student->id;
                                $answers[4]['text'] = 'Jatkuvasti';
                                $answers[4]['score'] = 0.00;


                                $answers[5]['question_id'] = $question_ids[7];
                                $answers[5]['student_id'] = $new_student->id;
                                $answers[5]['text'] = 'Harvoin';
                                $answers[5]['score'] = 0.00;

                                $answers[6]['question_id'] = $question_ids[8];
                                $answers[6]['student_id'] = $new_student->id;
                                $answers[6]['text'] = 'Joskus';
                                $answers[6]['score'] = 0.00;

                                $answers[7]['question_id'] = $question_ids[10];
                                $answers[7]['student_id'] = $new_student->id;
                                $answers[7]['text'] = 'Joskus';
                                $answers[7]['score'] = 0.00;

                                $answers[8]['question_id'] = $question_ids[11];
                                $answers[8]['student_id'] = $new_student->id;
                                $answers[8]['text'] = 'Joskus';
                                $answers[8]['score'] = 0.00;

                                $answers[9]['question_id'] = $question_ids[12];
                                $answers[9]['student_id'] = $new_student->id;
                                $answers[9]['text'] = 'Joskus';
                                $answers[9]['score'] = 0.00;

                                $answers[10]['question_id'] = $question_ids[13];
                                $answers[10]['student_id'] = $new_student->id;
                                $answers[10]['text'] = 'Jatkuvasti';
                                $answers[10]['score'] = 0.00;

                                $answers[11]['question_id'] = $question_ids[15];
                                $answers[11]['student_id'] = $new_student->id;
                                $answers[11]['text'] = '9';
                                $answers[11]['score'] = 0.00;

                                $answers[12]['question_id'] = $question_ids[16];
                                $answers[12]['student_id'] = $new_student->id;
                                $answers[12]['text'] = 'Urheilutouhut vievät aikaa koulutyöltä.';
                                $answers[12]['score'] = NULL;

                                foreach ($answers as $key5 => $answer) { // save answers
                                    $new_answer = $this->Quizzes->Students->Answers->newEntity();
                                    $new_answer = $this->Quizzes->Students->Answers->patchEntity($new_answer, $answer);
                                    $this->Quizzes->Students->Answers->save($new_answer);
                                } // end of anwers saving

                                // Prepare ranking data
                                $ranking['quiz_id'] = $new_quiz->id;
                                $ranking['student_id'] = $new_student->id;
                                $ranking['score'] = 0.00;
                                $ranking['total'] = 0.00; 

                                $new_ranking = $this->Quizzes->Rankings->newEntity();
                                $new_ranking = $this->Quizzes->Rankings->patchEntity($new_ranking, $ranking);
                                $this->Quizzes->Rankings->save($new_ranking);
                            } // 9th student answers/ranking save end



                        }
                    } // 3rd quiz student end

                } // Quiz 3 end

            } // Save Quiz end
        }
    }

}
