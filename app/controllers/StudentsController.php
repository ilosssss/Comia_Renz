<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

/**
 * Controller: StudentsController
 * 
 * Automatically generated via CLI.
 */
class StudentsController extends Controller {
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->call->model('StudentsModel');
        $data['students'] = $this->StudentsModel->all();
        $this->call->view('students/index', $data);
    }

    function create(){
        if ($this->io->method() == 'post') {
        $firstname= $this->io->post('first_name');
        $lastname= $this->io->post('last_name');
        $email= $this->io->post('email');

        $data = array(
            'first_name' => $firstname,
            'last_name' => $lastname,
            'email' => $email
        );
        if ($this->StudentsModel->insert($data)) {
           redirect(site_url('students'));
        }else{
            echo 'Error creating student.';
        }
    }else{
        $this->call->view('students/create');
    }
    }
    function update($id){
    $students = $this->StudentsModel->find($id);
    if(!$students) {
        echo "Students not found.";
        return;
    }
    if ($this->io->method() == 'post') {
        $firstname= $this->io->post('first_name');
        $lastname= $this->io->post('last_name');
        $email= $this->io->post('email');

        $data = array(
            'first_name' => $firstname,
            'last_name' => $lastname,
            'email' => $email
        );
        if ($this->StudentsModel->update($id, $data)) {
           redirect(uri: site_url('students'));
        }else{
            echo 'Error updating student.';
        }
    }else{
        $data['student'] = $students;
        $this->call->view('students/update', $data);
    }
   
    }
     function delete($id){
        if($this->StudentsModel->delete($id)){
        redirect(uri: site_url('students'));
    }else{
        echo 'Error deleting student.';
    }
    }
}