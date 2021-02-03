$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    var panel = {
        el: '#info-panel',
        selectedDateBlock: null,
        selectedEvent: null,
        init: function (isNew, e) {
            panel.clear();
            panel.hideError();
            panel.updateDate(e);
            if (isNew) {
                $(panel.el).addClass('new').removeClass('update');
                panel.selectedDateBlock = $(e.currentTarget);
            }
            else {
                $(panel.el).addClass('update').removeClass('new');
                panel.selectedDateBlock = $(e.currentTarget).closest('.date-block');
                var id = $(e.currentTarget).data('id');
                $.get("/events/" + id, { id: id },
                    function (event, textStatus, jqXHR) {
                        console.log(event);
                        $('form').find('input[name="id"]').val(event.event.id);
                        $('form').find('input[name="title"]').val(event.event.title);
                        $('form').find('input[name="start_time"]').val(event.event.start_time);
                        $('form').find('input[name="end_time"]').val(event.event.end_time);
                        $('form').find('[name=description]').val(event.event.description);
                    },
                    "json"
                ).fail(function (xhr) {
                    panel.showError(xhr.responseText);
                });
            }
        },
        open: function (isNew, e) {
            panel.init(isNew, e)
            $(panel.el).addClass('open').css({
                top: e.pageY + 'px',
                left: e.pageX + 'px'
            }).find('.title [contenteditable]').focus();
        },
        close: function (e) {
            $(panel.el).removeClass('open');
        },
        updateDate: function (e) {
            if ($(e.currentTarget).is('.date-block')) {
                var date = $(e.currentTarget).data('date');
            }
            else {
                var date = $(e.currentTarget).closest('.date-block').data('date');
            }
            var year = $('#calendar').data('year');
            var month = $('#calendar').data('month');
            $(panel.el).find('.month').text(month);
            $(panel.el).find('.date').text(date);
            $(panel.el).find('[name="year"]').val(year);
            $(panel.el).find('[name="month"]').val(month);
            $(panel.el).find('[name="date"]').val(date);
        },
        clear: function (e) {
            $(panel.el).find('input').val('');
            $(panel.el).find('textarea').val('');
        },
        showError: function (e) {
            $(panel.el).find('.error-msg').addClass('open');
            $(panel.el).find('.alert').text(e);
        },
        hideError: function (e) {
            $(panel.el).find('.alert').text('');
            $(panel.el).find('.error-msg').removeClass('open');
        },
    }

    $('.date-block:not(.empty)').dblclick(function (e) {
        panel.open(1, e); //new event
    }).on('dblclick', '.event', function (e) {
        e.stopPropagation();
        panel.open(0, e); // old event
        panel.selectedEvent = $(e.currentTarget);
    });
    $(panel.el)
        .on('click', 'button', function (e) {
            if ($(this).is('.create') || $(this).is('.update')) {
                if ($(this).is('.create')) {
                    var action = '/Gcalendar';
                    var data = $(panel.el).find('form').serialize();
                    console.log(data);
                }
                if ($(this).is('.update')) {
                    var id = $(panel.selectedEvent).data('id');
                    var action = '/events/' + id;
                    $(panel.el).find('form').append('<input type="hidden" name="_method" value="put">');
                    var data = $(panel.el).find('form').serialize();
                }
                // $.post(action, data, "json")
                //     .done(function (data, textStatus, jqXHR) {
                //         location.reload();
                //     })
                //     .fail(function (xhr) {
                //         $.each(xhr.responseJSON.errors, function (index, error) {
                //             panel.showError(error);
                //         });
                //         console.log(xhr);
                //     });
            }
            if ($(this).is('.cancel')) {
                panel.close();
            }
            if ($(this).is('.delete')) {
                var result = confirm('Do you really want to delete this event?');
                if (result) {
                    var id = panel.selectedEvent.data('id');
                    $.post("/events/" + id, { _method: 'delete' },
                        function (data, textStatus, jqXHR) {
                            panel.selectedEvent.remove();
                            panel.close();
                        },
                        "json"
                    );
                }
            }
        })
        .on('click', '.close', function (e) {
            $('button.cancel').click();
        });
});