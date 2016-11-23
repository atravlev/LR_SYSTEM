<?php
require_once 'core/init.php';

$user = new User();

if(!$user->isLoggedIn()) {
    Redirect::to('index.php');
}

if(Input::exists()){
    if(Token::check(Input::get('token'))) {
        $validate = new Validate();
        $validation = $validate->check($_POST, array(
            'password_current' => array(
                'required' => true,
                'min' => 6
            ),
            'password_new' => array(
                'required' => true,
                'min' => 6
            ),
            'password_new_again' => array(
                'required' => true,
                'matches' => 'password_new'
            )
        ));
        
        if($validation->passed()) {
            if(Hash::make(Input::get('password_current'), $user->data()->salt)
                    !== $user->data()->password) {
                echo 'Your current password is wrong !';
            }else{
                $salt = Hash::salt(32);
                $user->update($user->data()->id,array(
                    'password' => Hash::make(Input::get('password_new'),$salt),
                    'salt' => $salt
                ));
                
                Session::flash('home', 'Your password has been changed !');
                Redirect::to('index.php');
            }
        }
    }
}

?>

<form action="" method="POST">
    <div class="field">
        <label for="password_current">Enter your current password</label>
        <input type="password" name="password_current" id="password_current" value="" />
    </div>

    <div class="field">
        <label for="password_new">Enter your new password</label>
        <input type="password" name="password_new" id="password_new" />
    </div>

    <div class="field">
        <label for="password_new_again">Enter your new password again</label>
        <input type="password" name="password_new_again" id="password_again" />
    </div>

    <input type="hidden" name="token" value="<?php echo Token::generate(); ?>" />
    <input type="submit" value="Change password" />
</form>

