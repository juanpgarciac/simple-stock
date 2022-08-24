<?php 

namespace Core\Classes;

final class View
{

    private string $definition = '';

    private string $content = '';

    private $returnString = true;

    private $viewsDir = '';

    public function __construct($definition, $content = '', $returnString = false, $viewsDir = VIEWS_DIR)
    {
        $this->definition = $definition;
        $this->content = $content;
        $this->returnString = $returnString;
        $this->viewsDir = $viewsDir;
    }

    public function render($args = []):string
    {   
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
        if($this->returnString){ 
            return $content;                                  
        }
        echo $content;
        return '';
    }

    public function __invoke():string
    {
        $args = !empty(func_get_args()) ? func_get_args()[0] : [];
        return $this->render($args);
    }

    public static function view($definition)
    {
        return (new static($definition))();
    }
}