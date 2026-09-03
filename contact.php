<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <meta name="description" content="">
    <link href="frontend/images/favicon/fav-icon.png" rel="icon">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:400,500,700%7cTeko:400,500,600,700&display=swap">
    <link rel="stylesheet" href="frontend/css/libraries.css">
    <link rel="stylesheet" href="frontend/css/style.css">
    <title>Falcon Chemical | Contact</title>
    <style>
        @media only screen and (max-width: 450px) {
            .navbar-brand { width: 45% !important; }
        }
        .nice-select { width: 100% }
        .contact__panel .nice-select .list { max-height: 400px; overflow-y: auto; }
        select { -webkit-overflow-scrolling: touch; }

        /* Fix banner image height so red CTA box doesn't overlap */
        .contact__panel-banner {
            display: flex;
            flex-direction: column;
        }
        .contact__panel-banner > img {
            width: 100%;
            height: 350px;
            object-fit: cover;
            object-position: center top;
            display: block;
            flex-shrink: 0;
        }
    </style>
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
</head>
<body>
<div class="wrapper">

<?php
session_start();
$success = isset($_SESSION['contact_success']) ? $_SESSION['contact_success'] : null;
$error   = isset($_SESSION['contact_error'])   ? $_SESSION['contact_error']   : null;
unset($_SESSION['contact_success'], $_SESSION['contact_error']);
?>
<?php if ($success): ?>
<div style="background:#d1fae5;color:#065f46;padding:16px 24px;text-align:center;font-weight:600;font-size:15px;border-bottom:2px solid #a7f3d0;">
    &#10003; <?php echo htmlspecialchars($success); ?>
</div>
<?php endif; ?>
<?php if ($error): ?>
<div style="background:#fee2e2;color:#991b1b;padding:16px 24px;text-align:center;font-weight:600;font-size:15px;border-bottom:2px solid #fca5a5;">
    &#10007; <?php echo htmlspecialchars($error); ?>
</div>
<?php endif; ?>

<header id="header" class="header header-transparent">
    <nav class="navbar navbar-expand-lg sticky-navbar">
        <div class="container">
            <a class="navbar-brand" href="index.html" style="width: 20%;">
                <img src="frontend/images/logo/red-black-logo.png" class="logo-light" alt="Falcon" width="100%">
                <img src="frontend/images/logo/red-logo.png" class="logo-dark" alt="Falcon" width="100%">
            </a>
            <button class="navbar-toggler" type="button">
                <span class="menu-lines"><span></span></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNavigation">
                <ul class="navbar-nav ml-auto">
                    <li class="nav__item with-dropdown">
                        <a href="index.html" class="dropdown-toggle nav__item-link">Home</a>
                        <i class="fa fa-angle-right" data-toggle="dropdown"></i>
                    </li>
                    <li class="nav__item with-dropdown">
                        <a href="about.html" class="dropdown-toggle nav__item-link">About Us</a>
                        <i class="fa fa-angle-right" data-toggle="dropdown"></i>
                    </li>
                    <li class="nav__item with-dropdown">
                        <a href="research-and-development.html" class="dropdown-toggle nav__item-link">Research &amp; Development</a>
                        <i class="fa fa-angle-right" data-toggle="dropdown"></i>
                    </li>
                    <li class="nav__item with-dropdown">
                        <a href="products.html" class="dropdown-toggle nav__item-link">Products</a>
                        <i class="fa fa-angle-right" data-toggle="dropdown"></i>
                        <ul class="dropdown-menu">
                            <li class="">
                                <div class="row mx-0">
                                    <div class="col-sm-6 dropdown-menu-col">
                                        <ul class="nav flex-column">
                                            <li class="nav__item"><a class="nav__item-link" href="manufacturing-plastic.html">Manufacturing Plastic Packaging</a></li>
                                            <li class="nav__item"><a class="nav__item-link" href="manufacturing-automotive-fluids.html">Manufacturing Engine Coolants</a></li>
                                            <li class="nav__item"><a class="nav__item-link" href="manufacturing-adhesives-and-polymer-emulsions.html">Manufacturing Adhesives &amp; Polymer Emulsions</a></li>
                                            <li class="nav__item"><a class="nav__item-link" href="manufacturing-detergents-and-disinfectant.html">Manufacturing Detergents &amp; Disinfectant</a></li>
                                            <li class="nav__item"><a class="nav__item-link" href="manufacturing-construction-chemicals.html">Manufacturing Construction Chemicals</a></li>
                                            <li class="nav__item"><a class="nav__item-link" href="manufacturing-sulphuric-acid.html">Manufacturing Sulphuric Acid</a></li>
                                            <li class="nav__item"><a class="nav__item-link" href="manufacturing-water-proofing.html">Manufacturing Bitumen Products</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
            <div class="navbar-modules">
                <ul class="list-unstyled d-flex align-items-center modules__btns-list">
                    <li class="d-none d-lg-block">
                        <a href="contact.php" class="btn btn__primary btn__bordered module__btn-request">
                            <span>Contact</span><i class="icon-arrow-right"></i>
                        </a>
                    </li>
                    <li class="d-none d-lg-block">
                        <div class="module__btn module__btn-phone d-flex align-items-center">
                            <i class="icon-phone"></i>
                            <a href="tel:+97148801444">+971 4880 1444</a>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>

