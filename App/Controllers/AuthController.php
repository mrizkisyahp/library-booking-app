<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Services\AuthService;
use App\Core\Services\TurnstileService;
use App\Core\Services\Logger;
use App\Core\Exceptions\ValidationException;

class AuthController extends Controller
{
    public function __construct(
        private AuthService $auth,
        private TurnstileService $turnstile,
        private Logger $logger
    ) {
    }

    public function login(Request $request, Response $response)
    {
        $this->setLayout('auth');
        $this->setTitle('Login | Library Booking App');

        if ($request->isPost()) {

            $token = $request->input('cf-turnstile-response');
            $remoteIp = $request->ip();

            if (!$this->turnstile->verify($token, $remoteIp)) {
                flash('error', 'Captcha verification failed. Please try again.');
                return view('Auth/Login', [
                    'rememberedIdentifier' => $_COOKIE['remember_identifier'] ?? ''
                ]);
            }

            try {
                $validated = $request->validate([
                    'identifier' => ['required', 'string', 'max:255'],
                    'password' => ['required', 'string', 'max:255', 'min:6']
                ]);

                $remember = (bool) $request->input('remember', false);

                if ($this->auth->attempt($validated, $remember)) {
                    $currentUser = $this->auth->user();
                    $roleName = $currentUser?->role?->nama_role;

                    if ($currentUser?->id_user) {
                        $this->logger->auth('Logged in', $currentUser->id_user, "Email: {$currentUser->email}");
                    }

                    flash('success', 'Login successful!');
                    redirect($roleName === 'admin' ? '/admin' : '/dashboard');
                }
            } catch (ValidationException $e) {
                return view('Auth/Login', [
                    'rememberedIdentifier' => $_COOKIE['remember_identifier'] ?? '',
                    'validator' => $e->getValidator()
                ]);
            }
            flash('old_identifier', $validated['identifier']);
            flash('error', 'Invalid credentials');
        }
        return view('Auth/Login', [
            'rememberedIdentifier' => $_COOKIE['remember_me'] ?? ''
        ]);
    }

    public function register(Request $request, Response $response)
    {
        $this->setTitle('Register | Library Booking App');
        $this->setLayout('auth');
        return view('Auth/ChooseRegister');
    }

    public function registerMahasiswa(Request $request, Response $response)
    {
        $this->setTitle('Register Mahasiswa | Library Booking App');
        $this->setLayout('auth');

        if ($request->isPost()) {
            $token = $request->input('cf-turnstile-response');

            if (!$this->turnstile->verify($token, $request->ip())) {
                flash('error', 'CAPTCHA verification failed.');
                return view('Auth/Mahasiswa');
            }

            try {
                $validated = $request->validate([
                    'nama' => ['required', 'string', 'min:3'],
                    'nim' => ['required', 'string', 'min:10', 'max:10', 'unique:users,nim'],
                    'email' => ['required', 'email', 'unique:users,email'],
                    'password' => ['required', 'string', 'min:8', 'max:24'],
                    'confirm_password' => ['required', 'string', 'match:password'],
                    'jurusan' => ['required', 'string'],
                    'nomor_hp' => ['required', 'numeric'],
                ]);

                $registered = $this->auth->register([
                    'nama' => $validated['nama'],
                    'nim' => $validated['nim'],
                    'email' => $validated['email'],
                    'password' => $validated['password'],
                    'jurusan' => $validated['jurusan'],
                    'nomor_hp' => $validated['nomor_hp'],
                ], 'mahasiswa');

                $this->auth->sendVerificationOTP($registered);

                if ($registered->id_user) {
                    $this->logger->auth('registered', $registered->id_user, "Email: {$registered->email}");
                }

                flash('success', 'Registration successful! Check your email for verification code.');
                redirect('/verify');
            } catch (ValidationException $e) {
                flash('error', 'Registration failed! Please try again.');
                return view('Auth/Mahasiswa', [
                    'validator' => $e->getValidator()
                ]);
            }
        }
        return view('Auth/Mahasiswa');
    }

    public function registerDosen(Request $request, Response $response)
    {
        $this->setTitle('Register Dosen | Library Booking App');
        $this->setLayout('auth');

        if ($request->isPost()) {
            $token = $request->input('cf-turnstile-response');

            if (!$this->turnstile->verify($token, $request->ip())) {
                flash('error', 'CAPTCHA verification failed.');
                return view('Auth/Dosen');
            }

            try {
                $validated = $request->validate([
                    'nama' => ['required', 'string', 'min:3'],
                    'nip' => ['required', 'string', 'min:18', 'max:18', 'unique:users,nip'],
                    'email' => ['required', 'email', 'unique:users,email'],
                    'password' => ['required', 'string', 'min:8', 'max:24'],
                    'confirm_password' => ['required', 'string', 'match:password'],
                    'jurusan' => ['required', 'string'],
                    'nomor_hp' => ['required', 'numeric'],
                ]);

                $registered = $this->auth->register([
                    'nama' => $validated['nama'],
                    'nip' => $validated['nip'],
                    'email' => $validated['email'],
                    'password' => $validated['password'],
                    'jurusan' => $validated['jurusan'],
                    'nomor_hp' => $validated['nomor_hp'],
                ], 'dosen');

                $this->auth->sendVerificationOTP($registered);

                if ($registered->id_user) {
                    $this->logger->auth('registered', $registered->id_user, "Email: {$registered->email}");
                }

                flash('success', 'Registration successful! Check your email for verification code.');
                redirect('/verify');
            } catch (ValidationException $e) {
                flash('error', 'Registration failed! Please try again.');
                return view('Auth/Dosen', [
                    'validator' => $e->getValidator()
                ]);
            }
        }
        return view('Auth/Dosen');
    }

    public function logout(Request $request, Response $response)
    {
        $currentUser = $this->auth->user();

        $this->auth->logout();

        if ($currentUser) {
            $this->logger->auth('logged out', $currentUser->id_user, "Email: {$currentUser->email}");
        }

        flash('success', 'You have been logged out.');
        redirect('/');
    }
}