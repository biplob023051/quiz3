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
        $this->set('title_for_layout',__('Shared Quiz List'));

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
        $lang_strings['decline_question'] = __('Decline the quiz');
        $lang_strings['cancel'] = __('Cancel');
        $lang_strings['submit'] = __('Submit');
        $lang_strings['decline_reason'] = __('Enter decline reason if any!');
        $lang_strings['approve_question'] = __('Approve the quiz');
        $lang_strings['approve_body'] = __('If you approve the quiz, you have always option to decline sharing!');

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
                $this->Flash->error(__('Something went wrong, please try again later!'));
            } else {
                $message = ($this->request->data['is_approve'] == 1) ? __('You have successfully approved!') : __('You have successfully declined!');
                unset($this->request->data['random_id']);
                $quiz = $this->Quizzes->patchEntity($quiz, $this->request->data, ['validate' => '']);
                // pr($quiz);
                // exit;
                if ($this->Quizzes->save($quiz)) {
                     $this->Flash->success($message);
                } else {
                     $this->Flash->error(__('Something went wrong, please try again later!'));
                }
            }
        }
        $this->redirect($this->referer());
    }

    // Admin preview quiz
    public function preview($quiz_id = null) {
        $this->isAdminUser();
        if (empty($quiz_id)) {
            $this->Flash->error(__('No direct access to this location!'));
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

        //$this->render('\Quizzes\preview');
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
}
