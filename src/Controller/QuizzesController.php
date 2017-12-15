<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Network\Exception\NotFoundException;
use Cake\Datasource\ConnectionManager;
use Cake\Utility\Hash;
use Cake\Core\Configure;
use Cake\I18n\I18n;

/**
 * Quizzes Controller
 *
 * @property \App\Model\Table\QuizzesTable $Quizzes
 */
class QuizzesController extends AppController
{
    public $paginate = [
        'limit' => 25
    ];

    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Paginator');
        $this->loadComponent('Email');
        $this->Auth->allow(['live', 'no_permission']);
    }

    public function ajaxStudentUpdate() {
        $this->viewBuilder()->layout('ajax');
        $student = $this->Quizzes->Students->get($this->request->data['student_id'], ['contain' => ['Answers']]);
        $this->set('value1', $student);
        $filter = $this->Session->read('Filter');
        $quizDetails = $this->Quizzes->quizDetails($student['quiz_id'], $filter);
        $sl = (int)$this->request->data['sl'];
        $this->set(compact('quizDetails','sl'));
    }

    public function index() {
        $userId = $this->Auth->user('id');
        // pr($userId);
        // exit;
        $this->set('quizTypes', $this->quizTypes());
        $this->set('quizSharedType', $this->quizSharedType());

        $userPermissions = $this->userPermissions();
        // pr($userPermissions);
        // exit;
        $this->set(compact('userPermissions'));

        if ($this->request->is('post')) {
            $data = $this->request->data;
            if (isset($data['status'])) {
                $filter = $data['status'];
                $this->Session->write('Quizzes.status', $filter);
            }
        } else {
            if (!$this->Session->check('Quizzes.status')) {
                $filter = 1;
                $this->Session->write('Quizzes.status', $filter);
            } else {
                $filter = $this->Session->read('Quizzes.status');
            }
        }

        $orders = array();

        switch ($filter) {
            case 'all':
                $orders = array('Quizzes.status DESC');
                break;
            case 'shared':
                $options[] = array(
                    'Quizzes.shared' => 1,
                    'Quizzes.is_approve' => 1
                );
                break;
            case 'pending':
                $options[] = array(
                    'Quizzes.shared' => 1,
                    'Quizzes.is_approve' => NULL
                );
                break;
            case 'decline':
                $options[] = array(
                    'Quizzes.shared' => 1,
                    'Quizzes.is_approve' => 2
                );
                break;
            case 'private':
                $options[] = array(
                    'Quizzes.shared' => NULL
                );
                break;
            default:
                $options['Quizzes.status'] = $filter;
                break;
        }

        //$options['Quizzes.user_id'] = $userId;
        $options['Quizzes.user_id'] = $userId;
        $options[] = 'Quizzes.parent_quiz_id IS NULL';

        // pr($options);
        // exit;

        $quizzes = $this->Quizzes->find()
        ->where($options)
        ->contain([
            'Questions' => function($q) {
                $q->select([
                     'Questions.quiz_id',
                     'total' => $q->func()->count('Questions.quiz_id')
                ])
                ->group(['Questions.quiz_id']);
                return $q;
            }
        ])
        ->order($orders)->toArray();
// pr($quizzes);
// exit;
        if (empty($quizzes)) {
            $quiz_created = $this->Quizzes->find('all')
            ->where(['Quizzes.user_id' => $userId])
            ->contain([])
            ->select(['id'])
            ->first();
            $quiz_created = empty($quiz_created) ? false : true;
        } else {
            $quiz_created = true;
        }
        $data = array(
            'quizzes' => $quizzes,
        );

        $this->set(compact('data', 'filter', 'quiz_created'));
    }

    public function edit($quizId, $initial = '') {

        $this->accountStatus();
        // Check permission
        $userId = $this->Auth->user('id');

        $role = $this->Auth->user('account_level');
        if ($role == '51') {
            $conditions = ['id' => $quizId];
        } else {
            $conditions = ['id' => $quizId, 'user_id' => $userId];
        }
        
        if ($this->request->is(['patch', 'post', 'put'])) {
            $quiz = $this->Quizzes->find()->where($conditions)->contain([])->first();
            if (!empty($this->request->data['subjects'])) {
                $this->request->data['subjects'] = json_encode($this->request->data['subjects'], true);
            }

            if (!empty($this->request->data['classes'])) {
                $this->request->data['classes'] = json_encode($this->request->data['classes'], true);
            }
            $quiz = $this->Quizzes->patchEntity($quiz, $this->request->data);
            if ($this->Quizzes->save($quiz)) {
                $this->Flash->success(__('QUIZ_SAVED'));
                if (empty($quiz->parent_quiz_id)) {
                    return $this->redirect(array('action' => 'index'));
                } else {
                    return $this->redirect(array('controller' => 'quizzes', 'action' => 'shared', 'prefix' => 'admin'));
                }
            } else {
                $this->Flash->error(__('QUIZ_SAVE_FAILED'));
            }
        }

        $query = $this->Quizzes->find()
            ->where($conditions)
            ->contain([
                'Questions' => function($q) {
                    $q->contain([
                        'Choices' => function($q2) {
                            return $q2->order(['Choices.weight DESC', 'Choices.id ASC']);
                        },
                        'QuestionTypes' => function($q3) {
                            return $q3->select(['QuestionTypes.template_name', 'QuestionTypes.id', 'QuestionTypes.multiple_choices']);
                        }
                    ])
                    ->order(['Questions.weight DESC', 'Questions.id ASC']);
                    return $q;
                } 
            ]);

        $query->hydrate(false);
        $data = $query->first();

        if (empty($data))
            throw new NotFoundException;

        $query = $this->Quizzes->Questions->QuestionTypes->find()
            ->select(['name', 'template_name', 'multiple_choices', 'id', 'type']);
        $query->hydrate(false);
        $data['QuestionTypes'] = $query->toArray();
        
        if (!empty($initial)) {
            $this->set(compact('initial'));
        } 

        if (empty($data['questions'])) {
            $this->set('no_question', true);
        }

        $lang_strings['empty_question'] = __('NO_EMPTY_QUESTION');
        $lang_strings['same_choice'] = __('EMPTY_OR_SAME_NOT_ALLOWED');
        $lang_strings['single_greater'] = __('ONE_CHOICE_GREATER_THAN_ZERO');
        $lang_strings['correct_answer'] = __('ENTER_CORRECT_ANSWERS');
        $lang_strings['point_greater'] = __('ONE_CHOICE_GREATER_THAN_ZERO');
        $lang_strings['two_greater'] = __('TWO_CHOICES_GREATER_THAN_ZERO');
        $lang_strings['insert_another'] = __('CHOOSE_ANOTHER_CHOOSE');
        $lang_strings['youtube_url'] = __('ENTER_YOUTUBE_URL');
        $lang_strings['image_url'] = __('ENTER_IMAGE_URL');
        $lang_strings['header_q_title'] = __('ENTER_HEADER');
        $lang_strings['other_q_title'] = __('ENTER_QUESTION');

        $lang_strings['youtube_exp_text'] = __('VIDEO_EXPLANATION');
        $lang_strings['image_exp_text'] = __('IMAGE_EXPLANATION_TEXT');
        $lang_strings['other_exp_text'] = __('EXPLANATION_TEXT');
        $lang_strings['empty_header'] = __('ENTER_HEADER_TEXT');
        $lang_strings['no_choice_1'] = __('MINIMUM');
        $lang_strings['no_choice_2'] = __('CHOICES_REQUIRED');
        $lang_strings['drag_drop'] = __('DRAG_DROP');
        $lang_strings['upload'] = __('CHOOSE_FILE');

        // Load available classes (created by admin)
        $this->loadModel('Subjects');

        $classOptions = $this->Subjects->find('list', ['keyField' => 'id', 'valueField' => 'title'])
            ->where(['Subjects.isactive' => 1, 'Subjects.is_del IS NULL', 'Subjects.type' => 1])
            ->toArray();

        $subject_cond[] = array(
            'Subjects.isactive' => 1,
            'Subjects.is_del IS NULL',
            'Subjects.type IS NULL'
        );
        $c_user = $this->Session->read('Auth.User');
        // pr($c_user);
        // exit;
        if (!empty($c_user['subjects']) && ($role != '51')) {
            $selectedSubjects = json_decode($c_user['subjects'], true);
            $subject_cond[] = array('Subjects.id IN' => $selectedSubjects);
        }

        $subjectOptions = $this->Subjects->find('list', ['keyField' => 'id', 'valueField' => 'title'])->where($subject_cond)->toArray();

        if (!empty($subjectOptions)) {
            $subjectOptions = array(0 => __('ALL_SUBJECT')) + $subjectOptions;
        }
        
        if (!empty($classOptions)) {
            $classOptions = array(0 => __('ALL_CLASS')) + $classOptions;
        }

        $this->set('data', $data);
        $this->set(compact('lang_strings', 'classOptions', 'subjectOptions'));
    }

    public function add() {
        
        $this->accountStatus(); 

        $userId = $this->Auth->user('id');
        // if (!$this->Users->canCreateQuiz($userId))
        //     return $this->redirect(array('action' => 'index'));  

        $quiz = $this->Quizzes->newEntity();
        $quiz = $this->Quizzes->patchEntity($quiz, ['name' => __('NAME_QUIZ'), 'user_id' => $userId]);
       
        if (!$this->Quizzes->save($quiz)) {
            $this->Flash->error(__('Quiz Save Failed'));    
            return $this->redirect($this->referer());
        }
        // pr($quiz);
        // exit;
        if (!$this->Quizzes->updateAll(
            ['random_id' => $quiz->id . $this->randText(2, true)], 
            ['id' => $quiz->id])
        ) {
            $this->Quizzes->delete($quiz->id);
            $this->Flash->error(__('Quiz Save Failed'));
            return $this->redirect($this->referer());
        }
        
        //save statistics data
        $statisticsTable = TableRegistry::get('Statistics');
        $statistic = $statisticsTable->newEntity();
        $statistic->user_id = $userId;
        $statistic->type = 'quiz_create';
        $statistic->created = date("Y-m-d H:i:s");
        $statisticsTable->save($statistic);

        // pr($quiz);
        // pr($statisticsTable);
        // exit;

        // check if free user creating first quiz send email notification to admin
        $user = $this->Auth->user();
        if (empty($user['account_level']) || ($user['account_level'] == 22)) { // if this is the free user
            // check if its first quiz
            $quiz_count = $this->Quizzes->find()->where(['Quizzes.user_id' => $userId])->count();
            // pr($quiz_count);
            // exit;
            // $quiz_count = $query->select(['count' => $query->func()->count('*')])->toArray();
            // pr($quiz_count);
            // exit;

            if ($quiz_count == 1) {
                $admin_email = $this->Email->sendMail(Configure::read('AdminEmail'), __('[Verkkotesti] First quiz created'), $user, 'first_quiz_create');
                // pr($admin_email);
                // exit;
            } 
        }

        return $this->redirect(array(
                    'controller' => 'quizzes',
                    'action' => 'edit',
                    $quiz->id,
                    'initial'
        ));
    }

    public function present($id) {
        $quiz = $this->Quizzes->find()
        ->where(['Quizzes.id' => $id])
        ->contain([
            'Questions' => function($q) {
                $q->select([
                     'Questions.quiz_id',
                     'total' => $q->func()->count('Questions.quiz_id')
                ])
                ->group(['Questions.quiz_id']);
                return $q;
            }
        ])
        ->first();

        // pr($quiz);
        // exit;

        if (empty($quiz))
            throw new NotFoundException(__('INVALID_QUIZ!'));
        $this->set(compact('quiz', 'id'));
    }

    // ajax_preview
    public function ajaxPreview() {
        $this->viewBuilder()->layout('ajax');
        
        if (empty($this->request->data['present'])) {
            $conditions = [
                'Quizzes.id' => $this->request->data['quiz_id'],
                'Quizzes.parent_quiz_id IS NOT NULL',
            ];
        } else {
            $conditions = [
                'Quizzes.random_id' => $this->request->data['random_id'],
                'Quizzes.user_id' => $this->Auth->user('id')
            ];
            $this->set('class_preview', 1);
        }
        $data = $this->Quizzes->find('all', [
            'conditions' => $conditions,
            'contain' => [
                'Questions' => function($q) {
                    $q->contain([
                        'Choices' => function($q) {                        
                        return $q->order(['Choices.weight DESC', 'Choices.id ASC']);
                        },
                        'QuestionTypes' => array(
                            'fields' => array('QuestionTypes.template_name', 'QuestionTypes.id', 'QuestionTypes.multiple_choices')
                        )
                    ])
                    ->order(['Questions.weight DESC', 'Questions.id ASC']);
                    return $q;
                },
                'Users'
            ]
        ])->first();

        // pr($data);
        // exit;

        if (empty($data)) {
            $this->set('isError', true);
        } else {
            // $lang_strings[0] = __('CONNECTION_LOST');
            // $lang_strings[1] = __('ALL_QUESTIONS_ANSWERED');
            // $lang_strings[2] = __('QUESTIONS');
            // $lang_strings[3] = __('UNANSWERED');
            // $lang_strings[4] = __('WANT_TURN_IN_QUIZ');
            // $lang_strings[5] = __('FIRST_NAME_REQUIRED');
            // $lang_strings[6] = __('LAST_NAME_REQUIRED');
            // $lang_strings[7] = __('CLASS_REQUIRED');
            // $lang_strings['last_name_invalid'] = __('INVALID_LAST_NAME');
            // $lang_strings['first_name_invalid'] = __('INVALID_FIRST_NAME');
            // $lang_strings['class_invalid'] = __('INVALID_CLASS');
            // $lang_strings['right_click_disabled'] = __('RIGHT_CLICK_DISABLED');
            // $lang_strings['browser_switch'] = __('CANT_SWITCH_TAB');
            // $lang_strings['leave_quiz'] = __('SURELY_LEAVE_QUIZ');

            $this->set(compact('lang_strings', 'data'));
            //$this->set('quizRandomId', $this->request->data['random_id']);

        }
    }

    public function live($quizRandomId) {

        // start session for examination
        if (!$this->Session->check('started')) {
            $this->Session->destroy();
            $randomString = $this->randText(10);
            $this->Session->write('started', $randomString);
            $this->Session->write('random_id', $quizRandomId); // Write random_id on session to keey track of online students
            $this->redirect(array('controller' => 'Quizzes', 'action' => 'live', $quizRandomId, '?' => array('runningFor' => $randomString)));
        }

        if (!empty($this->request->query['runningFor']) && ($this->request->query['runningFor'] == $this->Session->read('started'))) {
            // Do nothing
            //$this->Session->delete($this->request->query['runningFor']);
        } else {
            // remove session and start new
            $this->Session->delete('started');
            $this->Session->delete('student_id');
            $this->Session->delete('random_id');
            $this->Session->destroy();
            $randomString = $this->randText(10);
            $this->Session->write('started', $randomString);
            $this->Session->write('random_id', $quizRandomId);
            $this->redirect(array('controller' => 'Quizzes', 'action' => 'live', $quizRandomId, '?' => array('runningFor' => $randomString)));
        }

        $data = $this->Quizzes->find('all')
        ->where([
            'Quizzes.random_id' => $quizRandomId,
            'Quizzes.status' => 1
        ])
        ->contain([
            'Questions' => function($q) {
                return $q->order(['Questions.weight DESC', 'Questions.id ASC'])
                ->contain([
                    'Choices' => function($q) {
                        return $q->order(['Choices.weight DESC', 'Choices.id ASC']);
                    },
                    'QuestionTypes' => function($q) {
                        return $q->select(['QuestionTypes.template_name', 'QuestionTypes.id', 'QuestionTypes.multiple_choices']);
                    }
                ]);
            },
            'Users'
        ])->first();

        // pr($data);
        // exit;

        if (empty($data)) {
            $this->set('title_for_layout', __('Closed'));
            $this->render('not_found');
        } elseif (empty($data->questions)) {
            $this->set('name', $data->name);
            $this->render('no_question');
        } else {
            $this->Session->write('user_language', $data->user->language);
            // pr($this->Session->read('user_language'));
            // exit;
            I18n::locale($data->user->language);
            // check user access level
            if ((($data->user->account_level == 0) || 
                (($data->user->account_level == 1) && (strtotime($data->user->expired) < time()))) 
                && ($data->student_count >= 40)) {
                $this->Flash->error(__('ONLY_FOURTY_STUDENTS'));
                return $this->redirect(array('controller' => 'quizzes', 'action' => 'no_permission'));
            }

            // Check session if student page reloaded
            if ($this->Session->check('student_id')) {
                $student = $this->Quizzes->Students->find('all')
                ->where(['Students.id' => (int) $this->Session->read('student_id')])
                ->contain(['Answers', 'Rankings'])
                ->first();
                // pr($student);
                // exit;
                // $this->request->data = $student;
                $this->set(compact('student'));
            }

            $lang_strings[0] = __('CONNECTION_LOST');
            $lang_strings[1] = __('ALL_QUESTIONS_ANSWERED');
            $lang_strings[2] = __('QUESTIONS');
            $lang_strings[3] = __('UNANSWERED');
            $lang_strings[4] = __('WANT_TURN_IN_QUIZ');
            $lang_strings[5] = __('FIRST_NAME_REQUIRED');
            $lang_strings[6] = __('LAST_NAME_REQUIRED');
            $lang_strings[7] = __('CLASS_REQUIRED');
            $lang_strings['last_name_invalid'] = __('INVALID_LAST_NAME');
            $lang_strings['first_name_invalid'] = __('INVALID_FIRST_NAME');
            $lang_strings['class_invalid'] = __('INVALID_CLASS');
            $lang_strings['right_click_disabled'] = __('RIGHT_CLICK_DISABLED');
            $lang_strings['browser_switch'] = __('CANT_SWITCH_TAB');
            $lang_strings['leave_quiz'] = __('SURELY_LEAVE_QUIZ');

            $lang_strings['disabled_submit'] = __('ANSWERS_SAVING');
            $lang_strings['enabled_submit'] = __('WANT_TURN_IN_QUIZ');

            $this->set('data', $data);
            $this->set(compact('lang_strings'));
            $this->set(compact('quizRandomId'));

        }
    }

    public function finish() {
        
    }

    public function check_diff_multi($array1, $array2){
        $result = array();
        foreach($array1 as $key => $val) {
             if(isset($array2[$key])){
               if(is_array($val) && $array2[$key]){
                   $result[$key] = $this->check_diff_multi($val, $array2[$key]);
               }
           } else {
               $result[$key] = $val;
           }
        }

        return $result;
    }

    public function table($quizId) {
        ini_set('max_execution_time', 300); // 5 mins
        ini_set('memory_limit', '-1');
        if (empty($quizId)) {
            return $this->redirect('/');
        }
        $this->accountStatus();

        // authenticate or not
        $checkPermission = $this->Quizzes->checkPermission($quizId, $this->Auth->user('id'), ['Students']);
        // pr($checkPermission);
        // exit;

        if (empty($checkPermission)) {
            throw new ForbiddenException;
        }
        

        if ($this->request->is('post')) {
            $data = $this->request->data;
            // pr($data);
            // exit;
            if (isset($data['Filter'])) {
                $filter = array('class' => !empty($data['Filter']['class']) ? $data['Filter']['class'] : 'all', 'daterange' => $data['Filter']['daterange']);
                $this->Session->write('Filter', $filter);
            }
        } else {
            if (!$this->Session->check('Filter')) {
                $filter = array('class' => 'all', 'daterange' => 'all');
                $this->Session->write('Filter', $filter);
            } else {
                $filter = $this->Session->read('Filter');
            }
        }

        // pr($filter);
        // exit;

        $quizDetails = $this->Quizzes->quizDetails($quizId, $filter);

        // pr($quizDetails);
        // exit;

        // get student id's for ajax auto checking
        $studentIds = array();
        foreach ($quizDetails->students as $key1 => $value1) {
            $diff = strtotime(date('Y-m-d H:i:s')) - strtotime($value1->submitted);
            if (empty($value1->status) && ($diff < 3600)) {
                $informations = array();
                $informations[] = array('fname' => $value1->fname, 'lname' => $value1->lname, 'class' => $value1->class);
                $answers = array();
                foreach ($value1->answers as $key2 => $value2) {
                    $answers[$value2->question_id] = $value2->text;
                }
                $informations[] = $answers;
                $studentIds[$value1->id] = $informations;
            }
        }

        // pr($studentIds);
        // exit;
    
        
        // find online students
        $onlineStds = $this->checkOnlineStudent($quizDetails->random_id);
        $this->set(compact('onlineStds'));

        $studentIds = json_encode(array('studentIds' => $studentIds, 'onlineStds' => $onlineStds));

        // pr($studentIds);
        // exit;

        // get student classes
        $classes = Hash::combine($checkPermission->students, '{n}.class', '{n}.class');
        //pr($classes);
        $classes = array_filter($classes);
        sort($classes);
        $new_class = array();
        foreach ($classes as $key => $cls) {
            $new_class[$cls] = $cls;
        }
        
        $classes = Hash::merge(array('all' => __('ALL_CLASS')), $new_class);

        $lang_strings['remove_question'] = __('SURELY_REMOVE');
        $lang_strings['with_points'] = __('ANSWER_WITH_POINTS');
        $lang_strings['positive_number'] = __('GIVE_POSITIVE_NUMBER');
        $lang_strings['update_require'] = __('SCORE_NOT_UPDATED');
        $lang_strings['more_point_1'] = __('POINTS_NOT_ALLOWED');
        $lang_strings['more_point_2'] = __('VALUE');
        $lang_strings['online_warning'] = __('STUDENT_ONLINE_AND_GIVING_TEST');

        $this->set(compact('quizDetails', 'classes', 'filter', 'studentIds', 'quizId', 'lang_strings'));
    }

    // Student online status
    public function checkOnlineStudent($random_id) {
        $onlineStds = array();
        //$time = time()+14400-60; // (14400 == 4 huors and 10 mins = 600)
        $time = time()+1440-60; // (14400 == 4 huors and 10 mins = 600)
        $conn = ConnectionManager::get('default');
        $stmt = $conn->execute('SELECT data FROM sessions WHERE expires > '. $time .' AND data LIKE "%started%" AND data LIKE "%' . $random_id . '%"');
        $sessions = $stmt ->fetchAll('assoc');
        foreach ($sessions as $session) {
            $tmp = explode('student_id|i:', $session['data']);
            if (!empty($tmp[1])) {
                $tmp = explode(';', $tmp[1]);
                if (!empty($tmp[0]) && is_numeric($tmp[0])) {
                    $onlineStds[] = $tmp[0];
                }
            } 
        }
        return $onlineStds;
    }

    // Ajax latest
    public function ajaxLatest() {
        $this->autoRender = false;
        if (!$this->Session->check('Filter')) {
            $filter = array('class' => 'all', 'daterange' => 'all');
            $this->Session->write('Filter', $filter);
        } else {
            $filter = $this->Session->read('Filter');
        }
        $quizDetails = $this->Quizzes->checkNewUpdate((int) $this->request->data['quizId'], $filter);
        // pr($quizDetails);
        // exit;
        $studentIds = array();
        foreach ($quizDetails->students as $key1 => $value1) {
            $informations = array();
            $informations[] = array('fname' => $value1->fname, 'lname' => $value1->lname, 'class' => $value1->class);
            $answers = array();
            foreach ($value1->answers as $key2 => $value2) {
                $answers[$value2->question_id] = $value2->text;
            }
            $informations[] = $answers;
            $studentIds[$value1->id] = $informations;
        }

        $onlineStds = $this->checkOnlineStudent($quizDetails['Quiz']['random_id']);
        // $oldOnlineStds = json_decode($this->request->data['onlineStds'], true);
        // $offlineStds = array_diff($oldOnlineStds, $onlineStds);

        echo json_encode(array('studentIds' => $studentIds, 'onlineStds' => $onlineStds));
    }

    public function ajaxUpdate() {
        $this->autoRender = false;
        // authenticate or not
        $checkPermission = $this->Quizzes->checkPermission((int)$this->request->data['quizId'], $this->Auth->user('id'));
        if (empty($checkPermission)) {
            throw new ForbiddenException;
        }

        // $old_data = json_decode($this->request->data['old_data'], true);
        // $new_data = json_decode($this->request->data['new_data'], true);
        $old_data = !empty($this->request->data['old_data']) ? $this->request->data['old_data'] : array();
        $new_data = $this->request->data['new_data'];

        $modified_ids = array();

        foreach ($new_data as $id1 => $dataset) {
            if (array_key_exists($id1, $old_data)) { // if old student
                $arraysAreEqual = ($dataset == $old_data[$id1]);
                if (empty($arraysAreEqual)) {
                    $modified_ids[] = $id1;
                }
            } else {
                $modified_ids[] = $id1; // new student found
            }
        }

        echo json_encode($modified_ids);
        exit;
    }

    /*
    * active / deactive quiz
    */
    public function changeStatus() {
        $this->accountStatus(); 

        $this->autoRender = false;
        $data = $this->request->data;
        // pr($data);
        // exit;
        // Check permission
        $userId = $this->Auth->user('id');
        
        $quiz = $this->Quizzes->find()->where(['Quizzes.id' => $data['quiz_id'], 'Quizzes.user_id' => $userId])->first();

        // pr($result);
        // exit;

        if (empty($quiz)) {
            $response['result'] = 0;
            $response['message'] = __('You are not authorized to do this action');
            echo json_encode($response);
            exit;
        }

        $quiz->status = empty($data['status']) ? 1 : 0;

        if ($this->Quizzes->save($quiz)) {
             if ($this->Session->check('Quizzes.status')) {
                $filter = $this->Session->read('Quizzes.status');
            } else {
                $filter = 1;
            }
            $response['result'] = 1;
            $response['filter'] = $filter;
            $response['message'] = __('Operation Successfuly Done');
            echo json_encode($response);
        }
    }

    public function single() {
        $this->autoRender = false;
        $quizId = $this->request->data['quiz_id'];


// select * from bdg left join res on bdg.bid = res.bid ;
// select * from (answers as Answer left join questions as Question on Answer.question_id = Question.id) 
//     left join quizzes as Quiz on Question.quiz_id = Quiz.id;

// select * from (bdg left join res on bdg.bid = res.bid) 
//     left join dom on res.rid = dom.rid where dom.rid is NULL;
// select * from (bdg left join res on bdg.bid = res.bid) 
//     left join dom on res.rid = dom.rid where res.rid is NULL;
// select * from (bdg left join res on bdg.bid = res.bid) 
//     left join dom on res.rid = dom.rid 
//     where dom.rid is NULL and res.rid is not NULL;


        // $this->Quizzes->virtualFields['no_of_answers'] = 'select count(*) from (answers as Answers left join questions as Questions on Answers.question_id = Questions.id) left join quizzes as Quizzes on Questions.quiz_id = Quizzes.id where Quizzes.id = {$quizId}';

        // $this->Quizzes->virtualFields = array(
        //     'question_count' => 'SELECT count(*) FROM questions as Questions WHERE Questions.quiz_id = Quizzes.id'
        // );

        // $quizzes = $this->Quizzes->find()
        // ->where($options)
        // ->contain([
        //     'Questions' => function($q) {
        //         $q->select([
        //              'Questions.quiz_id',
        //              'total' => $q->func()->count('Questions.quiz_id')
        //         ])
        //         ->group(['Questions.quiz_id']);
        //         return $q;
        //     }
        // ])
        // ->order($orders)->toArray();


        // $quizInfo = $this->Quizzes->find('all')
        // ->where(['Quizzes.id' => $quizId])
        // ->contain([
        //     'Questions' => function($q) {
        //         $q->select([
        //             'Questions.id',
        //             'Questions.quiz_id',
        //             'no_of_questions' => $q->func()->count('Questions.quiz_id')
        //         ])
        //         ->group(['Questions.quiz_id'])
        //         ->contain([
        //             'Answers' => function ($q2) {
        //                $q2->select([
        //                      'Answers.question_id',
        //                      'no_of_answers' => $q2->func()->count('Answers.question_id')
        //                 ])
        //                 ->group(['Answers.question_id']);
        //                 return $q2;
        //             }
        //         ]);
        //         return $q;
        //     }
        // ])
        // ->first();


        $quizInfo = $this->Quizzes->find()
        ->where(['Quizzes.id' => $quizId, 'Quizzes.user_id' => $this->Auth->user('id')])
        //->where(['Quizzes.id' => $quizId])
        ->contain([
            //'Questions' => ['Answers']
            'Questions' => function ($q) {
                return $q->autoFields(false)
                         ->select(['id', 'quiz_id'])
                         ->contain([
                                'Answers' => function ($q) {
                                    return $q->autoFields(false)
                                             ->select(['id', 'question_id']);
                                }
                            ]);
            }
        ])
        ->first();

        if (empty($quizInfo)) {
            $this->Flash->error(__('Invalid try, please try again later!'));
            $response['success'] = 0;
        } else {
            // response data
            $response['success'] = 1;
            $response['id'] = $quizInfo->id;
            $response['quiz_name'] = $quizInfo->name;
            $response['no_of_questions'] = count($quizInfo['questions']);
            $response['no_of_students'] = $quizInfo->student_count;
            $answers = 0;
            foreach ($quizInfo['questions'] as $key => $value) {
                if (!empty($value['answers'])) {
                   $answers = $answers + count($value['answers']);
                }
            }
            $response['no_of_answers'] = $answers;
        }

        echo json_encode($response);
        exit;
    }

    public function quizDelete($quizId) {
        $this->accountStatus(); 
        // authenticate or not
        $checkPermission = $this->Quizzes->checkPermission($quizId, $this->Auth->user('id'));
        //$checkPermission = $this->Quizzes->checkPermission($quizId, 5);
     
        if (empty($checkPermission)) {
            $this->Flash->error(__('Invalid try, please try again later!'));
            return $this->redirect($this->referer());
        }

        $questionIds = $this->Quizzes->Questions->find('list', ['keyField' => 'id', 'valueField' => 'id'])->where(['Questions.quiz_id' => $quizId])->toArray();

        // pr($questionIds);
        // exit;

        foreach ($questionIds as $key => $id) {
            # code...
            $this->Quizzes->Questions->Choices->deleteAll(array('question_id' => $id));
            $this->Quizzes->Questions->Answers->deleteAll(array('question_id' => $id));
        }
        $this->Quizzes->Students->deleteAll(array('quiz_id' => $quizId));
        $this->Quizzes->Rankings->deleteAll(array('quiz_id' => $quizId));
        $this->Quizzes->Questions->deleteAll(array('quiz_id' => $quizId));
        if ($this->Quizzes->deleteAll(['id' => $quizId])) {
            $this->Flash->success(__('QUIZ_DELETED'));
        }
        return $this->redirect($this->referer());
    }

    public function no_permission() {
        $this->set('title_for_layout', __('NO_PERMISSION'));
    }

    // print quiz answer
    public function ajaxPrintAnswer() {
        $this->accountStatus();
        $this->viewBuilder()->layout('ajax');
        $quizId = $this->request->data['quizId'];
        // authenticate or not
        $checkPermission = $this->Quizzes->checkPermission($quizId, $this->Auth->user('id'));
        if (empty($checkPermission)) {
            throw new ForbiddenException;
        }
        $filter = $this->Session->read('Filter');
        $quizDetails = $this->Quizzes->quizDetails($quizId, $filter);
        $this->set(compact('quizDetails', 'quizId'));
    }

    // Duplicate quiz
    // Only quiz name, questions and choices will be duplicated
    public function duplicate() {
        $this->autoRender = false;

        $response['result'] = 0;

        $quizId = $this->request->data['quiz_id'];
        $user_id = $this->Auth->user('id');

        $quiz = $this->Quizzes->find()
        ->where(['Quizzes.id' => $quizId, 'Quizzes.user_id' => $user_id])
        ->contain(['Questions'  => function($q) {
            return $q->contain(['Choices']);
        }])
        ->first();

        if (!empty($quiz)) {
            $new_quiz = array();
            $new_quiz['name'] = __('COPY_OF') . ' ' . $quiz->name;
            $new_quiz['user_id'] = $user_id;
            $new_quiz['description'] = $quiz->description;
            $new_quiz['status'] = 1;
            $new_quiz['show_result'] = $quiz->show_result;
            $new_quiz['anonymous'] = $quiz->anonymous;
            $new_quiz['subjects'] = $quiz->subjects;
            $new_quiz['classes'] = $quiz->classes;

            foreach ($quiz->questions as $key1 => $question) {
                $new_quiz['questions'][$key1]['question_type_id'] = $question->question_type_id;
                $new_quiz['questions'][$key1]['text'] = $question->text;
                $new_quiz['questions'][$key1]['explanation'] = $question->explanation;
                $new_quiz['questions'][$key1]['weight'] = $question->weight;
                $new_quiz['questions'][$key1]['max_allowed'] = $question->max_allowed;
                $new_quiz['questions'][$key1]['case_sensitive'] = $question->case_sensitive;
                foreach ($question->choices as $key2 => $choice) {
                    $new_quiz['questions'][$key1]['choices'][$key2]['text'] = $choice->text;
                    $new_quiz['questions'][$key1]['choices'][$key2]['points'] = $choice->points;
                    $new_quiz['questions'][$key1]['choices'][$key2]['weight'] = $choice->weight;
                }
            }

            $new_quiz = $this->Quizzes->newEntity($new_quiz, [
                'associated' => [
                    'Questions' => ['associated' => ['Choices']]
                ]
            ]);

            if ($this->Quizzes->save($new_quiz)) {
                if ($this->Quizzes->updateAll(
                        ['random_id' => $new_quiz->id . $this->randText(2, true)], 
                        ['id' => $new_quiz->id]
                    )
                ) {
                    $response['message'] = __('QUIZ_DUPLICATED');
                    $response['result'] = 1;
                    $response['id'] = $new_quiz->id;
                } else {
                    $this->Quizzes->delete($new_quiz->id);
                    $response['message'] = __('SOMETHING_WENT_WRONG');
                }
            } else {
                $response['message'] = __('SOMETHING_WENT_WRONG');
            }
        } else {
            $response['message'] = __('INVALID_QUIZ');
        }
        echo json_encode($response);
        exit;

    }

    // Method for sharing quiz
    public function share($quiz_id, $shared = null) {
        $this->autoRender = false;
        $this->accountStatus(); 
        // authenticate or not
        $quiz = $this->Quizzes->getAQuizRel($quiz_id, $this->Auth->user('id'));

        if (empty($quiz) || empty($quiz->questions[0]->total)) {
            $this->Flash->error(__('INVALID_QUIZ'));
            return $this->redirect(array('controller' => 'quizzes', 'action' => 'index'));
        }   
        // pr($quiz);
        // exit;
        if (empty($shared) && ($quiz->shared == 1) && empty($quiz->is_approve)) {
            $message = __('You had already shared this quiz. Please wait for admin approval!');
        } else if (empty($shared) && ($quiz->shared == 1) && ($quiz->is_approve == 1)) {
            $message = __('Your quiz already shared and approved!');
        } else if (empty($shared) && ($quiz->shared == 1) && ($quiz->is_approve == 2)) {
            $message = __('Sorry, your sharing has been declined by admin.');
        } else {
            // Proceed to next
        }

        if (!empty($message)) {
            $this->Flash->error($message);
            return $this->redirect(array('controller' => 'quiz', 'action' => 'index'));
        }
        $shared_copy = ($quiz->is_approve == 1) ? true : false;
        // Update shared field
        $quiz->shared = empty($shared) ? 1 : NULL;
        $quiz->is_approve = NULL;
        if ($this->Quizzes->save($quiz)) {
            // Send email to the admin
            if (empty($shared)) {
                $subject = __('QUIZ_SHARED_EMAIL');
                $template = 'quiz_shared';
                $message = __('QUIZ_SHARED_HOLD_APPROVAL');
                $admin_email = $this->Email->sendMail(Configure::read('AdminEmail'), $subject, $quiz, $template);
            } else {
                // If approved delete the shared copy
                if ($shared_copy) {
                    $shared = $this->Quizzes->sharedQuizCopy($quiz_id);
                    if ($this->Quizzes->delete($shared)) {
                        foreach ($shared->questions as $key => $question) {
                            $this->Quizzes->Questions->Choices->deleteAll(array('question_id' => $question->id));
                        }
                        $this->Quizzes->Questions->deleteAll(array('quiz_id' => $shared->id));
                    }
                }
                $subject = __('Sharing removed!');
                $template = 'remove_sharing';
                $message = __('SHARING_REMOVE_SUCCESS');
            }
            $this->Flash->success($message);
        } else {
            $this->Flash->error(__('SOMETHING_WENT_WRONG'));
        }
        return $this->redirect($this->referer());
    }

    public function bank() {
        $this->set('title_for_layout', __('PUBLIC_QUIZZES'));
        $this->hasQuizBankAccess();
        $this->loadModel('Subjects');
        $subjectOptions = $this->Subjects->find('list', ['keyField' => 'id', 'valueField' => 'title'])->where([
            'Subjects.type IS NULL',
            'Subjects.isactive' => 1,
            'Subjects.is_del IS NULL'
        ])->toArray();

        $classOptions = $this->Subjects->find('list', ['keyField' => 'id', 'valueField' => 'title'])->where([
            'Subjects.type' => 1,
            'Subjects.isactive' => 1,
            'Subjects.is_del IS NULL'
        ])->toArray();

        // Selected subjects
        $selectedSubjects = $this->Session->read('Auth.User.subjects');
        $selectedSubjects = !empty($selectedSubjects) ? json_decode($selectedSubjects, true) : [];

        foreach ($selectedSubjects as $term) {
            $term = trim($term);
            if (!empty($term)) {
                $conditions['OR'][] = array('Quizzes.subjects LIKE' => '%' . '"' . $term . '"' . '%');
            }
        }
        $user_id = $this->Auth->user('id');
        // Get pagination
        $conditions[] = array(
            'Quizzes.parent_quiz_id IS NOT NULL',
            'Downloads.id IS NULL'
        );

        $quizzes = $this->paginate($this->Quizzes->find()->where($conditions)
            ->join([
                'Downloads' => [
                    'table' => 'downloads',
                    'type' => 'LEFT',
                    'conditions' => [
                        'Downloads.quiz_id = Quizzes.id',
                        'Downloads.user_id' => $user_id
                    ]
                ]
            ])
        );
        if (!empty($subjectOptions)) {
            $subjectOptions = array(0 => __('ALL_SUBJECT')) + $subjectOptions;
        }
        
        if (!empty($classOptions)) {
            $classOptions = array(0 => __('ALL_CLASS')) + $classOptions;
        }

        $lang_strings['check_select'] = __('ONE_QUIZ_MANDATORY');
        $lang_strings['import_success'] = __('QUIZ_IMPORTED');

        $this->set(compact('subjectOptions', 'classOptions', 'selectedSubjects', 'quizzes', 'lang_strings'));
    }

    // Method for quiz bank pagination
    public function quiz_bank_pagination() {
        $this->temp_common();
        $this->render('/Elements/Quiz/quiz_bank_pagination');
    }

    // Test method 
    public function testLink() {
        $this->viewBuilder()->layout('ajax');
        $this->hasQuizBankAccess();
        $this->loadModel('Subjects');

        $subjectOptions = $this->Subjects->find('list', ['keyField' => 'id', 'valueField' => 'title'])->where([
            'Subjects.type IS NULL',
            'Subjects.isactive' => 1,
            'Subjects.is_del IS NULL'
        ])->toArray();

        // pr($subjectOptions);
        // exit;
        $classOptions = $this->Subjects->find('list', ['keyField' => 'id', 'valueField' => 'title'])->where([
            'Subjects.type' => 1,
            'Subjects.isactive' => 1,
            'Subjects.is_del IS NULL'
        ])->toArray();

        if (empty($this->request->data['subject_list'])) {
            $selectedSubjects = $this->Session->read('Auth.User.subjects');
            if (!empty($selectedSubjects)) {
                $this->request->data['subject_list'] = json_decode($selectedSubjects, true);
            } 
        }

        if (empty($this->request->data['page_no'])) {
            $this->request->query['page'] = 1;
        } else {
            $this->request->query['page'] = $this->request->data['page_no'];
        }

        // pr($this->request->data);
        // exit;

        if (!empty($this->request->data['subject_list'])) {
            foreach ($this->request->data['subject_list'] as $term) {
                $term = trim($term);
                if (!empty($term)) {
                    $conditions[0]['OR'][] = array('Quizzes.subjects LIKE' => '%' . '"' . $term . '"' . '%');
                }
            }
        }

        if (!empty($this->request->data['class_list'])) {
            foreach ($this->request->data['class_list'] as $term) {
                $term = trim($term);
                if (!empty($term)) {
                    $conditions[1]['OR'][] = array('Quizzes.classes LIKE' => '%' . '"' . $term . '"' . '%');
                }
            }
        }

        $user_id = $this->Auth->user('id');

        // Get pagination
        $conditions[2] = array(
            'Quizzes.parent_quiz_id IS NOT NULL',
            'Downloads.id IS NULL'
        );

        if (!empty($this->request->data['order_type']) && !empty($this->request->data['order_field'])) {
            $order = ['Quizzes.' . $this->request->data['order_field'] . ' ' . $this->request->data['order_type']];
            $order_type = ($this->request->data['order_type'] == 'asc') ? 'desc' : 'asc';
            $this->set('order_type', $order_type);
            $this->set('order_field', $this->request->data['order_field']);
        } else {
            $order = ['Quizzes.created ASC'];
        }

        // pr($this->Paginator->settings);
        // exit;

        // pr($this->request->params);
        // exit;

        try {
            // pr($conditions);
            // exit;
            $quizzes = $this->paginate($this->Quizzes->find()->where($conditions)
                ->join([
                    'Downloads' => [
                        'table' => 'downloads',
                        'type' => 'LEFT',
                        'conditions' => [
                            'Downloads.quiz_id = Quizzes.id',
                            'Downloads.user_id' => $user_id
                        ]
                    ]
                ])
                ->order($order)
                //->page($this->request->params['named']['page'])
            );
        } catch (NotFoundException $e) { 
            $this->request->query['page'] = 1;
            $quizzes = $this->paginate($this->Quizzes->find()->where($conditions)
                ->join([
                    'Downloads' => [
                        'table' => 'downloads',
                        'type' => 'LEFT',
                        'conditions' => [
                            'Downloads.quiz_id = Quizzes.id',
                            'Downloads.user_id' => $user_id
                        ]
                    ]
                ])
            );
        }

        $this->set(compact('quizzes', 'subjectOptions', 'classOptions'));
        $this->render('/Element/Quiz/quiz_bank_pagination');
    }

    // ajax duplicate
    // Duplicate quiz
    // Only quiz name, questions and choices will be duplicated
    public function ajaxImport() {
        $this->autoRender = false;
        $this->accountStatus();
        $this->hasQuizBankAccess();
        $response['result'] = 0;

        $user_id = $this->Auth->user('id');

        // pr($this->request->data);
        // exit;

        // Check maximum import permission
        if ($this->Auth->user('account_level') == 22) {
            $imported_quiz_count = $this->Quizzes->Users->Downloads->find()->where(['Downloads.user_id' => $user_id])->count();
            // pr($imported_quiz_count);
            // exit;
            if ($imported_quiz_count >= DOWNLOAD_LIMIT) {
                $response['message'] = __('YOU_HAVE_EXCEEDED_MAX_IMPORT');
            } else {
                if ((count($this->request->data['quiz_id'])+$imported_quiz_count) > DOWNLOAD_LIMIT) {
                    $response['message'] = __('CANT_PROCESS_REQUEST') . ' ' . (DOWNLOAD_LIMIT - $imported_quiz_count);
                }
            }
        }
        if (!empty($response['message'])) {
            echo json_encode($response);
            exit;
        }

        $quizInfo = $this->Quizzes->find()
        ->where([
            'Quizzes.id IN' => $this->request->data['quiz_id'],
            'Quizzes.parent_quiz_id IS NOT NULL'
        ])
        ->contain([
            'Questions' => function($q) {
                return $q->contain([
                    'Choices'
                ]);
            }
        ])
        ->toArray();



        if (!empty($quizInfo) && (count($quizInfo) == count($this->request->data['quiz_id']))) {
            foreach ($quizInfo as $key => $quiz) {
                // Check if already imported
                $is_imported = $this->Quizzes->Users->Downloads->find('all', array(
                    'conditions' => array(
                        'Downloads.quiz_id' => $quiz->id,
                        'Downloads.user_id' => $user_id
                    )
                ))->count();
                // pr($is_imported);
                // exit;
                // End of checking
                if (empty($is_imported)) { // if Not imported before
                    // Imported quiz data
                    $download = $this->Quizzes->Downloads->newEntity();
                    $download->user_id = $user_id;
                    $download->quiz_id = $quiz->id;

                    // pr($download);
                    // exit;

                    $new_quiz = array();
                    $new_quiz['name'] = __('COPY_OF') . ' ' . $quiz->name;
                    $new_quiz['user_id'] = $user_id;
                    $new_quiz['description'] = $quiz->description;
                    $new_quiz['status'] = 1;
                    $new_quiz['show_result'] = $quiz->show_result;
                    $new_quiz['anonymous'] = $quiz->anonymous;
                    $new_quiz['subjects'] = $quiz->subjects;
                    $new_quiz['classes'] = $quiz->classes;

                    // pr($new_quiz);
                    // exit;

                    foreach ($quiz->questions as $key1 => $question) {
                        $new_quiz['questions'][$key1]['question_type_id'] = $question->question_type_id;
                        $new_quiz['questions'][$key1]['text'] = $question->text;
                        $new_quiz['questions'][$key1]['explanation'] = $question->explanation;
                        $new_quiz['questions'][$key1]['weight'] = $question->weight;
                        $new_quiz['questions'][$key1]['max_allowed'] = $question->max_allowed;
                        $new_quiz['questions'][$key1]['case_sensitive'] = $question->case_sensitive;

                        if (!empty($question->choices)) {
                            foreach ($question->choices as $key2 => $choice) {
                                $new_quiz['questions'][$key1]['choices'][$key2]['text'] = $choice->text;
                                $new_quiz['questions'][$key1]['choices'][$key2]['points'] = $choice->points;
                                $new_quiz['questions'][$key1]['choices'][$key2]['weight'] = $choice->weight;
                            }
                        }
                    }

                    $new_quiz = $this->Quizzes->newEntity($new_quiz, [
                        'associated' => [
                            'Questions' => ['associated' => ['Choices']]
                        ]
                    ]);

                    if ($this->Quizzes->save($new_quiz)) {
                        if ($this->Quizzes->updateAll(
                            ['random_id' => $new_quiz->id . $this->randText(2, true)], 
                            ['id' => $new_quiz->id]
                        )) {
                            $response['Quiz'][$key]['id'] = $new_quiz->id;
                            $response['Quiz'][$key]['name'] = $new_quiz->name;
                            $this->Quizzes->Users->Downloads->save($download); 
                            $success_least_one = true;
                        } else {
                            $this->Quizzes->delete($new_quiz->id);
                        }
                    }
                }
            }
            if (!empty($success_least_one)) {
                $response['message'] = __('IMPORTED_SUCCESS');
                $response['result'] = 1;
            } else {
                $response['message'] = __('SOMETHING_WENT_WRONG');
            }
        } else {
            $response['message'] = __('INVALID_QUIZ');
        }
        echo json_encode($response);
        exit;

    }

    private function quizTypes() {
        return array(
            '1' => __('ACTIVE_QUIZZES'), 
            '0' => __('ARCHIVED_QUIZZES'), 
            'all' => __('ALL_QUIZ'),
        );
    }

    private function quizSharedType() {
        return array(
            'shared' => __('SHARED_QUIZZES'),
            'pending' => __('PENDING_QUIZZES'),
            'decline' => __('DECLINED_QUIZZES'),
            'private' => __('PRIVATE_QUIZZES') 
        );
    }

    // Method for quiz auto update
    public function ajaxQuizUpdate($id = null) {
        $this->autoRender = false;
        $output['success'] = false;
        $quiz = $this->Quizzes->find()->where(['id' => $id, 'user_id' => $this->Auth->user('id')])->first();
        if (empty($quiz)) {
            $output['message'] = __('NO_PERMISSION');
            echo json_encode($output);
            exit;
        }
        if (in_array($this->request->data['field'], ['name', 'description', 'show_result', 'anonymous', 'subjects', 'classes'])) {
            $data[$this->request->data['field']] = (in_array($this->request->data['field'], ['subjects', 'classes']) && !empty($this->request->data['value'])) ? json_encode($this->request->data['value'], true) : $this->request->data['value'];
            $quiz = $this->Quizzes->patchEntity($quiz, $data);
            if ($this->Quizzes->save($quiz)) {
                $output['success'] = true;
                $output['message'] = __('SAVE_SUCCESS');
            } else {
                $output['message'] = __('QUIZ_SAVE_FAILED');
            }
        }
        echo json_encode($output);
    }

}
