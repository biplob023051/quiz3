<?php
namespace App\Controller\Admin;

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
        'limit' => 3,
        'sortWhitelist' => [
            'id', 'name', 'Users.name', 'created', 'is_approve'
        ]
    ];

    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Paginator');
        $this->loadComponent('Email');
        $this->Auth->allow(['present', 'live', 'no_permission']);
    }

    // List of shared quiz
    public function shared() {
        $this->isAdminUser();
        $this->set('title_for_layout',__('SHARED_QUIZ_LIST'));

        if ($this->request->is('post')) {
            $data = $this->request->data;
            if (isset($data['Quiz'])) {
                $filter = $data['Quiz']['is_approve'];
                $this->Session->write('Quizzes.is_approve', $filter);
            }
            $this->redirect(array('controller' => 'quizzes', 'action' => 'shared'));
        } else {
            if (!$this->Session->check('Quizzes.is_approve')) {
                $filter = 'all';
                $this->Session->write('Quizzes.is_approve', $filter);
            } else {
                $filter = $this->Session->read('Quizzes.is_approve');
            }
        }
        $this->set(compact('filter'));

        if ($filter == 'all') {
            // Do nothing
            $order = ['Quizzes.is_approve ASC'];
        } else {    
            // pr($filter);
            if ($filter == 3) {
                $conditions[] = ['Quizzes.is_approve IS NULL'];
            } else {
                $conditions[] = ['Quizzes.is_approve' =>  $filter];
            }
            $order = ['Quizzes.created ASC'];
        }

        $conditions[] = ['Quizzes.shared' => 1];

        $contain = ['Users'];

        // pr($this->Paginator->settings);

        // pr($this->Paginator->paginate('Quiz'));
        // exit;

        try {
            $quizzes = $this->paginate($this->Quizzes->find()->where($conditions)
                ->contain($contain)
                ->order($order)
            )->toArray();
            $this->set(compact('quizzes'));
        } catch (NotFoundException $e) { 
            // when pagination error found redirect to first page e.g. paging page not found
            return $this->redirect(array('controller' => 'quiz', 'action' => 'shared'));
        }

        // Language strings
        $lang_strings['decline_question'] = __('DECLINE_QUIZ');
        $lang_strings['cancel'] = __('CANCEL');
        $lang_strings['submit'] = __('SUBMIT');
        $lang_strings['decline_reason'] = __('ENTER_DECLINE_REASON');
        $lang_strings['approve_question'] = __('APPROVE_QUIZ');
        $lang_strings['approve_body'] = __('IF_APPROVE_CAN_DECLINE');

        $this->set(compact('lang_strings'));

    }

    // Admin decline method
    public function manageShare() {
        $this->isAdminUser();
        if ($this->request->is('post')) {
            $quiz = $this->Quizzes->find('all', array(
                'conditions' => array(
                    'Quizzes.random_id' => $this->request->data['random_id'],
                    'Quizzes.shared' => 1
                )
            ))->first();

            // pr($quiz);
            // exit;

            if (empty($quiz)) {
                $this->Flash->error(__('SOMETHING_WENT_WRONG'));
            } else {
                $message = ($this->request->data['is_approve'] == 1) ? __('You have successfully approved!') : __('You have successfully declined!');
                unset($this->request->data['random_id']);
                $quiz = $this->Quizzes->patchEntity($quiz, $this->request->data, ['validate' => '']);
                // pr($quiz);
                // exit;
                if ($this->Quizzes->save($quiz)) {
                     $this->Flash->success($message);
                } else {
                     $this->Flash->error(__('SOMETHING_WENT_WRONG'));
                }
            }
        }
        $this->redirect($this->referer());
    }

    // Admin preview quiz
    public function preview($quiz_id = null) {
        $this->isAdminUser();
        if (empty($quiz_id)) {
            $this->Flash->error(__('NO_DIRECT_ACCESS_PAGE'));
            return $this->redirect(array('controller' => 'quizzes', 'action' => 'shared'));
        }

        $data = $this->Quizzes->find('all')
            ->where(['Quizzes.id' => $quiz_id])
            ->contain([
                'Users',
                'Questions' => function($q) {
                    return $q->contain([
                            'Choices' => function($q) {
                                return $q->order(['Choices.weight DESC', 'Choices.id ASC']);
                            },
                            'QuestionTypes' => function($q) {
                                return $q->select(['QuestionTypes.template_name', 'QuestionTypes.id', 'QuestionTypes.multiple_choices']);
                            }

                        ])
                        ->order(['Questions.weight DESC', 'Questions.id ASC']);
                }
            ])
            ->first();

        // pr($data);
        // exit;

        if (empty($data)) {
            $this->Flash->error(__('Quiz not found'));
            return $this->redirect(array('controller' => 'quizzes', 'action' => 'shared'));
        }

        $data->question_type = $this->Quizzes->Questions->QuestionTypes->find('all')->select(['name', 'template_name', 'multiple_choices', 'id', 'type'])->toArray();

        // pr($data);
        // exit;

        if (empty($data->questios)) {
            $this->set('no_question', true);
        }

        $lang_strings['empty_question'] = __('NO_EMPTY_QUESTION');
        $lang_strings['same_choice'] = __('EMPTY_OR_SAME_NOT_ALLOWED');
        $lang_strings['single_greater'] = __('ONE_CHOICE_GREATER_THAN_ZERO');
        $lang_strings['correct_answer'] = __('SEPARATE_SEMICOLON');
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

        // Load available classes (created by admin)
        $this->loadModel('Subjects');
        $classOptions = $this->Subjects->find('list')
        ->where([
            'Subjects.isactive' => 1,
            'Subjects.is_del IS NULL',
            'Subjects.type' => 1
        ])
        ->toArray();

        // pr($classOptions);
        // exit;

        $subject_cond[] = array(
            'Subjects.isactive' => 1,
            'Subjects.is_del IS NULL',
            'Subjects.type IS NULL'
        );

        if (!empty($data->user->subjects)) {
            $selectedSubjects = json_decode($data->user->subjects, true);
            $subject_cond[] = array('Subjects.id' => $selectedSubjects);
        }

        $subjectOptions = $this->Subjects->find('list')->where($subject_cond)->toArray();
        // pr($subjectOptions);
        // exit;

        if (!empty($subjectOptions)) {
            $subjectOptions = array(0 => __('ALL_SUBJECT')) + $subjectOptions;
        }
        
        if (!empty($classOptions)) {
            $classOptions = array(0 => __('ALL_CLASS')) + $classOptions;
        }

        // pr($this->Session->read('Auth.User'));
        // exit;

        // pr($data);
        // exit;

        $this->set('data', $data);
        $this->set(compact('lang_strings', 'classOptions', 'subjectOptions'));

        //$this->render('\Quizzes\preview');
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
}
