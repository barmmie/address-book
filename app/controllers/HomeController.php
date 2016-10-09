<?php
/**
 * Created by PhpStorm.
 * User: barmmie
 * Date: 04/10/2016
 * Time: 12:32 PM
 */

namespace App\Controllers;

use Twig_Environment;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends BaseController
{

    public function welcome() {
        return $this->_view('home/welcome');
    }
}