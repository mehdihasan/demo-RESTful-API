<?php

/*
http://code.tutsplus.com/tutorials/a-beginners-guide-to-http-and-rest--net-16340
*/

class Server {
    /* The array key works as id and is used in the URL
       to identify the resource.
    */
    private $contacts = array('jim' => array('address' => '356 Trailer Park Avenue', 'url' => '/clients/jim'),
                              'anne' => array('address' => '6321 Tycoon Road', 'url'=> '/clients/anne')
);

    public function serve() {
      
        //$uri = $_SERVER['REQUEST_URI']; // by mehdi
        $uri = $_SERVER['PATH_INFO'];
        $method = $_SERVER['REQUEST_METHOD'];
        $paths = explode('/', $this->paths($uri));
        array_shift($paths); // Hack; get rid of initials empty string
        $resource = array_shift($paths);
      
        if ($resource == 'clients') {
            $name = array_shift($paths);
	
            if (empty($name)) {
                $this->handle_base($method);
            } else {
                $this->handle_name($method, $name);
            }
          
        } else {
            // We only handle resources under 'clients'
            header('HTTP/1.1 404 Not Found');
        } 
    }
        
    private function handle_base($method) {
        switch($method) {
        case 'GET':
            $this->result();
            break;
        default:
            header('HTTP/1.1 405 Method Not Allowed');
            header('Allow: GET');
            break;
        }
    }

    private function handle_name($method, $name) {
        switch($method) {
        case 'PUT':
            $this->create_contact($name);
            break;

        case 'DELETE':
            $this->delete_contact($name);
            break;
      
        case 'GET':
            $this->display_contact($name);
            break;

        default:
            header('HTTP/1.1 405 Method Not Allowed');
            header('Allow: GET, PUT, DELETE');
            break;
        }
    }

    private function create_contact($name){
        if (isset($this->contacts[$name])) {
            header('HTTP/1.1 409 Conflict');
            return;
        }
        /* PUT requests need to be handled
         * by reading from standard input.
         */
        $data = json_decode(file_get_contents('php://input'));
        if (is_null($data)) {
            header('HTTP/1.1 400 Bad Request');
            $this->result();
            return;
        }
        $this->contacts[$name] = $data; 
        $this->result();
    }
    
    private function delete_contact($name) {
        if (isset($this->contacts[$name])) {
            unset($this->contacts[$name]);
            $this->result();
        } else {
            header('HTTP/1.1 404 Not Found');
        }
    }
    
    private function display_contact($name) {
        if (array_key_exists($name, $this->contacts)) {
            echo json_encode($this->contacts[$name]);
        } else {
            header('HTTP/1.1 404 Not Found');
        }
    }
    
    private function paths($url) {
        $uri = parse_url($url);
        return $uri['path'];
    }
    
    /**
     * Displays a list of all contacts.
     */
    private function result() {
        header('Content-type: application/json');
        echo json_encode($this->contacts);
    }
  }

$server = new Server;
$server->serve();

?>