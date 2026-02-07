<?php
namespace App\Services;
use App\Models\User;
use Illuminate\Http\Request;

class UserService{

    public function user()
    {
        return User::where('id','>','1')->get();
    }
    public function userById($id)
    {
        return User::findOrFail($id);
    }
    public function userCreate(Request $request)
    {
        $in = $request->except('_token');
        $in['password'] = bcrypt(12345678);
        $in['status'] = $request->status == 'on' ? '1' : '0';
        $user = User::create($in);

        $role = $request->get('roles', '');
        if (!empty($role)) {
            $user->syncRoles([$role]); 
        }

        $permissions = $request->get('permissions', []);
        if (!empty($permissions)) {
    $user->syncPermissions($permissions); // Use syncPermissions to replace any existing permissions
}
return $user;
}
public function userUpdate(Request $request, $id)
{
    $user = $this->userById($id);
    $in = $request->except('_token');
    $in['status'] = $request->status == 'on' ? '1' : '0';
    $user->update($in);
    $role = $request->get('roles', '');
    if (!empty($role)) {
        $user->syncRoles([$role]); 
    }
    $permissions = $request->get('permissions', []);
    $user->syncPermissions($permissions); 
    return $user;
}
public function userDelete($id)
{
    $user = $this->userById($id);
    $user->syncPermissions([]);
    $user->roles()->detach();
    $user->status = 0;
    $user->save();
    return $user;
}
}
