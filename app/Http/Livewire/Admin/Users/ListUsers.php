<?php

namespace App\Http\Livewire\Admin\Users;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;
use  App\Models\User;

class ListUsers extends Component
{   
    public $state = [];
    public $user;
    public $showEditModal = false;
    public $userIdBeingRemoved = null;
    public function addnew()
    {
        $this->state =[] ;
        $this->showEditModal=false;
        $this->dispatchBrowserEvent('show-form');
    }
   // public $name,$email,$password,$password_confirmation;
 
    public function updateUser()
    {
     $validatedData = Validator::make($this->state, [
      'name' => 'required',
      'email' => 'email|required|unique:users,email,'.$this->user->id,
      'password' => 'sometimes|confirmed',
      ])->validate();
        if(!empty( $validatedData['password'])){
        $validatedData['password'] = bcrypt( $validatedData['password']) ;
        }
   
    
     $this->user->update($validatedData);
      $this->dispatchBrowserEvent('hide-form', ['message' => 'User Updated succesfully']);
     
    }
    public function edit(User $user)
    {   
    
        $this->showEditModal = true;
        $this->user =$user;
        $this->state = $user->toArray();
        $this->dispatchBrowserEvent('show-form');
    }
    public function  createUser()
    {
        $validatedData = Validator::make($this->state, [
            'name' => 'required',
            'email' => 'email|required|unique:users',
            'password' => 'required|confirmed',
            ])->validate();
      
            $validatedData['password'] = bcrypt( $validatedData['password']) ;
            User::create($validatedData);
          //   session()->flash('message', 'User added Succesfully !');
            $this->dispatchBrowserEvent('hide-form', ['message' => 'User added succesfully']);
            return redirect()->back();
    }
    public function confirmUserRemovel($userID)
    {
    $this->userIdBeingRemoved = $userID;
    $this->dispatchBrowserEvent('show-delete-modal');
    }
    public function deleteUser()
    {
        $user = User::findOrFail($this->userIdBeingRemoved);
        $user->delete();
        $this->dispatchBrowserEvent('hide-delete-modal', ['message' => 'User Delete succesfully']);
    }
    public function render()
    {
        $users = User::latest()->paginate();
        return view('livewire.admin.users.list-users',[
            'users' => $users,
        ]);
    }
}
