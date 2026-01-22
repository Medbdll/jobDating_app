<?php

namespace App\Controllers\back;

use App\Core\BaseController;
use App\Models\Company;

class CampanyController extends BaseController
{
    public function create()
    {
        $this->render('back/companies/create', [
            'title' => 'Ajouter une Entreprise'
        ]);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => $_POST['name'],
                'location' => $_POST['location'],
                'sector' => $_POST['sector'] ?? null,
                'email' => $_POST['email'] ?? null,
                'phone' => $_POST['phone'] ?? null
            ];

            $companyModel = new Company();
            
            if ($companyModel->create($data)) {
                $_SESSION['flash']['success'] = 'Entreprise créée avec succès!';
                $this->redirect('/admin/dashboard');
            } else {
                $_SESSION['flash']['error'] = 'Erreur lors de la création de l\'entreprise';
                $this->redirect('/companies/create');
            }
        }
    }

    public function index()
    {
        $companyModel = new Company();
        $companies = $companyModel->getAll();
        
        $this->render('back/companies/index', [
            'companies' => $companies,
            'title' => 'Gérer les Entreprises'
        ]);
    }

    public function edit($id)
    {
        $companyModel = new Company();
        $company = $companyModel->find($id);
        
        if (!$company) {
            $_SESSION['flash']['error'] = 'Entreprise non trouvée';
            $this->redirect('/companies');
            return;
        }
        
        $this->render('back/companies/edit', [
            'company' => $company,
            'title' => 'Modifier une Entreprise'
        ]);
    }

    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => $_POST['name'],
                'location' => $_POST['location'],
                'sector' => $_POST['sector'] ?? null,
                'email' => $_POST['email'] ?? null,
                'phone' => $_POST['phone'] ?? null
            ];

            $companyModel = new Company();
            
            if ($companyModel->update($id, $data)) {
                $_SESSION['flash']['success'] = 'Entreprise mise à jour avec succès!';
                $this->redirect('/companies');
            } else {
                $_SESSION['flash']['error'] = 'Erreur lors de la mise à jour de l\'entreprise';
                $this->redirect("/companies/edit/$id");
            }
        }
    }

    public function delete($id)
    {
        $companyModel = new Company();
        
        if ($companyModel->delete($id)) {
            $_SESSION['flash']['success'] = 'Entreprise supprimée avec succès!';
        } else {
            $_SESSION['flash']['error'] = 'Erreur lors de la suppression de l\'entreprise';
        }
        
        $this->redirect('/companies');
    }
}