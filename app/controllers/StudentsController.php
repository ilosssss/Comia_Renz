<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class StudentsController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->call->model('StudentsModel');
    }

    public function index()
    {
        // ✅ Safely get query params
        $page = (int) ($this->io->get('page') ?? 1);
        if ($page < 1) $page = 1;

        $q = $this->io->get('q') ?? '';

        $records_per_page = 5;

        // ✅ Fetch records with pagination
        $all = $this->StudentsModel->page($q, $records_per_page, $page);

        $data['students'] = $all['records'];
        $total_rows = $all['total_rows'];
        $data['search'] = $q; // pass to view

        // ✅ Build pagination links
        $this->call->library('pagination');
        $config = [
            'base_url' => site_url('students'),
            'total_rows' => $total_rows,
            'per_page' => $records_per_page,
            'query_string_segment' => 'page',
            'page_query_string' => TRUE,
            'full_tag_open' => '<ul class="pagination">',
            'full_tag_close' => '</ul>',
            'num_tag_open' => '<li class="page-item"><span class="page-link">',
            'num_tag_close' => '</span></li>',
            'cur_tag_open' => '<li class="page-item active"><span class="page-link">',
            'cur_tag_close' => '</span></li>',
            'next_tag_open' => '<li class="page-item"><span class="page-link">',
            'next_tag_close' => '</span></li>',
            'prev_tag_open' => '<li class="page-item"><span class="page-link">',
            'prev_tag_close' => '</span></li>'
        ];
        $this->pagination->initialize($config);
        $data['page'] = $this->pagination->create_links();

        $this->call->view('students/index', $data);
    }

    public function create()
    {
        if ($this->form_validation->submitted())
        {
            $data = [
                'first_name' => $this->io->post('first_name', TRUE),
                'last_name'  => $this->io->post('last_name', TRUE),
                'email'      => $this->io->post('email', TRUE),
            ];

            if ($this->StudentsModel->insert($data)) {
                redirect('students');
            } else {
                show_error('Failed to create student');
            }
        }

        $this->call->view('students/create');
    }

    public function update($id)
    {
        if ($this->form_validation->submitted())
        {
            $data = [
                'first_name' => $this->io->post('first_name', TRUE),
                'last_name'  => $this->io->post('last_name', TRUE),
                'email'      => $this->io->post('email', TRUE),
            ];

            if ($this->StudentsModel->update($id, $data)) {
                redirect('students');
            } else {
                show_error('Failed to update student');
            }
        }

        $student = $this->StudentsModel->get_where(['id' => $id]);
        $this->call->view('students/update', ['student' => $student]);
    }

    public function delete($id)
    {
        if ($this->StudentsModel->delete($id)) {
            redirect('students');
        } else {
            show_error('Failed to delete student');
        }
    }
}
