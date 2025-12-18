<?php 

?>

<!doctype html>
<html lang="en">
    <head>
        <title>Title</title>
        <!-- Required meta tags -->
        <meta charset="utf-8" />
        <meta
            name="viewport"
            content="width=device-width, initial-scale=1, shrink-to-fit=no"
        />

        <!-- Bootstrap CSS v5.2.1 -->
        <link
            href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
            rel="stylesheet"
            integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN"
            crossorigin="anonymous"
        />
    </head>

    <body class="bg-success-subtle">
        <header>
            <!-- place navbar here -->
        </header>
        <main>
            <div class="d-flex p-5 mt-5 justify-content-center align-items-center vh-100">
                <div class="card container p-5" style="width:30%;">
                    <div class="d-flex justify-content-center mb-3">
                        <h1>บันทึกข้อมูลนักศึกษา</h1>
                    </div>
                    <div class="d-flex justify-content-center mb-3">
                        <h2>ระบบแจ้งซ่อมอุปกรณ์</h2>
                    </div>

                    <form action="">
                        <div class="mb-3">
                            <label for="" class="form-label">คำนำหน้า</label>
                            <select
                                class="form-select form-select-lg"
                                name=""
                                id=""
                            >
                                <option selected>เลือก</option>
                                <option value="">นาย</option>
                                <option value="">นางสาว</option>
                                <option value="">Mr</option>
                                <option value="">Ms</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="" class="form-label">ชื่อจริง</label>
                            <input
                                type="text"
                                class="form-control"
                                name=""
                                required
                                id=""
                                aria-describedby=""
                                placeholder="ชื่อผู้ใช้"
                            /></div>
                        <div class="mb-3">
                            <label for="" class="form-label">นามสกุล</label>
                            <input
                                type="password"
                                class="form-control"
                                name=""
                                required
                                id=""
                                placeholder="รหัสผ่าน"
                            />
                        </div>
                        <div class="mb-3">
                        <label for="" class="form-label">รหัสนักศึกษา</label>
                        <input
                            type="text"
                            class="form-control"
                            name=""
                            required
                            id=""
                            aria-describedby=""
                            placeholder="674259xxx"
                        /></div>
                        <div>
                            <button
                                type="submit"
                                class="btn btn-success mb-3"
                            >
                                บันทึกข้อมูล
                            </button>
                            
                        </div>
                    </form>
                </div>
            </div>
        </main>
        <footer>
            <!-- place footer here -->
        </footer>
        <!-- Bootstrap JavaScript Libraries -->
        <script
            src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
            integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
            crossorigin="anonymous"
        ></script>

        <script
            src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"
            integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+"
            crossorigin="anonymous"
        ></script>
    </body>
</html>
