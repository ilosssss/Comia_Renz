<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class StudentsModel extends Model {
    protected $table = 'students';
    protected $primary_key = 'id';
    protected $allowed_fields = ['first_name', 'last_name', 'email'];
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
     * @param int $records_per_page Rows per page
     * @param int $page Current page number
     * @return array ['total_rows' => int, 'records' => array]
     */
    public function page($q = '', $records_per_page = 5, $page = 1)
    {
        $offset = ($page - 1) * $records_per_page;

        // Count total rows
        $countQuery = $this->db->table($this->table);
        if (!empty($q)) {
            $countQuery->like('first_name', $q)
                       ->or_like('last_name', $q)
                       ->or_like('email', $q);
        }
        $countResult = $countQuery->select_count('*', 'count')->get();
        $total_rows = isset($countResult['count']) ? (int) $countResult['count'] : 0;

        // Get paginated records
        $query = $this->db->table($this->table);
        if (!empty($q)) {
            $query->like('first_name', $q)
                  ->or_like('last_name', $q)
                  ->or_like('email', $q);
        }
        $records = $query->limit($records_per_page, $offset)->get_all();

        return [
            'total_rows' => $total_rows,
            'records'    => $records
        ];
    }
}

?>
<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class UserModel extends Model {
    protected $table = 'users';
    protected $primary_key = 'id';
    protected $allowed_fields = ['username', 'email'];
    protected $validation_rules = [
        'username' => 'required|min_length[3]|max_length[100]',
        'email'    => 'required|valid_email|max_length[150]'
    ];

    public function __construct()
    {
        parent::__construct();
    }

    public function page($q = '', $records_per_page = null, $page = null)
    {
        if (is_null($page)) {
            return [
                'total_rows' => $this->db->table($this->table)->count_all(),
                'records'    => $this->db->table($this->table)->get_all()
            ];
        } else {
            $query = $this->db->table($this->table);

            if (!empty($q)) {
                $query->like('username', '%'.$q.'%')
                      ->or_like('email', '%'.$q.'%');
            }

            $countQuery = clone $query;
            $data['total_rows'] = $countQuery->select_count('*', 'count')->get()['count'];

            $data['records'] = $query->pagination($records_per_page, $page)->get_all();

            return $data;
        }
    }
}
?>