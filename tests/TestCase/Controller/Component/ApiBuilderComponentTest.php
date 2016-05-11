<?php
namespace Api\Test\TestCase\Controller\Component;

use Api\Controller\Component\ApiBuilderComponent;
use Cake\Controller\ComponentRegistry;
use Cake\TestSuite\TestCase;

/**
 * Api\Controller\Component\ApiBuilderComponent Test Case
 */
class ApiBuilderComponentTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \Api\Controller\Component\ApiBuilderComponent
     */
    public $ApiBuilder;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $registry = new ComponentRegistry();
        $this->ApiBuilder = new ApiBuilderComponent($registry);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->ApiBuilder);

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
