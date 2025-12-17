<!doctype html>
<html lang="en">

<head>
    <title>Title</title>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <!-- Bootstrap CSS v5.2.1 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
</head>

<body>
    <header>
        <!-- place navbar here -->
    </header>
    <main>
        <div class="container d-flex justify-content-center align-items-center vh-100">
            <div class="card shadow p-4" style="width: 380px;">
                <div>
                    <h1>ระบบแจ้งซ่อม</h1> 
                </div>
                <form action="backend/auth_api.php">
                    <div class="mb-3">
                        <label for="" class="form-label">Username</label>
                        <input
                            type="text"
                            class="form-control"
                            name=""
                            id=""
                            aria-describedby="emailHelpId"
                        />
                    </div>
                    <div class="mb-3">
                        <label for="" class="form-label">Password</label>
                        <input
                            type="password"
                            class="form-control"
                            name=""
                            id=""
                            placeholder=""
                        />
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-box-arrow-in-right"></i> Login
                    </button>
                </form>
                <a class="mt-3" href="register.php">register</a>
            </div>
        </div>
        
    </main>
    <footer>
        <!-- place footer here -->
    </footer>
    <!-- Bootstrap JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
        crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"
        integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+"
        crossorigin="anonymous"></script>
</body>

</html>