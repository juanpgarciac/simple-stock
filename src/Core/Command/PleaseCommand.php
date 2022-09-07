<?php 

namespace Core\Command;

use Core\Interfaces\ISingleton;
use Core\Traits\Singleton;
use Core\Traits\Utils;

class PleaseCommand implements ISingleton
{

    use Singleton;
    use DefaultClosures;

    public const CAN_DO = [
        'create' => [
            'controller' => [self::class,"create_controller"],
            'model'      => [self::class,"create_model"],
            'repository' => [self::class,"create_repository"],
        ],
        'list' => [
            'route'      => [self::class,"list_routes"],
            'routes'     => [self::class,"list_routes"],
            'commands'   => [self::class,"list_commands"],
        ]
    ];

    private $commandPool = [];

    private $commandHelpPool = [];

    private array $commandOptionsBag = [];

    private function __construct()
    {
        $this->registerCommands(self::CAN_DO);
    }

    public function registerCommand(string $action, string $subject, callable $closure) 
    {
        if(is_callable($closure)){
            if(!isset($this->commandPool[$action])){
                $this->commandPool[$action] = [];
            }
            $this->commandPool[$action][$subject] = $closure;
        }else{
            trigger_error("command $action $subject could not be register because doesn't have a valid closure",E_USER_WARNING);
        }
    }

    public function registerCommandHelp($action, $subject, array $help) 
    {
        if(!isset($this->commandHelpPool[$action])){
            $this->commandHelpPool[$action] = [];
        }
        $this->commandHelpPool[$action][$subject] = $help;
    }

    public function registerCommands($commands = [])
    {
        foreach ($commands as $action => $subject) {
            if(!is_array($subject)){
                $subject = ['' => $subject];
            }
            foreach ($subject as $subjectkey => $closure) {
                $this->registerCommand($action, $subjectkey, $closure);
            }
        }
    }

    public function do(string $action, $subject = null, array $withOptions = [])
    {
        $action = strtolower($action);
        $actions = array_keys($this->commandPool);
        if(empty($action) || !in_array($action,$actions)){
            throw new \InvalidArgumentException("$action $subject is not a valid command. I need a verb like: ".implode(', ', $actions), 1);
        }

        $subjects = array_keys($this->commandPool[$action]);
        $subject = strtolower($subject ?? '');
        if(!in_array($subject,$subjects)){            
            throw new \InvalidArgumentException("$action $subject is not a valid command. I need a noun like: ".implode(', ', $subjects)."   ", 1);
        }

        $closure = $this->commandPool[$action][$subject];

        $this->commandOptionsBag = $withOptions;

        return Utils::callClosure($closure, $withOptions);

    }

    public function theOptions():array
    {
        return $this->commandOptionsBag;
    }

    public function doFromCLI(array $argv = ['please'])
    {
        if(empty($argv)){
            die("\nplease command usage: php please action [argument1] [argument2] [--option1] [--option2=\"with value\"]   \n");
            //throw new \InvalidArgumentException("No arguments sent", 1);
        }

        if(empty($argv[0]) || $argv[0] !== 'please'){
            die("\nplease command usage: php please action [argument1] [argument2] [--option1] [--option2=\"with value\"]   \n");
            //From command line the first argument is the script name, so it is intended to use the script 'please'. 
            //throw new \InvalidArgumentException("You did not say please. Be polite: \$argv = ['please', ...] ", 1);
        }
    
        array_shift($argv);//Now I am talking with a polite person. 
     
        //I will take the options with the formats: -o[value] | --option[=value] | --option[="bigger value"] | 
        $preopts = preg_grep("/^\-(([a-zA-Z0-9]{1}\=?\w*)|(\-[a-zA-Z0-9]+)(\=.+)?)$/",$argv);   
        $options = [];
        if(!empty($preopts)){            
            $argv = array_diff($argv, $preopts); //I will take out all the options from the argv array
            foreach ($preopts as $option) {
                preg_match("/^(\-[a-z]|\-\-[a-zA-Z0-9]+)/",$option,$match);        
                $options[str_replace('-','',$match[0])] = str_replace([$match[0],'='],'',$option);
            }
        }

        $action = array_shift($argv); // First argument is a verb
        $subject = array_shift($argv); // Second optional a noun
        
        if(!empty($argv)){
            //If the array still not empty, I will take the next argument as the option "name"
            $options['name'] = $options['name'] ?? array_shift($argv);

            //I will save any other argument left into the _args array just in case.
            while(count($argv) > 0){
                $options['_args'][] =  array_shift($argv);
            }
        }

        if(empty($action)){
            print ("\nplease command usage:\nphp please <action> [argument1] [argument2] [--option1] [--option2=\"with value\"] ... [--optionN]  \n");
            print ("\nACTIONS\t\tSUBJECT\n");
            exit(0);
        }
        
        //Finally do verb to noun with options. 
        $output = $this->do($action,$subject,$options);

        if(isset($options['thanks'])){
            print "\nYou are welcome!\n";
        }

        print $output;
    }

    private function list_commands()
    {
        foreach ($this->commandPool as $action => $subject) {
            print "$action\n";

        }
    }


    //php please action [subject] [--option ] [--option2="with value"]
    //action
    //subject1
    //-o, --option        //description
    //subject2


}