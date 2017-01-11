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
        'limit' => 3
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
        if ($this->Auth->user('account_level') != 51) {
            $this->Flash->success(__('No permission!'));
            return $this->redirect(['controller' => 'users', 'action' => 'logout', 'prefix' => false]);
        }
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
            );
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
    public function manage_share() {
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
    public function preview($quiz_id = null) {
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
}
