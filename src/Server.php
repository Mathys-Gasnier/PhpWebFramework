<?php
namespace Framework;

class Server {
    private ControllerManager $controllerManager;
    private Router $router;
    
    public function __construct(array $controllers) {
        try {
            $this->controllerManager = new ControllerManager($controllers);
            $this->router = new Router($this->controllerManager);
        }catch(\Error $err) {
            http_response_code(500);
            echo "Internal Server Error<br/><br/>\n\n";
            throw $err;
        }
    }
    
    public function handle() {

        // Could also get from SERVER['REQUEST_URI']
        $path = isset($_GET['path']) ? $_GET['path'] : '';

        // Is there an easier way to do it ?
        $method = match ($_SERVER['REQUEST_METHOD']) {
            "GET" => Method::GET,
            "POST" => Method::POST
        };

        // The params are just what's in $_GET
        $params = $_GET;
        // Get all key=>value headers
        $headers = array_change_key_case(getallheaders());
        // Getting the raw text body is dumb in php.
        $body = file_get_contents('php://input');

        try {
            $request = new Request($path, $method, $params, $headers, $body);

            $response = $this->router->route($request);
        }catch(\Error $err) {
            http_response_code(500);
            echo "Internal Server Error<br/><br/>\n\n";
            throw $err;
        }
    	
        http_response_code($response->code);
        
        foreach($response->headers as $header) {
            header($header);
        }

        echo $response->body;
    }
    
}
