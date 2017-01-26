<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\QuestionTypesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\QuestionTypesTable Test Case
 */
class QuestionTypesTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\QuestionTypesTable
     */
    public $QuestionTypes;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.question_types',
        'app.questions',
        'app.quizzes',
        'app.users',
        'app.helps',
        'app.downloads',
        'app.statistics',
        'app.randoms',
        'app.rankings',
        'app.students',
        'app.answers',
        'app.choices'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('QuestionTypes') ? [] : ['className' => 'App\Model\Table\QuestionTypesTable'];
        $this->QuestionTypes = TableRegistry::get('QuestionTypes', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->QuestionTypes);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
