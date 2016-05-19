<?php
/**
 * Created by PhpStorm.
 * User: Tom
 * Date: 26/01/2016
 * Time: 10:10
 */

namespace SITE\Task;


use PHX\Core\Task\Task;

class FormTask extends Task
{
    public function __construct()
    {
        $this->defaultMessageBox = '[data-box="ajax"]';
        parent::__construct();
    }

    public function run()
    {
        if(isset($_POST['name']))
        {
            $this->removeClass('fail');
            $this->addClass('ok');
            $this->setHtml('<span style="color:green">Succes</span>');
            $this->emptyForm();
            return $this->build();
        }
        else
        {
            $this->error();
            $this->setClass('fail'); // Ici équivaut a $this->removeClass('ok'); puis  $this->addClass('fail');
            $this->setText('Erreur');
            $this->addCallback('test', 'Une erreur est survenue');
            return $this->build();
        }
    }
}