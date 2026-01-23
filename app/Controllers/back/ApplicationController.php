<?php

namespace App\Controllers\back;

use App\Core\BaseController;
use App\Models\Application;

class ApplicationController extends BaseController
{
    public function index()
    {
        $applicationModel = new Application();
        $applications = $applicationModel->getAllWithDetails();
        
        $this->render('back/applications/index', [
            'applications' => $applications,
            'title' => 'Gérer les Candidatures'
        ]);
    }

    public function showDetails($id)
    {
        $applicationModel = new Application();
        $application = $applicationModel->findById($id);
        
        $this->render('back/applications/details', [
            'application' => $application
        ]);
    }

    public function accept($id)
    {
        $applicationModel = new Application();
        
        if ($applicationModel->updateStatus($id, 'accepted')) {
            $_SESSION['flash']['success'] = 'Candidature acceptée avec succès!';
        } else {
            $_SESSION['flash']['error'] = 'Erreur lors de l\'acceptation de la candidature';
        }
        
        $this->redirect('/applications');
    }

    public function reject($id)
    {
        $applicationModel = new Application();
        
        if ($applicationModel->updateStatus($id, 'rejected')) {
            $_SESSION['flash']['success'] = 'Candidature rejetée avec succès!';
        } else {
            $_SESSION['flash']['error'] = 'Erreur lors du rejet de la candidature';
        }
        
        $this->redirect('/applications');
    }

    public function pending($id)
    {
        $applicationModel = new Application();
        
        if ($applicationModel->updateStatus($id, 'pending')) {
            $_SESSION['flash']['success'] = 'Candidature mise en attente avec succès!';
        } else {
            $_SESSION['flash']['error'] = 'Erreur lors de la mise en attente de la candidature';
        }
        
        $this->redirect('/applications');
    }
}
