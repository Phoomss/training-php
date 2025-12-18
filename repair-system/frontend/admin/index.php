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

    <body class="bg-danger-subtle">
       <?php require_once '../layouts/navbar.php'?>
        <main >
            <div class="card container p-5 mt-5">
                <div class="d-flex justify-content-between">
                    <h1>ระบบแจ้งซ่อมอุปกรณ์ (เจ้าหน้าที่)</h1>
                    <div>
                    <a
                    name=""
                    id=""
                    class="btn btn-primary btn-lg"
                    href="form_equipment.php"
                    role="button"
                    >เพิ่มอุปกรณ์</a
                ></div>
                </div>
                
                
                    <div
                        class="table-responsive"
                    >
                        <table
                            class="table"
                        >
                            <thead>
                                <tr>
                                    <th scope="col">หมายเลขคำร้อง</th>
                                    <th scope="col">อุปกรณ์</th>
                                    <th scope="col">ข้อความ</th>
                                    <th scope="col">สถานะ</th>
                                    <th scope="col">ช่าง</th>
                                    <th scope="col">ยืนยัน</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="">
                                    <td scope="row">R1C1</td>
                                    <td>R1C2</td>
                                    <td>R1C3</td>
                                    <td>R1C3</td>
                                    <td>
                                        <form action="">
                                        <div class="mb-3">
                                            <select
                                                class="form-select form-select-lg"
                                                name=""
                                                id=""
                                                required
                                            >
                                                <option selected>Select one</option>
                                                <option value="">New Delhi</option>
                                                <option value="">Istanbul</option>
                                                <option value="">Jakarta</option>
                                            </select>
                                        </div>
                                        
                                    </td>
                                    <td>
                                        <a
                                            name=""
                                            id=""
                                            class="btn btn-success"
                                            href="#"
                                            role="button"
                                            >ยืนยัน</a
                                        >
                                        </form>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
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
