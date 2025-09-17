<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class StudentsController extends Controller {
    public function __construct()
    {
        parent::__construct();
        $this->call->model('StudentsModel');
        $this->call->library('pagination'); 
    }

    public function index()
    {
        // Current page
        $page = (int)($this->io->get('page') ?? 1);
        if ($page < 1) $page = 1;

        // Search query
        $q = trim($this->io->get('q') ?? '');

        // Records per page
        $records_per_page = 5;

        // Fetch data from model
        $all = $this->StudentsModel->page($q, $records_per_page, $page);
        $data['students'] = $all['records'];
        $total_rows = $all['total_rows'];

        // ✅ Pagination setup
        $this->pagination->set_options([
            'first_link'     => '⏮ First',
            'last_link'      => 'Last ⏭',
            'next_link'      => 'Next →',
            'prev_link'      => '← Prev',
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

        // ✅ Correct base URL (keeps search if present)
        $base_url = site_url('students') . (!empty($q) ? '?q=' . urlencode($q) : '');
        $this->pagination->initialize($total_rows, $records_per_page, $page, $base_url);

        $data['page'] = $this->pagination->paginate();

        // Render students/index.php
        $this->call->view('students/index', $data);
    }

    public function create()
    {
        if ($this->io->method() == 'post') {
            $data = [
                'first_name' => $this->io->post('first_name'),
                'last_name'  => $this->io->post('last_name'),
                'email'      => $this->io->post('email')
            ];

            if ($this->StudentsModel->insert($data)) {
                redirect('students');
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
            $data = [
                'first_name' => $this->io->post('first_name'),
                'last_name'  => $this->io->post('last_name'),
                'email'      => $this->io->post('email')
            ];

            if ($this->StudentsModel->update($id, $data)) {
                redirect('students');
            } else {
                echo 'Error updating student.';
            }
        } else {
            $this->call->view('students/update', ['student' => $student]);
        }
    }

    public function delete($id)
    {
        if ($this->StudentsModel->delete($id)) {
            redirect('students');
        } else {
            echo 'Error deleting student.';
        }
    }
}
