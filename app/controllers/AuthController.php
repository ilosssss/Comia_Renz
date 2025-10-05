<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class AuthController extends Controller
{
    public function register()
    {
        $this->call->library('auth');

        if ($this->io->method() == 'post') {
            $username = $this->io->post('username');
            $email = $this->io->post('email');
            $password = $this->io->post('password');
            $role = $this->io->post('role') ?? 'user';

            if (!empty($username) && !empty($password)) {
                if ($this->auth->register($username, $password, $role, $email)) {
                    redirect('auth/login');
                    return;
                }
            }
        }

        $this->call->view('auth/register');
    }

    public function login()
    {
        $this->call->library('auth');

        if ($this->io->method() == 'post') {
            $username = $this->io->post('username');
            $password = $this->io->post('password');

            if ($this->auth->login($username, $password)) {
                // Redirect based on role
                $this->call->library('session');
                $role = $this->session->userdata('role');
                // All roles go to the table; permissions are enforced there
                redirect('users/view');
                return;
            } else {
                echo 'Login failed!';
            }
        }

        $this->call->view('auth/login');
    }

    public function dashboard()
    {
        $this->call->library(['auth', 'session']);

        if (!$this->auth->is_logged_in()) {
            redirect('auth/login');
            return;
        }

        $this->call->view('auth/dashboard', ['session' => $this->session]);
    }

    public function logout()
    {
        $this->call->library('auth');
        $this->auth->logout();
        redirect('auth/login');
    }
}
?>


