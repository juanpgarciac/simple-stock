<?php 

namespace Core\Classes;

final class View
{

    private string $definition = '';

    private string $content = '';

    private $returnString = false;

    private $viewsDir = '';

    private array $viewBag = [];

    public function __construct($definition, $content = '', $returnString = false, $viewsDir = VIEWS_DIR)
    {
        $this->definition = $definition;
        $this->content = $content;
        $this->returnString = $returnString;
        $this->viewsDir = $viewsDir;
    }

    public function render($args = [], $returnString = false):string
    {   
        if(!empty($this->viewBag))
            $args = array_merge($args,$this->viewBag);
        $content = '';
        if(!empty($this->content)){
            $content = vsprintf($this->content,$args);
        }else{                       
            $supportedExtensions = ['php','html','phtml'];
            foreach ($supportedExtensions as $extension) {
                $path = path($this->viewsDir, $this->definition.'.'.$extension);
                if(is_file($path)){
                    ob_start();
                    extract($args); 
                    include $path;                    
                    $content = ob_get_contents();
                    ob_end_clean();  
                    break;  
                }
            }
        }
        if($returnString){ 
            return $content;                                  
        }
        echo $content;
    }

    public function __invoke():string
    {
        $args = !empty(func_get_args()) ? func_get_args()[0] : [];
        return $this->render($args,$this->returnString);
    }

    public static function view($definition)
    {
        return (new static($definition))();
    }

    public function with(mixed $args = []):View
    {
        $args = is_array($args) ? $args : [ 'viewBagItem'.count($this->viewBag) => $args  ]; 
        $this->viewBag  = array_merge($this->viewBag, $args);        
        return $this;
    }

}