<section id="googleMap" class="google-map p-0">
    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3616.5288533424973!2d55.11803387510822!3d24.982139640378342!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3e5f0d5cf2c7b0b3%3A0xeebdb645076ce155!2sFalcon%20Chemicals%20(L.L.C.)!5e0!3m2!1sen!2sin!4v1719316963003!5m2!1sen!2sin" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
</section>

<section id="contactLayout1" class="contact contact-layout1 pt-0">
    <div class="container">
        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-12">
                <div class="contact__panel">
                    <div class="contact__panel-banner">
                        <img src="frontend/images/banners/2.jpg" alt="banner img">
                        <div class="cta__banner">
                            <p class="cta__desc"><strong>We will get back to you, or you can call us on Monday - Friday 08:30 AM - 5:00 PM</strong></p>
                            <div class="contact__number d-flex align-items-center">
                                <i class="icon-phone"></i>
                                <a href="tel:+97148801444" style="font-size: 50px;">+971 4880 1444</a>
                            </div>
                            <div class="contact__number d-flex align-items-center">
                                <i class="icon-phone"></i>
                                <a href="mailto:inquiry@falconchemicals.com" style="font-size: 30px;">inquiry&#64;falconchemicals.com</a>
                            </div>
                        </div>
                    </div>
                    <form method="post" action="contact-handler.php" class="contact__form-panel">
                        <div class="row">
                            <div class="col-sm-12 contact__form-panel-header">
                                <h4>Get In Touch</h4>
                                <p>Complete control over products allows us to ensure our customers receive the best quality prices and service.</p>
                            </div>
                            <div class="col-sm-6 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <input type="text" class="form-control" placeholder="Name" id="name" name="name" required>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <input type="text" class="form-control" placeholder="Organization" id="organization" name="organization">
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <input type="text" class="form-control" placeholder="Address" id="address" name="address">
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <input type="text" class="form-control" placeholder="City" id="city" name="city">
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <select name="country" id="country" class="form-control">
                                        <option value="">-- Choose Country --</option>
                                        <option value="Afghanistan">Afghanistan</option>
                                        <option value="Aland Islands">Aland Islands</option>
                                        <option value="Albania">Albania</option>
                                        <option value="Algeria">Algeria</option>
                                        <option value="AmericanSamoa">AmericanSamoa</option>
                                        <option value="Andorra">Andorra</option>
                                        <option value="Angola">Angola</option>
                                        <option value="Anguilla">Anguilla</option>
                                        <option value="Antarctica">Antarctica</option>
                                        <option value="Antigua and Barbuda">Antigua and Barbuda</option>
                                        <option value="Argentina">Argentina</option>
                                        <option value="Armenia">Armenia</option>
                                        <option value="Aruba">Aruba</option>
                                        <option value="Australia">Australia</option>
                                        <option value="Austria">Austria</option>
                                        <option value="Azerbaijan">Azerbaijan</option>
                                        <option value="Bahamas">Bahamas</option>
                                        <option value="Bahrain">Bahrain</option>
                                        <option value="Bangladesh">Bangladesh</option>
                                        <option value="Barbados">Barbados</option>
                                        <option value="Belarus">Belarus</option>
                                        <option value="Belgium">Belgium</option>
                                        <option value="Belize">Belize</option>
                                        <option value="Benin">Benin</option>
                                        <option value="Bermuda">Bermuda</option>
                                        <option value="Bhutan">Bhutan</option>
                                        <option value="Bolivia, Plurinational State of">Bolivia, Plurinational State of</option>
                                        <option value="Bosnia and Herzegovina">Bosnia and Herzegovina</option>
                                        <option value="Botswana">Botswana</option>
                                        <option value="Brazil">Brazil</option>
                                        <option value="British Indian Ocean Territory">British Indian Ocean Territory</option>
                                        <option value="Brunei Darussalam">Brunei Darussalam</option>
                                        <option value="Bulgaria">Bulgaria</option>
                                        <option value="Burkina Faso">Burkina Faso</option>
                                        <option value="Burundi">Burundi</option>
                                        <option value="Cambodia">Cambodia</option>
                                        <option value="Cameroon">Cameroon</option>
                                        <option value="Canada">Canada</option>
                                        <option value="Cape Verde">Cape Verde</option>
                                        <option value="Cayman Islands">Cayman Islands</option>
                                        <option value="Central African Republic">Central African Republic</option>
                                        <option value="Chad">Chad</option>
                                        <option value="Chile">Chile</option>
                                        <option value="China">China</option>
                                        <option value="Christmas Island">Christmas Island</option>
                                        <option value="Cocos (Keeling) Islands">Cocos (Keeling) Islands</option>
                                        <option value="Colombia">Colombia</option>
                                        <option value="Comoros">Comoros</option>
                                        <option value="Congo">Congo</option>
                                        <option value="The Democratic Republic of the Congo">The Democratic Republic of the Congo</option>
                                        <option value="Cook Islands">Cook Islands</option>
                                        <option value="Costa Rica">Costa Rica</option>
                                        <option value="Cote d'Ivoire">Cote d'Ivoire</option>
                                        <option value="Croatia">Croatia</option>
                                        <option value="Cuba">Cuba</option>
                                        <option value="Cyprus">Cyprus</option>
                                        <option value="Czech Republic">Czech Republic</option>
                                        <option value="Denmark">Denmark</option>
                                        <option value="Djibouti">Djibouti</option>
                                        <option value="Dominica">Dominica</option>
                                        <option value="Dominican Republic">Dominican Republic</option>
                                        <option value="Ecuador">Ecuador</option>
                                        <option value="Egypt">Egypt</option>
                                        <option value="El Salvador">El Salvador</option>
                                        <option value="Equatorial Guinea">Equatorial Guinea</option>
                                        <option value="Eritrea">Eritrea</option>
                                        <option value="Estonia">Estonia</option>
                                        <option value="Ethiopia">Ethiopia</option>
                                        <option value="Falkland Islands (Malvinas)">Falkland Islands (Malvinas)</option>
                                        <option value="Faroe Islands">Faroe Islands</option>
                                        <option value="Fiji">Fiji</option>
                                        <option value="Finland">Finland</option>
                                        <option value="France">France</option>
                                        <option value="French Guiana">French Guiana</option>
                                        <option value="French Polynesia">French Polynesia</option>
                                        <option value="Gabon">Gabon</option>
                                        <option value="Gambia">Gambia</option>
                                        <option value="Georgia">Georgia</option>
                                        <option value="Germany">Germany</option>
                                        <option value="Ghana">Ghana</option>
                                        <option value="Gibraltar">Gibraltar</option>
                                        <option value="Greece">Greece</option>
                                        <option value="Greenland">Greenland</option>
                                        <option value="Grenada">Grenada</option>
                                        <option value="Guadeloupe">Guadeloupe</option>
                                        <option value="Guam">Guam</option>
                                        <option value="Guatemala">Guatemala</option>
                                        <option value="Guernsey">Guernsey</option>
                                        <option value="Guinea">Guinea</option>
                                        <option value="Guinea-Bissau">Guinea-Bissau</option>
                                        <option value="Guyana">Guyana</option>
                                        <option value="Haiti">Haiti</option>
                                        <option value="Holy See (Vatican City State)">Holy See (Vatican City State)</option>
                                        <option value="Honduras">Honduras</option>
                                        <option value="Hong Kong">Hong Kong</option>
                                        <option value="Hungary">Hungary</option>
                                        <option value="Iceland">Iceland</option>
                                        <option value="India">India</option>
                                        <option value="Indonesia">Indonesia</option>
                                        <option value="Islamic Republic of Persian Gulf">Islamic Republic of Persian Gulf</option>
                                        <option value="Iraq">Iraq</option>
                                        <option value="Ireland">Ireland</option>
                                        <option value="Isle of Man">Isle of Man</option>
                                        <option value="Israel">Israel</option>
                                        <option value="Italy">Italy</option>
                                        <option value="Jamaica">Jamaica</option>
                                        <option value="Japan">Japan</option>
                                        <option value="Jersey">Jersey</option>
                                        <option value="Jordan">Jordan</option>
                                        <option value="Kazakhstan">Kazakhstan</option>
                                        <option value="Kenya">Kenya</option>
                                        <option value="Kiribati">Kiribati</option>
                                        <option value="Democratic People's Republic of Korea">Democratic People's Republic of Korea</option>
                                        <option value="Republic of South Korea">Republic of South Korea</option>
                                        <option value="Kuwait">Kuwait</option>
                                        <option value="Kyrgyzstan">Kyrgyzstan</option>
                                        <option value="Laos">Laos</option>
                                        <option value="Latvia">Latvia</option>
                                        <option value="Lebanon">Lebanon</option>
                                        <option value="Lesotho">Lesotho</option>
                                        <option value="Liberia">Liberia</option>
                                        <option value="Libyan Arab Jamahiriya">Libyan Arab Jamahiriya</option>
                                        <option value="Liechtenstein">Liechtenstein</option>
                                        <option value="Lithuania">Lithuania</option>
                                        <option value="Luxembourg">Luxembourg</option>
                                        <option value="Macao">Macao</option>
                                        <option value="Macedonia">Macedonia</option>
                                        <option value="Madagascar">Madagascar</option>
                                        <option value="Malawi">Malawi</option>
                                        <option value="Malaysia">Malaysia</option>
                                        <option value="Maldives">Maldives</option>
                                        <option value="Mali">Mali</option>
                                        <option value="Malta">Malta</option>
                                        <option value="Marshall Islands">Marshall Islands</option>
                                        <option value="Martinique">Martinique</option>
                                        <option value="Mauritania">Mauritania</option>
                                        <option value="Mauritius">Mauritius</option>
                                        <option value="Mayotte">Mayotte</option>
                                        <option value="Mexico">Mexico</option>
                                        <option value="Federated States of Micronesia">Federated States of Micronesia</option>
                                        <option value="Moldova">Moldova</option>
                                        <option value="Monaco">Monaco</option>
                                        <option value="Mongolia">Mongolia</option>
                                        <option value="Montenegro">Montenegro</option>
                                        <option value="Montserrat">Montserrat</option>
                                        <option value="Morocco">Morocco</option>
                                        <option value="Mozambique">Mozambique</option>
                                        <option value="Myanmar">Myanmar</option>
                                        <option value="Namibia">Namibia</option>
                                        <option value="Nauru">Nauru</option>
                                        <option value="Nepal">Nepal</option>
                                        <option value="Netherlands">Netherlands</option>
                                        <option value="New Caledonia">New Caledonia</option>
                                        <option value="New Zealand">New Zealand</option>
                                        <option value="Nicaragua">Nicaragua</option>
                                        <option value="Niger">Niger</option>
                                        <option value="Nigeria">Nigeria</option>
                                        <option value="Niue">Niue</option>
                                        <option value="Norfolk Island">Norfolk Island</option>
                                        <option value="Northern Mariana Islands">Northern Mariana Islands</option>
                                        <option value="Norway">Norway</option>
                                        <option value="Oman">Oman</option>
                                        <option value="Pakistan">Pakistan</option>
                                        <option value="Palau">Palau</option>
                                        <option value="Palestinian Territory, Occupied">Palestinian Territory, Occupied</option>
                                        <option value="Panama">Panama</option>
                                        <option value="Papua New Guinea">Papua New Guinea</option>
                                        <option value="Paraguay">Paraguay</option>
                                        <option value="Peru">Peru</option>
                                        <option value="Philippines">Philippines</option>
                                        <option value="Pitcairn">Pitcairn</option>
                                        <option value="Poland">Poland</option>
                                        <option value="Portugal">Portugal</option>
                                        <option value="Puerto Rico">Puerto Rico</option>
                                        <option value="Qatar">Qatar</option>
                                        <option value="Romania">Romania</option>
                                        <option value="Russia">Russia</option>
                                        <option value="Rwanda">Rwanda</option>
                                        <option value="Reunion">Reunion</option>
                                        <option value="Saint Barthelemy">Saint Barthelemy</option>
                                        <option value="Saint Helena, Ascension and Tristan Da Cunha">Saint Helena, Ascension and Tristan Da Cunha</option>
                                        <option value="Saint Kitts and Nevis">Saint Kitts and Nevis</option>
                                        <option value="Saint Lucia">Saint Lucia</option>
                                        <option value="Saint Martin">Saint Martin</option>
                                        <option value="Saint Pierre and Miquelon">Saint Pierre and Miquelon</option>
                                        <option value="Saint Vincent and the Grenadines">Saint Vincent and the Grenadines</option>
                                        <option value="Samoa">Samoa</option>
                                        <option value="San Marino">San Marino</option>
                                        <option value="Sao Tome and Principe">Sao Tome and Principe</option>
                                        <option value="Saudi Arabia">Saudi Arabia</option>
                                        <option value="Senegal">Senegal</option>
                                        <option value="Serbia">Serbia</option>
                                        <option value="Seychelles">Seychelles</option>
                                        <option value="Sierra Leone">Sierra Leone</option>
                                        <option value="Singapore">Singapore</option>
                                        <option value="Slovakia">Slovakia</option>
                                        <option value="Slovenia">Slovenia</option>
                                        <option value="Solomon Islands">Solomon Islands</option>
                                        <option value="Somalia">Somalia</option>
                                        <option value="South Africa">South Africa</option>
                                        <option value="South Sudan">South Sudan</option>
                                        <option value="South Georgia and the South Sandwich Islands">South Georgia and the South Sandwich Islands</option>
                                        <option value="Spain">Spain</option>
                                        <option value="Sri Lanka">Sri Lanka</option>
                                        <option value="Sudan">Sudan</option>
                                        <option value="Suriname">Suriname</option>
                                        <option value="Svalbard and Jan Mayen">Svalbard and Jan Mayen</option>
                                        <option value="Swaziland">Swaziland</option>
                                        <option value="Sweden">Sweden</option>
                                        <option value="Switzerland">Switzerland</option>
                                        <option value="Syrian Arab Republic">Syrian Arab Republic</option>
                                        <option value="Taiwan">Taiwan</option>
                                        <option value="Tajikistan">Tajikistan</option>
                                        <option value="United Republic of Tanzania">United Republic of Tanzania</option>
                                        <option value="Thailand">Thailand</option>
                                        <option value="Timor-Leste">Timor-Leste</option>
                                        <option value="Togo">Togo</option>
                                        <option value="Tokelau">Tokelau</option>
                                        <option value="Tonga">Tonga</option>
                                        <option value="Trinidad and Tobago">Trinidad and Tobago</option>
                                        <option value="Tunisia">Tunisia</option>
                                        <option value="Turkey">Turkey</option>
                                        <option value="Turkmenistan">Turkmenistan</option>
                                        <option value="Turks and Caicos Islands">Turks and Caicos Islands</option>
                                        <option value="Tuvalu">Tuvalu</option>
                                        <option value="Uganda">Uganda</option>
                                        <option value="Ukraine">Ukraine</option>
                                        <option value="United Arab Emirates">United Arab Emirates</option>
                                        <option value="United Kingdom">United Kingdom</option>
                                        <option value="United States">United States</option>
                                        <option value="Uruguay">Uruguay</option>
                                        <option value="Uzbekistan">Uzbekistan</option>
                                        <option value="Vanuatu">Vanuatu</option>
                                        <option value="Bolivarian Republic of Venezuela">Bolivarian Republic of Venezuela</option>
                                        <option value="Vietnam">Vietnam</option>
                                        <option value="Virgin Islands, British">Virgin Islands, British</option>
                                        <option value="Virgin Islands, U.S.">Virgin Islands, U.S.</option>
                                        <option value="Wallis and Futuna">Wallis and Futuna</option>
                                        <option value="Yemen">Yemen</option>
                                        <option value="Zambia">Zambia</option>
                                        <option value="Zimbabwe">Zimbabwe</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <input type="email" class="form-control" placeholder="Email" id="email" name="email" required>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <input type="text" class="form-control" placeholder="Phone" id="phone" name="phone">
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <input type="text" class="form-control" placeholder="Website" id="website" name="website">
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12 col-lg-12">
                                <div class="form-group">
                                    <textarea class="form-control" placeholder="Topic of interest" id="message" name="message" required></textarea>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12 col-lg-12">
                                <div class="form-group">
                                    <div class="cf-turnstile" data-sitekey="0x4AAAAAAC82PjF0UN0piB06"></div>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12 col-lg-12">
                                <button type="submit" class="btn btn__secondary btn__block">
                                    <span>Submit Request</span><i class="icon-arrow-right"></i>
                                </button>
                                <div class="contact-result"></div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="contactInfo" class="contact contact-info pt-0">
    <div class="container">
        <div class="row">
            <div class="col-sm-12 col-md-4 col-lg-4">
                <div class="contact-info-box">
                    <h4 class="contact__info-box-title">UAE Office</h4>
                    <ul class="contact__info-list list-unstyled">
                        <li>Email: <a href="mailto:inquiry@falconchemicals.com">inquiry&#64;falconchemicals.com</a></li>
                        <li>Address:<br>Plot # 599_0163, D 86 (North)
                            First Al Khail Street, Expo Road,
                            Jebel Ali Industrial Area # 3</li>
                        <li>Phone:<br>+971 48801444</li>
                        <li>Hours:<br>08:30 AM - 5:00 PM</li>
                    </ul>
                </div>
            </div>
            <div class="col-sm-12 col-md-4 col-lg-4">
                <div class="contact-info-box">
                    <h4 class="contact__info-box-title">Abu Dhabi Office</h4>
                    <ul class="contact__info-list list-unstyled">
                        <li>Contact: Anil Meka - Sales Manager</li>
                        <li>Email: <a href="mailto:anil@falconchemicals.com">anil&#64;falconchemicals.com</a></li>
                        <li>Address:<br>202, Rolex Building
                            Sheikh Rashid Bin Saeed St
                            Al Danah - Zone 1 - Abu Dhabi</li>
                        <li>Phone:<br>+971 48801444</li>
                        <li>Hours:<br>08:30 AM - 5:00 PM</li>
                    </ul>
                </div>
            </div>
            <div class="col-sm-12 col-md-4 col-lg-4"></div>
        </div>
    </div>
