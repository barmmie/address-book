<?php namespace App\Library;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Created by PhpStorm.
 * User: barmmie
 * Date: 08/10/2016
 * Time: 3:43 PM
 */
class Csrf
{

    /**
     * @var \RandomLib\Factory
     */
    private $generator;
    private $session;

    public function __construct(\RandomLib\Factory $factory, Session $session)
    {

        $this->generator = $factory->getMediumStrengthGenerator();
        $this->session = $session;
    }
    function getCSRFToken() {
        $nonce = $this->generator->generateString(64);

        $this->session->set("csrf_tokens/$nonce", true);

        return $nonce;
    }

    function validateCSRFToken($token) {

        if($this->session->has("csrf_tokens/$token")) {
            $this->session->remove("csrf_tokens/$token");
            return true;
        }

        return false;
    }

}