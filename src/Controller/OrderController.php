<?php

namespace App\Controller;

use App\Model\DishManager;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;

class OrderController extends AbstractController
{
    /**
     * Add dish to an order
     */
    public function add(int $id)
    {
        $this->increment($id);
    }

    /**
     * Sub dish to an order
     */
    public function substract(int $id)
    {
        $this->increment($id, -1);
    }

    private function increment(int $id, int $increment = 1)
    {
        $dishManager = new DishManager();
        $dish = $dishManager->selectOneById($id);

        if ($dish) {
            $dish['quantity'] = ($_SESSION['order'][$id]['quantity'] ?? 0) + $increment;
            $_SESSION['order'][$id] = $dish;
            if ($_SESSION['order'][$id]['quantity'] <= 0) {
                unset($_SESSION['order']);
            }
        }

        header("Location: /order/index");
    }

    /**
     * List an order
     */
    public function index()
    {
        $errors = $order = [];
        $dishes = $_SESSION['order'] ?? [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $order = array_map('trim', $_POST);
            if (empty($order['email'])) {
                $errors[] = 'L\'email est obligatoire';
            }
            if (!filter_var($order['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Mauvais format pour l\'email';
            }
            if (empty($dishes)) {
                $errors[] = 'Le panier est vide';
            }

            if (empty($errors)) {
                // enregistrer l'order en database
                // envoyer un email de confirmation
                $transport = Transport::fromDsn(MAILER_DSN);
                $mailer = new Mailer($transport);

                $html = $this->twig->render('Order/email.html.twig', [
                    'dishes' => $_SESSION['order'],
                    'order' => $order,
                ]);

                $email = (new Email())
                    ->from(MAIL_FROM)
                    ->to($order['email'])
                    ->subject('Hop la pizza - confirmation de commande')
                    ->html($html);

                $mailer->send($email);

                unset($_SESSION['order']);

                header('Location: /home/index/?notification=La commande est bien passée');
            }
        }

        return $this->twig->render('Order/index.html.twig', [
            'dishes' => $dishes,
            'errors' => $errors,
            'order' => $order,
        ]);
    }

    /**
     * Empty cart
     */
    public function empty()
    {
        unset($_SESSION['order']);
        header("Location: /order/index");
    }
}
