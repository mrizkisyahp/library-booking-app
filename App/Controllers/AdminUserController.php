<?php

namespace App\Controllers;

use App\Core\App;
use App\Core\Request;
use App\Core\Response;
use App\Core\Controller;
use App\Core\Middleware\AdminMiddleware;
use App\Models\User;
use App\Core\Services\Logger;

class AdminUserController extends Controller {
    protected ?User $currentUser = null;

    public function __construct() {
        $this->registerMiddleware(new AdminMiddleware());
        $this->currentUser = App::$app->user instanceof User ? App::$app->user : null;
    }

    public function index() {
        $this->setLayout('main');
        $this->setTitle('Admin User Management | Library Booking App');

        $perPage = 20;
        $page = (int)($_GET['page'] ?? 1);
        $page = max(1, $page);

        $users = User::findPaginated($page, $perPage);
        // echo '<pre>';
        // print_r($users);
        // echo '</pre>';
        // exit;
        return $this->render('Admin/Users/Index', [
            'users' => $users,
            'currentPage' => $page,
            'perPage' => $perPage,
            'totalUsers' => User::count(),
            'totalActive' => User::countActive(),
            'totalPending' => User::countPending(),
            'totalSuspended' => User::countSuspended()
        ]);
    }
}