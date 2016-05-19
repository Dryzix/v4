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

class HomeController extends Controller
{
    protected $tpl = 'tpl/templates/test.tpl';

    public function index(){
        Motor::getTemplater()->injectVars(['title' => 'Home']);
        return $this->render('tpl/views/test.tpl');
    }

    public function index2($id){
        Motor::getTemplater()->injectVars(['id' => $id]);
        return Motor::getTemplater()->loadTemplate(Motor::getConf('site')->get('rootPath') . 'tpl/tpl2.tpl');
    }
}