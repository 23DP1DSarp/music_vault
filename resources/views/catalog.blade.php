<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/catalog.css'])
    <title>Catalog</title>
</head>
<body>
    <nav>
        <div id="navwrapper">
        <a href="/">
            <div id="logo">
                <img src="images/nav_images/vinyl_icon.svg">
                <p>MusicVault</p>
            </div>
        </a>

        <div id="navbuttons">
            <ul>
                <li><a href="/showcatalog"><b>Catalog</b></a></li>
                <li>Genres</li>
                <li>Artists</li>
                <li>Forums</li>
                @auth
                <li><a href="/showaddalbum">Add Album</a></li>
                @endauth
            </ul>
        </div>

        <div id="rightbuttons">
            
            <input type="text" id="searchbar" name="recordsearch" placeholder="Search records...">
            <img id="shoppingcart" src="images/nav_images/shopping_cart_icon.svg">
            @auth
            <a href="/showprofile">{{Auth::user()->name}}</a>
            <form action="/logout" method="POST">
                @csrf
                <button id="logoutbtn">Log out</button>
            </form>
            @else
            <a href="/showlogin">Log In</a>
            <a href="/showsignup">Sign Up</a>
            @endauth
        </div>
    </div>
    </nav>


    <main>
        
       <form id="filters">
        <h1>Filters</h1>
            <div id="genre_filters">
                <h2>Genre</h2>
                <div class="genre_filter">
                    <input type="checkbox" name="genre1" value="Rock">
                    <label for="genre1"> Rock</label>
                </div>
                <div class="genre_filter">
                    <input type="checkbox" name="genre2" value="Jazz">
                    <label for="genre2"> Jazz</label>
                </div>
                <div class="genre_filter">
                    <input type="checkbox" name="genre3" value="Electronic">
                    <label for="genre3"> Electronic</label>
                </div>
                <div class="genre_filter">
                    <input type="checkbox" name="genre4" value="Pop">
                    <label for="genre4"> Pop</label>
                </div>
                <div class="genre_filter">
                    <input type="checkbox" name="genre5" value="Hip-Hop">
                    <label for="genre5"> Hip-Hop</label>
                </div>
                <div class="genre_filter">
                    <input type="checkbox" name="genre6" value="Classical">
                    <label for="genre6"> Classical</label>
                </div>
            </div>

            <div id="price_range_filters">
                <h2>Price Range</h2>
                <div id="price_range_filters_inputs">
                    <input type="text" name="min">
                    <p> - </p>
                    <input type="text" name="max"> 
                </div>
            </div>

            <div id="decade_filters">
                <h2>Decade</h2>
                <div class="decade_filter">
                    <input type="checkbox" name="decade1" value="2020">
                    <label for="decade1"> 2020</label>
                </div>
                <div class="decade_filter">
                    <input type="checkbox" name="decade2" value="2010">
                    <label for="decade2"> 2010</label>
                </div>
                <div class="decade_filter">
                    <input type="checkbox" name="decade3" value="2000">
                    <label for="decade3"> 2000</label>
                </div>
                <div class="decade_filter">
                    <input type="checkbox" name="decade4" value="1990">
                    <label for="decade4"> 1990</label>
                </div>
                <div class="decade_filter">
                    <input type="checkbox" name="decade5" value="1980">
                    <label for="decade5"> 1980</label>
                </div>
                <div class="decade_filter">
                    <input type="checkbox" name="decade6" value="1970">
                    <label for="decade6"> 1970</label>
                </div>
            </div>
       </form>

       <div id="albums">
            <h1>Catalog</h1>
            <div id="album_cards">
            @forelse ($albums as $album)
                <div id="album_data">
                    @if ($album->cover)
                        <img src="{{ asset('storage/'.$album->cover) }}">
                    @endif
                    <a href="/album_info/{{$album->id}}"><h3>{{$album['title']}}</h3></a>
                    <p>{{$album['author']}}</p>
                    <div id="genre_and_year">
                    <p>{{$album['genre']}}</p>
                    <p>&nbsp;•&nbsp;</p>
                    <p>{{date('Y', strtotime($album['release_date']))}}</p>
                    </div>
                </div>
            @empty
            <p>No albums found.</p>
            @endforelse
            </div>
            </div>
        </div>

    </main>

    <footer>
        <div id="footer_wrapper">
        <div id="footer_top">
            <div id="footer_info">
                <div id="footer_logo">
                    <img src="/images/footer_images/vinyl_icon.svg">
                    <p>MusicVault</p>
                </div>
                <p id="footer_info_text">
                    Your premier destination for music records. Discover,
                    collect, and enjoy music the way it was meant to be
                    heard.
                </p>

                <div id="icons">
                    <img class="icon" src="/images/footer_images/facebook_icon.svg">
                    <img class="icon" src="/images/footer_images/instagram_icon.svg">
                    <img class="icon" src="/images/footer_images/twitter_icon.svg">
                    <img class="icon" src="/images/footer_images/youtube_icon.svg">
                </div>
                
            </div>

            <div>
                <h6>Quick Links</h6>

                <ul>
                    <li>New Releases</li>
                    <li>Pre-Orders</li>
                    <li>Sale Items</li>
                    <li>Rare Finds</li>
                    <li>Gift Cards</li>
                </ul>
            </div>

            <div>
                
                <h6>Genres</h6>

                <ul>
                    <li>Rock</li>
                    <li>Jazz</li>
                    <li>Electronic</li>
                    <li>Hip-Hop</li>
                    <li>Classical</li>
                </ul>
            </div>

            <div id="subscribe_form">
                <h6>Stay Updated</h6>
                <p>Get notified about new releases and exclusive deals.</p>

                <form action="" method="post">
                    <input id="email_input" placeholder="Enter your email" name="subscription-email" type="email" required>
                    <input id="subscribe_form_submit" type="submit" value="Subscribe">
                </form>
            </div>
        </div>

        
        <div id="footer_bottom">

            <ul>
                <li>Privacy Policy</li>
                <li>Terms of Service</li>
                <li>Shipping Info</li>
                <li>Returns</li>
            </ul>

            <p>&copy; 2025 MusicVault. All rights reserved.</p>

        </div>
        </div>
    </footer>

</body>
</html>