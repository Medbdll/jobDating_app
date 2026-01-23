<?php

namespace App\Controllers\back;

use App\Core\BaseController;
use App\Core\Auth;
use App\Models\Application;
use App\Models\Announcement;
use App\Models\Student;
use App\Core\Session;

class StudentController extends BaseController
{
    private $auth;

    public function __construct()
    {
        parent::__construct();
        $this->auth = new Auth();
    }
    

    // list tous les etudient pour l'afichage de l' admin 
    public function index()
    {
        $this->auth->requireAuth('admin');
        
        $studentModel = new Student();
        $students = $studentModel->getAllWithDetails();
        
        $this->render('back/students/index', [
            'students' => $students
        ]);
    }
   


    public function dashboard()
    {
        $this->auth->requireAuth('student');
        $session = Session::getInstance();
        $applicationModel = new Application();
        $announcementModel = new Announcement();
        $studentModel = new Student();
        
        $studentId = $applicationModel->getStudentIdByUserId($session->get('user_id'));
        
        // Handle search functionality
        $searchQuery = $_GET['search'] ?? null;
        
        if ($searchQuery && !empty(trim($searchQuery))) {
            $announcements = $announcementModel->search(trim($searchQuery));
        } else {
            $announcements = $announcementModel->getAllActive();
        }
        
        // Get student data
        $student = $studentModel->findByUserId($session->get('user_id'));
        
        // Get statistics
        $stats = [
            'total_applications' => $applicationModel->countByStatus(null, $studentId),
            'pending_applications' => $applicationModel->countByStatus('pending', $studentId),
            'accepted_applications' => $applicationModel->countByStatus('accepted', $studentId),
            'rejected_applications' => $applicationModel->countByStatus('rejected', $studentId),
            'total_announcements' => $announcementModel->countAll(),
            'new_announcements' => count($announcementModel->getRecent(5))
        ];
        
        // Get recent applications
        $recentApplications = $applicationModel->getByStudent($studentId);
        $recentApplications = array_slice($recentApplications, 0, 3);
        
        // Get recommended announcements (based on student's specialisation)
        $recommendedAnnouncements = [];
        if ($student && $student['specialisation']) {
            $allAnnouncements = $announcementModel->getAllActive();
            $recommendedAnnouncements = array_filter($allAnnouncements, function($announcement) use ($student) {
                return stripos($announcement['description'] . ' ' . ($announcement['skills'] ?? ''), $student['specialisation']) !== false;
            });
            $recommendedAnnouncements = array_slice($recommendedAnnouncements, 0, 3);
        }
        
        $this->render('back/students/dashboard', [
            'student' => $student,
            'stats' => $stats,
            'recent_applications' => $recentApplications,
            'recommended_announcements' => $recommendedAnnouncements,
            'announcements' => $announcements,
            'search_query' => $searchQuery,
            'session' => $session,
            'flash_messages' => $session->getFlash()
        ]);
    }

    public function searchAnnouncements()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        $searchQuery = $_GET['search'] ?? '';
        $typeFilter = $_GET['type'] ?? '';
        
        $announcementModel = new Announcement();
        
        if (!empty(trim($searchQuery))) {
            $announcements = $announcementModel->search(trim($searchQuery));
        } else {
            $announcements = $announcementModel->getAllActive();
        }
        
        // Apply type filter if specified
        if (!empty($typeFilter)) {
            $announcements = array_filter($announcements, function($announcement) use ($typeFilter) {
                return $announcement['contract_type'] === $typeFilter;
            });
        }
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'announcements' => array_values($announcements),
            'count' => count($announcements)
        ]);
    }

    public function profile()
    {
        $this->auth->requireAuth('student');
        $session = Session::getInstance();
        $studentModel = new Student();
        
        $student = $studentModel->findByUserId($session->get('user_id'));
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Update profile logic here
            $data = [
                'promotion' => $_POST['promotion'] ?? $student['promotion'],
                'specialisation' => $_POST['specialization'] ?? $student['specialisation'],
                'bio' => $_POST['bio'] ?? $student['bio'],
                'skills' => $_POST['skills'] ?? $student['skills'],
                'linkedin_url' => $_POST['linkedin_url'] ?? $student['linkedin_url'],
                'portfolio_url' => $_POST['portfolio_url'] ?? $student['portfolio_url']
            ];
            
            // Update logic would go here
            $_SESSION['flash']['success'] = 'Profil mis à jour avec succès!';
            $this->redirect('/student/dashboard/profile');
            return;
        }
        
        $this->render('back/students/profile', [
            'student' => $student,
            'session' => $session,
            'flash_messages' => $session->getFlash()
        ]);
    }

    public function applications()
    {
        $this->auth->requireAuth('student');
        
        $session = Session::getInstance();
        $applicationModel = new Application();
        
        $studentId = $applicationModel->getStudentIdByUserId($session->get('user_id'));
        $applications = $applicationModel->getByStudent($studentId);
        
        $this->render('back/students/applications', [
            'applications' => $applications,
            'session' => $session,
            'flash_messages' => $session->getFlash()
        ]);
    }
}