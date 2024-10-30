<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AJAX Form Example</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
</head>
<body>

<!-- Button to Open Modal -->
<button class="btn btn-primary" data-toggle="modal" data-target="#chartModal">
    Open Chart Form
</button>

<!-- Modal -->
<div class="modal fade" id="chartModal" tabindex="-1" role="dialog" aria-labelledby="chartModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="chartModalLabel">Generate Chart</h4>
            </div>
            <div class="modal-body">
                <form id="chartForm">
                    @csrf
                    <div class="form-group">
                        <label for="type">Chart Type:</label>
                        <input type="text" class="form-control" id="type" name="types" required>
                        <div class="text-danger"></div>
                    </div>
                    <div class="form-group">
                        <label for="yearFrom">Year From:</label>
                        <input type="text" class="form-control" id="yearFrom" name="yearFrom" required>
                        <div class="text-danger"></div>
                    </div>
                    <div class="form-group">
                        <label for="yearTo">Year To:</label>
                        <input type="text" class="form-control" id="yearTo" name="yearTo" required>
                        <div class="text-danger"></div>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#chartForm').on('submit', function(e) {
        e.preventDefault();

        // Clear previous error messages
        $('.text-danger').empty();

        // Hide the modal before submitting
        $('#chartModal').modal('hide'); 

        $.ajax({
            url: '{{ route("charts2") }}', // Change this to your route
            method: 'POST',
            data: $(this).serialize(),
            success: function(data) {
                // Hide the modal on success
                alert('Chart generated successfully!');
            },
            error: function(xhr) {
                // Show the modal again if there are validation errors
                $('#chartModal').modal('show');

                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;

                    // Display validation errors
                    $.each(errors, function(key, messages) {
                        $(`#${key}`).siblings('.text-danger').html(messages.join(', '));
                    });
                } else {
                    console.error('An unexpected error occurred:', xhr);
                }
            }
        });
    });
});
</script>

</body>
</html>
