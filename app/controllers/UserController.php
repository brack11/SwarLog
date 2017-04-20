<?php

use MrJuliuss\Syntara\Controllers\BaseController;
use MrJuliuss\Syntara\Services\Validators\User as UserValidator;

class UserController extends BaseController {
	public function index() {
        $this->layout = View::make('user.new');
	}

	public function store() {
       try
        {	
        	$validator = new UserValidator(Input::all(), 'create');
            if(!$validator->passes())
            {
                return Response::json(array('userCreated' => false, 'errorMessages' => $validator->getErrors()));
            }
            // create user

            $user = Sentry::getUserProvider()->create(array(
                'email'    => Input::get('email'),
                'password' => Input::get('pass'),
                'username' => Input::get('username'),
                'last_name' => (string)Input::get('last_name'),
                'first_name' => (string)Input::get('first_name'),
                // 'activated' => true,
                // 'activated_at' => time(),
            ));
            $group = Sentry::getGroupProvider()->findByName('loggers');
            $user->addGroup($group);
        }
        catch (\Cartalyst\Sentry\Users\LoginRequiredException $e){} // already catch by validators
        catch (\Cartalyst\Sentry\Users\PasswordRequiredException $e){} // already catch by validators
        catch (\Cartalyst\Sentry\Groups\GroupNotFoundException $e){}
        catch (\Cartalyst\Sentry\Users\UserExistsException $e)
        {
            return json_encode(array('userCreated' => false, 'message' => trans('syntara::users.messages.user-email-exists'), 'messageType' => 'danger'));
        }
        catch(\Exception $e)
        {
            return Response::json(array('userCreated' => false, 'message' => trans('syntara::users.messages.user-name-exists'), 'messageType' => 'danger'));
        }

        return json_encode(array('userCreated' => true, 'redirectUrl' => URL::route('config.index')));
	}

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        $user = User::find($id);
        File::deleteDirectory('uploads/backups/'.$user->username);
        $user->delete();
    }
}