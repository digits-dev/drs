@extends('crudbooster::admin_template')
@section('content')
@push('head')
    <style>
        .modal-content {
            -webkit-border-radius: 10px !important;
            -moz-border-radius: 10px !important;
            border-radius: 10px !important;
        }
        .modal-header {
            -webkit-border-radius: 10px 10px 0px 0px !important;
            -moz-border-radius: 10px 10px 0px 0px !important;
            border-radius: 10px 10px 0px 0px !important;
        }
        #passwordStrengthBar {
            display: flex;
            justify-content: space-between;
            width: 100%;
        }

        .close-button {
            position: absolute;
            right: 15px;
            top: 17px;
            font-size: 2rem;
            cursor: pointer;
        }

        /* Hide all announcement modals initially */
        .announcement-modal {
            display: none;
        }

        /* Scrollable modal body */
        .modal-body {
            max-height: 560px; /* Adjust this value based on your needs */
            overflow-y: auto;
        }

        ::-webkit-scrollbar-track
        {
            /* -webkit-box-shadow: inset 0 0 6px rgba(32, 83, 178, 0.3); */
            background-color: #F5F5F5;
        }

        ::-webkit-scrollbar
        {
            width: 5px;
            background-color: #F5F5F5;
        }

        ::-webkit-scrollbar-thumb
        {
            background-color: #3c8dbc;
            /* border: px solid #367fa9; */
        }

    </style>
@endpush

@if (count($unreadAnnouncements) > 0)
    <div class="modal fade" id="tos-modal" role="dialog" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-md">
            @foreach ($unreadAnnouncements as $announcement)
                <div class="modal-content announcement-modal" id="announcement-modal-{{ $announcement->id }}">
                    <div class="modal-header btn-primary" style="text-center; font-size: 20px; font-weight:bold">
                        <i class="fa fa-info"></i> {{ $announcement->title }}
                        <span class="close-button" data-announcement-id="{{ $announcement->id }}">&times;</span>
                    </div>
                    <div class="modal-body">
                        {!! $announcement->message !!}
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary dismiss-modal" data-announcement-id="{{ $announcement->id }}">Got it!</button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif

@endsection
@push('bottom')
    <script type="text/javascript">
        $(document).ready(function() {
            var announcementIndex = 0;
            var announcements = $('.announcement-modal');
            console.log(announcements);
            function showNextAnnouncement() {
                if (announcementIndex < announcements.length) {
                    var currentAnnouncement = announcements.eq(announcementIndex);
                    $('#tos-modal').modal('show');
                    currentAnnouncement.show();
                }
            }

            // Show the first announcement on page load
            $(window).on('load', function() {
                showNextAnnouncement();
            });

            // Handle dismissal of the modal
            $('.dismiss-modal, .close-button').on('click', function() {
                var announcementId = $(this).data('announcement-id');

                // Hide the current modal
                $('#announcement-modal-' + announcementId).hide();

                // Send AJAX request to store in session
                $.ajax({
                    url: "{{ route('read-announcement') }}",
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}", // CSRF token
                        announcement_id: announcementId
                    },
                    success: function(response) {
                        console.log(response.message); // Optional: Show success message in console
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText); // Optional: Handle error
                    }
                });

                // Move to the next announcement
                announcementIndex++;
                if (announcementIndex < announcements.length) {
                    showNextAnnouncement();
                } else {
                    $('#tos-modal').modal('hide'); // Close the modal if no more announcements
                }
            });
        });
    </script>
@endpush