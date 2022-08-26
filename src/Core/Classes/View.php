<?php 

namespace Core\Classes;

final class View
{

    private string $definition = '';

    private string $content = '';

    private $returnString = false;

    private $viewsDir = '';

    private array $viewBag = [];

    private ?View $layout = null;

    public function __construct($definition, $content = '', $returnString = false, $viewsDir = VIEWS_DIR)
    {
        $this->definition = $definition;
        $this->content = $content;
        $this->returnString = $returnString;
        $this->viewsDir = $viewsDir;
    }

    private function getLayoutFromViewFileComment($file):void
    {
        if(!empty($file)){            
            $tokens = token_get_all(file_get_contents($file));
            $filtered = array_filter($tokens, fn($arr)=> ($arr[0] == T_DOC_COMMENT || $arr[0] == T_COMMENT) && preg_match('/.*\@layout.*/',$arr[1]));
            if(!empty($filtered) && is_array($filtered)){
                $filtered = ($filtered[array_key_first($filtered)]);
                $layout = preg_replace('/(\@(\blayout\b))|[^a-z\_\-\.0-9]|\b\*\/\b/i','',explode(' ',$filtered[1]));
                $layout = (implode('',$layout));                
                $this->layout($layout);
            }
        }
    }

    public function layout($definition = null)
    {
        if(!is_null($definition))
            $this->layout = new self(str_replace('.','/',$definition),'',$this->returnString,$this->viewsDir);
        return $this;
    }

    public function render(array|null $args = [], $returnString = false):string
    {   $args = is_null($args) ? [] : $args;
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
                    $this->getLayoutFromViewFileComment($path);                    
                    break;  
                }
            }
        }

        if(!is_null($this->getLayout())){        
            return $this->getLayout()->render(['yield'=>$content], $returnString);
        }

        if($returnString){             
            return $content;                                  
        }
        echo $content;
        return '';
    }

    public function getLayout():View|null
    {
        return $this->layout;
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