<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\HelpsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\HelpsTable Test Case
 */
class HelpsTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\HelpsTable
     */
    public $Helps;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.helps',
        'app.users',
        'app.imported_quizzes',
        'app.quizzes',
        'app.statistics'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('Helps') ? [] : ['className' => 'App\Model\Table\HelpsTable'];
        $this->Helps = TableRegistry::get('Helps', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Helps);

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
