<?php
namespace Controllers;

use Core\Classes\Controller;
use Models\CategoryRepository;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = (new CategoryRepository(app()->getAppStorage()))->results();
        view('/category/index')->with(compact('categories'))->render();
    }

    public function edit($id)
    {   $cat =[];
        if($id){
            $cat = (new CategoryRepository(app()->getAppStorage()))->find($id);
            
        }
        return $cat;
    }

    public function store()
    {
        $id = request('id');
        $message = 'created';
        if(is_null($id)){
            $category = (new CategoryRepository(app()->getAppStorage()))->insert(request());
        }else{
            $category = (new CategoryRepository(app()->getAppStorage()))->update(request());
            $message = 'updated';
        }
        if($category === false){
            redirect('/category?message=Error&error=1');    
        }
        
        redirect('/category?message=Category '.$message);
    }

    public function destroy($id)
    {
        (new CategoryRepository(app()->getAppStorage()))->delete($id);
        redirect('/category?message=Category deleted');
    }
}