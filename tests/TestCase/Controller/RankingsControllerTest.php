<?php
namespace App\Test\TestCase\Controller;

use App\Controller\RankingsController;
use Cake\TestSuite\IntegrationTestCase;

/**
 * App\Controller\RankingsController Test Case
 */
class RankingsControllerTest extends IntegrationTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.rankings',
        'app.quizzes',
        'app.users',
        'app.helps',
        'app.imported_quizzes',
        'app.statistics',
        'app.randoms',
        'app.questions',
        'app.question_types',
        'app.answers',
        'app.choices',
        'app.students'
    ];

    /**
     * Test index method
     *
     * @return void
     */
    public function testIndex()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test view method
     *
     * @return void
     */
    public function testView()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test add method
     *
     * @return void
     */
    public function testAdd()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test edit method
     *
     * @return void
     */
    public function testEdit()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test delete method
     *
     * @return void
     */
    public function testDelete()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
