<?php
/**
 * Created by PhpStorm.
 * User: barmmie
 * Date: 04/10/2016
 * Time: 12:33 PM
 */

namespace App\Controllers;


use Valitron\Validator;

class ContactController extends BaseController
{

    public function index()
    {

        $contatcts = [];

        $query = $this->db->prepare("SELECT * FROM contacts");

        if($query) {
            $query->execute();
            $contacts = $query->fetchall();
        }

        return $this->_view('contacts/list', compact('contacts'));
    }

    public function create()
    {

        return $this->_view('contacts/add');
    }

    public function store()
    {

        $values = [
            'name' => $this->request->get('name'),
            'email' => $this->request->get('email')
        ];
        
        if(! $this->validateCsrf()) {
            return $this->_withOldInput($values)
                        ->_withError('Invalid or expired CSRF Token . Try again')
                        ->_redirect('/add');
        }

        $v = new Validator($values);

        $v->addRule('unique', function($field, $value, array $params) {

            $table = $params[0];
            $query = $this->db->prepare("SELECT count(*) FROM $table WHERE $field = :query");

            if($query) {

                $results = $query->execute(['query' => $value]);
                if($query->fetchColumn() > 0) {
                    return false;
                } else {
                    return true;
                }
            }

            return false;

        });

        $v->rule('required', ['email', 'name']);
        $v->rule('email', 'email');
        $v->rule('unique', 'email', 'contacts')->message('Email address already exists in database.');

        if(! $v->validate()) {
            return $this->_withFormErrors($v->errors())
                        ->_withOldInput($values)
                        ->_redirect('/add');
        }


        try{
            $statement = $this->db->prepare("INSERT INTO contacts(name, email, created_at)
                                    VALUES(:name, :email, :created_at)");
            $statement->execute($values);
        } catch(\Exception $e) {
            return $this->_withOldInput($values)
                        ->_withError('An error occured: '.$e->getMessage())
                        ->_redirect('/add');
        }


        return $this->_withInfo('New user added succesfully')
                    ->_redirect('/list');
    }

   
}


