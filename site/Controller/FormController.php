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
use SITE\Task\FormTask;

class FormController extends Controller
{
    protected $tpl = 'tpl/templates/test.tpl';

    public function test(){
        return (new FormTask())->run();
    }

    public function index2($id){
        Motor::getTemplater()->injectVars(['id' => $id]);
        return Motor::getTemplater()->loadTemplate(Motor::getConf('site')->get('rootPath') . 'tpl/tpl2.tpl');
    }
}