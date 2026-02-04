<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/login.css'])
    <title>Log In</title>
</head>
<body>
    <nav>
        <a href="/">
            <div id="logo">
                <img src="/images/nav_images/vinyl_icon.svg">
                <p>MusicVault</p>
            </div>
        </a>

        <a href="/showsignup">Sign Up</a>
       
    </nav>

    @auth
    <p>You are logged in!</p>
    <form action="/logout" method="POST">
    @csrf
    <button>Log out</button>
    </form>
    @else
    <main>
            <h1>Log In</h1>
            <form id="sign_up_form" action="/login" method="post">
                @csrf
                <div class="form_parts">
                    <label>Name</label>
                    <input name="loginname" type="nane">
                </div>

                <div class="form_parts">
                    <label>Password</label>
                    <input name="loginpassword" type="password">
                </div>

                <div class="form_parts">
                    <input id="submit_btn" type="submit" value="Log In">
                </div>

            </form>
    </main>
    @endauth

    
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