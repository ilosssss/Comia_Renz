<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class StudentsController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        // ✅ Load your StudentsModel (must match file/class name)
        $this->call->model('StudentsModel');
    }

    /**
     * Show students list with pagination + search
     */
    public function index()
    {
        // ✅ Use io->get safely with default values
        $q     = $this->io->get('q') ?? '';
        $page  = (int) ($this->io->get('page') ?? 1);
        $limit = 5; // rows per page

        // ✅ Get paginated records
        $result = $this->StudentsModel->page($q, $limit, $page);

        $students   = $result['records'];
        $total_rows = $result['total_rows'];

        // ✅ Simple pagination links
        $total_pages = ceil($total_rows / $limit);
        $pagination = '';
        if ($total_pages > 1) {
            $pagination .= '<nav><ul class="pagination">';
            for ($i = 1; $i <= $total_pages; $i++) {
                $active = ($i == $page) ? 'active' : '';
                $pagination .= '<li class="page-item '.$active.'">
                                  <a class="page-link" href="'.site_url('students?page='.$i.'&q='.urlencode($q)).'">'.$i.'</a>
                                </li>';
            }
            $pagination .= '</ul></nav>';
        }

        // ✅ Pass data to view
        $data = [
            'students' => $students,
            'page'     => $pagination,
            'q'        => $q
        ];

        $this->call->view('students/index', $data);
    }

    /**
     * Show create form
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
        $data = [
            'first_name' => $this->io->post('first_name'),
            'last_name'  => $this->io->post('last_name'),
            'email'      => $this->io->post('email')
        ];

        if ($this->StudentsModel->insert($data)) {
            redirect('students');
        } else {
            show_error('Failed to insert student.');
        }
    }

    /**
     * Show update form
     */
    public function update($id)
    {
        $student = $this->StudentsModel->get_where(['id' => $id]);
        $this->call->view('students/update', ['student' => $student]);
    }

    /**
     * Save updated student
     */
    public function save($id)
    {
        $data = [
            'first_name' => $this->io->post('first_name'),
            'last_name'  => $this->io->post('last_name'),
            'email'      => $this->io->post('email')
        ];

        if ($this->StudentsModel->update($id, $data)) {
            redirect('students');
        } else {
            show_error('Failed to update student.');
        }
    }

    /**
     * Delete student
     */
    public function delete($id)
    {
        if ($this->StudentsModel->delete($id)) {
            redirect('students');
        } else {
            show_error('Failed to delete student.');
        }
    }
}
