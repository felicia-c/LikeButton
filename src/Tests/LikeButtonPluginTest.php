<?php

namespace AHWEBDEV\FacebookAWD\Plugin\LikeButton\Tests;

use AHWEBDEV\FacebookAWD\FacebookAWD;

class LikeButtonPluginTest extends \WP_UnitTestCase
{
    /**
     * @var FacebookAWD
     */
    protected $object;

    public function setUp()
    {
        parent::setUp();
        $this->object = getFacebookAWD();
    }

    /**
     * @covers AHWEBDEV\FacebookAWD\FacebookAWD::boot
     * @todo   Implement testBoot().
     */
    public function testBoot()
    {
        $instance = FacebookAwd::boot();
        $this->assertTrue($instance instanceof FacebookAWD);
    }

}
