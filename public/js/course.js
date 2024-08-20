$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    // Update and Store Course
    $(document).on('submit', '.course_form_submit', function(event){        
        event.preventDefault();
        var location = window.location.href;
        var form = $(this).closest("form")[0];
        var action = $(this).attr("action");
        var method = $(this).attr("method");
        let formData = new FormData(form); 
        $.ajax({
           
            type: method,
            url: action,
            data: formData,
            processData: false,
            contentType: false,
            cache: false,
            context: this,
            dataType: 'json',
            success: function (data) {
                // alert("fsafsa");
                // console.log(data);
                // console.log("title");
                // console.log("location");
                // console.log(location);
                var update_id = $('#update_id').val();
                if (data.success) {
                    swal({text: update_id ? "Course Updated Successfully!" :"Course Added Successfully!", type: 
                        "success"})
                        .then(function(){ 
                            if (update_id) {
                                // location.reload();
                                window.location = '/course';
                            }else{
                                window.location = '/course';
                                // window.location.href = data.redirect;
                                // $(".course_form_submit")[0].reset();
                            }
                        }
                    );
                    // $(".varient_submit").trigger("reset");
                    // $(".alert-success").prop("hidden", false);
                    // $(".alert-success").html(data.message);
                    // $('#varient_id').val(data.records.id);
                    // $('.varient_option').html(data.records.html);
                }
                else{
                    swal({text: data.message, type: "error"});
                    // printErrorMsg(data.message.error);
                    // $('.alert-danger').removeClass("d-none");
                    // $('.alert-danger').append(data.message.error);
                }
            },
            error: function (e) {
                if(e.responseJSON.message){
                    swal({text: e.responseJSON.message, type: "error"});
                }else{
                    swal({text: e.statusText+'! please try again later.', type: "error"});
                }
            }
        });
    });

    // Delete Course
    // $(document).on('click', '#delete_course', function(event){
    //     event.preventDefault();
    //     var id = $(this).attr("id-attr");
    //     var url = $(this).attr("action-attr");
    //     alert(url);
    //     swal({
    //         title: "Are you sure to Delete!",
    //         icon: "warning",
    //         buttons: [
    //             'cancel',
    //             'yes'
    //         ],
    //         dangerMode: true,
    //     }).then(function(isConfirm) {
    //         if (isConfirm) {
    //             $.ajax({
    //                 type: 'post',
    //                 url: url,
    //                 data: {id:id},
    //                 processData: false,
    //                 contentType: false,
    //                 cache: false,
    //                 context: this,
    //                 dataType: 'json',
    //                 success: function (data) {
    //                     swal("Course deleted Successfully", {
    //                         icon: "success",
    //                     });
    //                     $("#"+id+"").remove();
    //                 },
    //                 error: function (e) { }
    //             });
    //         }
    //     });
       
    // });

    // function printErrorMsg (msg) {
    //     $(".print-error-msg").find("ul").html('');
    //     $(".print-error-msg").css('display','block');
    //     $.each( msg, function( key, value ) {
    //         $(".print-error-msg").find("ul").append('<li>'+value+'</li>');
    //     });
    // }

    // // Course Filters
    // var path = $(location).attr("pathname");
    // if (path === '/course'){
    // }

    // $(document).on('click', '.var_links .pagination a', function(event) {
    //     event.preventDefault();

    //     var page = $(this).attr('href').split('page=')[1];
    //     $('#courseFilterPage').val(page);
    //     getCourseAjaxData();
    // });

    // $(document).on('keyup', '.courseFormFilter', function(event) {
    //     $('#courseFilterPage').val(1);
    //     getCourseAjaxData();
    // });

    // function getCourseAjaxData() {
    //     // $('.loaderOverlay').fadeIn();
    
    //     jQuery.ajax({
    //         url: "/get_course",
    //         data: $("#courseFilterForm").serializeArray(),
    //         method: 'POST',
    //         dataType: 'html',
    //         success: function(response) {
    //             // $('.loaderOverlay').fadeOut();
    //             $("#all_courses").html(response);
    
    //             if (feather) {
    //                 feather.replace({
    //                     width: 14,
    //                     height: 14
    //                 });
    //             }
    //         }
    //     });
    // }

});

