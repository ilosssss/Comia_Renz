<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class Auth
{
    protected $_lava;

    public function __construct()
    {
        $this->_lava = lava_instance();
        $this->_lava->call->database();
        $this->_lava->call->library('session');
    }

    /**
     * Register a new user
     *
     * @param string $username
     * @param string $password
     * @param string $role
     * @return bool
     */
    public function register($username, $password, $role = 'user', $email = null)
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $payload = [
            'username' => $username,
            'password' => $hash,
            'role' => $role,
            'created_at' => date('Y-m-d H:i:s')
        ];
        if (!is_null($email)) {
            $payload['email'] = $email;
        }
        return $this->_lava->db->table('users')->insert($payload);
    }

    /**
     * Login user
     *
     * @param string $username
     * @param string $password
     * @return bool
     */
    public function login($username, $password)
    {
        $user = $this->_lava->db->table('users')
                        ->where('username', $username)
                        ->get();

        if ($user && isset($user['password']) && password_verify($password, $user['password'])) {
            $this->_lava->session->set_userdata([
                'user_id' => $user['id'],
                'username' => $user['username'],
                'role' => $user['role'],
                'logged_in' => true
            ]);
            return true;
        }

        return false;
    }

    /**
     * Check if user is logged in
     *
     * @return bool
     */
    public function is_logged_in()
    {
        return (bool) $this->_lava->session->userdata('logged_in');
    }

    /**
     * Check user role
     *
     * @param string $role
     * @return bool
     */
    public function has_role($role)
    {
        return $this->_lava->session->userdata('role') === $role;
    }

    /**
     * Logout user
     *
     * @return void
     */
    public function logout()
    {
        $this->_lava->session->unset_userdata(['user_id','username','role','logged_in']);
    }
}
?>


