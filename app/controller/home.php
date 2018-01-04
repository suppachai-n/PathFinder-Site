<?php

use Controller\View\View;
use Controller\Auth\Login;
use Controller\Auth\Register;
use Controller\User\Profile;
use Controller\Utils\Email;

class Home extends Controller{
  protected $indexlayout = 'app/view/layouts/index.php';
  protected $homelayout = 'app/view/layouts/home.php';
  protected $timeline = 'app/view/layouts/main/timeline/index.php';
  protected $formlogin = 'app/view/layouts/index/forms/formlogin.php';
  protected $registerlogin = 'app/view/layouts/index/forms/formregister.php';
  protected $recoverform = 'app/view/layouts/index/forms/formforgotpassword.php';
  protected $createprofile = 'app/view/layouts/index/forms/formcreateprofile.php';
  protected $favorite = 'app/view/layouts/main/user/favorite.php';

  public function __construct(){

  }

  public static function index(){
    if(isset($_SESSION['social_id'])){
      $user = Profile::profileLoad($_SESSION['social_id']);
      $user = mysqli_fetch_assoc($user);
      if($user['status'] == 0 && $user['skip'] == 0){
        header("Location: /home/createprofile/");
        exit();
      }else{
        echo View::render($this->homelayout, "home");
      }
    }else{
      echo View::render($this->indexlayout, $this->formlogin);
    }
  }

  public static function registration(){
    if(isset($_SESSION['social_id'])){
      header("Location: /");
      exit();
    }else{
      echo View::render($this->indexlayout, $this->registerlogin);
    }
  }

  public static function recover(){
    if(isset($_SESSION['social_id'])){
      echo View::render($this->indexlayout, $this->recoverform);
    }else{
      header("Location: /");
      exit();
    }
  }

  public static function sendRecover(){
    if(isset($_SESSION['social_id'])){
      Email::sendEmail($_SESSION['social_id'], 1);
    }else{
      header("Location: /");
      exit();
    }
  }

  public static function createprofile(){
    if(!isset($_SESSION['social_id'])){
      header("Location: /");
      exit();
    }else{
      $user = Profile::profileLoad($_SESSION['social_id']);
      $user = mysqli_fetch_assoc($user);
      if($user['status'] == 0){
        echo View::render($this->indexlayout, $this->createprofile);
      }else if($user['skip'] == 1){
        header("Location: /");
        exit();
      }else{
        header("Location: /");
        exit();
      }
    }
  }

  public static function favorites(){
    if(isset($_SESSION['social_id'])){
      echo View::render($this->homelayout, array("favorite", $this->favorite));
    }else{
      header("Location: /");
      exit();
    }
  }

  public static function login(){
    $email = $_POST['email'];
    $pass = $_POST['password'];

    Login::login($email, $pass);
  }

  public static function logout(){
    Login::logout();
  }

  public static function changelanguage(){
    $_SESSION['language'] = $_GET['language'];
    if(isset($_SESSION['social_id'])){
      Login::setUserLang($_SESSION['social_id'], $_GET['language']);
    }
  }
}
