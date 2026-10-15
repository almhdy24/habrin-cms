<?php
/**
 * Admin - Menu Management
 */

declare(strict_types=1);

namespace Controllers;

use Core\Controller;
use Core\Session;
use Core\Auth;
use Models\MenuModel;

class AdminMenuController extends Controller
{
    private MenuModel $menu;

    public function __construct()
    {
        parent::__construct();
        Auth::requireAdmin();
        $this->menu = $this->model(MenuModel::class);
    }

    public function index(): void
    {
        $items = $this->menu->getItems();
        $this->view('admin/menu/index', [
            'items' => $items,
            'flash' => Session::getFlash(),
            'csrf_token' => Session::csrfToken(),
        ]);
    }

    public function create(): void
    {
        if ($this->isPost()) {
            $this->validateCsrf();
            $label = trim($this->input('label'));
            $url = trim($this->input('url'));
            $order = (int) $this->input('order', 0);

            if (empty($label) || empty($url)) {
                Session::flash('error', 'Label and URL are required.');
                $this->redirect('/admin/menu/create');
            }

            $this->menu->addItem([
                'label' => htmlspecialchars($label),
                'url' => $url,
                'order' => $order,
            ]);

            Session::flash('success', 'Menu item added.');
            $this->redirect('/admin/menu');
        }

        $this->view('admin/menu/form', [
            'item' => null,
            'flash' => Session::getFlash(),
            'csrf_token' => Session::csrfToken(),
        ]);
    }

    public function edit(int|string $id): void
    {
        $id = (int) $id;
        $items = $this->menu->getItems();
        if (!isset($items[$id])) {
            Session::flash('error', 'Item not found.');
            $this->redirect('/admin/menu');
        }
        $item = $items[$id];

        if ($this->isPost()) {
            $this->validateCsrf();
            $label = trim($this->input('label'));
            $url = trim($this->input('url'));
            $order = (int) $this->input('order', 0);

            if (empty($label) || empty($url)) {
                Session::flash('error', 'Label and URL are required.');
                $this->redirect('/admin/menu/edit/' . $id);
            }

            $this->menu->updateItem($id, [
                'label' => htmlspecialchars($label),
                'url' => $url,
                'order' => $order,
            ]);

            Session::flash('success', 'Menu item updated.');
            $this->redirect('/admin/menu');
        }

        $this->view('admin/menu/form', [
            'item' => $item,
            'id' => $id,
            'flash' => Session::getFlash(),
            'csrf_token' => Session::csrfToken(),
        ]);
    }

    public function delete(int|string $id): void
    {
        $id = (int) $id;
        if ($this->isPost()) {
            $this->validateCsrf();
            $this->menu->deleteItem($id);
            Session::flash('success', 'Menu item deleted.');
            $this->redirect('/admin/menu');
        }
        // Show confirmation if GET
        $items = $this->menu->getItems();
        if (!isset($items[$id])) {
            Session::flash('error', 'Item not found.');
            $this->redirect('/admin/menu');
        }
        $this->view('admin/menu/delete', [
            'item' => $items[$id],
            'id' => $id,
            'csrf_token' => Session::csrfToken(),
        ]);
    }

    public function moveUp(int|string $id): void
    {
        $id = (int) $id;
        if ($this->isPost()) {
            $this->validateCsrf();
            if ($this->menu->moveUp($id)) {
                Session::flash('success', 'Menu item moved up.');
            }
        }
        $this->redirect('/admin/menu');
    }

    public function moveDown(int|string $id): void
    {
        $id = (int) $id;
        if ($this->isPost()) {
            $this->validateCsrf();
            if ($this->menu->moveDown($id)) {
                Session::flash('success', 'Menu item moved down.');
            }
        }
        $this->redirect('/admin/menu');
    }

    private function validateCsrf(): void
    {
        $token = $this->input('csrf_token', '');
        if (!Session::validateCsrfToken($token)) {
            Session::flash('error', 'Invalid CSRF token.');
            $this->redirect('/admin/menu');
        }
    }
}
