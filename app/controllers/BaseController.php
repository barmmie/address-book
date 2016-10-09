<?php
namespace App\Controllers;


use App\Library\Csrf;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Twig_Environment;
use Valitron\Validator;

class BaseController
{

    /**
     * @var \Plates
     */
    private $twig;
    /**
     * @var Request
     */
    public $request;
    /**
     * @var PDO
     */
    private $pdo;
    /**
     * @var Session
     */
    public $session;
    /**
     * @var Csrf
     */
    private $csrf;

    public function __construct(Twig_Environment $twig, Request $request, \PDO $pdo, Session $session, Csrf $csrf)
    {

        $this->twig = $twig;
        $this->request = $request;
        $this->db = $pdo;
        $this->session = $session;
        $this->csrf = $csrf;
    }


    public function _view($path, $vars = [])
    {

        $vars['_csrfToken_'] = $this->csrf->getCSRFToken();
        $vars['_infos_'] = $this->session->getFlashBag()->get('info');
        $vars['_errors_'] = $this->session->getFlashBag()->get('error');
        $vars['_formErrors_'] = $this->session->getFlashBag()->get('errorBag');
        $vars['_oldInput_'] = $this->session->getFlashBag()->get('oldInputBag');

        return new Response($this->twig->render("$path.html.twig", $vars));
    }

    public function _redirect($url)
    {
        return new RedirectResponse($url);
    }

    public function _withFormErrors($errors)
    {
        $this->session->getFlashBag()->set('errorBag', $errors);

        return $this;
    }

    public function _withError($error)
    {
        $this->session->getFlashBag()->add('error', $error);

        return $this;
    }

    public function _withInfo($info)
    {
        $this->session->getFlashBag()->add('info', $info);

        return $this;
    }

    public function _withOldInput($input)
    {
        $this->session->getFlashBag()->set('oldInputBag', $input);

        return $this;
    }

    public function validateCsrf() {
        return $this->request->getMethod() == 'POST' && $this->csrf->validateCSRFToken($this->request->get('_csrf_token'));

    }

    public function _getSanitizedInput($name, $filter = FILTER_SANITIZE_STRING)
    {
        $value = $this->request->get($name);
        $sanitized_value = filter_var($value, $filter);
        return $sanitized_value;
    }
}