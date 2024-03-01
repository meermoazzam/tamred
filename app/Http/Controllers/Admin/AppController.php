<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Comment\UpdateRequest as CommentUpdateRequest;
use App\Http\Requests\Post\UpdateRequest;
use App\Http\Requests\User\UpdateRequest as UserUpdateRequest;
use App\Http\Resources\AlbumResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\CommentResource;
use App\Services\AlbumService;
use App\Http\Resources\PostResource;
use App\Http\Resources\UserResource;
use App\Models\Album;
use App\Models\BlockUser;
use App\Models\Category;
use App\Models\CategoryPost;
use App\Models\Chat\Message;
use App\Models\Chat\Participant;
use App\Models\Comment;
use App\Models\FollowUser;
use App\Models\Media;
use App\Models\Post;
use App\Models\Reaction;
use App\Models\User;
use App\Models\UserCategory;
use App\Models\UserMeta;
use Exception;
use Illuminate\Http\Request;
use App\Services\CategoryService;
use App\Services\CommentService;
use App\Services\PostService;
use App\Services\UserService;
use App\Traits\ResponseManager;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class AppController extends Controller
{
    use ResponseManager;

    public $albumService, $userService, $postService, $categoryService, $commentService;

    public function __construct(
        AlbumService $albumService, UserService $userService,
        PostService $postService, CategoryService $categoryService,
        CommentService $commentService
    ){
        $this->albumService = $albumService;
        $this->userService = $userService;
        $this->categoryService = $categoryService;
        $this->postService = $postService;
        $this->commentService = $commentService;
    }

    public function login(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if ($validation->fails()) {
                return back()->with([
                    'error' => 'Validation failed',
                ]);
            }

            $user = User::where('email', $request->email)->where('is_admin', 1)->first();
            if ($user) {
                if (!Auth::attempt($request->only(['email', 'password'], $request->remember ? true : false))) {
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

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login.get');
    }

    public function dashboard()
    {

        return view('admin.dashboard');
    }

    public function getUsers()
    {
        $users = UserResource::collection(User::withCount(['post', 'follower', 'following'])->get());
        return view('admin.users.index')->with(['users' => $users]);
    }
    public function updateUsers(UserUpdateRequest $request)
    {
        try {
            $data = $request->validated();
            if($request->status) {
                $data['status'] = $request->status;
            }
            $isUpdated = User::where('id', $request->id)->update($data);

            return $this->jsonSuccess(200, 'Successfully updated!');
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

    public function getPosts()
    {
        $posts = PostResource::collection(Post::with('user', 'media')->statusNot('draft')->get());
        return view('admin.posts.index')->with(['posts' => $posts]);
    }
    public function updatePosts(UpdateRequest $request)
    {
        try {
            $data = $request->validated();
            if($request->status) {
                $data['status'] = $request->status;
            }
            $isUpdated = Post::where('id', $request->id)->update($data);
            return $this->jsonSuccess(200, 'Successfully updated!');
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }



    // categories
    public function getCategories()
    {
        $categories = CategoryResource::collection(Category::withCount('subCategories')->with('parent')->get());
        return view('admin.categories.index')->with(['categories' => $categories]);
    }
    public function createCategories(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
	    		'name' => 'required|string',
	    	]);
	    	if($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation Failed, Please enter the required fields',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = [
                'name' => $request->name,
                'parent_id' => $request->parent_id != 0 ? $request->parent_id : null,
            ];

            if ($request->file) {
                $file = $request->file('file');
                $slug = 'images/categories/' . $file->getClientOriginalName();
                $file->storeAs('images/categories/', $file->getClientOriginalName(), 'public');
                $data['icon'] = $slug;
            }

            $category = Category::create($data);
            return $this->jsonSuccess(200, 'Successfully created!');
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }
    public function updateCategories(Request $request)
    {
        try {
            $data = [
                'name' => $request->name,
                'parent_id' => $request->parent_id != 0 ? $request->parent_id : null,
            ];

            if ($request->file) {
                $file = $request->file('file');
                $slug = 'images/categories/' . $file->getClientOriginalName();
                $file->storeAs('images/categories/', $file->getClientOriginalName(), 'public');
                $data['icon'] = $slug;
            }

            $isUpdated = Category::where('id', $request->id)->update($data);
            return $this->jsonSuccess(200, 'Successfully updated!');
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }
    public function deleteCategories(Request $request)
    {
        return $this->categoryService->delete($request->id);
    }


    // albums
    public function getAlbums()
    {
        $albums = AlbumResource::collection(Album::with('user')->withCount('posts')->get());
        $updatedAlbums = $albums->map(function($album) {
            $album->media_count = $album->media_count;
            return $album;
        });
        $albums = $updatedAlbums;

        return view('admin.albums.index')->with(['albums' => $albums]);
    }
    public function updateAlbums(Request $request)
    {
        try {
            Album::where('id', $request->id)->update(['name' => $request->name]);
            return $this->jsonSuccess(200, 'Successfully updated!');
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }
    public function deleteAlbums(Request $request)
    {
        $album = Album::where('id', $request->id)->first();
        return $this->albumService->delete($album->user_id, $request->id);
    }
    public function recoverAlbums(Request $request)
    {
        try {
            $album = Album::where('id', $request->id)->update(['status' => 'published']);
            return $this->jsonSuccess(200, 'Album recoverd as published');
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }


    // comments
    public function getComments()
    {
        $comments = CommentResource::collection(Comment::with('user')->withCount('children')->get());
        return view('admin.comments.index')->with(['comments' => $comments]);
    }
    public function updateComments(CommentUpdateRequest $request)
    {
        try {
            $data = $request->validated();
            if($request->status) {
                $data['status'] = $request->status;
            }
            Comment::where('id', $request->id)->update($data);
            return $this->jsonSuccess(200, 'Successfully updated!');
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

    public function columnToKey($array, string $column = 'id')
    {
        $result = [];
        foreach ($array as $row) {
            if ($row->$column) {
                $result[$row[$column]] = $row;
            }
        }
        return $result;
    }
}
