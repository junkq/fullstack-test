<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Комментарии</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/comments.css') ?>">
</head>
<body>
<main class="container py-4 py-md-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4">
                <div>
                    <h1 class="mb-1">Комментарии</h1>
                    <p class="text-muted mb-0">Мнения и сообщения пользователей</p>
                </div>
                <div class="form-inline mt-3 mt-md-0">
                    <label class="mr-2" for="sort">Сортировка</label>
                    <select class="custom-select mr-2" id="sort">
                        <option value="id">По ID</option>
                        <option value="date">По дате</option>
                    </select>
                    <select class="custom-select" id="direction" aria-label="Направление сортировки">
                        <option value="desc">По убыванию</option>
                        <option value="asc">По возрастанию</option>
                    </select>
                </div>
            </div>
            <div id="alert" class="alert d-none" role="alert"></div>
            <div id="comments" aria-live="polite"></div>
            <nav aria-label="Страницы комментариев">
                <ul id="pagination" class="pagination justify-content-center"></ul>
            </nav>
            <section class="card shadow-sm mt-4">
                <div class="card-body p-4">
                    <h2 class="h4 mb-3">Добавить комментарий</h2>
                    <form id="comment-form" novalidate>
                        <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
                        <div class="form-group">
                            <label for="name">Электронная почта</label>
                            <input class="form-control" type="email" id="name" name="name" maxlength="254" required autocomplete="email">
                            <div class="invalid-feedback" data-error="name">Введите корректный адрес электронной почты.</div>
                        </div>
                        <div class="form-group">
                            <label for="text">Комментарий</label>
                            <textarea class="form-control" id="text" name="text" rows="4" maxlength="5000" required></textarea>
                            <div class="invalid-feedback" data-error="text">Введите текст комментария.</div>
                        </div>
                        <div class="form-group">
                            <label for="date">Дата</label>
                            <input class="form-control" type="date" id="date" name="date" required>
                            <div class="invalid-feedback" data-error="date">Выберите дату.</div>
                        </div>
                        <button class="btn btn-primary" type="submit">Добавить</button>
                    </form>
                </div>
            </section>
        </div>
    </div>
</main>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="<?= base_url('assets/comments.js') ?>"></script>
</body>
</html>
