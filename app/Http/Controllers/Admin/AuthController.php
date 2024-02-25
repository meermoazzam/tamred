<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\AlbumResource;
use App\Http\Resources\CategoryResource;
use App\Services\AlbumService;
use App\Http\Resources\PersonalResource;
use App\Http\Resources\PostResource;
use App\Http\Resources\UserResource;
use App\Mail\EmailVerification;
use App\Mail\ForgotPassword;
use App\Models\Album;
use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use App\Models\UserMeta;
use Exception;
use Illuminate\Http\Request;
use App\Services\AuthService;
use App\Services\CategoryService;
use App\Services\PostService;
use App\Services\UserService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{

    public $albumService, $userService, $postService, $categoryService;

    public function __construct(AlbumService $albumService, UserService $userService, PostService $postService, CategoryService $categoryService) {
    	$this->albumService = $albumService;
    	$this->userService = $userService;
    	$this->categoryService = $categoryService;
    	$this->postService = $postService;
    }

    public function login(Request $request) {
        try {
            $validation = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if($validation->fails()){
                return back()->with([
                    'error' => 'Validation failed',
                ]);
            }

            $user = User::where('email', $request->email)->where('is_admin', 1)->first();
            if ($user) {
                if(!Auth::attempt($request->only(['email', 'password'], $request->remember ? true : false))){
                    return back()->with([
                        'error' => 'Incorrect Email or Password',
                    ]);
                } else {
                    return redirect()->route('dashboard')->with(['success' => 'Logged in successfully!']);
                }
            } else {
                return back()->with([
                    'error' => 'Incorrect Email or Password',
                ]);
            }

        } catch (Exception $exception) {
			return back()->with([
                'error' => 'Server Error, ' . $exception->getMessage() . '',
            ]);
    	}
    }

    public function logout(){
        Auth::logout();
        return redirect()->route('login.get');
    }

    public function dashboard() {

        return view('admin.dashboard');
    }

    public function getUsers() {
        $users = UserResource::collection(User::withCount(['post', 'follower', 'following'])->get());
        return view('admin.users.index')->with(['users' => $users]);
    }

    public function getPosts() {
        $posts = PostResource::collection(Post::with('user', 'media', 'categories')->get());
        return view('admin.posts.index')->with(['posts' => $posts]);
    }

    public function getAlbums() {
        $albums = AlbumResource::collection(Album::with('user')->withCount('posts','media')->get());
        return view('admin.albums.index')->with(['albums' => $albums]);
    }

    public function getCategories() {
        $categories = CategoryResource::collection(Category::withCount('subCategories')->get());
        return view('admin.categories.index')->with(['categories' => $categories]);
    }


}
