<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Network\Exception\NotFoundException;
use Cake\Datasource\ConnectionManager;
use Cake\Utility\Hash;

/**
 * Quizzes Controller
 *
 * @property \App\Model\Table\QuizzesTable $Quizzes
 */
class QuizzesController extends AppController
{
    public $paginate = [
        'limit' => 3
    ];

    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Paginator');
        $this->loadComponent('Email');
        $this->Auth->allow(['present', 'live', 'no_permission']);
    }

    public function ajax_student_update() {
        $this->viewBuilder()->layout('ajax');
        $student = $this->Quizzes->Student->findById($this->request->data['student_id']);
        $student['id'] = $student['Student']['id'];
        $student['fname'] = $student['Student']['fname'];
        $student['lname'] = $student['Student']['lname'];
        $student['class'] = $student['Student']['class'];
        $student['submitted'] = $student['Student']['submitted'];
        $student['quiz_id'] = $student['Student']['quiz_id'];
        $student['status'] = $student['Student']['status'];
        unset($student['Student']);
        
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
        $data = array(
            'quizzes' => $quizzes,
        );

        $lang_strings['delete_quiz_1'] = __('There are ');
        $lang_strings['delete_quiz_2'] = __(' answers, ');
        $lang_strings['delete_quiz_3'] = __(' students, and ');
        $lang_strings['delete_quiz_4'] = __(' number of questions. This can not be undone. Are you sure want to delete?');
        $lang_strings['delete_quiz_5'] = __('Delete quiz ');
        $lang_strings['request_sent'] = __('Upgrade Pending');
        $lang_strings['share_quiz'] = __('Share quiz');
        $lang_strings['share_quiz_question'] = __('Do you want to share the quiz?');
        $lang_strings['remove_share'] = __('Cancel share quiz');
        $lang_strings['remove_share_question'] = __('Do you want to remove sharing the quiz?');
        $lang_strings['remove_shared_quiz'] = __('Cancel share');
        $lang_strings['check_select'] = __('Please choose at least one quiz to import!');
        $lang_strings['import_success'] = __('Quiz imported successfully');

        $this->set(compact('data', 'filter', 'lang_strings'));
    }

    public function edit($quizId, $initial = '') {

        $this->accountStatus();
        // Check permission
        $userId = $this->Auth->user('id');
        
        // $result = $this->Quizzes->find('count', array(
        //     'conditions' => array(
        //         'Quizzes.id = ' => $quizId,
        //         'Quizzes.user_id = ' => $userId
        //     )
        // ));
        
        // if ($result < 1)
        //     throw new ForbiddenException;
        
        if ($this->request->is('post')) {
            if (!empty($this->request->data['Quiz']['subjects'])) {
                $this->request->data['Quiz']['subjects'] = json_encode($this->request->data['Quiz']['subjects'], true);
            }

            if (!empty($this->request->data['Quiz']['classes'])) {
                $this->request->data['Quiz']['classes'] = json_encode($this->request->data['Quiz']['classes'], true);
            }

            $data = $this->request->data;
            // pr($data);
            // exit;
            $this->Quizzes->id = $quizId;
            $this->Quizzes->set($data['Quiz']);
            if ($this->Quizzes->validates()) {
                $this->Quizzes->save();
                return $this->redirect(array('action' => 'index'));
            } else {
                $error = array();
                foreach ($this->Quizzes->validationErrors as $_error) {
                    $error[] = $_error[0];
                }
                $this->Flash->error($error);
                if (!empty($initial)) {
                    return $this->redirect(array('action' => 'edit', $quizId, $initial));
                } else {
                    return $this->redirect(array('action' => 'edit', $quizId));
                }
            }
        }

        $data = $this->Quizzes->find()
            ->where(['Quizzes.id' => $quizId, 'Quizzes.user_id' => $userId])
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
            ])
            ->first();
        //pr($data);
        // exit;

        if (empty($data))
            throw new NotFoundException;

        $data->question_type = $this->Quizzes->Questions->QuestionTypes->find()
            ->select(['name', 'template_name', 'multiple_choices', 'id', 'type'])
            ->toArray();

        // pr($data['QuestionTypes']);
        // exit;
        if (!empty($initial)) {
            $this->set(compact('initial'));
        } 

        if (empty($data['Question'])) {
            $this->set('no_question', true);
        }

        $lang_strings['empty_question'] = __('Empty Question Is Not Permit');
        $lang_strings['same_choice'] = __('Empty or Same Choices Are Not Permit');
        $lang_strings['single_greater'] = __('At least a point should be greater than 0');
        $lang_strings['correct_answer'] = __('Enter correct answers, if multiple answers comma separated');
        $lang_strings['point_greater'] = __('At least point should be greater than 0');
        $lang_strings['two_greater'] = __('At least 2 points should be greater than 0');
        $lang_strings['insert_another'] = __('You put only one correct answers, please choose another point greater than 0!!!');
        $lang_strings['youtube_url'] = __('Please enter Youtube url');
        $lang_strings['image_url'] = __('Please enter image url');
        $lang_strings['header_q_title'] = __('Enter the header');
        $lang_strings['other_q_title'] = __('Enter the question');

        $lang_strings['youtube_exp_text'] = __('Video explanation text');
        $lang_strings['image_exp_text'] = __('Image explanation text');
        $lang_strings['other_exp_text'] = __('Explanation text');
        $lang_strings['empty_header'] = __('Please enter Header text');

        // Load available classes (created by admin)
        $this->loadModel('Subjects');

        $classOptions = $this->Subjects->find('list', ['keyField' => 'id', 'valueField' => 'title'])
            ->where(['Subjects.isactive' => 1, 'Subjects.is_del IS NULL', 'Subjects.type' => 1])
            ->toArray();


        // pr($classOptions);
        // exit;

        $subject_cond[] = array(
            'Subjects.isactive' => 1,
            'Subjects.is_del IS NULL',
            'Subjects.type IS NULL'
        );
        $c_user = $this->Session->read('Auth.User');
        // pr($c_user);
        // exit;
        if (!empty($c_user['subjects'])) {
            $selectedSubjects = json_decode($c_user['subjects'], true);
            $subject_cond[] = array('Subjects.id IN' => $selectedSubjects);
        }

        $subjectOptions = $this->Subjects->find('list', ['keyField' => 'id', 'valueField' => 'title'])->where($subject_cond)->toArray();

        // pr($subjectOptions);
        // exit;

        if (!empty($subjectOptions)) {
            $subjectOptions = array(0 => __('All Subjects')) + $subjectOptions;
        }
        
        if (!empty($classOptions)) {
            $classOptions = array(0 => __('All Classes')) + $classOptions;
        }

        // pr($this->Session->read('Auth.User'));
        // exit;

        // pr($data);
        // exit;

        $this->set('data', $data);
        $this->set(compact('lang_strings', 'classOptions', 'subjectOptions'));
    }

    public function add() {
        
        $this->accountStatus(); 

        $userId = $this->Auth->user('id');
        // if (!$this->Users->canCreateQuiz($userId))
        //     return $this->redirect(array('action' => 'index'));  

        $quiz = $this->Quizzes->newEntity();
        $quiz = $this->Quizzes->patchEntity($quiz, ['name' => __('Name the quiz'), 'user_id' => $userId]);
        // pr($quiz);
        // exit;
        $this->Quizzes->save($quiz);
        // save random number as random_id
        $quiz->random_id = $quiz->id . $this->randText(2);
        $this->Quizzes->save($quiz);
        
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
                $admin_email = $this->Email->sendMail('test@test.com', __('[Verkkotesti] First quiz created'), $user, 'first_quiz_create');
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
        $quiz = $this->Quizzes->find('all', array(
            'conditions' => array('Quizzes.id' => $id)
        ))->first();

        if (empty($quiz))
            throw new NotFoundException(__('Invalid quiz!'));
        $this->set(compact('quiz', 'id'));
    }

    // ajax_preview
    public function ajaxPreview() {
        $this->viewBuilder()->layout('ajax');
        $data = $this->Quizzes->find('all', array(
            'conditions' => array(
                'Quizzes.random_id' => $this->request->data['random_id'],
                'Quizzes.shared' => 1,
                'Quizzes.is_approve' => 1
            ),
            'contain' => array(
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
            )
        ))->first();

        // pr($data);
        // exit;

        if (empty($data)) {
            $this->set('isError', true);
        } else {
            $lang_strings[0] = __('Internet connection has been lost, please try again later.');
            $lang_strings[1] = __('All questions answered. Turn in your quiz?');
            $lang_strings[2] = __('Questions ');
            $lang_strings[3] = __(' unanswered.');
            $lang_strings[4] = __(' Turn in your quiz?');
            $lang_strings[5] = __('First Name is Required');
            $lang_strings[6] = __('Last Name is Required');
            $lang_strings[7] = __('Class is Required');
            $lang_strings['last_name_invalid'] = __('Invalid Last Name');
            $lang_strings['first_name_invalid'] = __('Invalid First Name');
            $lang_strings['class_invalid'] = __('Invalid Class');
            $lang_strings['right_click_disabled'] = __('Sorry, right click disabled');
            $lang_strings['browser_switch'] = __('Sorry, you are not allowed to switch browser tab');
            $lang_strings['leave_quiz'] = __('Are you sure that you want to leave this quiz?');

            $this->set(compact('lang_strings', 'data'));
            $this->set('quizRandomId', $this->request->data['random_id']);

        }
    }

    public function live($quizRandomId) {

        // start session for examination
        if (!$this->Session->check('started')) {
            $this->Session->destroy();
            $randomString = $this->randText(10);
            $this->Session->write('started', $randomString);
            $this->Session->write('random_id', $quizRandomId); // Write random_id on session to keey track of online students
            $this->redirect(array('controller' => 'quiz', 'action' => 'live', $quizRandomId, '?' => array('runningFor' => $randomString)));
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
            $this->redirect(array('controller' => 'quiz', 'action' => 'live', $quizRandomId, '?' => array('runningFor' => $randomString)));
        }

        $this->Quizzes->Behaviors->load('Containable');
        $this->Quizzes->bindModel(
               array(
                 'belongsTo'=>array(
                     'User'=>array(
                       'className'  =>  'User',
                       'foreignKey' => 'user_id'
                   )          
               )
            ), false // Note the false here!
        );
        $data = $this->Quizzes->find('first', array(
            'conditions' => array(
                'random_id = ' => $quizRandomId,
                'Quizzes.status' => 1
            ),
            'contain' => array(
                'Question' => array(
                    'Choice' => array('order' => array('Choice.weight DESC', 'Choice.id ASC')),
                    'QuestionType' => array(
                        'fields' => array('template_name', 'id', 'multiple_choices')
                    ),
                    'order' => array('Question.weight DESC', 'Question.id ASC')
                ),
                'User'
            )
        ));

        if (empty($data)) {
            $this->set('title_for_layout', __('Closed'));
            $this->render('not_found');
        } else {
            // check user access level
            if ((($data['User']['account_level'] == 0) || 
                (($data['User']['account_level'] == 1) && (strtotime($data['User']['expired']) < time()))) 
                && ($data['Quiz']['student_count'] >= 40)) {
                $this->Flash->error(__('Sorry, only allow 40 students to take this quiz.'));
                return $this->redirect(array('controller' => 'quiz', 'action' => 'no_permission'));
            }

            // Check session if student page reloaded
            if ($this->Session->check('student_id')) {
                $student = $this->Quizzes->Student->find('first', array(
                    'conditions' => array(
                        'Student.id' => (int) $this->Session->read('student_id')
                    ),
                    'contain' => array('Answer', 'Ranking')
                ));
                $this->request->data = $student;
                // pr($this->request->data);
                // exit;
            }

            $lang_strings[0] = __('Internet connection has been lost, please try again later.');
            $lang_strings[1] = __('All questions answered. Turn in your quiz?');
            $lang_strings[2] = __('Questions ');
            $lang_strings[3] = __(' unanswered.');
            $lang_strings[4] = __(' Turn in your quiz?');
            $lang_strings[5] = __('First Name is Required');
            $lang_strings[6] = __('Last Name is Required');
            $lang_strings[7] = __('Class is Required');
            $lang_strings['last_name_invalid'] = __('Invalid Last Name');
            $lang_strings['first_name_invalid'] = __('Invalid First Name');
            $lang_strings['class_invalid'] = __('Invalid Class');
            $lang_strings['right_click_disabled'] = __('Sorry, right click disabled');
            $lang_strings['browser_switch'] = __('Sorry, you are not allowed to switch browser tab');
            $lang_strings['leave_quiz'] = __('Are you sure that you want to leave this quiz?');

            $lang_strings['disabled_submit'] = __('Please hold, your answers are saving...');
            $lang_strings['enabled_submit'] = __('Turn in your quiz');

            $this->disableCache();
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
        // function cmp($a, $b) {
        //     if ($a == $b) {
        //         return 0;
        //     }
        //     return ($a < $b) ? -1 : 1;
        // }
        // uasort($classes, 'cmp');
        //// classes merge with all class
       
        
        $classes = Hash::merge(array('all' => __('All Classes')), $new_class);

        $lang_strings['remove_question'] = __('Are you sure you want to remove ');
        $lang_strings['with_points'] = __(') answer with points ');
        $lang_strings['positive_number'] = __('Please Give a postive number!');
        $lang_strings['update_require'] = __('You have not updated score yet!');
        $lang_strings['more_point_1'] = __('Points not allowed more than ');
        $lang_strings['more_point_2'] = __(' value');
        $lang_strings['online_warning'] = __('Student is online and giving test! ');

        $this->set(compact('quizDetails', 'classes', 'filter', 'studentIds', 'quizId', 'lang_strings'));
    }

    // Student online status
    public function checkOnlineStudent($random_id) {
        $onlineStds = array();
        //$time = time()+14400-60; // (14400 == 4 huors and 10 mins = 600)
        $time = time()+14400-60; // (14400 == 4 huors and 10 mins = 600)

        $conn = ConnectionManager::get('default');

        // pr($conn);
        // exit;
        $stmt = $conn->execute('SELECT data FROM sessions WHERE expires > ' . $time . ' AND data LIKE ' . "'%" . '"' . $random_id . '"' . "%'");
        $sessions = $stmt ->fetchAll('assoc');
        // pr($sessions);
        // exit;


        // $sessions = $this->Quizzes->query('SELECT data FROM sessions WHERE expires > ' . $time . ' AND data LIKE ' . "'%" . '"' . $random_id . '"' . "%'")->toArray();
        // pr($sessions);
        // exit;
        foreach ($sessions as $session) {
            $tmp = explode(';random_id|', $session[$this->Quizzes->tablePrefix . 'cake_sessions']['data']);
            if (!empty($tmp[1]) && (strpos($tmp[1], 'student_id') !== false)) {
                $tmp = explode('student_id', $tmp[1]);
                $q_random_id = explode('"', $tmp[0]);
                $q_student_id = explode('"', $tmp[1]);
                if (!empty($q_random_id[1]) && ($q_random_id[1] == $random_id) && !empty($q_student_id[1])) {
                    $onlineStds[] = $q_student_id[1];
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

    public function ajax_update() {
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
            $this->Flash->success(__('You have Successfuly deleted quiz'));
        }
        return $this->redirect($this->referer());
    }

    public function no_permission() {
        $this->set('title_for_layout', __('No permission'));
    }

    // print quiz answer
    public function ajax_print_answer() {
        $this->accountStatus();
        $this->layout = "ajax";
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
            $new_quiz['name'] = __('Copy of:') . ' ' . $quiz->name;
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

            // pr($new_quiz);
            // exit;

            if ($this->Quizzes->save($new_quiz)) {
                $random_id = $new_quiz->id . $this->randText(2);
                $this->Quizzes->save($new_quiz);
                $response['message'] = __('Duplicated Successfully');
                $response['result'] = 1;
                $response['id'] = $new_quiz->id;
            } else {
                $response['message'] = __('Something went wrong, please try again later!');
            }
        } else {
            $response['message'] = __('Invalid Quiz!');
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
            $this->Flash->error(__('Invalid quiz'));
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
        // pr($quiz);
        // exit;


        // Update shared field
        $quiz->shared = empty($shared) ? 1 : NULL;
        if ($this->Quizzes->save($quiz)) {
            // Send email to the admin
            if (empty($shared)) {
                $subject = __('[Verkkotesti] A quiz has been shared!');
                $template = 'quiz_shared';
                $message = __('You have successfully shared the quiz. Please hold for admin approval.');

                $admin_email = $this->Email->sendMail('test@test.com', $subject, $quiz, $template);
                //$admin_email = $this->Email->sendMail('biplob.weblancer@gmail.com', $subject, $quiz, $template);

                // pr($admin_email);
                // exit;
            } else {
                $subject = __('Sharing removed!');
                $template = 'remove_sharing';
                $message = __('You have successfully remove sharing');
            }
            $this->Flash->success($message);
        } else {
            $this->Flash->error(__('Something went wrong, please try again later!'));
        }
        return $this->redirect($this->referer());
    }

    // Method for listing all quizzes from quiz bank
    public function ajaxBank() {
        $this->temp_common();
    }

    public function temp_common() {
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

        // pr($classOptions);
        // exit;
        // Selected subjects
        $selectedSubjects = $this->Session->read('Auth.User.subjects');

        // pr($selectedSubjects);
        // exit;

        if (!empty($selectedSubjects)) {
            $selectedSubjects = json_decode($selectedSubjects, true);
        } else {
            $selectedSubjects = array();
        }

        // pr($selectedSubjects);
        // exit;
        foreach ($selectedSubjects as $term) {
            $term = trim($term);
            if (!empty($term)) {
                $conditions['OR'][] = array('Quizzes.subjects LIKE' => '%' . '"' . $term . '"' . '%');
            }
        }

        $user_id = $this->Auth->user('id');

        //$this->Quizzes->virtualFields['imported'] = 'SELECT count(*) FROM imported_quizzes AS ImportedQuiz WHERE (Downloads.user_id = '.$user_id.' AND Downloads.quiz_id = Quizzes.id)';

        // Get pagination
        $conditions[] = array(
            'Quizzes.shared' => 1,
            'Quizzes.is_approve' => 1,
            'Downloads.id IS NULL'
        );

        // pr($this->Paginator->settings);
        // exit;

        $quizzes = $this->paginate($this->Quizzes->find()->where($conditions)
            ->join([
                'downloads' => [
                    'table' => 'Downloads',
                    'type' => 'LEFT',
                    'conditions' => [
                        'Downloads.quiz_id = Quizzes.id',
                        'Downloads.user_id' => $user_id
                    ]
                ]
            ])
        );


        // pr($quizzes);
        // exit;

        if (!empty($subjectOptions)) {
            $subjectOptions = array(0 => __('All Subjects')) + $subjectOptions;
        }
        
        if (!empty($classOptions)) {
            $classOptions = array(0 => __('All Classes')) + $classOptions;
        }

        $this->set(compact('subjectOptions', 'classOptions', 'selectedSubjects', 'quizzes'));
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
            'Quizzes.shared' => 1,
            'Quizzes.is_approve' => 1,
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
                    'downloads' => [
                        'table' => 'Downloads',
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
                    'downloads' => [
                        'table' => 'Downloads',
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
                $response['message'] = __('Sorry, you have exceeded maximum limit of import quiz. Please upgrade your account to get unlimited access on quiz bank!');
            } else {
                if ((count($this->request->data['random_id'])+$imported_quiz_count) > DOWNLOAD_LIMIT) {
                    $response['message'] = __('Sorry, we can\'t process your request! You have left only import') . ' ' . (DOWNLOAD_LIMIT - $imported_quiz_count);
                }
            }
        }
        if (!empty($response['message'])) {
            echo json_encode($response);
            exit;
        }

        // $quizInfo = $this->Quizzes->find('all', array(
        //     'conditions' => array(
        //         'Quizzes.random_id IN' => $this->request->data['random_id'],
        //         'Quizzes.shared' => 1
        //     ),
        //     'contain' => array(
        //         'Questions' => array(
        //             'Choices'
        //         )
        //     )
        // ))->toArray();

        $quizInfo = $this->Quizzes->find()
        ->where([
            'Quizzes.random_id IN' => $this->request->data['random_id'],
            'Quizzes.shared' => 1
        ])
        ->contain([
            'Questions' => function($q) {
                return $q->contain([
                    'Choices'
                ]);
            }
        ])
        ->toArray();

        // pr($this->request->data['random_id']);
        // pr($quizInfo);
        // exit;

        if (!empty($quizInfo) && (count($quizInfo) == count($this->request->data['random_id']))) {
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
                    $new_quiz['name'] = __('Copy of:') . ' ' . $quiz->name;
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

                    // pr($new_quiz);
                    // exit;

                    $new_quiz = $this->Quizzes->newEntity($new_quiz, [
                        'associated' => [
                            'Questions' => ['associated' => ['Choices']]
                        ]
                    ]);

                    //pr($quizInfo);
            
                    // pr($new_quiz);
                    // exit;
                    
                    // $quiz = $this->Quizzes->save($quiz);

                    // pr($quiz);
                    // exit;

                    if ($this->Quizzes->save($new_quiz)) {
                        $random_id = $new_quiz->id . $this->randText(2);
                        $this->Quizzes->save($new_quiz);
                        $response['Quiz'][$key]['id'] = $new_quiz->id;
                        $response['Quiz'][$key]['name'] = $new_quiz->name;
                        $this->Quizzes->Users->Downloads->save($download); 
                    } else {
                        $response['message'] = __('Something went wrong, please try again later!');
                    }
                }
            }
            $response['message'] = __('Imported Successfully');
            $response['result'] = 1;
        } else {
            $response['message'] = __('Invalid Quiz!');
        }
        echo json_encode($response);
        exit;

    }

    // List of shared quiz
    public function admin_shared() {
        if ($this->Auth->user('account_level') != 51)
            throw new ForbiddenException;
        $this->set('title_for_layout',__('Shared Quiz List'));

        if ($this->request->is('post')) {
            $data = $this->request->data;
            if (isset($data['Quiz'])) {
                $filter = $data['Quiz']['is_approve'];
                $this->Session->write('Quizzes.is_approve', $filter);
            }
            $this->redirect(array('controller' => 'quiz', 'action' => 'shared', 'admin' => true));
        } else {
            if (!$this->Session->check('Quizzes.is_approve')) {
                $filter = 'all';
                $this->Session->write('Quizzes.is_approve', $filter);
            } else {
                $filter = $this->Session->read('Quizzes.is_approve');
            }
        }

        $this->set(compact('filter'));
        $this->Paginator->settings = $this->paginate;
        if ($filter == 'all') {
            // Do nothing
            $this->Paginator->settings['order'] = 'Quizzes.is_approve ASC';
        } else {    
            // pr($filter);
            $this->Paginator->settings['conditions'][] = array(
                'Quizzes.is_approve' => ($filter == 3) ? NULL : $filter,
            );
            $this->Paginator->settings['order'] = 'Quizzes.created ASC';
        }

        $this->Quizzes->Behaviors->load('Containable');

        $this->Paginator->settings['conditions'][] = array(
            'Quizzes.shared' => 1,
        );

        $this->Paginator->settings['contain'] = array(
            'User'
        );

        // pr($this->Paginator->settings);

        // pr($this->Paginator->paginate('Quiz'));
        // exit;

        try {
            $this->set('quizzes', $this->Paginator->paginate('Quiz'));
        } catch (NotFoundException $e) { 
            // when pagination error found redirect to first page e.g. paging page not found
            return $this->redirect(array('controller' => 'quiz', 'action' => 'shared', 'admin' => true));
        }

        // Language strings
        $lang_strings['decline_question'] = __('Decline the quiz');
        $lang_strings['cancel'] = __('Cancel');
        $lang_strings['submit'] = __('Submit');
        $lang_strings['decline_reason'] = __('Enter decline reason if any!');
        $lang_strings['approve_question'] = __('Approve the quiz');
        $lang_strings['approve_body'] = __('If you approve the quiz, you have always option to decline sharing!');

        $this->set(compact('lang_strings'));

    }

    // Admin decline method
    public function admin_manage_share() {
        if ($this->Auth->user('account_level') != 51)
            throw new ForbiddenException;

        if ($this->request->is('post')) {
            $quiz = $this->Quizzes->find('first', array(
                'conditions' => array(
                    'Quizzes.random_id' => $this->request->data['Quiz']['random_id'],
                    'Quizzes.shared' => 1
                ),
                'recursive' => -1,
                'fields' => array('Quizzes.id')
            ));

            if (empty($quiz)) {
                $this->Flash->error(__('Something went wrong, please try again later!'));
            } else {
                unset($this->request->data['Quiz']['random_id']);
                $this->request->data['Quiz']['id'] = $quiz['Quiz']['id'];
                $message = ($this->request->data['Quiz']['is_approve'] == 1) ? __('You have successfully approved!') : __('You have successfully declined!');
                // pr($this->request->data);
                // exit;
                $this->Quizzes->validator()->remove('name');
                if ($this->Quizzes->save($this->request->data)) {
                     $this->Flash->success($message);
                } else {
                     $this->Flash->error(__('Something went wrong, please try again later!'));
                }
            }
        }
        $this->redirect($this->referer());
    }

    // Admin preview quiz
    public function admin_preview($quiz_id = null) {
        if ($this->Auth->user('account_level') != 51)
            throw new ForbiddenException;
        if (empty($quiz_id)) {
            $this->Flash->error(__('No direct access to this location!'));
            $this->redirect(array('controller' => 'quiz', 'action' => 'shared', 'admin' => true));
        }

        $this->Quizzes->Behaviors->load('Containable');
        $data = $this->Quizzes->find('first', array(
            'conditions' => array(
                'Quizzes.id = ' => $quiz_id
            ),
            'contain' => array(
                'Question' => array(
                    'Choice' => array('order' => array('Choice.weight DESC', 'Choice.id ASC')),
                    'QuestionType' => array(
                        'fields' => array('template_name', 'id', 'multiple_choices')
                    ),
                    'order' => array('Question.weight DESC', 'Question.id ASC')
                ),
                'User'
            )
        ));

        if (empty($data))
            throw new NotFoundException;

        $this->QuestionType->Behaviors->load('Containable');
        $this->QuestionType->contain();
        $data['QuestionTypes'] = $this->QuestionType->find('all', array(
            'fields' => array('name', 'template_name', 'multiple_choices', 'id', 'type')
        ));

        if (empty($data['Question'])) {
            $this->set('no_question', true);
        }

        $lang_strings['empty_question'] = __('Empty Question Is Not Permit');
        $lang_strings['same_choice'] = __('Empty or Same Choices Are Not Permit');
        $lang_strings['single_greater'] = __('At least a point should be greater than 0');
        $lang_strings['correct_answer'] = __('Enter correct answers, if multiple answers comma separated');
        $lang_strings['point_greater'] = __('At least point should be greater than 0');
        $lang_strings['two_greater'] = __('At least 2 points should be greater than 0');
        $lang_strings['insert_another'] = __('You put only one correct answers, please choose another point greater than 0!!!');
        $lang_strings['youtube_url'] = __('Please enter Youtube url');
        $lang_strings['image_url'] = __('Please enter image url');
        $lang_strings['header_q_title'] = __('Enter the header');
        $lang_strings['other_q_title'] = __('Enter the question');

        $lang_strings['youtube_exp_text'] = __('Video explanation text');
        $lang_strings['image_exp_text'] = __('Image explanation text');
        $lang_strings['other_exp_text'] = __('Explanation text');
        $lang_strings['empty_header'] = __('Please enter Header text');

        // Load available classes (created by admin)
        $this->loadModel('Subject');
        $classOptions = $this->Subject->find('list', array(
            'conditions' => array(
                'Subject.isactive' => 1,
                'Subject.is_del' => NULL,
                'Subject.type' => 1
            ),
            'recursive' => -1
        ));

        $subject_cond[] = array(
            'Subject.isactive' => 1,
            'Subject.is_del' => NULL,
            'Subject.type' => NULL
        );

        if (!empty($data['User']['subjects'])) {
            $selectedSubjects = json_decode($data['User']['subjects'], true);
            $subject_cond[] = array('Subject.id' => $selectedSubjects);
        }

        $subjectOptions = $this->Subject->find('list', array(
            'conditions' => $subject_cond,
            'recursive' => -1
        ));

        if (!empty($subjectOptions)) {
            $subjectOptions = array(0 => __('All Subject')) + $subjectOptions;
        }
        
        if (!empty($classOptions)) {
            $classOptions = array(0 => __('All Class')) + $classOptions;
        }

        // pr($this->Session->read('Auth.User'));
        // exit;

        // pr($data);
        // exit;

        $this->set('data', $data);
        $this->set(compact('lang_strings', 'classOptions', 'subjectOptions'));
    }

    private function quizTypes() {
        return array(
            '1' => __('Active Quizzes'), 
            '0' => __('Archived Quizzes'), 
            'all' => __('All Quizzes'),
        );
    }

    private function quizSharedType() {
        return array(
            'shared' => __('Shared Quizzes'),
            'pending' => __('Pending Quizzes'),
            'decline' => __('Decline Quizzes'),
            'private' => __('Private Quizzes') 
        );
    }

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index_1()
    {
        $this->paginate = [
            'contain' => ['Users', 'Randoms']
        ];
        $quizzes = $this->paginate($this->Quizzes);

        $this->set(compact('quizzes'));
        $this->set('_serialize', ['quizzes']);
    }

    /**
     * View method
     *
     * @param string|null $id Quiz id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view_1($id = null)
    {
        $quiz = $this->Quizzes->get($id, [
            'contain' => ['Users', 'Randoms', 'Downloads', 'Questions', 'Rankings', 'Students']
        ]);

        $this->set('quiz', $quiz);
        $this->set('_serialize', ['quiz']);
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|void Redirects on successful add, renders view otherwise.
     */
    public function add_1()
    {
        $quiz = $this->Quizzes->newEntity();
        if ($this->request->is('post')) {
            $quiz = $this->Quizzes->patchEntity($quiz, $this->request->data);
            if ($this->Quizzes->save($quiz)) {
                $this->Flash->success(__('The quiz has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The quiz could not be saved. Please, try again.'));
            }
        }
        $users = $this->Quizzes->Userss->find('list', ['limit' => 200]);
        $randoms = $this->Quizzes->Randoms->find('list', ['limit' => 200]);
        $this->set(compact('quiz', 'users', 'randoms'));
        $this->set('_serialize', ['quiz']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Quiz id.
     * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit_1($id = null)
    {
        $quiz = $this->Quizzes->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $quiz = $this->Quizzes->patchEntity($quiz, $this->request->data);
            if ($this->Quizzes->save($quiz)) {
                $this->Flash->success(__('The quiz has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The quiz could not be saved. Please, try again.'));
            }
        }
        $users = $this->Quizzes->Userss->find('list', ['limit' => 200]);
        $randoms = $this->Quizzes->Randoms->find('list', ['limit' => 200]);
        $this->set(compact('quiz', 'users', 'randoms'));
        $this->set('_serialize', ['quiz']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Quiz id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete_1($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $quiz = $this->Quizzes->get($id);
        if ($this->Quizzes->delete($quiz)) {
            $this->Flash->success(__('The quiz has been deleted.'));
        } else {
            $this->Flash->error(__('The quiz could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