</section>

<footer id="footer" class="footer">
    <div class="footer-top">
        <div class="container">
            <div class="row">
                <div class="col-sm-12 col-md-12 col-lg-4 col-xl-3 footer__widget footer__widget-about">
                    <h6 class="footer__widget-title">Quick Contact</h6>
                    <div class="footer__widget-content">
                        <p class="footer__contact-phone">
                            <i class="fa fa-envelope"></i>
                            <a href="mailto:inquiry@falconchemicals.com">inquiry&#64;falconchemicals.com</a>
                        </p>
                        <p class="footer__contact-phone">
                            <i class="icon-phone"></i>
                            <a href="tel:+97148801444">+971 4880 1444</a>
                        </p>
                    </div>
                </div>
                <div class="col-6 col-sm-6 col-md-6 col-lg-4 col-xl-3 footer__widget footer__widget-nav">
                    <h6 class="footer__widget-title">Address</h6>
                    <div class="footer__widget-content">
                        <nav>
                            <ul class="list-unstyled">
                                <p class="text-light">Physical Address: Plot # 599_0163, D 86 (North) First Al Khail Street, Expo Road, Jebel Ali Industrial Area # 3<br>Post Box: 2924, Dubai., U.A.E.</p>
                                <p class="text-light"><b>Working hours: 08:30 AM - 5:00 PM</b></p>
                                <ul class="social__icons">
                                    <li><a href="https://www.linkedin.com/in/falcon-chemicals-6417532b0/" target="_blank"><i class="fa fa-linkedin"></i></a></li>
                                </ul>
                            </ul>
                        </nav>
                    </div>
                </div>
                <div class="col-6 col-sm-6 col-md-6 col-lg-3 col-xl-2 footer__widget footer__widget-nav">
                    <h6 class="footer__widget-title">Quick Links</h6>
                    <div class="footer__widget-content">
                        <nav>
                            <ul class="list-unstyled">
                                <li><a href="about.html">About Us</a></li>
                                <li><a href="products.html">Product</a></li>
                                <li><a href="research-and-development.html">R &amp; D</a></li>
                                <li><a href="contact.php">Contact Us</a></li>
                                <li><a href="frontend/images/doc/Company%20brochure%20(2).pdf">Company Brochure</a></li>
                                <li><a href="frontend/images/doc/FALCHEM%20PROFILE.pdf">Company Profile</a></li>
                            </ul>
                        </nav>
                    </div>
                </div>
                <div class="col-sm-12 col-md-10 col-lg-6 col-xl-4 footer__widget footer__widget-newsletter">
                    <h6 class="footer__widget-title">Our certifications</h6>
                    <a href="frontend/images/certificate/Falcon%20Chemicals%20LLC%20-%20ISO%2022716-2007%20-%20Expiry%20on%2011-07-2025.pdf" download>
                        <img src="frontend/images/certificate/2.jpg" style="padding: 10px;">
                    </a>
                    <a href="frontend/images/certificate/Falcon%20Chemicals%20LLC%20-%20ISO%2014001-2015%20-%20Expiry%20on%2029-07-2027.pdf" download>
                        <img src="frontend/images/certificate/3.jpg" style="padding: 10px;">
                    </a>
                    <a href="frontend/images/certificate/Falcon%20Chemicals%20LLC%20-%20ISO%209001_2015%20-%20Expiry%20on%2015-07-2026.pdf" download>
                        <img src="frontend/images/certificate/4.jpg" style="padding: 10px;">
                    </a>
                    <a href="frontend/images/certificate/Dubai%20Central%20Laboratory%20Certificate%20-%20Expiry%20on%2028-05-2025.pdf" download>
                        <img src="frontend/images/certificate/1.jpg" style="padding: 10px;">
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="container">
            <div class="row">
                <div class="col-sm-12 col-md-3 col-lg-3">
                    <a href="index.html"><img src="frontend/images/logo/f_logo.svg" alt="logo"></a>
                </div>
                <div class="col-sm-12 col-md-9 col-lg-9 text-right">
                    <div class="footer__copyright"></div>
                </div>
            </div>
        </div>
    </div>
</footer>

<button id="scrollTopBtn"><i class="fa fa-long-arrow-up"></i></button>

<div class="module__search-container">
    <i class="fa fa-times close-search"></i>
    <form class="module__search-form">
        <input type="text" class="search__input" placeholder="Type Words Then Enter">
        <button class="module__search-btn"><i class="fa fa-search"></i></button>
    </form>
</div>

</div><!-- .wrapper -->

<script src="frontend/js/jquery-3.3.1.min.js"></script>
<script src="frontend/js/plugins.js"></script>
<script src="frontend/js/main.js"></script>

</body>
</html>
