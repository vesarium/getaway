function fetchAppointmentDays() {
    var date = $('#bookTimings th:last').html();
    var url = $('#endOfList').data('href');
    $.ajax({
        type: "POST",
        url: url,
        data: {'date': date},
        success: function (response) {
            if (response.status === 'success') {
                $('#bookTimings tbody').append(response.html);
                $(window).data('ajaxready', true);
            }
        }
    });
}

$(document).ready(function () {

    $(document).on('click', '#bookTimings .available', function () {
        $("#booking-details").val($(this).html());
        $("#appointmentDate").html($(this).parent('tr').find('th').html());
        $("#booking_time").val($(this).html());
        $("#booking_date").val($(this).parent('tr').find('th').html());
        $("#bookingFormModal").modal('show');
    });
    
    $('.bookAppointment').data('ajaxready', true).on('scroll', function() {
        if ($(window).data('ajaxready') === false) {
            return;
        }
        
        if ($(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight) {
            $(window).data('ajaxready', false);
            fetchAppointmentDays();
        }
    });
    

    var formValidator = $("form[name='BookingDetailsForm']").validate({
        rules: {
            firstname: 'required',
            lastname: 'required',
            email: {
                required: true,
                email: true
            },
            phoneno: 'required'
        },
        messages: {
            firstname: "Please enter your First Name",
            lastname: "Please enter your Last Name",
            email: {
                required: "Please enter your Email",
                email: "Please enter a valid email address"
            },
            phoneno: "Please enter your Phone No"
        },
        submitHandler: function (form) {
            form.submit();
        }
    });

    $('#bookingFormModal').on('hidden.bs.modal', function (e) {
        $("#booking-details").val('');
        formValidator.resetForm();
    });

});