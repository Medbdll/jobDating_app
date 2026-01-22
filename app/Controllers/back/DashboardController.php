<?php

namespace App\Controllers\back;

use App\Models\User;
use App\Models\Company;
use App\Models\Announcement;
use App\Core\BaseController;
use App\Core\Auth;

class DashboardController extends BaseController
{
    private $auth;

    public function __construct()
    {
        parent::__construct();
        $this->auth = new Auth();
        
        
        $this->auth->requireAuth('admin');
    }

    public function index()
    {
        $userModel = new User();
        $companyModel = new Company();
        $announcementModel = new Announcement();

        $stats = [
            'students'      => $userModel->countStudents(),
            'companies'     => $companyModel->countCompanies(),
            'announcements' => $announcementModel->countAll(),
            'archived'      => $announcementModel->countArchived()
        ];

        $recentAnnouncements = $announcementModel->getRecent(3);

        $this->render('back/dashboard/index', [
            'stats' => $stats,
            'recentAnnouncements' => $recentAnnouncements
        ]);
    }
}