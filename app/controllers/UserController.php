<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class UserController extends Controller {
    private $records_per_page = 5;

    public function __construct()
    {
        parent::__construct();
		$this->call->library(['auth', 'session']);
		if (!$this->auth->is_logged_in()) {
			redirect('auth/login');
			return;
		}
        $this->call->model('UserModel');
        $this->call->library('pagination');
    }

    public function view()
    {
        $page = (isset($_GET['page']) && $_GET['page'] !== '') ? (int) $_GET['page'] : 1;
        $q    = (isset($_GET['q']) && $_GET['q'] !== '') ? trim($_GET['q']) : '';

        $all   = $this->UserModel->page($q, $this->records_per_page, $page);
        $users = $all['records'];
        $total_rows = $all['total_rows'];

        $this->pagination->set_options([
            'first_link'     => '⏮ First',
            'last_link'      => 'Last ⏭',
            'next_link'      => 'Next →',
            'prev_link'      => '← Prev',
            'page_delimiter' => '&page='
        ]);
        $this->pagination->set_theme('bootstrap');
        $this->pagination->initialize(
            $total_rows,
            $this->records_per_page,
            $page,
            site_url('users/view') . '?q=' . urlencode($q)
        );

        $base_url = site_url('users/view') . '?q=' . urlencode($q);
        $pagination_html = $this->renderPagination($total_rows, $this->records_per_page, $page, $base_url);

        $isAdmin = $this->auth->has_role('admin');
        $showEmail = true; // show email to all users
        $colspan = $isAdmin ? 4 : 3;
        $headerRow = $isAdmin
            ? '<tr><th>ID</th><th>Player</th><th>Email</th><th>Actions</th></tr>'
            : '<tr><th>ID</th><th>Player</th><th>Email</th></tr>';
        $rows = '';
        if (!empty($users)) {
            foreach ($users as $u) {
                $actionButtons = '';
                if ($isAdmin) {
                    $actionButtons = '<div class="action-buttons">
                            <a href="' . $this->escape(site_url('users/update/'.$u['id'])) . '" class="btn btn-edit">
                                <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                                </svg>
                                Edit
                            </a>
                            <a href="' . $this->escape(site_url('users/delete/'.$u['id'])) . '" class="btn btn-delete">
                                <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
                                </svg>
                                Delete
                            </a>
                        </div>';
                }
                $rows .= '<tr class="table-row">'
                    . '<td class="table-cell id-cell">' . $this->escape($u['id']) . '</td>'
                    . '<td class="table-cell">'
                        . '<div class="user-info">'
                            . '<div class="avatar">' . strtoupper(substr($u['username'], 0, 2)) . '</div>'
                            . '<span class="username">' . $this->escape($u['username']) . '</span>'
                        . '</div>'
                    . '</td>'
                    . ($showEmail ? '<td class="table-cell email-cell">' . $this->escape($u['email']) . '</td>' : '')
                    . ($isAdmin ? '<td class="table-cell action-cell">' . $actionButtons . '</td>' : '')
                . '</tr>';
            }
        } else {
            $rows = '<tr><td colspan="'.$colspan.'" class="empty-state">
                <div class="empty-content">
                    <svg width="48" height="48" fill="currentColor" viewBox="0 0 24 24" style="opacity: 0.3;">
                        <path d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2ZM21 9V7L15 1H5C3.89 1 3 1.89 3 3V21C3 22.11 3.89 23 5 23H19C20.11 23 21 22.11 21 21V9M19 9H14V4H5V21H19V9Z"/>
                    </svg>
                    <h3>No players found</h3>
                    <p>Try adjusting your search or add your first player</p>
                </div>
            </td></tr>';
        }

        $escaped_q = $this->escape($q);
        $add_url = $this->escape(site_url('users/create'));
        $logout_url = $this->escape(site_url('auth/logout'));
        $add_button = '';
        if ($isAdmin) {
            $add_button = '<a href="'.$add_url.'" class="btn btn-success">
                            <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                            </svg>
                            Add Player
                        </a>';
        }
        $logout_button = '<a href="'.$logout_url.'" class="btn btn-secondary">Logout</a>';

        echo '<!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Player Management</title>
            <style>
                * { margin: 0; padding: 0; box-sizing: border-box; }
                body {
                    background: linear-gradient(135deg, #2d1b69 0%, #1a1a2e 100%);
                    min-height: 100vh;
                    font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
                    padding: 2rem;
                }
                .container { max-width: 1200px; margin: 0 auto; }
                .header { text-align: center; margin-bottom: 3rem; animation: fadeInDown 0.8s ease-out; }
                .header h1 {
                    font-size: 3rem; font-weight: 700; color: white;
                    text-shadow: 0 4px 20px rgba(0,0,0,0.3); margin-bottom: 0.5rem;
                }
                .header p { color: rgba(255,255,255,0.9); font-size: 1.1rem; font-weight: 300; }
                .search-section {
                    background: rgba(40, 44, 52, 0.9);
                    backdrop-filter: blur(20px);
                    border-radius: 20px;
                    padding: 2rem;
                    margin-bottom: 2rem;
                    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
                    border: 1px solid rgba(139, 92, 246, 0.2);
                    animation: fadeInUp 0.8s ease-out 0.2s both;
                }
                .search-form { display: flex; gap: 1rem; align-items: center; flex-wrap: wrap; }
                .search-input {
                    flex: 1; min-width: 300px; padding: 1rem 1.5rem; border: 2px solid #4a5568;
                    border-radius: 12px; font-size: 1rem; transition: all 0.3s ease; 
                    background: rgba(40, 44, 52, 0.8); color: #e2e8f0;
                }
                .search-input:focus { 
                    outline: none; border-color: #a855f7; 
                    box-shadow: 0 0 0 3px rgba(168, 85, 247, 0.1); 
                }
                .search-input::placeholder { color: #9ca3af; }
                .btn {
                    padding: 1rem 1.5rem; border: none; border-radius: 12px; font-size: 1rem;
                    font-weight: 600; cursor: pointer; text-decoration: none; display: inline-flex;
                    align-items: center; gap: 0.5rem; transition: all 0.3s ease; white-space: nowrap;
                }
                .btn-primary {
                    background: linear-gradient(135deg, #a855f7 0%, #8b5cf6 100%);
                    color: white; box-shadow: 0 4px 15px rgba(168, 85, 247, 0.3);
                }
                .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(168, 85, 247, 0.5); }
                .btn-success {
                    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
                    color: white; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
                }
                .btn-success:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4); }
                .table-container {
                    background: rgba(40, 44, 52, 0.9);
                    backdrop-filter: blur(20px);
                    border-radius: 20px;
                    overflow: hidden;
                    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
                    border: 1px solid rgba(139, 92, 246, 0.2);
                    animation: fadeInUp 0.8s ease-out 0.4s both;
                }
                .table { width: 100%; border-collapse: collapse; }
                .table-header { background: linear-gradient(135deg, #4a5568 0%, #2d3748 100%); color: white; }
                .table-header th {
                    padding: 1.5rem 1rem; text-align: left; font-weight: 600; font-size: 0.9rem;
                    text-transform: uppercase; letter-spacing: 0.05em;
                }
                .table-header th:nth-child(1) { width: 80px; }
                .table-header th:nth-child(2) { width: 300px; }
                .table-header th:nth-child(3) { width: 300px; }
                .table-header th:nth-child(4) { width: 200px; }
                .table-row { transition: all 0.3s ease; border-bottom: 1px solid #e2e8f0; }
                .table-row:hover { background: #f7fafc; transform: scale(1.01); }
                .table-cell { padding: 1.5rem 1rem; vertical-align: middle; }
                .id-cell { font-weight: 700; color: #667eea; font-size: 1.1rem; }
                .user-info { display: flex; align-items: center; gap: 1rem; }
                .avatar {
                    width: 40px; height: 40px; border-radius: 50%;
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    display: flex; align-items: center; justify-content: center;
                    color: white; font-weight: 700; font-size: 0.9rem;
                }
                .username { font-weight: 600; color: #2d3748; font-size: 1.1rem; }
                .email-cell { color: #718096; font-size: 0.95rem; }
                .action-buttons { display: flex; gap: 0.5rem; }
                .btn-edit {
                    background: linear-gradient(135deg, #a855f7 0%, #8b5cf6 100%);
                    color: white; padding: 0.5rem 1rem; font-size: 0.85rem;
                    box-shadow: 0 2px 8px rgba(168, 85, 247, 0.3);
                }
                .btn-edit:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(168, 85, 247, 0.4); }
                .btn-delete {
                    background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
                    color: white; padding: 0.5rem 1rem; font-size: 0.85rem;
                    box-shadow: 0 2px 8px rgba(107, 114, 128, 0.3);
                }
                .btn-delete:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(107, 114, 128, 0.4); }
                .pagination-container { padding: 2rem; text-align: center; background: rgba(40, 44, 52, 0.6); }
                .pagination {
                    display: inline-flex; align-items: center; gap: 0.5rem; 
                    background: rgba(30, 27, 75, 0.8);
                    padding: 1rem; border-radius: 12px; 
                    box-shadow: 0 4px 15px rgba(0,0,0,0.3);
                    border: 1px solid rgba(139, 92, 246, 0.2);
                }
                .pagination a, .pagination span {
                    padding: 0.5rem 1rem; border-radius: 8px; text-decoration: none; font-weight: 500;
                    transition: all 0.3s ease; min-width: 40px; text-align: center; color: #e5e7eb;
                }
                .pagination a { background: rgba(55, 65, 81, 0.8); border: 1px solid #4b5563; }
                .pagination a:hover {
                    background: #a855f7; color: white; transform: translateY(-1px);
                    box-shadow: 0 2px 8px rgba(168, 85, 247, 0.4);
                }
                .pagination .active {
                    background: linear-gradient(135deg, #a855f7 0%, #8b5cf6 100%);
                    color: white; font-weight: 700;
                }
                .pagination .disabled {
                    background: rgba(55, 65, 81, 0.4); color: #6b7280; cursor: not-allowed; 
                    border: 1px solid #374151;
                }
                .empty-state { padding: 4rem 2rem; text-align: center; }
                .empty-content h3 { color: #e5e7eb; margin: 1rem 0 0.5rem; font-size: 1.5rem; }
                .empty-content p { color: #9ca3af; font-size: 1rem; }
                @keyframes fadeInDown {
                    from { opacity: 0; transform: translateY(-30px); }
                    to { opacity: 1; transform: translateY(0); }
                }
                @keyframes fadeInUp {
                    from { opacity: 0; transform: translateY(30px); }
                    to { opacity: 1; transform: translateY(0); }
                }
                @media (max-width: 768px) {
                    body { padding: 1rem; }
                    .header h1 { font-size: 2rem; }
                    .search-form { flex-direction: column; align-items: stretch; }
                    .search-input { min-width: auto; }
                    .table-container { overflow-x: auto; }
                    .action-buttons { flex-direction: column; }
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>🎮 Player Management</h1>
                    <p>Manage your gaming community with style</p>
                </div>
                <div class="search-section">
                    <form method="GET" action="" class="search-form">
                        <input type="text" name="q" value="'.$escaped_q.'" placeholder="Search for players..." class="search-input">
                        <button type="submit" class="btn btn-primary">
                            <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                            </svg>
                            Search
                        </button>
                        '.$add_button.'
                        '.$logout_button.'
                    </form>
                </div>
                <div class="table-container">
                    <table class="table">
                        <thead class="table-header">
                            '.$headerRow.'
                        </thead>
                        <tbody>'.$rows.'</tbody>
                    </table>
                    <div class="pagination-container">'.$pagination_html.'</div>
                </div>
            </div>
        </body>
        </html>';
    }

    public function create()
    {
        if (!$this->auth->has_role('admin')) {
            redirect('users/view');
            return;
        }
        if ($this->io->method() === 'post') {
            $data = [
                'username' => $this->io->post('username'),
                'email'    => $this->io->post('email')
            ];
            try {
                $this->UserModel->insert($data);
                redirect('users/view');
            } catch (Exception $e) {
                $this->showError('creating player', $e);
            }
        } else {
            echo '<!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Add Player</title>
                <style>
                    * { margin: 0; padding: 0; box-sizing: border-box; }
                    body {
                        background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
                        min-height: 100vh; font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
                        display: flex; align-items: center; justify-content: center; padding: 2rem;
                    }
                    .form-container {
                        background: rgba(255,255,255,0.95); backdrop-filter: blur(20px);
                        border-radius: 20px; padding: 3rem; width: 100%; max-width: 500px;
                        box-shadow: 0 25px 50px rgba(0,0,0,0.15); animation: slideUp 0.8s ease-out;
                    }
                    .form-header { text-align: center; margin-bottom: 2rem; }
                    .form-header h1 {
                        font-size: 2.5rem; color: #2d3748; margin-bottom: 0.5rem; font-weight: 700;
                    }
                    .form-header p { color: #718096; font-size: 1.1rem; }
                    .form-group { margin-bottom: 1.5rem; }
                    .form-group label {
                        display: block; color: #4a5568; font-weight: 600;
                        margin-bottom: 0.5rem; font-size: 1rem;
                    }
                    .form-input {
                        width: 100%; padding: 1rem 1.5rem; border: 2px solid #e2e8f0;
                        border-radius: 12px; font-size: 1rem; transition: all 0.3s ease; background: white;
                    }
                    .form-input:focus {
                        outline: none; border-color: #48bb78;
                        box-shadow: 0 0 0 3px rgba(72, 187, 120, 0.1);
                    }
                    .form-actions { display: flex; gap: 1rem; margin-top: 2rem; }
                    .btn {
                        padding: 1rem 2rem; border: none; border-radius: 12px; font-size: 1rem;
                        font-weight: 600; cursor: pointer; text-decoration: none; display: inline-flex;
                        align-items: center; justify-content: center; gap: 0.5rem;
                        transition: all 0.3s ease; flex: 1;
                    }
                    .btn-success {
                        background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
                        color: white; box-shadow: 0 4px 15px rgba(72, 187, 120, 0.3);
                    }
                    .btn-success:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(72, 187, 120, 0.4); }
                    .btn-secondary {
                        background: #e2e8f0; color: #4a5568; border: 2px solid #cbd5e0;
                    }
                    .btn-secondary:hover { background: #cbd5e0; transform: translateY(-2px); }
                    @keyframes slideUp {
                        from { opacity: 0; transform: translateY(50px); }
                        to { opacity: 1; transform: translateY(0); }
                    }
                </style>
            </head>
            <body>
                <div class="form-container">
                    <div class="form-header">
                        <h1>➕ Add Player</h1>
                        <p>Create a new player profile</p>
                    </div>
                    <form method="post">
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" name="username" id="username" class="form-input" required placeholder="Enter username">
                        </div>
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" name="email" id="email" class="form-input" required placeholder="Enter email address">
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn btn-success">
                                <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/>
                                </svg>
                                Save Player
                            </button>
                            <a href="'. $this->escape(site_url('users/view')) .'" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </body>
            </html>';
        }
    }

    public function update($id)
    {
        if (!$this->auth->has_role('admin')) {
            redirect('users/view');
            return;
        }
        if ($this->io->method() === 'post') {
            $data = [
                'username' => $this->io->post('username'),
                'email'    => $this->io->post('email')
            ];
            try {
                $this->UserModel->update($id, $data);
                redirect('users/view');
            } catch (Exception $e) {
                $this->showError('updating player', $e);
            }
        } else {
            $user = $this->UserModel->find($id);
            if (!$user) {
                echo '<div style="background:#1E1B29; min-height:50vh; padding:20px; color:#E6E6E6;">Player not found</div>';
                return;
            }
            
            $escaped_username = $this->escape($user['username']);
            $escaped_email = $this->escape($user['email']);
            $back_url = $this->escape(site_url('users/view'));
            
            echo '<!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Edit Player</title>
                <style>
                    * { margin: 0; padding: 0; box-sizing: border-box; }
                    body {
                        background: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%);
                        min-height: 100vh; font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
                        display: flex; align-items: center; justify-content: center; padding: 2rem;
                    }
                    .form-container {
                        background: rgba(255,255,255,0.95); backdrop-filter: blur(20px);
                        border-radius: 20px; padding: 3rem; width: 100%; max-width: 500px;
                        box-shadow: 0 25px 50px rgba(0,0,0,0.15); animation: slideUp 0.8s ease-out;
                    }
                    .form-header { text-align: center; margin-bottom: 2rem; }
                    .form-header h1 {
                        font-size: 2.5rem; color: #2d3748; margin-bottom: 0.5rem; font-weight: 700;
                    }
                    .form-header p { color: #718096; font-size: 1.1rem; }
                    .form-group { margin-bottom: 1.5rem; }
                    .form-group label {
                        display: block; color: #4a5568; font-weight: 600;
                        margin-bottom: 0.5rem; font-size: 1rem;
                    }
                    .form-input {
                        width: 100%; padding: 1rem 1.5rem; border: 2px solid #e2e8f0;
                        border-radius: 12px; font-size: 1rem; transition: all 0.3s ease; background: white;
                    }
                    .form-input:focus {
                        outline: none; border-color: #ed8936;
                        box-shadow: 0 0 0 3px rgba(237, 137, 54, 0.1);
                    }
                    .form-actions { display: flex; gap: 1rem; margin-top: 2rem; }
                    .btn {
                        padding: 1rem 2rem; border: none; border-radius: 12px; font-size: 1rem;
                        font-weight: 600; cursor: pointer; text-decoration: none; display: inline-flex;
                        align-items: center; justify-content: center; gap: 0.5rem;
                        transition: all 0.3s ease; flex: 1;
                    }
                    .btn-primary {
                        background: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%);
                        color: white; box-shadow: 0 4px 15px rgba(237, 137, 54, 0.3);
                    }
                    .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(237, 137, 54, 0.4); }
                    .btn-secondary {
                        background: #e2e8f0; color: #4a5568; border: 2px solid #cbd5e0;
                    }
                    .btn-secondary:hover { background: #cbd5e0; transform: translateY(-2px); }
                    @keyframes slideUp {
                        from { opacity: 0; transform: translateY(50px); }
                        to { opacity: 1; transform: translateY(0); }
                    }
                </style>
            </head>
            <body>
                <div class="form-container">
                    <div class="form-header">
                        <h1>✏️ Edit Player</h1>
                        <p>Update player information</p>
                    </div>
                    <form method="post">
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" name="username" id="username" class="form-input" required 
                                   placeholder="Enter username" value="'.$escaped_username.'">
                        </div>
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" name="email" id="email" class="form-input" required 
                                   placeholder="Enter email address" value="'.$escaped_email.'">
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/>
                                </svg>
                                Update Player
                            </button>
                            <a href="'.$back_url.'" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </body>
            </html>';
        }
    }

    public function delete($id)
    {
        if (!$this->auth->has_role('admin')) {
            redirect('users/view');
            return;
        }
        $user = $this->UserModel->find($id);
        if (!$user) {
            redirect('users/view');
            return;
        }

        if ($this->io->method() === 'post' && $this->io->post('confirm') === 'yes') {
            try {
                $this->UserModel->delete($id);
                redirect('users/view');
            } catch (Exception $e) {
                $this->showError('deleting player', $e);
            }
        } else {
            $escaped_username = $this->escape($user['username']);
            $back_url = $this->escape(site_url('users/view'));
            
            echo '<!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Delete Player</title>
                <style>
                    * { margin: 0; padding: 0; box-sizing: border-box; }
                    body {
                        background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%);
                        min-height: 100vh; font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
                        display: flex; align-items: center; justify-content: center; padding: 2rem;
                    }
                    .confirmation-container {
                        background: rgba(255,255,255,0.95); backdrop-filter: blur(20px);
                        border-radius: 20px; padding: 3rem; width: 100%; max-width: 500px;
                        box-shadow: 0 25px 50px rgba(0,0,0,0.15); text-align: center; animation: slideUp 0.8s ease-out;
                    }
                    .warning-icon { color: #f56565; margin-bottom: 1rem; font-size: 3rem; }
                    .confirmation-header h1 {
                        font-size: 2rem; color: #2d3748; margin-bottom: 1rem; font-weight: 700;
                    }
                    .confirmation-message {
                        color: #4a5568; font-size: 1.1rem; line-height: 1.6; margin-bottom: 2rem;
                    }
                    .player-info {
                        background: #f7fafc; border-radius: 12px; padding: 1.5rem;
                        margin: 1.5rem 0; border-left: 4px solid #f56565;
                    }
                    .player-name { font-weight: 700; color: #2d3748; font-size: 1.2rem; }
                    .form-actions { display: flex; gap: 1rem; margin-top: 2rem; }
                    .btn {
                        padding: 1rem 2rem; border: none; border-radius: 12px; font-size: 1rem;
                        font-weight: 600; cursor: pointer; text-decoration: none; display: inline-flex;
                        align-items: center; justify-content: center; gap: 0.5rem;
                        transition: all 0.3s ease; flex: 1;
                    }
                    .btn-danger {
                        background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%);
                        color: white; box-shadow: 0 4px 15px rgba(245, 101, 101, 0.3);
                    }
                    .btn-danger:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(245, 101, 101, 0.4); }
                    .btn-secondary {
                        background: #e2e8f0; color: #4a5568; border: 2px solid #cbd5e0;
                    }
                    .btn-secondary:hover { background: #cbd5e0; transform: translateY(-2px); }
                    @keyframes slideUp {
                        from { opacity: 0; transform: translateY(50px); }
                        to { opacity: 1; transform: translateY(0); }
                    }
                </style>
            </head>
            <body>
                <div class="confirmation-container">
                    <div class="warning-icon">⚠️</div>
                    <div class="confirmation-header">
                        <h1>Delete Player</h1>
                    </div>
                    <div class="confirmation-message">
                        Are you sure you want to permanently delete this player? This action cannot be undone.
                    </div>
                    <div class="player-info">
                        <div class="player-name">'.$escaped_username.'</div>
                    </div>
                    <form method="post">
                        <input type="hidden" name="confirm" value="yes">
                        <div class="form-actions">
                            <button type="submit" class="btn btn-danger">
                                <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
                                </svg>
                                Yes, Delete
                            </button>
                            <a href="'.$back_url.'" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </body>
            </html>';
        }
    }

    private function escape($string)
    {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }

    private function showError($action, $exception)
    {
        echo '<!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Error</title>
            <style>
                * { margin: 0; padding: 0; box-sizing: border-box; }
                body {
                    background: linear-gradient(135deg, #e53e3e 0%, #c53030 100%);
                    min-height: 100vh; font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
                    display: flex; align-items: center; justify-content: center; padding: 2rem;
                }
                .error-container {
                    background: rgba(255,255,255,0.95); backdrop-filter: blur(20px);
                    border-radius: 20px; padding: 3rem; width: 100%; max-width: 600px;
                    box-shadow: 0 25px 50px rgba(0,0,0,0.15); text-align: center; animation: slideUp 0.8s ease-out;
                }
                .error-icon { color: #e53e3e; margin-bottom: 1rem; font-size: 4rem; }
                .error-header h1 {
                    font-size: 2.5rem; color: #2d3748; margin-bottom: 1rem; font-weight: 700;
                }
                .error-message {
                    color: #4a5568; font-size: 1.1rem; line-height: 1.6; margin-bottom: 2rem;
                    background: #fed7d7; padding: 1rem; border-radius: 8px; border-left: 4px solid #e53e3e;
                }
                .btn {
                    padding: 1rem 2rem; border: none; border-radius: 12px; font-size: 1rem;
                    font-weight: 600; cursor: pointer; text-decoration: none; display: inline-flex;
                    align-items: center; justify-content: center; gap: 0.5rem;
                    transition: all 0.3s ease; background: #4a5568; color: white;
                }
                .btn:hover { background: #2d3748; transform: translateY(-2px); }
                @keyframes slideUp {
                    from { opacity: 0; transform: translateY(50px); }
                    to { opacity: 1; transform: translateY(0); }
                }
            </style>
        </head>
        <body>
            <div class="error-container">
                <div class="error-icon">❌</div>
                <div class="error-header">
                    <h1>Error</h1>
                </div>
                <div class="error-message">
                    An error occurred while '.$action.': '.$this->escape($exception->getMessage()).'
                </div>
                <a href="'.$this->escape(site_url('users/view')).'" class="btn">
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.42-1.41L7.83 13H20v-2z"/>
                    </svg>
                    Back to Players
                </a>
            </div>
        </body>
        </html>';
    }

    private function renderPagination($total_rows, $per_page, $current_page, $base_url)
    {
        $total_pages = ceil($total_rows / $per_page);
        if ($total_pages <= 1) {
            return '';
        }

        $pagination = '<div class="pagination">';
        
        // Previous page
        if ($current_page > 1) {
            $prev_page = $current_page - 1;
            $pagination .= '<a href="'.$base_url.'&page='.$prev_page.'">← Prev</a>';
        } else {
            $pagination .= '<span class="disabled">← Prev</span>';
        }
        
        // Page numbers
        $start = max(1, $current_page - 2);
        $end = min($total_pages, $current_page + 2);
        
        if ($start > 1) {
            $pagination .= '<a href="'.$base_url.'&page=1">1</a>';
            if ($start > 2) {
                $pagination .= '<span class="disabled">...</span>';
            }
        }
        
        for ($i = $start; $i <= $end; $i++) {
            if ($i == $current_page) {
                $pagination .= '<span class="active">'.$i.'</span>';
            } else {
                $pagination .= '<a href="'.$base_url.'&page='.$i.'">'.$i.'</a>';
            }
        }
        
        if ($end < $total_pages) {
            if ($end < $total_pages - 1) {
                $pagination .= '<span class="disabled">...</span>';
            }
            $pagination .= '<a href="'.$base_url.'&page='.$total_pages.'">'.$total_pages.'</a>';
        }
        
        // Next page
        if ($current_page < $total_pages) {
            $next_page = $current_page + 1;
            $pagination .= '<a href="'.$base_url.'&page='.$next_page.'">Next →</a>';
        } else {
            $pagination .= '<span class="disabled">Next →</span>';
        }
        
        $pagination .= '</div>';
        
        return $pagination;
    }
}

?>