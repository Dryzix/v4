<?php
/**
 * Created by PhpStorm.
 * User: Tom
 * Date: 18/01/2016
 * Time: 11:12
 */

namespace SITE\Controller;


use PHX\Core\Controller\Controller;
use PHX\Core\Motor;

class SimpleController extends Controller
{
    protected $tpl = 'tpl/templates/test.tpl';

    public function loop(){
        Motor::getTemplater()->injectVars(['title' => 'Loop']);
        return $this->render('tpl/views/simple/loop.tpl');
    }

    public function vars(){
        Motor::getTemplater()->injectVars(['title' => 'Variables']);
        return $this->render('tpl/views/simple/vars.tpl');
    }
}