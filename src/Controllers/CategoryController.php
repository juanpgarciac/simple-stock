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
    {   $category =[];
        if($id){
            $category = (new CategoryRepository(app()->getAppStorage()))->find($id);
        }
        $categories = (new CategoryRepository(app()->getAppStorage()))->results();        
        return compact('category','categories');
    }

    public function store()
    {
        $insert = request('_request');
        $id = request('id');
        $insert['parent_id'] = empty($insert['parent_id']) ? null : $insert['parent_id']; 
        $message = 'created';
        if(is_null($id)){
            $category = (new CategoryRepository(app()->getAppStorage()))->insert($insert);
        }else{
            $category = (new CategoryRepository(app()->getAppStorage()))->update($insert);
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