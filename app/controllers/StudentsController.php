<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class StudentsController extends Controller {
    public function __construct()
    {
        parent::__construct();
        $this->call->model('StudentsModel');
        $this->call->library('pagination'); // ✅ pagination library
    }

    public function index()
    {
        // Current page (ensure integer)
        $page = 1;
        if (isset($_GET['page']) && !empty($_GET['page'])) {
            $page = (int) $this->io->get('page');
            if ($page < 1) $page = 1;
        }

        // Search query
        $q = '';
        if (isset($_GET['q']) && !empty($_GET['q'])) {
            $q = trim($this->io->get('q'));
        }

        $records_per_page = 5;

        // Get paginated data from model
        $all = $this->StudentsModel->page($q, $records_per_page, $page);
        $data['students'] = $all['records'];
        $total_rows = $all['total_rows'];

        // Pagination setup (Bootstrap 5)
        $this->pagination->set_options([
            'first_link'     => '⏮ First',
            'last_link'      => 'Last ⏭',
            'next_link'      => 'Next →',
            'prev_link'      => '← Prev',
            'page_delimiter' => '&page=',
            'full_tag_open'  => '<ul class="pagination justify-content-center mt-3">', 
            'full_tag_close' => '</ul>',
            'num_tag_open'   => '<li class="page-item"><span class="page-link">',
            'num_tag_close'  => '</span></li>',
            'cur_tag_open'   => '<li class="page-item active"><span class="page-link">',
            'cur_tag_close'  => '</span></li>',
            'prev_tag_open'  => '<li class="page-item"><span class="page-link">',
            'prev_tag_close' => '</span></li>',
            'next_tag_open'  => '<li class="page-item"><span class="page-link">',
            'next_tag_close' => '</span></li>',
            'first_tag_open' => '<li class="page-item"><span class="page-link">',
            'first_tag_close'=> '</span></li>',
            'last_tag_open'  => '<li class="page-item"><span class="page-link">',
            'last_tag_close' => '</span></li>',
        ]);
        $this->pagination->set_theme('default');

        // ✅ FIXED: base URL now points to /students instead of root
        $this->pagination->initialize(
            $total_rows,
            $records_per_page,
            $page,
            site_url('students') . '?q=' . urlencode($q)
        );

        $data['page'] = $this->pagination->paginate();

        // Render view
        $this->call->view('students/index', $data);
    }

    public function create()
    {
        if ($this->io->method() == 'post') {
            $firstname = $this->io->post('first_name');
            $lastname  = $this->io->post('last_name');
            $email     = $this->io->post('email');

            $data = [
                'first_name' => $firstname,
                'last_name'  => $lastname,
                'email'      => $email
            ];

            if ($this->StudentsModel->insert($data)) {
                redirect();
            } else {
                echo 'Error creating student.';
            }
        } else {
            $this->call->view('students/create');
        }
    }

    public function update($id)
    {
        $student = $this->StudentsModel->find($id);
        if (!$student) {
            echo "Student not found.";
            return;
        }

        if ($this->io->method() == 'post') {
            $firstname = $this->io->post('first_name');
            $lastname  = $this->io->post('last_name');
            $email     = $this->io->post('email');

            $data = [
                'first_name' => $firstname,
                'last_name'  => $lastname,
                'email'      => $email
            ];

            if ($this->StudentsModel->update($id, $data)) {
                redirect();
            } else {
                echo 'Error updating student.';
            }
        } else {
            $data['student'] = $student;
            $this->call->view('students/update', $data);
        }
    }

    public function delete($id)
    {
        if ($this->StudentsModel->delete($id)) {
            redirect();
        } else {
            echo 'Error deleting student.';
        }
    }
}
