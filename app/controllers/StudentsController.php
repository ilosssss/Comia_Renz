<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class StudentsController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        // Load the model
        $this->call->model('Student_model');
    }

    /**
     * Show all students with search + pagination
     */
    public function index()
    {
        // ✅ Fix: Safe fetching of page
        $page = isset($_GET['page']) ? (int) $this->io->get('page') : 1;
        if ($page < 1) $page = 1;

        $limit  = 5;                          // records per page
        $offset = ($page - 1) * $limit;

        // ✅ Fix: Safe fetching of search query
        $q = isset($_GET['q']) ? trim($this->io->get('q')) : '';

        // Get students with search & pagination
        $students = $this->Student_model->get_students($limit, $offset, $q);
        $total    = $this->Student_model->count_students($q);

        // Prepare pagination links
        $this->call->library('pagination');
        $config = [
            'base_url'     => site_url('students'),
            'total_rows'   => $total,
            'per_page'     => $limit,
            'cur_page'     => $page,
            'page_query_string' => true,
            'query_string_segment' => 'page',
        ];
        $this->pagination->initialize($config);
        $data['page'] = $this->pagination->create_links();

        // Pass data to view
        $data['students'] = $students;
        $data['q']        = $q;

        $this->call->view('students/index', $data);
    }

    /**
     * Show the create student form
     */
    public function create()
    {
        $this->call->view('students/create');
    }

    /**
     * Store new student
     */
    public function store()
    {
        $first = trim($this->io->post('first_name'));
        $last  = trim($this->io->post('last_name'));
        $email = trim($this->io->post('email'));

        if ($this->Student_model->insert($first, $last, $email)) {
            redirect('students');
        } else {
            echo "❌ Failed to save student.";
        }
    }

    /**
     * Show update form
     */
    public function update($id)
    {
        $student = $this->Student_model->get_student($id);

        if (!$student) {
            show_404();
            return;
        }

        $data['student'] = $student;
        $this->call->view('students/update', $data);
    }

    /**
     * Save updated student
     */
    public function save($id)
    {
        $first = trim($this->io->post('first_name'));
        $last  = trim($this->io->post('last_name'));
        $email = trim($this->io->post('email'));

        if ($this->Student_model->update($id, $first, $last, $email)) {
            redirect('students');
        } else {
            echo "❌ Failed to update student.";
        }
    }

    /**
     * Delete student
     */
    public function delete($id)
    {
        if ($this->Student_model->delete($id)) {
            redirect('students');
        } else {
            echo "❌ Failed to delete student.";
        }
    }
}
