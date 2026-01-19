<?php

namespace App\Core;
class BaseController{
     
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