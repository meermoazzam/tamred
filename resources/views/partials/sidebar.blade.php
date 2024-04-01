
<style>
    #sidebar {

        background: #32373d;
        color: #fff;
        position: fixed;
        height: 100vh;
    }
    #sidebar ul li a {
        padding: 15px 15px;
        display: block;
        color: rgba(255, 255, 255, 0.6);
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }
    #sidebar ul li {
        font-size: 24px;
    }
    *, *::before, *::after {
        -webkit-box-sizing: border-box;
        box-sizing: border-box;
    }

    #sidebar ul li a i {
        width: 20px;
    }

    li {
        display: list-item;
        text-align: -webkit-match-parent;
    }

    ul {
        display: block;
        list-style-type: disc;
        margin-block-start: 1em;
        margin-block-end: 1em;
        margin-inline-start: 0px;
        margin-inline-end: 0px;
        padding-inline-start: 40px;
    }

</style>
    <nav id="sidebar" class="col-md-2">
        <div class="img bg-wrap text-center py-4">
            <div class="user-logo">
                {{-- <div class="img" style="background-image: url(images/logo.jpg);"></div> --}}
                <h3>Tamred</h3>
            </div>
        </div>
        <ul class="list-unstyled components ">
            <li>
                <a href="{{ route('dashboard'); }}">
                    {{-- <i class="fas fa-home"></i> --}}
                    Dashboard
                </a>
            </li>
            <li>
                <a href="{{ route('admin.users.get'); }}">
                    {{-- <i class="fas fa-home"></i> --}}
                    Users
                </a>
            </li>
            <li>
                <a href="{{ route('admin.posts.get'); }}">
                    {{-- <i class="fas fa-home"></i> --}}
                    Posts
                </a>
            </li>
            <li>
                <a href="{{ route('admin.albums.get'); }}">
                    {{-- <i class="fas fa-home"></i> --}}
                    Albums
                </a>
            </li>
            <li>
                <a href="{{ route('admin.categories.get'); }}">
                    {{-- <i class="fas fa-home"></i> --}}
                    Categories
                </a>
            </li>
            <li>
                <a href="{{ route('admin.comments.get'); }}">
                    {{-- <i class="fas fa-home"></i> --}}
                    Comments
                </a>
            </li>
            <li>
                <a href="{{ route('admin.adds.get'); }}">
                    {{-- <i class="fas fa-home"></i> --}}
                    ads
                </a>
            </li>
            <li>
                <a href="{{ route('admin.explorescreendata.get'); }}">
                    {{-- <i class="fas fa-home"></i> --}}
                    Expl.Screen Data
                </a>
            </li>
        </ul>
        <ul class="list-unstyled components" style="position: absolute; bottom: 0px;">
            <li>
                <a href="{{ route('logout'); }}">
                    {{-- <i class="fas fa-home"></i> --}}
                    Logout
                </a>
            </li>
        </ul>
    </nav>
