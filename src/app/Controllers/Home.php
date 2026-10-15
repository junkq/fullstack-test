<?php

namespace App\Controllers;

use App\Models\CommentModel;
use CodeIgniter\HTTP\ResponseInterface;

class Home extends BaseController
{
    private const PER_PAGE = 3;

    public function index(): string
    {
        return view('comments');
    }

    public function comments(): ResponseInterface
    {
        $page = max(1, (int) $this->request->getGet('page'));
        $sort = $this->normalizeSort((string) $this->request->getGet('sort'));
        $direction = $this->normalizeDirection((string) $this->request->getGet('direction'));
        $model = new CommentModel();
        $total = $model->countAllResults();
        $pages = max(1, (int) ceil($total / self::PER_PAGE));
        $page = min($page, $pages);
        $items = $model->orderBy($sort, $direction)->findAll(self::PER_PAGE, ($page - 1) * self::PER_PAGE);

        return $this->response->setJSON([
            'items' => $items,
            'pagination' => ['page' => $page, 'pages' => $pages, 'total' => $total],
        ]);
    }

    public function create(): ResponseInterface
    {
        $rules = [
            'name' => 'required|valid_email|max_length[254]',
            'text' => 'required|max_length[5000]',
            'date' => 'required|valid_date[Y-m-d]',
        ];
        $messages = [
            'name' => [
                'required' => 'Укажите электронную почту.',
                'valid_email' => 'Введите корректный адрес электронной почты.',
                'max_length' => 'Адрес электронной почты слишком длинный.',
            ],
            'text' => [
                'required' => 'Введите текст комментария.',
                'max_length' => 'Комментарий не должен превышать 5000 символов.',
            ],
            'date' => [
                'required' => 'Выберите дату.',
                'valid_date' => 'Введите корректную дату.',
            ],
        ];

        if (! $this->validate($rules, $messages)) {
            return $this->response->setStatusCode(422)->setJSON([
                'message' => 'Проверьте заполнение формы.',
                'errors' => $this->validator->getErrors(),
                'csrfHash' => csrf_hash(),
            ]);
        }

        $model = new CommentModel();
        $model->insert([
            'name' => trim((string) $this->request->getPost('name')),
            'text' => trim((string) $this->request->getPost('text')),
            'date' => $this->request->getPost('date'),
        ]);

        return $this->response->setStatusCode(201)->setJSON([
            'message' => 'Комментарий добавлен.',
            'csrfHash' => csrf_hash(),
        ]);
    }

    public function delete(int $id): ResponseInterface
    {
        $model = new CommentModel();

        if ($id < 1 || $model->find($id) === null) {
            return $this->response->setStatusCode(404)->setJSON([
                'message' => 'Комментарий не найден.',
                'csrfHash' => csrf_hash(),
            ]);
        }

        $model->delete($id);

        return $this->response->setJSON([
            'message' => 'Комментарий удалён.',
            'csrfHash' => csrf_hash(),
        ]);
    }

    private function normalizeSort(string $sort): string
    {
        return in_array($sort, ['id', 'date'], true) ? $sort : 'id';
    }

    private function normalizeDirection(string $direction): string
    {
        return strtolower($direction) === 'asc' ? 'asc' : 'desc';
    }
}
