<?php
namespace Api\Test\TestCase\Shell\Task;

use Api\Shell\Task\TransformerTask;
use Cake\TestSuite\TestCase;

/**
 * Api\Shell\Task\TransformerTask Test Case
 */
class TransformerTaskTest extends TestCase
{

    /**
     * ConsoleIo mock
     *
     * @var \Cake\Console\ConsoleIo|\PHPUnit_Framework_MockObject_MockObject
     */
    public $io;

    /**
     * Test subject
     *
     * @var \Api\Shell\Task\TransformerTask
     */
    public $Transformer;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->io = $this->getMock('Cake\Console\ConsoleIo');

        $this->Transformer = $this->getMock('Api\Shell\Task\TransformerTask', [], [$this->io]);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Transformer);

        parent::tearDown();
    }

    /**
     * Test main method
     *
     * @return void
     */
    public function testMain()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
