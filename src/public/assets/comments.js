(function ($) {
    'use strict';

    var state = { page: 1 };
    var $form = $('#comment-form');

    function showAlert(message, type) {
        $('#alert').removeClass('d-none alert-success alert-danger').addClass('alert-' + type).text(message);
    }

    function hideAlert() {
        $('#alert').addClass('d-none').text('');
    }

    function setCsrf(hash) {
        if (hash) {
            $form.find('input[type="hidden"]').val(hash);
        }
    }

    function renderComments(items) {
        var $container = $('#comments').empty();

        if (!items.length) {
            $('<div>', { class: 'alert alert-light border', text: 'Комментариев пока нет.' }).appendTo($container);
            return;
        }

        $.each(items, function (_, item) {
            var $card = $('<article>', { class: 'card comment-card shadow-sm mb-3' });
            var $body = $('<div>', { class: 'card-body' }).appendTo($card);
            var $meta = $('<div>', { class: 'comment-meta d-flex align-items-center justify-content-between mb-3' }).appendTo($body);
            var $identity = $('<div>').appendTo($meta);
            $('<strong>', { text: item.name }).appendTo($identity);
            $('<span>', { class: 'badge badge-light ml-2', text: '#' + item.id }).appendTo($identity);
            $('<time>', { class: 'text-muted small', datetime: item.date, text: item.date }).appendTo($meta);
            $('<p>', { class: 'comment-text mb-3', text: item.text }).appendTo($body);
            $('<button>', { class: 'btn btn-sm btn-outline-danger delete-comment', type: 'button', text: 'Удалить', 'data-id': item.id }).appendTo($body);
            $card.appendTo($container);
        });
    }

    function renderPagination(pagination) {
        var $pagination = $('#pagination').empty();

        if (pagination.pages < 2) {
            return;
        }

        for (var page = 1; page <= pagination.pages; page += 1) {
            var $item = $('<li>', { class: 'page-item' + (page === pagination.page ? ' active' : '') });
            $('<button>', { class: 'page-link', type: 'button', text: page, 'data-page': page, 'aria-label': 'Страница ' + page }).appendTo($item);
            $item.appendTo($pagination);
        }
    }

    function loadComments() {
        $('#comments').attr('aria-busy', 'true');
        return $.getJSON('/comments', { page: state.page, sort: $('#sort').val(), direction: $('#direction').val() })
            .done(function (response) {
                state.page = response.pagination.page;
                renderComments(response.items);
                renderPagination(response.pagination);
            }).fail(function () {
                showAlert('Не удалось загрузить комментарии.', 'danger');
            }).always(function () {
                $('#comments').attr('aria-busy', 'false');
            });
    }

    function clearErrors() {
        $form.find('.is-invalid').removeClass('is-invalid');
    }

    $form.on('submit', function (event) {
        event.preventDefault();
        clearErrors();
        hideAlert();

        if (!this.checkValidity()) {
            $(this).find(':invalid').addClass('is-invalid');
            return;
        }

        var $button = $form.find('button[type="submit"]').prop('disabled', true);
        $.ajax({ url: '/comments', method: 'POST', data: $form.serialize(), dataType: 'json' })
            .done(function (response) {
                setCsrf(response.csrfHash);
                $form[0].reset();
                state.page = 1;
                showAlert(response.message, 'success');
                loadComments();
            }).fail(function (xhr) {
                var response = xhr.responseJSON || {};
                setCsrf(response.csrfHash);
                $.each(response.errors || {}, function (field, message) {
                    $form.find('[name="' + field + '"]').addClass('is-invalid');
                    $form.find('[data-error="' + field + '"]').text(message);
                });
                showAlert(response.message || 'Не удалось добавить комментарий.', 'danger');
            }).always(function () {
                $button.prop('disabled', false);
            });
    });

    $('#sort, #direction').on('change', function () {
        state.page = 1;
        hideAlert();
        loadComments();
    });

    $('#pagination').on('click', '.page-link', function () {
        state.page = Number($(this).data('page'));
        loadComments();
    });

    $('#comments').on('click', '.delete-comment', function () {
        var id = $(this).data('id');
        var $token = $form.find('input[type="hidden"]');
        var data = {};
        data[$token.attr('name')] = $token.val();

        $.ajax({ url: '/comments/' + id + '/delete', method: 'POST', data: data, dataType: 'json' })
            .done(function (response) {
                setCsrf(response.csrfHash);
                showAlert(response.message, 'success');
                loadComments();
            }).fail(function (xhr) {
                var response = xhr.responseJSON || {};
                setCsrf(response.csrfHash);
                showAlert(response.message || 'Не удалось удалить комментарий.', 'danger');
            });
    });

    loadComments();
}(jQuery));
