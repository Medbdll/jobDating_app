<?php

namespace App\Controllers\back;

use App\Core\BaseController;
use App\Models\Announcement;
use App\Models\Company;

class AnnouncementController extends BaseController
{
    public function create()
    {
        $companyModel = new Company();
        $companies = $companyModel->getAll();
        
        $this->render('back/announcements/create', [
            'companies' => $companies
        ]);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'company_id' => $_POST['company_id'],
                'title' => $_POST['title'],
                'description' => $_POST['description'],
                'contract_type' => $_POST['contract_type'],
                'location' => $_POST['location'],
                'skills' => $_POST['skills']
            ];

            $announcementModel = new Announcement();
            
            if ($announcementModel->create($data)) {
                $_SESSION['flash']['success'] = 'Annonce créée avec succès!';
                $this->redirect('/dashboard');
            } else {
                $_SESSION['flash']['error'] = 'Erreur lors de la création de l\'annonce';
                $this->redirect('/announcements/create');
            }
        }
    }

    public function archive($id)
    {
        $announcementModel = new Announcement();
        
        if ($announcementModel->update($id, ['deleted' => 1])) {
            $_SESSION['flash']['success'] = 'Annonce archivée avec succès!';
        } else {
            $_SESSION['flash']['error'] = 'Erreur lors de l\'archivage';
        }
        
        $this->redirect('/dashboard');
    }

    public function archived()
    {
        $announcementModel = new Announcement();
        $archivedAnnouncements = $announcementModel->getArchived();
        
        $this->render('back/announcements/archived', [
            'archivedAnnouncements' => $archivedAnnouncements
        ]);
    }
}