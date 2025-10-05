<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account</title>
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{min-height:100vh;display:flex;align-items:center;justify-content:center;padding:2rem;background:linear-gradient(135deg,#10b981 0%,#059669 100%);font-family:Segoe UI,Arial,sans-serif}
        .card{width:100%;max-width:520px;background:#fff;border-radius:16px;box-shadow:0 25px 60px rgba(0,0,0,.25);overflow:hidden}
        .header{padding:1.25rem 1.75rem;background:linear-gradient(135deg,#111827 0%,#1f2937 100%);color:#fff}
        .header h1{font-size:1.4rem}
        .content{padding:1.5rem}
        label{display:block;margin:.25rem 0 .5rem;color:#374151;font-weight:700}
        input,select{width:100%;padding:.85rem 1rem;border:2px solid #e5e7eb;border-radius:10px;background:#fff}
        input:focus,select:focus{outline:none;border-color:#10b981;box-shadow:0 0 0 3px rgba(16,185,129,.15)}
        .field{margin-top:1rem}
        .actions{display:flex;gap:.75rem;margin-top:1.25rem}
        .btn{flex:1;padding:.9rem 1rem;border:0;border-radius:10px;font-weight:700;cursor:pointer;transition:.2s}
        .btn-primary{background:linear-gradient(135deg,#a855f7 0%,#8b5cf6 100%);color:#fff}
        .btn-secondary{background:linear-gradient(135deg,#10b981 0%,#059669 100%);color:#fff;text-decoration:none;text-align:center}
        .muted{margin-top:1rem;color:#6b7280}
        .muted a{color:#0ea5e9}
    </style>
</head>
<body>
    <div class="card">
        <div class="header"><h1>Create your account</h1></div>
        <div class="content">
            <form action="<?= site_url('auth/register'); ?>" method="post">
                <div class="field">
                    <label>Username</label>
                    <input type="text" name="username" placeholder="Choose a username" required>
                </div>
                <div class="field">
                    <label>Email</label>
                    <input type="email" name="email" placeholder="you@example.com" required>
                </div>
                <div class="field">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="Create a password" required>
                </div>
                <div class="field">
                    <label>Role</label>
                    <select name="role">
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div class="actions">
                    <button class="btn btn-primary" type="submit">Create account</button>
                    <a class="btn btn-secondary" href="<?= site_url('auth/login'); ?>">Back to login</a>
                </div>
                <div class="muted">After registration, sign in to access the players table.</div>
            </form>
        </div>
    </div>
</body>
</html>

