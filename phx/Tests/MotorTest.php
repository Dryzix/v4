<?php
/**
 * Created by PhpStorm.
 * User: Tom
 * Date: 15/01/2016
 * Time: 12:00
 */

namespace PHX\Test;

require '../vendor/autoload.php';


use PHX\Core\Motor;
use PHX\Core\Templater\Templater;

class Test extends \PHPUnit_Framework_TestCase
{
    public function testGetTemplater()
    {
        $motor = Motor::getTemplater();
        $this->assertEquals($motor, new Templater());
    }

}
