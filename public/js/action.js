$(document).ready(function () {
    var source = $('#event-template').html();
    var eventTemplate = Handlebars.compile(source);
    $.each(events, function (indexInArray, event) {
        var eventUI = eventTemplate(event);
        var date = event.date;
        $('#calendar').find('.date-block[data-date="' + date + '"]').find('.events').append(eventUI);
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
                $.post("event/read.php", { id: id },
                    function (event, textStatus, jqXHR) {
                        $('form').find('input[name="id"]').val(event.id);
                        $('form').find('input[name="title"]').val(event.title);
                        $('form').find('input[name="start_time"]').val(event.start_time);
                        $('form').find('input[name="end_time"]').val(event.end_time);
                        $('form').find('[name=description]').val(event.description);
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
                    var action = 'event/create.php';
                }
                if ($(this).is('.update')) {
                    var action = 'event/update.php';
                }
                var data = $(panel.el).find('form').serialize();
                $.post(action, data, "json")
                    .done(function (data, textStatus, jqXHR) {
                        if ($(e.currentTarget).is('.update')) {
                            panel.selectedEvent.remove();
                        }
                        var eventUI = eventTemplate(data);
                        panel.selectedDateBlock.find('.event').each(function (index, event) {
                            var eventFromTime = $(event).data('from').split(':');
                            var newEventFromTime = $(eventUI).data('from').split(':');
                            if (eventFromTime[0] > newEventFromTime[0] || (eventFromTime[0] == newEventFromTime[0] && eventFromTime[1]) > newEventFromTime[1]) {
                                $(event).before(eventUI);
                                return false;
                            }
                        });
                        if (panel.selectedDateBlock.find('.event[data-id="' + data.id + '"] ').length == 0) {
                            panel.selectedDateBlock.find('.events').append(eventUI);
                        }
                        panel.close();
                    })
                    .fail(function (xhr) {
                        panel.showError(xhr.responseText);
                        console.log();
                    });
            }
            if ($(this).is('.cancel')) {
                panel.close();
            }
            if ($(this).is('.delete')) {
                var result = confirm('Do you really want to delete this event?');
                if (result) {
                    var id = panel.selectedEvent.data('id');
                    $.post("event/delete.php", { id: id },
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