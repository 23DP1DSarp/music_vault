<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/login.css'])
    <title>Sign Up</title>
</head>
<body>
    <nav>
        <div id="logo">
            <img src="images/nav_images/vinyl_icon.svg">
            <p>MusicVault</p>
        </div>
        <a href="/showlogin">Log In</a>
       
    </nav>

    <main>
            <h1>Sign Up</h1>
            <form id="sign_up_form" action="/register" method="post">
                @csrf
                <div class="form_parts">
                    <label>Username</label>
                    <input name="name" type="text" class="@error('name')  is-invalid @enderror">
                    @error('name')
                        <div class="error_message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form_parts">
                    <label>Password</label>
                    <input name="password" type="password" class="@error('password')  is-invalid @enderror">
                    @error('password')
                        <div class="error_message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form_parts">
                    <label>Email</label>
                    <input name="email" type="email" class="@error('email')  is-invalid @enderror">
                    @error('email')
                        <div class="error_message">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form_parts">
                    <label>Date of Birth</label>
                    <input name="date_of_birth" type="date" class="@error('date_of_birth')  is-invalid @enderror">
                    @error('date_of_birth')
                        <div class="error_message">You must be at least 13 years old to register.</div>
                    @enderror
                </div>

                <div class="form_parts">
                    <input id="submit_btn" type="submit" value="Sign Up">
                </div>

            </form>
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
                    <img class="icon" src="images/facebook_icon.svg">
                    <img class="icon" src="images/instagram_icon.svg">
                    <img class="icon" src="images/twitter_icon.svg">
                    <img class="icon" src="images/youtube_icon.svg">
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