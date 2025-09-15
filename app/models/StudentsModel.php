<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class StudentsModel extends Model {
    protected $table = 'students';
    protected $primary_key = 'id';
    protected $allowed_fields = ['first_name', 'last_name', 'email']; // ✅ allowed fields
    protected $validation_rules = [
        'first_name' => 'required|min_length[2]|max_length[100]',
        'last_name'  => 'required|min_length[2]|max_length[100]',
        'email'      => 'required|valid_email|max_length[150]'
    ];

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Pagination-enabled query
     * @param string $q Search string
     * @param int|null $records_per_page Rows per page
     * @param int|null $page Current page number
     * @return array ['total_rows' => int, 'records' => array]
     */
    public function page($q = '', $records_per_page = null, $page = null)
    {
        $query = $this->db->table($this->table);

        // ✅ Add search (first_name, last_name, email)
        if (!empty($q)) {
            $query->like('first_name', $q)
                  ->or_like('last_name', $q)
                  ->or_like('email', $q);
        }

        // ✅ Count total rows
        $countQuery = clone $query;
        $countResult = $countQuery->select_count('*', 'count')->get();

        $total_rows = (is_array($countResult) && isset($countResult[0]['count']))
            ? (int) $countResult[0]['count']
            : 0;

        // ✅ If no pagination, return everything
        if (is_null($page) || is_null($records_per_page)) {
            return [
                'total_rows' => $total_rows,
                'records'    => $query->get_all()
            ];
        }

        // ✅ Apply pagination (per_page, page_number)
        $records = $query->pagination($records_per_page, $page)->get_all();

        return [
            'total_rows' => $total_rows,
            'records'    => $records
        ];
    }
}
