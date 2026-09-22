<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="assets/img/favicon.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/styles.css">
    <title>Portofolio Gagan Suganda</title>
</head>

<body>
    <header class="profile container">
        <i class="ri-moon-line change-theme" id="theme-button"></i>

        <div class="profile__container grid">
            <div class="profile__data">
                <div class="profile__border">
                    <div class="profile__perfil">
                        <img src="assets/img/profile.png" alt="">
                    </div>
                </div>

                <h2 class="profile__name">Gagan Suganda</h2>
                <h3 class="profile__profession">Web developer</h3>

                <ul class="profile__social">
                    <a href="https://www.instagram.com/gagans.id" target="_blank" class="profile__social-link">
                        <i class="ri-instagram-line"></i>
                    </a>
                    <a href="https://www.linkedin.com/in/gagan-suganda" target="_blank" class="profile__social-link">
                        <i class="ri-linkedin-box-line"></i>
                    </a>
                    <a href="https://github.com/gagansuganda" target="_blank" class="profile__social-link">
                        <i class="ri-github-line"></i>
                    </a>
                    <a href="mailto:gagan.suganda98@gmail.com" target="_blank" class="profile__social-link">
                        <i class="ri-mail-line"></i>
                    </a>
                </ul>
            </div>

            <div class="profile__info grid">
                <div class="profile__info-group">
                    <h3 class="profile__info-number">7</h3>
                    <p class="profile__info-description">
                        Years of <br> work
                    </p>
                </div>
                <div class="profile__info-group">
                    <h3 class="profile__info-number">+124</h3>
                    <p class="profile__info-description">
                        Completed <br> projects
                    </p>
                </div>
                <div class="profile__info-group">
                    <h3 class="profile__info-number">96</h3>
                    <p class="profile__info-description">Satisfied <br> customers</p>
                </div>
            </div>

            <div class="profile__buttons">
                <a download="" href="assets/pdf/gagansuganda-cv.pdf" class="button">
                    Download CV <i class="ri-download-line"></i>
                </a>

                <div class="profile__buttons-small">
                    <a href="https://api.whatsapp.com/send?phone=+6289664044727&text=Hello, more information!"
                        target="_blank" class="button button__small button__gray">
                        <i class="ri-whatsapp-line"></i>
                    </a>
                    <a href="https://m.me/gagans.id" target="_blank" class="button button__small button__gray">
                        <i class="ri-messenger-line"></i>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!--=============== MAIN ===============-->
    <main class="main">
        <section class="filters container">
            <!--=============== FILTERS TABS ===============-->
            <ul class="filters__content">
                <button class="filters__button filter-tab-active" data-target="#abouts">
                    Abouts
                </button>
                <button class="filters__button" data-target="#projects">
                    Projects
                </button>
                <button class="filters__button" data-target="#contact">
                    Contact
                </button>
            </ul>

            <div class="filters__sections">
                <!--=============== ABOUTS ===============-->
                <div class="abouts__content filters__active" data-content id="abouts">
                    <div class="text-center mb-2">
                        <h2>About Me</h2>
                    </div>
                    <div class="abouts__area mb-2">
                        <p class="abouts__summary">
                            I am website developer with 2 years of experience in website development, using CodeIgniter
                            for developing a website and web applications. I am a graduate of CIC Catur Insan Cendekia
                            Cirebon majoring in Computer Science. I have the ability to program using PHP, ASP.Net C#,
                            Javascript, Codeigniter, MySQL, SQL Server, and Git. Have good analytical and communication
                            skills and be able to work individually or in a team.
                        </p>
                    </div>

                    <div class="works__area mb-2">
                        <h2 class="works__title">Work Experiences</h2>
                        <hr>
                        <div class="mb-1">
                            <h3 class="works__company">PT. Ipro Solusi Canggih <span> (Oct 2022 - Dec 2023)</span></h3>
                            <h3 class="works__name">Programmer</h3>
                            <p class="works__description">
                            <ul style="list-style-type: circle; text-align: justify;">
                                <li>Implementing modifications based on the blueprint provided by the systems analyst.
                                </li>
                                <li>Adjusting the source code using Visual Studio and uploading the work to Bitbucket.
                                </li>
                                <li>Modifying the database structure using SQL Server Management Studio.</li>
                                <li>Resolving bugs identified by testers during the System Integration Testing (SIT) and
                                    User Acceptance Testing (UAT) processes.</li>
                            </ul>
                            </p>
                        </div>
                        <div>
                            <h3 class="works__company">PT. Lee Yin Gapuran Garment Indonesia <span> (Jan 2022 - Sep
                                    2022)</span></h3>
                            <h3 class="works__name">PPIC</h3>
                            <p class="works__description">
                            <ul style="list-style-type: circle; text-align: justify;">
                                <li>Create material request pabric.</li>
                                <li>Share breakdown to admin pabric, admin sewing, admin finishing.</li>
                                <li>Create and share trimcard to production.</li>
                            </ul>
                            </p>
                        </div>
                    </div>

                    <div class="works__area mb-2">
                        <h2 class="works__title">Education</h2>
                        <hr>
                        <div>
                            <h3>CIC University Cirebon <span> (Aug 2017 - Sep 2021)</span></h3>
                            <span>Bachelor's Degree in Computer Science, 3.88/4.00</span>
                        </div>
                    </div>

                    <div>
                        <h2 class="works__title">Skills</h2>
                        <hr>
                        <div>
                            <h3>Frontend Developer</h3>
                            <p style="margin-bottom: .5rem;">HTML, Bootstrap, JavaScript, ASP.Net, Git</p>
                            <h3>Backend Developer</h3>
                            <p>PHP, C#, MySQL, SQL Server</p>
                        </div>
                    </div>
                </div>


                <!--=============== PROJECTS ===============-->
                <div data-content id="projects">
                    <div class="text-center mb-2">
                        <h2>My Project</h2>
                    </div>

                    <div class="projects__content grid">
                        <article class="projects__card">
                            <img src="assets/img/project/kuesioner.jpg" alt="" class="projects__img">
                            <div class="projects__modal">
                                <div>
                                    <span class="projects__subtitle">Web</span>
                                    <h3 class="projects__title">Kuesioner Biro Administrasi Akademik dan Kemahasiswaan
                                    </h3>
                                    <a href="#" class="projects__button button button__small">
                                        <i class="ri-link"></i>
                                    </a>
                                </div>
                            </div>
                        </article>

                        <article class="projects__card">
                            <img src="assets/img/project/virtual-class.jpg" alt="" class="projects__img">

                            <div class="projects__modal">
                                <div>
                                    <span class="projects__subtitle">Web</span>
                                    <h3 class="projects__title">Virtual Class</h3>
                                    <a href="#" class="projects__button button button__small">
                                        <i class="ri-link"></i>
                                    </a>
                                </div>
                            </div>
                        </article>

                    </div>
                </div>


                <!--=============== CONTACT ===============-->
                <div class="contact__content" data-content id="contact">
                    <div class="text-center mb-2">
                        <h2>Contact Me</h2>
                    </div>

                    <div class="contact-form-wrapper">
                        <form action="" method="POST">
                            <div class="mb-1">
                                <label for="fullname" class="form-label">Full Name</label>
                                <input type="text" name="fullname" id="fullname" placeholder="enter your name"
                                    class="contact-form-input" autocomplete="off" />
                            </div>

                            <div class="mb-1">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" name="email" id="email" placeholder="enter your email"
                                    class="contact-form-input" autocomplete="off" />
                            </div>

                            <div>
                                <label for="message" class="form-label"> Message </label>
                                <textarea rows="5" name="message" id="message" class="contact-form-input"></textarea>
                            </div>

                            <button class="contact-btn">
                                Send Message
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="footer container">
        <span class="footer__copy">
            &#169; <strong>gagans.id</strong>. All rigths reserved
        </span>
    </footer>

    <script src="assets/js/scrollreveal.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>

</html>