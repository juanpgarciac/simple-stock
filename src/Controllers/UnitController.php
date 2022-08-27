<?php
namespace Controllers;

use Core\Classes\Controller;
use Models\UnitRepository;

class UnitController extends Controller
{
    public function index()
    {
        $units = (new UnitRepository(app()->getAppStorage()))->results();
        view('/unit/index')->with(compact('units'))->render();
    }

    public function edit($id)
    {   $record =[];
        if($id){
            $record = (new UnitRepository(app()->getAppStorage()))->find($id);
            
        }
        return $record;
    }

    public function store()
    {
        $id = request('id');
        if(empty(request('unit'))){
            redirect('/unit/create?message=Unit description cannot be empty&error=1');  
        }
        $message = 'created';
        if(is_null($id)){
            $unit = (new UnitRepository(app()->getAppStorage()))->insert(request());
        }else{
            $unit = (new UnitRepository(app()->getAppStorage()))->update(request());
            $message = 'updated';
        }
        if($unit === false){
            redirect('/unit?message=Error&error=1');    
        }
        
        redirect('/unit?message=Unit '.$message);
    }

    public function destroy($id)
    {
        (new UnitRepository(app()->getAppStorage()))->delete($id);
        redirect('/unit?message=Unit deleted');
    }
}