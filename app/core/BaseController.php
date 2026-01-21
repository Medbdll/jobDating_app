<?php

namespace App\Core;

class BaseController
{
    protected $session;

    public function __construct()
    {
        $this->session = Session::getInstance();
    }

    protected function view(string $view, array $data = [])
    {
        extract($data);

        $path = __DIR__ . '/../../views/' . $view . '.php';

        if (file_exists($path)) {
            require $path;
        } else {
            echo "View [$view] not found!";
        }
    }

    protected function render(string $view, array $data = []): void
    {
        // Pass session instance to the view data
        $data['session'] = $this->session;
        
        // Extract flash messages to make them available in templates
        $data['errors'] = $this->session->flash('errors');
        $data['error'] = $this->session->flash('error');
        $data['success'] = $this->session->flash('success');
        $data['old'] = $this->session->flash('old');
        
        // Add flash messages as a nested array for template access
        $data['flash'] = $this->session->getFlash();
        
        View::render($view, $data);
    }

    
    protected function redirect(string $url)
    {
        header("Location: $url");
        exit;
    }

    
    protected function json($data, int $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}