<?php
namespace Api\Test\TestCase\Model\Behavior;

use Api\Model\Behavior\CursorHelperBehavior;
use Cake\TestSuite\TestCase;

/**
 * Api\Model\Behavior\CursorHelperBehavior Test Case
 */
class CursorHelperBehaviorTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \Api\Model\Behavior\CursorHelperBehavior
     */
    public $CursorHelper;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->CursorHelper = new CursorHelperBehavior();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->CursorHelper);

        parent::tearDown();
    }

    /**
     * Test initial setup
     *
     * @return void
     */
    public function testInitialization()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
