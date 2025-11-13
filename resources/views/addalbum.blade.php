<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/addalbum.css'])
    <title>Add Album</title>
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
            </ul>
        </div>

        <div id="rightbuttons">
            
            <input type="text" id="searchbar" name="recordsearch" placeholder="Search records...">
            <img id="shoppingcart" src="images/nav_images/shopping_cart_icon.svg">
            @auth
            <p>{{Auth::user()->name}}</p>
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
        @auth
        <h1>Add Album</h1>
        <div id="form_wrapper">
        
            <form id="add_album_with_tracks" action="/add_album_with_tracks" method="POST" enctype="multipart/form-data">
            @csrf
                <div id="album_wrapper">
                    <div id="input_side">
                        <label>Title</label>
                        <input class="album_input" name="title" type="text">
                        <label>Author</label>
                        <input class="album_input" name="author" type="text">
                        <label>Genre</label>
                        <input class="album_input" name="genre" type="text">
                        <label>Date of release</label>
                        <input class="album_input" name="release_date" type="date">
                    </div>
                        
                        <div id="album_cover_side">
                            <label>Cover</label>
                            <input name="cover" type="file" accept="image/*">
                        </div>
                </div>
            
            
            
            
                        
                        <h1>Track List</h1>
                        
                        @for ($i = 0; $i < 5; $i++)
                        <div id="track_list">
                            <div class="track_info">
                                <div class="input_labels">
                                    <label>Track Nr.</label>
                                    <input type="number" class="track_nr" name="tracks[{{ $i }}][position]">
                                </div>
                                <div class="input_labels">
                                    <label>Author</label>
                                    <input type="text" class="author" name="tracks[{{ $i }}][artist]">
                                </div>
                                <div class="input_labels">
                                    <label>Title</label>
                                    <input type="text" class="title" name="tracks[{{ $i }}][song_title]">
                                </div>
                                <div class="input_labels">
                                    <label>Duration</label>
                                    <input type="text" class="duration" name="tracks[{{ $i }}][duration]">
                                </div>
                            </div>
                            @endfor
                            
                            
                        </div> 
                      </form>
                
                <input id="submit_btn" type="submit" value="Add Album">
                </div> 
                
            
        
        @else
        <h1>This page is only for registered users.</h1>
        @endauth
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

            <p>&copy; 2025 VinylVault. All rights reserved.</p>

        </div>
        </div>
    </footer>

</body>
</html>