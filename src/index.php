<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Bootstrap File Upload</title>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <script type="application/javascript" src="/js/index.js"></script>
    </head>
    <body class="bg-light">
        <div class="container mt-5">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="card-title mb-4">Upload a File</h4>

                    <div class="mb-3">
                        <label for="file" class="form-label">Choose file</label>
                        <input class="form-control" type="file" id="file" name="file" accept=".csv, .xlsx .xls">
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="talent_pool" name="talent_pool">
                        <label class="form-check-label" for="talent_pool">
                            Import to Talent Pool
                        </label>

                    </div>
                    <button class="btn btn-primary js-upload-file">Upload</button>
                    <button class="btn btn-primary js-upload-file-loader" type="button" disabled>
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Loading...
                    </button>
                </div>
                <div id="response" class="mt-3"></div>
            </div>
        </div>
    </body>
</html>
