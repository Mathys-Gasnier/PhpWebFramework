<?php
namespace Framework;

class Server {
    private ControllerManager $controllerManager;
    private Router $router;
    
    public function __construct(array $controllers) {
        $this->controllerManager = new ControllerManager($controllers);

        $this->router = new Router($this->controllerManager);
        
    }
    
    public function handle() {

        $path = isset($_GET['path']) ? $_GET['path'] : '';
        
        $method = match ($_SERVER['REQUEST_METHOD']) {
            "GET" => Method::GET,
            "POST" => Method::POST
        };

        $params = $_GET;
        $body = file_get_contents('php://input');

        $request = new Request(
            $path, $method, $params, $body
        );

        $response = $this->router->route($request);
    	
        http_response_code($response->code);
        echo $response->body;
    }
    
}
