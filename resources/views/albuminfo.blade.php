<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Album Info</title>
    @vite(['resources/css/albuminfo.css'])
</head>
<body>
     <nav>
        <div id="navwrapper">
        <a href="/">
            <div id="logo">
                <img src="/images/nav_images/vinyl_icon.svg">
                <p>MusicVault</p>
            </div>
        </a>

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
            <img id="shoppingcart" src="/images/nav_images/shopping_cart_icon.svg">
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
        <div id="album_section">
            <div id="album_info">
                <img id="album_cover" src="{{ asset('storage/'.$album->cover) }}">
                <div id="album_text_info">
                    <h1>{{$album->title}} - {{$album->author}}</h1>
                    <p>{{$album->notes}}</p>
                </div>
            </div>

            <h1>Tracklist</h1>
            <div id="tracklist">
                <div id="track_position_col">
                    <h4 id="track_position_title">№</h4>
                    @foreach ($tracks as $track)
                    <p class="track_nr">{{$track->position}}</p>
                    @endforeach
                </div>

                <div id="track_title_col">
                    <h4 id="track_title">Title</h4>
                    @foreach ($tracks as $track)
                    <p class="title">{{$track->song_title}}</p>
                    @endforeach
                </div>

                <div id="track_artist_col">
                    <h4 id="artist_title">Artist</h4>
                    @foreach ($tracks as $track)
                    <p class="artist">{{$track->artist}}</p>
                    @endforeach
                </div>

                <div id="track_duration_col">
                    <h4 id="duration_title">Duration</h4>
                    @foreach ($tracks as $track)
                    <p class="duration">{{$track->duration}}</p>
                    @endforeach
                </div>
                
            </div>
            

        </div>

            <div id="album_data">
                <h1>Album data</h1>
                <p id="author">Author: {{$album->author}}</p>
                <p id="release_date">Release date: {{$album->release_date}}</p>
                <p id="country">Country: {{$album->country}}</p>
                <p id="genre">Genre: {{$album->genre}}</p>
                <p id="label">Label: {{$album->label}}</p>
                <p id="format">Format: {{$album->format}}</p>

                <hr>

                <div id="button_sec">
                    <button id="add_to_cart_btn">Add to cart</button>
                    <form action="/add_to_collection/{{$album->id}}" method="POST">@csrf<button id="add_to_collection_btn">Add to collection</button></form>
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