<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\QuizzesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\QuizzesTable Test Case
 */
class QuizzesTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\QuizzesTable
     */
    public $Quizzes;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.quizzes',
        'app.users',
        'app.helps',
        'app.imported_quizzes',
        'app.statistics',
        'app.randoms',
        'app.questions',
        'app.rankings',
        'app.students'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('Quizzes') ? [] : ['className' => 'App\Model\Table\QuizzesTable'];
        $this->Quizzes = TableRegistry::get('Quizzes', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Quizzes);

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

    /**
     * Test buildRules method
     *
     * @return void
     */
    public function testBuildRules()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
