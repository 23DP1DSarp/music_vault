<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css'])
    <title>Main page</title>
</head>
<body>
    <nav>
        <div id="navwrapper">
        <div id="logo">
            <img src="images/nav_images/vinyl_icon.svg">
            <p>MusicVault</p>
        </div>

        <div id="navbuttons">
            <ul>
                <li>New Releases</li>
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
        <div id="hero_section">
            <div id="left_side">
                <h1>Discover Your Next Favorite Record</h1>
                <p id="subtext">From rare pressings to the latest releases. Curated vinyl records for every music lover.</p>

                <div id="hero_buttons">
                    <button id="shop_button">Shop New Releases</button>
                    <button id="browse_button">Browse Collection</button>
                </div>

                <div id="stats">
                    <div class="stat">
                        <h2>10K+</h2>
                        <p>Records in Stock</p>
                    </div>

                    <div class="stat">
                        <h2>500+</h2>
                        <p>Artists</p>
                    </div>

                    <div class="stat">
                        <h2>50+</h2>
                        <p>Genres</p>
                    </div>
                </div>
            </div>

            <div id="right_side">
                <img src="images/main_page_images/Vinyl_records_collection.png">
            </div>

        </div>


        <div id="record_browse">
            <h4>Browse Records</h4>
            <p id="results_count">6 records found</p>

            <div id="filters">
                <form action="/show_albums" method="GET">
                    <label>Genre:</label>
                    <button class="form_button">All</button>
                    <button class="form_button" type="submit" name="genre" value="Rock">Rock</button>
                    <button class="form_button" name="genre" value="Jazz">Jazz</button>
                    <button class="form_button" name="genre" value="Electronic">Electronic</button>
                    <button class="form_button" name="genre" value="Pop">Pop</button>
                    <button class="form_button" name="genre" value="Hip-Hop">Hip-Hop</button>
                    <button class="form_button" name="genre" value="Classical">Classical</button>

                    <label>Condition:</label>
                    <select name="condition" id="condtion_select">
                        <option value="brand_new">Brand New</option>
                        <option value="new">New</option>
                        <option value="used">Used</option>
                    </select>

                    <label>Sort By:</label>
                    <select name="sort" id="sort_select">
                        <option>Name: A-Z</option>
                        <option>Name: Z-A</option>
                        <option>Price: Highest</option>
                        <option>Price: Lowest</option>
                    </select>
                </form>

                


            </div>
        </div>

        <div id="albums">
            <h2>All Albums</h2>
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
                    <form action="/add_to_collection/{{$album->id}}" method="POST">@csrf<button id="add_to_collection_btn">Add to collection</button></form>
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
                    <img src="images/footer_images/vinyl_icon.svg">
                    <p>MusicVault</p>
                </div>
                <p id="footer_info_text">
                    Your premier destination for music records. Discover,
                    collect, and enjoy music the way it was meant to be
                    heard.
                </p>

                <div id="icons">
                    <img class="icon" src="images/footer_images/facebook_icon.svg">
                    <img class="icon" src="images/footer_images/instagram_icon.svg">
                    <img class="icon" src="images/footer_images/twitter_icon.svg">
                    <img class="icon" src="images/footer_images/youtube_icon.svg">
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