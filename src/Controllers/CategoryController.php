<?php
namespace Controllers;

use Core\Classes\Controller;
use Models\CategoryRepository;

class CategoryController extends Controller
{
    private $categoryRepository;

    public function __construct()
    {
        $this->categoryRepository = new CategoryRepository(app()->getAppStorage());
    }
    
    public function index()
    {
        $categories = $this->categoryRepository->results();
        view('/category/index')->with(compact('categories'))->render();
    }

    public function edit($id)
    {   $category =[];
        if($id){
            $category = $this->categoryRepository->find($id);
        }
        $categories = $this->categoryRepository->orderBy('category')->results();        
        return compact('category','categories');
    }

    public function store()
    {
        if(empty(request('category'))){
            back('?message=Category description cannot be empty&error=1');  
        }
        $insert = request('_request');
        $id = request('id');
        $insert['parent_id'] = empty($insert['parent_id']) ? null : $insert['parent_id']; 
        $message = 'created';
        if(is_null($id)){
            $category = $this->categoryRepository->insert($insert);
        }else{
            $category = $this->categoryRepository->update($insert);
            $message = 'updated';
        }
        if($category === false){
            redirect('/category?message=Error&error=1');    
        }
        
        redirect('/category?message=Category '.$message);
    }

    public function destroy($id)
    {
        $this->categoryRepository->delete($id);
        redirect('/category?message=Category deleted');
    }
}