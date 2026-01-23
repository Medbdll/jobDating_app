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
        
        if (empty($companies)) {
            $_SESSION['flash']['error'] = 'Aucune entreprise disponible. Veuillez d\'abord créer des entreprises.';
        }
        
        $this->render('back/announcements/create', [
            'companies' => $companies
        ]);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['company_id']) || empty($_POST['company_id'])) {
                $_SESSION['flash']['error'] = 'Veuillez sélectionner une entreprise';
                $this->redirect('/announcements/create');
                return;
            }
            
            $data = [
                'company_id' => $_POST['company_id'],
                'title' => $_POST['title'],
                'description' => $_POST['description'],
                'contract_type' => $_POST['type'] ?? 'internship',
                'location' => $_POST['location'] ?? null,
                'skills' => $_POST['skills'] ?? null
            ];

            $announcementModel = new Announcement();
            
            if ($announcementModel->create($data)) {
                $_SESSION['flash']['success'] = 'Annonce créée avec succès!';
                $this->redirect('/admin/dashboard');
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
        
        $this->redirect('/admin/dashboard');
    }

    public function archived()
    {
        $announcementModel = new Announcement();
        $archivedAnnouncements = $announcementModel->getArchived();
        
        $this->render('back/announcements/archived', [
            'archivedAnnouncements' => $archivedAnnouncements
        ]);
    }

    public function unarchive($id)
    {
        $announcementModel = new Announcement();
        
        if ($announcementModel->update($id, ['deleted' => 0])) {
            $_SESSION['flash']['success'] = 'Annonce désarchivée avec succès!';
        } else {
            $_SESSION['flash']['error'] = 'Erreur lors de la désarchivage';
        }
        
        $this->redirect('/announcements/archived');
    }

    public function deleteArchived($id)
    {
        $announcementModel = new Announcement();
        
        if ($announcementModel->deletePermanent($id)) {
            $_SESSION['flash']['success'] = 'Annonce supprimée avec succès!';
        } else {
            $_SESSION['flash']['error'] = 'Erreur lors de la suppression';
        }
        
        $this->redirect('/announcements/archived');
    }

    public function edit($id)
    {
        $announcementModel = new Announcement();
        $companyModel = new Company();
        
        $announcement = $announcementModel->findById($id);
        $companies = $companyModel->getAll();
        
        if (!$announcement) {
            $_SESSION['flash']['error'] = 'Annonce non trouvée';
            $this->redirect('/admin/dashboard');
            return;
        }
        
        $this->render('back/announcements/edit', [
            'announcement' => $announcement,
            'companies' => $companies
        ]);
    }

    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['company_id']) || empty($_POST['company_id'])) {
                $_SESSION['flash']['error'] = 'Veuillez sélectionner une entreprise';
                $this->redirect('/announcements/edit/' . $id);
                return;
            }
            
            $data = [
                'company_id' => $_POST['company_id'],
                'title' => $_POST['title'],
                'description' => $_POST['description'],
                'contract_type' => $_POST['type'] ?? 'internship',
                'location' => $_POST['location'] ?? null,
                'skills' => $_POST['skills'] ?? null
            ];

            $announcementModel = new Announcement();
            
            if ($announcementModel->update($id, $data)) {
                $_SESSION['flash']['success'] = 'Annonce mise à jour avec succès!';
                $this->redirect('/admin/dashboard');
            } else {
                $_SESSION['flash']['error'] = 'Erreur lors de la mise à jour de l\'annonce';
                $this->redirect('/announcements/edit/' . $id);
            }
        }
    }

    public function show($id)
    {
        $announcementModel = new Announcement();
        $announcement = $announcementModel->findById($id);
        
        if (!$announcement) {
            $_SESSION['flash']['error'] = 'Annonce non trouvée';
            $this->redirect('/admin/dashboard');
            return;
        }
        
        $this->render('back/announcements/show', [
            'announcement' => $announcement
        ]);
    }

    public function delete($id)
    {
        $announcementModel = new Announcement();
        
        if ($announcementModel->delete($id)) {
            $_SESSION['flash']['success'] = 'Annonce supprimée avec succès!';
        } else {
            $_SESSION['flash']['error'] = 'Erreur lors de la suppression de l\'annonce';
        }
        
        $this->redirect('/admin/dashboard');
    }
}