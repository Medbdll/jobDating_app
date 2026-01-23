<?php

namespace App\Controllers\front;

use App\Models\Announcement;
use App\Models\Company;
use App\Models\Application;
use App\Core\BaseController;
use App\Core\Auth;
use App\Core\Session;

class DashboardController extends BaseController
{
    private $auth;

    public function __construct()
    {
        parent::__construct();
        $this->auth = new Auth();
        
        // Require student authentication
        $this->auth->requireAuth('student');
    }

    public function index()
    {
        $announcementModel = new Announcement();
        $companyModel = new Company();
        $applicationModel = new Application();
        $session = Session::getInstance();

        // Handle search functionality
        $searchQuery = $_GET['search'] ?? null;
        
        if ($searchQuery && !empty(trim($searchQuery))) {
            $announcements = $announcementModel->search(trim($searchQuery));
        } else {
            $announcements = $announcementModel->getAllActive();
        }
        
        // Check if student has already applied to any announcement
        $studentId = $applicationModel->getStudentIdByUserId($session->get('user_id'));
        $hasAnyApplication = $studentId ? $applicationModel->hasApplied($studentId) : false;
        
        // Get statistics
        $stats = [
            'total_announcements' => $announcementModel->countAll(),
            'total_companies' => $companyModel->countCompanies()
        ];

        $this->render('front/dashboard/index', [
            'announcements' => $announcements,
            'stats' => $stats,
            'has_any_application' => $hasAnyApplication,
            'search_query' => $searchQuery,
            'session' => $session,
            'flash_messages' => $session->getFlash()
        ]);
    }

    public function showAnnouncement($id)
    {
        $announcementModel = new Announcement();
        $announcement = $announcementModel->findById($id);
        
        if (!$announcement || $announcement['deleted'] == 1) {
            $_SESSION['flash']['error'] = 'Annonce non trouvée';
            $this->redirect('/student/dashboard');
            return;
        }

        // Check if student has already applied to any announcement
        $session = Session::getInstance();
        $applicationModel = new Application();
        $studentId = $applicationModel->getStudentIdByUserId($session->get('user_id'));
        $hasApplied = $studentId ? $applicationModel->hasApplied($studentId) : false; // Check any application
        $hasAppliedToThis = $hasApplied ? $applicationModel->hasApplied($studentId, $id) : false; // Check specific announcement
        
        $this->render('front/dashboard/show', [
            'announcement' => $announcement,
            'has_applied' => $hasApplied,
            'has_applied_to_this' => $hasAppliedToThis,
            'has_any_application' => $hasApplied, // New variable for UI consistency
            'session' => $session,
            'flash_messages' => $session->getFlash()
        ]);
    }

    public function apply($id)
    {
        $announcementModel = new Announcement();
        $announcement = $announcementModel->findById($id);
        
        if (!$announcement || $announcement['deleted'] == 1) {
            $_SESSION['flash']['error'] = 'Annonce non trouvée';
            $this->redirect('/student/dashboard');
            return;
        }
        
        // Check if student has already applied to any announcement
        $session = Session::getInstance();
        $applicationModel = new Application();
        $studentId = $applicationModel->getStudentIdByUserId($session->get('user_id'));
        $hasApplied = $studentId ? $applicationModel->hasApplied($studentId) : false; // Check any application
        
        if ($hasApplied) {
            $_SESSION['flash']['error'] = 'Vous avez déjà postulé à une offre. Vous ne pouvez postuler qu\'à une seule offre.';
            $this->redirect("/student/dashboard/announcement/{$id}");
            return;
        }
        
        $this->render('front/dashboard/apply', [
            'announcement' => $announcement,
            'session' => $session,
            'flash_messages' => $session->getFlash()
        ]);
    }

    public function storeApplication($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect("/student/dashboard/announcement/{$id}");
            return;
        }

        $announcementModel = new Announcement();
        $announcement = $announcementModel->findById($id);
        
        if (!$announcement || $announcement['deleted'] == 1) {
            $_SESSION['flash']['error'] = 'Annonce non trouvée';
            $this->redirect('/student/dashboard');
            return;
        }

        $session = Session::getInstance();
        $applicationModel = new Application();
        $studentId = $applicationModel->getStudentIdByUserId($session->get('user_id'));

        // Check if student has already applied to any announcement
        $hasApplied = $studentId ? $applicationModel->hasApplied($studentId) : false; 

        if ($hasApplied) {
            $_SESSION['flash']['error'] = 'Vous avez déjà postulé à une offre. Vous ne pouvez postuler qu\'à une seule offre.';
            $this->redirect("/student/dashboard/announcement/{$id}");
            return;
        }

        // Validate form data
        $coverLetter = trim($_POST['cover_letter'] ?? '');
        
        if (empty($coverLetter)) {
            $_SESSION['flash']['error'] = 'Veuillez rédiger une lettre de motivation';
            $this->redirect("/student/dashboard/apply/{$id}");
            return;
        }

        // Create application
        $applicationData = [
            'student_id' => $studentId,
            'announcement_id' => $id,
            'cover_letter' => $coverLetter,
            'status' => 'pending'
        ];

        if ($applicationModel->create($applicationData)) {
            $_SESSION['flash']['success'] = 'Votre candidature a été envoyée avec succès!';
        } else {
            $_SESSION['flash']['error'] = 'Une erreur est survenue lors de l\'envoi de votre candidature';
        }

        $this->redirect("/student/dashboard/announcement/{$id}");
    }

    public function myApplications()
    {
        $session = Session::getInstance();
        $applicationModel = new Application();
        $studentId = $applicationModel->getStudentIdByUserId($session->get('user_id'));
        
        $applications = $applicationModel->getByStudent($studentId);
        
        $this->render('front/dashboard/applications', [
            'applications' => $applications,
            'session' => $session,
            'flash_messages' => $session->getFlash()
        ]);
    }
}
