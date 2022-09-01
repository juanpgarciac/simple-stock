<?php
namespace Controllers;

use Core\Classes\Controller;
use Models\UnitRepository;

class UnitController extends Controller
{
    private $unitRepository;

    public function __construct()
    {
        $this->unitRepository = new UnitRepository(app()->getAppStorage());
    }

    public function index()
    {
        $units = $this->unitRepository->results();
        view('/unit/index')->with(compact('units'))->render();
    }

    public function edit($id)
    {   $record =[];
        if($id){
            $record = $this->unitRepository->find($id);
            
        }
        return $record;
    }

    public function store()
    {
        $id = request('id');
        if(empty(request('unit'))){
            back('?message=Unit description cannot be empty&error=1');  
        }
        $message = 'created';
        if(is_null($id)){
            $unit = $this->unitRepository->insert(request());
        }else{
            $unit = $this->unitRepository->update(request());
            $message = 'updated';
        }
        if($unit === false){
            redirect('/unit?message=Error&error=1');    
        }
        
        redirect('/unit?message=Unit '.$message);
    }

    public function destroy($id)
    {
        $this->unitRepository->delete($id);
        redirect('/unit?message=Unit deleted');
    }
}